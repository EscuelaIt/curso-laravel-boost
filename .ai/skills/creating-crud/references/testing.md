# Tests del CRUD

Tests de Pest (feature), creados con `php artisan make:test --pest {Entity}/...`. Lee antes la skill `testing-best-practices`.

## Archivos de referencia

| Archivo | Cubre |
| --- | --- |
| `tests/Feature/User/ListUserControllerTest.php` | Listado: invitados 401, listado autenticado, búsqueda por keyword, filtros por columna permitida, columnas no permitidas ignoradas, filtros personalizados (activo/inactivo), atributos sensibles ocultos. |
| `tests/Feature/User/UserControllerTest.php` | Store, show, update y destroy: invitados 401 con dataset, creación 201, validación 422, unique en store/update, 404, borrado y autorización 403. |
| `tests/Feature/User/ActionUserControllerTest.php` | Acciones: invitados 401, ejecución correcta, casos límite de la lógica, resultado de error y validación de `data`. |
| `tests/Feature/UserSectionTest.php` | Página Inertia del listado: redirección a login y componente `users/Index`. |
| `tests/Feature/UserSinglePageTest.php` | Página Inertia del single: login, componente `users/Show` con la prop del id y `ErrorPage` con 404 si no existe. |

## Patrones

- Autenticación en la API con `Sanctum::actingAs($user)`; en las páginas web con `$this->actingAs($user)`.
- Peticiones con `getJson` / `postJson` / `putJson` / `deleteJson` y rutas por nombre (`route('api.users.store')`).
- Comprueba la estructura del wrapper, no solo el código HTTP:

```php
$response->assertCreated()
    ->assertJsonPath('status', 201)
    ->assertJsonPath('message', 'User created.')
    ->assertJsonPath('data.email', 'ana@example.com')
    ->assertJsonMissingPath('data.password');
```

- Validación: `assertUnprocessable()`, `assertJsonPath('message', 'Unprocessable Entity')` y `assertJsonValidationErrors([...])` (los errores van en `errors`).
- Listado: los items están en `data.result.data` y el total en `data.countItems`. Filtros como query string: `'filters' => [['name' => 'is_favorite', 'active' => 'true', 'value' => 'true']]`; búsqueda con `'keyword' => '...'`.
- Acciones: `postJson(route('api.users.action'), ['type' => 'UserMessageAction', 'relatedIds' => [...], 'data' => [...]])`; el resultado va en `data.action` y `data.data.*`; los errores de la acción responden 422 con su `message`.
- Accesos de invitados agrupados con un dataset (`->with([...])`) de método + nombre de ruta.
- Estado de la base de datos con `assertDatabaseHas`, `assertModelMissing`/`assertModelExists` (o `assertSoftDeleted` si el modelo usa `SoftDeletes`) y `expect(Model::count())`.
- Datos con factories y sus estados (`User::factory()->favorite()`).
- Páginas: `assertInertia(fn (Assert $page) => $page->component('users/Show')->where('userId', $user->id))`.

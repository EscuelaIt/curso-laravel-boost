# Backend del CRUD (API REST)

Implementación de referencia: CRUD de usuarios. Crea las clases con `php artisan make:*` (`make:controller User/StoreUserController`, `make:request User/StoreUserRequest`, `make:class`...) y después adapta el contenido copiando el archivo equivalente de usuarios.

## Rutas — `routes/api.php`

```php
Route::middleware('auth:sanctum')->name('api.')->group(function () {
    Route::get('/users', [ListUserController::class, 'index'])->name('users.index');
    Route::post('/users', [StoreUserController::class, 'store'])->name('users.store');
    Route::post('/users/action', ActionUserController::class)->name('users.action');
    Route::get('/users/{user}', [ShowUserController::class, 'show'])->name('users.show');
    Route::match(['put', 'patch'], '/users/{user}', [UpdateUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [DeleteUserController::class, 'destroy'])->name('users.destroy');
});
```

- Añade las rutas de la nueva entidad **dentro del mismo grupo** (no crees otro grupo).
- Nunca `Route::resource()` / `Route::apiResource()`.
- Los controladores CRUD usan el nombre de método REST (`index`, `store`, `show`, `update`, `destroy`); el de acciones es invocable (`__invoke`).
- `/users/action` va antes que `/users/{user}`.
- La URL base (`api/users`) es la que recibe `CrudConfigBuilder` en el frontend; `@dile/crud` construye el resto de URLs (`api/users/{id}`, `api/users/action`) a partir de ella, así que respeta exactamente este esquema.
- La autenticación es por sesión con Sanctum (`$middleware->statefulApi()` en `bootstrap/app.php`), por eso los componentes Lit pueden llamar a la API sin tokens.

## Controladores — `app/Http/Controllers/User/`

Cada controlador tiene un único caso de uso, un PHPDoc corto y devuelve `Illuminate\Http\Response` generada por `APIResponse`.

| Caso de uso | Archivo | Respuesta |
| --- | --- | --- |
| Listar | `ListUserController.php` | `$this->list($this->userListService)` (trait `ResourceListable`, servicio inyectado por constructor) |
| Crear | `StoreUserController.php` | `APIResponse::created($user, 'User created.')` |
| Mostrar | `ShowUserController.php` | `APIResponse::ok($user, 'User retrieved.')` |
| Actualizar | `UpdateUserController.php` | `APIResponse::ok($user, 'User updated.')` |
| Eliminar | `DeleteUserController.php` | `APIResponse::ok($user, 'User deleted.')` — devuelve el recurso eliminado |
| Acciones | `ActionUserController.php` | `$this->handleAction($userActionService)` (trait `ActionHandler`, servicio inyectado en `__invoke`) |

Patrones:

- Route model binding (`User $user`) en show/update/delete; si no existe, el 404 lo envuelve `bootstrap/app.php`.
- Crear y actualizar usan `$request->validated()`. Los valores calculados en el servidor se añaden en el controlador:

```php
$user = User::create([
    ...$request->validated(),
    'password' => Str::password(),
]);
```

- Eliminar recibe su propio FormRequest (`DeleteUserRequest`) aunque no tenga reglas, para centralizar la autorización.
- Se devuelve el modelo directamente como `data`; los atributos sensibles se ocultan en el modelo con `#[Hidden([...])]` (ver `app/Models/User.php`), no en el controlador.

## FormRequests — `app/Http/Requests/User/`

- `StoreUserRequest.php` y `UpdateUserRequest.php`: `rules()` + `failedValidation()` con `APIResponse::unprocessableEntity($validator->errors())`.
- Si las reglas se comparten entre store/update (u otros formularios de la app), se extraen a un trait en `app/Concerns/` (ver `app/Concerns/ProfileValidationRules.php`). En update, las reglas `unique` ignoran el registro actual: `$this->profileRules($this->route('user')->id)`.
- Campos opcionales con `'sometimes'` (p. ej. `'is_favorite' => ['sometimes', 'boolean']`).
- Autorización en `authorize()` y respuesta en `failedAuthorization()` (ver `DeleteUserRequest.php`):

```php
public function authorize(): bool
{
    return $this->user()->isNot($this->route('user'));
}

protected function failedAuthorization(): void
{
    APIResponse::forbidden([], 'You cannot delete your own user.');
}
```

- No se piden en el formulario ni se validan campos que genera el servidor.

## Listado — `ListService`

Archivo: `app/Services/ListServices/UserListService.php`. Detalle completo en la skill `laravel-api-kit-development` (`references/api-resource-listing.md`).

```php
class UserListService extends ListService
{
    protected string $listModel = User::class;
    protected ?int $maxPerPage = 100;
    protected ?array $availableFilterColumns = ['is_favorite'];
    protected ?array $availableScopes = [];
    protected ?array $availableIncludes = [];

    protected function applyKeywordFilter(?string $keyword): void
    {
        $this->query->similar($keyword);
    }

    protected function customFilters(): array
    {
        return [new EmailDomainFilter];
    }
}
```

- La búsqueda por palabra clave delega en un scope `similar()` del modelo (atributo `#[Scope]`, ver `app/Models/User.php`), que no hace nada si el keyword está vacío.
- `availableFilterColumns` es una lista blanca explícita: solo esas columnas se pueden filtrar por igualdad.
- Filtros que no son igualdad de columna: clase en `app/Services/ListServices/Filters/` que extiende `CustomFilter`, con `$filterName` y `apply(Builder $query)` (ver `EmailDomainFilter.php`). Guía en `laravel-api-kit-development` → `references/search-custom-filters.md`.
- La respuesta del listado viene en `data.result.data` (items), `data.countItems`, etc. Por eso el frontend necesita un `ResponseApiAdapter` propio (ver `references/frontend.md`).

## Acciones — `ActionService` + `CrudAction`

Guías: `laravel-api-kit-development` → `references/api-action-system-configuration.md` y `references/creating-api-actions.md`.

- Servicio: `app/Services/ActionServices/UserActionService.php`, con `$actionModel` y el mapa `$actionTypes` (`'UserMessageAction' => UserMessageAction::class`). La **clave** del mapa es el nombre que usa el frontend en `actions.list/single[].name` y en el atributo `action` del formulario de la acción.
- Acción: `app/Actions/User/UserMessageAction.php`, extiende `CrudAction`:
  - `validationRules()` con las reglas de los campos del formulario de la acción (llegan en `data`).
  - `handle()` trabaja con `$this->models` (los seleccionados por `relatedIds`), `$this->data` y `$this->user`, y devuelve `createActionResultSuccess($message, $data)` o `createActionResultError($message)`.
- Payload que envía `@dile/crud`: `{ type: 'UserMessageAction', relatedIds: [...], data: {...} }`.
- El borrado usa el endpoint `DELETE` (el `DeleteAction` genérico que trae el scaffold de la config se retira) y eventualmente puede haber acciones de borrado para poder borrar entidades en lote.

## Modelo, factory y seeder

- `app/Models/User.php`: `$guarded`, `casts()`, `#[Hidden]`, PHPDoc `@property`, scope `similar()`. En modelos nuevos añadir `SoftDeletes` (regla `.ai/rules/models.md`).
- `database/factories/UserFactory.php`: estados con nombre de dominio (`favorite()`) que usan los tests.
- `database/seeders/UserSeeder.php`: seeder dedicado a la entidad, llamado desde `DatabaseSeeder`.

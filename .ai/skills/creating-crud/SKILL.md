---
name: creating-crud
description: "Úsala siempre que se pida crear un CRUD, gestionar una nueva entidad en el panel, añadir endpoints REST de una entidad (listar, crear, ver, editar, borrar, acciones) o crear sus componentes de @dile/crud. Describe el patrón completo del proyecto: API con laravel-api-kit + apiwrapper, componentes Lit de dile-components y páginas Inertia/Svelte, tomando el CRUD de usuarios como implementación de referencia."
license: MIT
metadata:
  author: laravel
---

# Sistemas de CRUD

Un CRUD en esta aplicación es una pieza vertical que atraviesa tres capas:

1. **API REST JSON** (`routes/api.php`): un controlador por caso de uso, respuestas con `APIResponse` (apiwrapper), listado y acciones con `escuelait/laravel-api-kit`.
2. **Componentes Lit** con `@dile/crud` (`resources/js/components/{entity}/` + `resources/js/resources/{entity}Config.js`), que consumen esa API.
3. **Páginas Inertia + Svelte** (`resources/js/pages/{entities}/`) que solo montan el custom element dentro del layout.

La implementación de referencia es el **CRUD de usuarios**. Antes de crear un CRUD nuevo, abre los archivos equivalentes de usuarios y cópialos adaptando nombres: son ejemplos probados que funcionan.

## Skills relacionadas que hay que activar

- `laravel-api-kit-development`: detalle de `ListService`, filtros personalizados, `ActionService` y `CrudAction`.
- `dile-components`: API de los componentes `@dile/ui` y `@dile/crud`.
- `inertia-svelte-development` y `wayfinder-development`: páginas, layout props y rutas tipadas.
- `testing-best-practices`: antes de escribir los tests.
- Reglas de `.ai/rules/controllers.md` y `.ai/rules/models.md` (controlador por caso de uso agrupado por entidad, `$guarded`, `SoftDeletes` en modelos nuevos).

## Convenciones de nombres (entidad `Product` como ejemplo)

| Pieza | Nombre | Referencia en usuarios |
| --- | --- | --- |
| Carpeta de controladores | `app/Http/Controllers/Product/` | `app/Http/Controllers/User/` |
| Controladores API | `List/Store/Show/Update/Delete/ActionProductController` | `ListUserController`, `StoreUserController`... |
| Controlador de página single | `ShowProductPageController` | `app/Http/Controllers/User/ShowUserPageController.php` |
| FormRequests | `app/Http/Requests/Product/{Store,Update,Delete}ProductRequest` | `app/Http/Requests/User/` |
| ListService | `app/Services/ListServices/ProductListService.php` | `app/Services/ListServices/UserListService.php` |
| Filtros personalizados | `app/Services/ListServices/Filters/*Filter.php` | `app/Services/ListServices/Filters/EmailDomainFilter.php` |
| ActionService | `app/Services/ActionServices/ProductActionService.php` | `app/Services/ActionServices/UserActionService.php` |
| Acciones | `app/Actions/Product/{Nombre}Action.php` | `app/Actions/User/UserMessageAction.php` |
| Nombres de ruta API | `api.products.{index,store,action,show,update,destroy}` | `routes/api.php` |
| Nombres de ruta web | `products.index`, `products.show` | `routes/web.php` |
| Config de dile-crud | `resources/js/resources/productConfig.js` | `resources/js/resources/userConfig.js` |
| Componentes Lit | `resources/js/components/product/eit-product-{crud,single,item,form,detail}.js` | `resources/js/components/user/` |
| Componente de acción | `eit-product-{nombre}-action.js` | `resources/js/components/user/eit-user-message-action.js` |
| Páginas Svelte | `resources/js/pages/products/{Index,Show}.svelte` | `resources/js/pages/users/` |
| Tests | `tests/Feature/Product/*` + `tests/Feature/Product{Section,SinglePage}Test.php` | `tests/Feature/User/`, `tests/Feature/UserSectionTest.php`, `tests/Feature/UserSinglePageTest.php` |

El prefijo de los custom elements del proyecto es `eit-`.

## Checklist para crear un CRUD

1. **Modelo** (si no existe): migración, modelo con `$guarded` y `SoftDeletes`, `casts()`, `#[Hidden]` para campos sensibles, scope `similar()` para la búsqueda por palabra clave, factory con estados útiles y seeder dedicado registrado en `DatabaseSeeder`.
2. **Backend API**: FormRequests, controladores, `ListService`, (opcional) `ActionService` + acciones, rutas en `routes/api.php`. Ver `references/backend.md`.
3. **Frontend Lit**: `{entity}Config.js`, componentes `crud`, `single`, `item`, `form`, `detail` y los de acciones. Ver `references/frontend.md`.
4. **Panel Svelte**: rutas web, páginas `Index` y `Show`, entrada en el sidebar. Ver `references/frontend.md`.
5. **Tests** de API y de páginas. Ver `references/testing.md`.
6. `vendor/bin/pint --dirty --format agent`, ejecutar los tests afectados y pedir al usuario que ejecute la suite completa. Si el usuario no ve los cambios en el panel, sugerir `npm run dev` / `npm run build`.

## Reglas clave (resumen)

- **Nada de `Route::resource` / `apiResource`**: las rutas se declaran una a una en `routes/api.php`, porque el listado usa un controlador propio y cada recurso tiene rutas personalizadas (como `/action`) que deben verse en el archivo de rutas.
- Las rutas API siguen REST: `GET /products`, `POST /products`, `POST /products/action`, `GET /products/{product}`, `PUT|PATCH /products/{product}`, `DELETE /products/{product}`. La ruta `/action` se declara **antes** de `/{product}`.
- Todas las rutas API van dentro del grupo `Route::middleware('auth:sanctum')->name('api.')` ya existente, para que sus nombres (`api.products.index`) no choquen con los de las páginas web (`products.index`).
- Un controlador por caso de uso. Validación y autorización siempre en FormRequest, con `failedValidation()` / `failedAuthorization()` respondiendo con `APIResponse`.
- Todas las respuestas con `Negartarh\APIWrapper\Facades\APIResponse` y tipo de retorno `Illuminate\Http\Response`. Mensajes en inglés y sin `__()`.
- Los 401 y 404 de `api/*` ya se envuelven globalmente en `bootstrap/app.php`; los 404 del panel renderizan `ErrorPage` desde `app/Providers/AppServiceProvider.php`. No los gestiones en cada controlador.
- Svelte solo pone la página (layout, `Heading`, breadcrumbs, toasts); toda la funcionalidad de CRUD la resuelven los componentes Lit de `@dile/crud` hablando con `/api/...`.
- Los campos que calcula el servidor (como la contraseña aleatoria en `StoreUserController`) no se piden en el formulario ni se validan en el FormRequest.

## Referencias

- `references/backend.md`: rutas, controladores, FormRequests, ListService, filtros, ActionService y acciones, con fragmentos de los archivos de usuarios.
- `references/frontend.md`: config de `@dile/crud`, componentes Lit, feedback con toasts, páginas Svelte, rutas web y sidebar.
- `references/testing.md`: qué tests crear y cómo comprobar la estructura del wrapper.

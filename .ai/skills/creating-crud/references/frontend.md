# Frontend del CRUD (Lit + @dile/crud + Inertia/Svelte)

Implementación de referencia: CRUD de usuarios. Reparto: los componentes Lit hacen toda la funcionalidad hablando con `/api/...`; Svelte solo aporta la página, el layout, los breadcrumbs y los toasts. Consulta la skill `dile-components` para la API de cada componente `@dile/*`.

## Configuración de @dile/crud — `resources/js/resources/userConfig.js`

Es el archivo central del CRUD. Exporta una instancia de `CrudConfigBuilder` que usan tanto el componente de listado como el de single:

```js
class UserResponseApiAdapter extends ResponseApiAdapter {
  getElementList() {
    return this.response.data.result.data;
  }
}

export const userConfig = new CrudConfigBuilder('api/users', {
  responseAdapter: new UserResponseApiAdapter(),
  templates: { item, insertForm, updateForm, help, detail, relations, formActions, formSingleActions },
  customization: { ... },
  computeItemId: (element) => element.id,
  sort: { ... },
  availableFilters: [ ... ],
  actions: { list: [...], single: [...] },
});
```

Puntos importantes:

- **Endpoint**: `'api/{entities}'` sin barra inicial (el `baseURL` `/` lo pone Axios en `resources/js/boostrap.js`).
- **`ResponseApiAdapter` propio**: obligatorio, porque apiwrapper mete el listado paginado en `data.result.data`. Sobrescribe `getElementList()` como en usuarios.
- **Imports**: la config importa los componentes que usa en sus templates (`eit-user-item`, `eit-user-form`, `eit-user-detail`, las acciones y `@dile/ui/components/pages/pages.js`).
- **templates**:
  - `item`: `<eit-user-item .item=${item}>`.
  - `insertForm` / `updateForm`: el mismo `<eit-user-form>` con `id="insertform"` / `id="updateform"`.
  - `detail`: `<eit-user-detail .item=${item}>` (vista del single).
  - `formActions` / `formSingleActions`: un `<dile-pages attrForSelected="action" selected="${actionName}">` con un componente de formulario por acción, cada uno con `action="{NombreAccion}"`.
- **actions**: `list` (acciones masivas sobre los seleccionados) y `single` (sobre un item). `name` debe coincidir con la clave de `$actionTypes` del `ActionService`. Si hay acciones en el listado, `hideCheckboxSelection: false`.
- **customization / sort / availableFilters**: ajústalos a lo que soporta realmente el `ListService`. Los filtros de `availableFilters` deben corresponder a `availableFilterColumns` o a los `CustomFilter` del backend; si no hay filtros reales, deja `disableFilter: true` (el scaffold trae filtros de ejemplo `column`/`column2` que no hay que dejar activos). Lo mismo con `disableSort` y las opciones de ordenación.

## Componentes Lit — `resources/js/components/user/`

Todos son `LitElement` con `static styles` (`:host { display: block; }`), `static get properties()` y `customElements.define('eit-...')` al final.

| Componente | Archivo | Qué hace |
| --- | --- | --- |
| `eit-user-crud` | `eit-user-crud.js` | Envuelve `<dile-crud .config=${this.config} language=...>`; `this.config = userConfig.getConfig()` en el constructor. Lo monta la página `Index`. |
| `eit-user-single` | `eit-user-single.js` | Envuelve `<dile-crud-single relatedId=... .config=... language=...>` (import `@dile/crud/components/single/crud-single`). Lo monta la página `Show`. |
| `eit-user-item` | `eit-user-item.js` | Una fila del listado; el nombre enlaza a la página single (`/users/${this.item.id}`). |
| `eit-user-form` | `eit-user-form.js` | Formulario de alta/edición con el mixin `DileForm(LitElement)`; campos `dile-input` con `name` igual al campo de la API, `label`, `hideErrorOnInput` y `focusOnStart` en el primero. Los errores 422 de `errors` se pintan solos en cada campo por su `name`. |
| `eit-user-detail` | `eit-user-detail.js` | Presentación del item en el single. |
| `eit-user-message-action` | `eit-user-message-action.js` | Formulario de una acción con `DileForm`; los `name` de sus campos son las claves de `validationRules()` de la `CrudAction`. |

- El formulario solo contiene los campos que el usuario edita; los que calcula el servidor no aparecen (en usuarios se retiraron los de contraseña).
- Importa cada componente de `@dile/ui` que se use (`@dile/ui/components/input/input.js`, `.../textarea/textarea.js`...).

## Feedback al usuario — `resources/js/lib/crud-feedback.ts`

`initializeCrudFeedback()` (llamado en `resources/js/app.ts`) escucha en `window` los eventos que emiten los componentes de `@dile/crud` (`delete-success`, `delete-error`, `crud-action-success`, `crud-action-error`) y los muestra con toasts de `svelte-sonner`, usando `detail.msg` (el `message` de la API) o un mensaje por defecto. Es global: no hay que hacer nada por cada CRUD. Si necesitas feedback de otro evento de dile-crud, añádelo al array `crudFeedbackEvents`.

## Páginas Svelte — `resources/js/pages/users/`

### Listado — `Index.svelte`

- Breadcrumbs estáticos en `<script module>` con `export const layout = { breadcrumbs: [...] }`, usando la ruta de Wayfinder `index` de `@/routes/users`.
- En `onMount`, importa dinámicamente `@/boostrap.js` (configura Axios de `@dile/crud` con credenciales y XSRF) y después el componente Lit. Se hace dinámico porque los custom elements necesitan `window`.
- Renderiza `AppHead`, `Heading` y el custom element con `language="es"`:

```svelte
<AppHead title="Users" />

<div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
    <Heading title="Users" description="Manage the users of the project" />

    <eit-user-crud language="es"></eit-user-crud>
</div>
```

### Detalle — `Show.svelte`

- Recibe como prop solo el id (`userId`); los datos los carga `eit-user-single` desde la API.
- Breadcrumbs dinámicos con `setLayoutProps` dentro de `$effect` (dependen de la prop): `index()` y `show(userId)`.
- Escucha `oncrud-item-detail-load-error` para mostrar `toast.error(...)` y redirigir con `router.visit(dashboard())`.

## Rutas web y navegación

- `routes/web.php`, dentro del grupo `['auth', 'verified']`:

```php
Route::inertia('users', 'users/Index')->name('users.index');
Route::get('users/{user}', ShowUserPageController::class)->whereNumber('user')->name('users.show');
```

- `app/Http/Controllers/User/ShowUserPageController.php`: invocable, usa route model binding (404 si no existe → `ErrorPage`, configurado en `app/Providers/AppServiceProvider.php`) y pasa solo el id: `Inertia::render('users/Show', ['userId' => $user->id])`.
- Wayfinder genera `@/routes/users` (`index`, `show`) a partir de estas rutas; úsalas en páginas y sidebar en lugar de URLs a mano.
- Entrada en el menú: `resources/js/components/AppSidebar.svelte`, en `mainNavItems`, con un icono de `@lucide/svelte` y `href: users()` (`import { index as users } from '@/routes/users'`).
- Textos de la UI de Svelte en inglés, como en el resto del starter kit; las etiquetas de acciones en `userConfig.js` pueden ir en español (`'Enviar mensaje'`) porque `@dile/crud` se usa con `language="es"`.

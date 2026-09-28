## Frontend del panel (Inertia + Svelte + Lit)

El panel usa Inertia con Svelte, partiendo del starter kit oficial de Laravel para Svelte. El reparto de responsabilidades es estricto:

- **Svelte** para todo lo que es estructura y experiencia de usuario: páginas (`resources/js/pages`), layouts, navegación, breadcrumbs, cabeceras de sección y feedback al usuario (toasts con `svelte-sonner`, flash toasts de Inertia, modales del panel). Reutiliza los componentes y patrones del starter kit (`components/ui/*`, `Heading`, `AppHead`...) antes de crear otros nuevos.
- **Lit** con los packages de `dile-components` (`@dile/ui`, `@dile/crud`, `@dile/utils`, `@dile/iconlib`) para la funcionalidad: CRUD de entidades, formularios, listados y cualquier interacción que hable con la API REST.
- La página Svelte solo monta el custom element (`<{prefix}-{entity}-crud language="es">`) y lo envuelve con el layout, el `Heading` y los breadcrumbs. No reimplementes en Svelte lo que ya resuelve `@dile/crud`.
- Los componentes Lit hablan con la API JSON (`/api/...`), no con endpoints de Inertia. Las rutas de Inertia solo renderizan páginas.

---
paths:
  - 'app/Http/Controllers/**'
---

# Controllers

## Un controlador independiente por cada caso de uso
Cada caso de uso (listar, crear, mostrar, actualizar, eliminar, acciones personalizadas...) va en su propio controlador, agrupado por entidad en su carpeta (p. ej. `app/Http/Controllers/User/StoreUserController.php`). No agrupes varios casos de uso en un mismo controlador tipo resource.

Nombra el controlador con el verbo del caso de uso + la entidad: `ListUserController`, `StoreUserController`, `ShowUserController`, `UpdateUserController`, `DeleteUserController`.

## Controladores agrupados en carpetas por entidad
Los controladores se organizan en ramilletes: todos los controladores de una misma entidad viven en una carpeta con el nombre de la entidad en singular y PascalCase, con su namespace correspondiente. Por ejemplo, todos los de usuarios van en `app/Http/Controllers/User/` (namespace `App\Http\Controllers\User`).

No dejes controladores de una entidad sueltos en la raíz de `app/Http/Controllers`. Créalos con `php artisan make:controller User/StoreUserController`.

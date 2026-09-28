## Respuestas de los endpoints de API

Todas las respuestas de la API JSON (`routes/api.php`) se generan con el package `negartarh/apiwrapper`, que viene instalado con `escuelait/laravel-api-kit`. Así todas las respuestas comparten la misma estructura: `status`, `message`, `data`, `errors`, `execution` y `version`.

- No devuelvas `response()->json(...)`, arrays ni modelos directamente desde un controlador de API. Usa siempre la facade `Negartarh\APIWrapper\Facades\APIResponse`.
- Usa el método que corresponda al código HTTP según REST. Firma: `APIResponse::metodo($data, $message, $headers)`; el mensaje es opcional y, si se omite, se usa el texto estándar del código HTTP.
  - `ok($data, $message)`: 200 al consultar o actualizar un recurso.
  - `created($data, $message)`: 201 al crear un recurso.
  - `ok($data, $message)` también al eliminar, devolviendo el recurso eliminado (el frontend con `@dile/crud` espera `message` y `data`).
  - `badRequest($errors)`: 400, `unauthorized()`: 401, `forbidden()`: 403, `notFound()`: 404, `conflict()`: 409, `unprocessableEntity($errors)`: 422.
- Los métodos con código 4xx/5xx lanzan una `HttpResponseException` en lugar de devolver la respuesta, así que se pueden usar para cortar la ejecución en cualquier punto.
- Los errores 401 (sin autenticar) y 404 (ruta o modelo inexistente) de las rutas `api/*` ya se envuelven de forma global en `bootstrap/app.php` con `APIResponse::unauthorized()` y `APIResponse::notFound()`. No los gestiones en cada controlador.
- Las validaciones se hacen en un FormRequest que sobrescribe `failedValidation()` para responder con el mismo formato:

```php
use Illuminate\Contracts\Validation\Validator;
use Negartarh\APIWrapper\Facades\APIResponse;

protected function failedValidation(Validator $validator): void
{
    APIResponse::unprocessableEntity($validator->errors());
}
```

- No envuelvas los mensajes con `__()`: el wrapper ya los traduce (`apiwrapper.localization` está activado). Pasa el texto en inglés, por ejemplo `APIResponse::created($user, 'User created.')`, y añade la traducción a los archivos de idioma si hace falta.
- Los métodos del controlador devuelven `Illuminate\Http\Response`, que es lo que genera el wrapper (no `JsonResponse`).
- Ejemplo de controlador:

```php
public function store(StoreUserRequest $request): Response
{
    $user = User::create($request->validated());

    return APIResponse::created($user, 'User created.');
}
```

- En los tests, comprueba la estructura del wrapper: el código HTTP, `message`, los datos dentro de `data` y los errores de validación dentro de `errors`.

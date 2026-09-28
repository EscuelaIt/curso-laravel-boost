---
name: creating-crud
description: "Cada vez que se solicita la creación de un CRUD en la aplicación se debe atender siguiendo las convenciones y buenas prácticas establecidas."
license: MIT
metadata:
  author: laravel
---

# Sistemas de CRUD

A continuación se detallan las pautas y convenciones a seguir al crear un CRUD en la aplicación.

## Endpoints

Los endpoints deben seguir las convenciones de REST API. 

## No usar resources de Laravel en las rutas

No quiero usar resources de Laravel en las rutas, prefiero ver un grupo de endpoints definidos manualmente, ya que los listados los hago con mi propio controlador y vamos a añadir otras rutas personalizadas en cada recurso que quiero ver definidas en el archivo de rutas.

## Validaciones

Las validaciones deben ser realizadas con sus formrequest.

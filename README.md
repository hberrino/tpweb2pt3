# TPE Web 2 - Parte 3

Integrante: Hernan Berrino Malaccorto

Este repo corresponde a la parte 3 del TPE. La idea sigue siendo la misma del trabajo anterior: una tienda de ropa y accesorios de estetica gotica. En esta parte no hay frontend, solamente una API REST para poder consultar y modificar productos desde Postman o desde otro sistema.

La base de datos es la misma que se uso en la entrega anterior: `goth_store`. Inclui el archivo `goth_store.sql` en este repo para poder importarla de nuevo si hace falta. No agregue tablas nuevas ni cambie columnas, para no romper la entrega anterior.

Para probarlo hay que poner la carpeta del proyecto dentro de `C:\xampp\htdocs\`, prender Apache y MySQL desde XAMPP e importar la base desde phpMyAdmin. No importa el nombre exacto de la carpeta. Si la carpeta se llama `tpweb2pt3`, se prueba con:

`http://localhost/tpweb2pt3`

Si la carpeta se llama distinto, se cambia esa parte de la URL. Por ejemplo, si la carpeta se llama `api-goth`, seria:

`http://localhost/api-goth`

El codigo no tiene hardcodeado el nombre de la carpeta.

Token para POST y PUT:

`goth-store-token`

En Postman se manda en Headers:

`X-API-Token: goth-store-token`

Los GET no necesitan token.

Endpoints:

GET `/api/productos`

Trae todos los productos.

Ejemplo:

`http://localhost/nombre-carpeta/api/productos`

Tambien acepta ordenar:

`http://localhost/nombre-carpeta/api/productos?sort=precio&order=DESC`

Campos para ordenar:

`producto_id`, `nombre`, `precio`, `categoria_id`, `categoria_nombre`

Tambien acepta filtrar por categoria:

`http://localhost/nombre-carpeta/api/productos?categoria_id=4`

Tambien acepta buscar por nombre:

`http://localhost/nombre-carpeta/api/productos?buscar=baggy`

Tambien acepta paginar:

`http://localhost/nombre-carpeta/api/productos?page=1&limit=3`

Se pueden combinar parametros:

`http://localhost/nombre-carpeta/api/productos?categoria_id=4&sort=precio&order=ASC&page=1&limit=2`

GET `/api/productos/{id}`

Trae un producto puntual.

Ejemplo:

`http://localhost/nombre-carpeta/api/productos/1`

POST `/api/productos`

Crea un producto. Necesita token.

URL:

`http://localhost/nombre-carpeta/api/productos`

Headers:

`Content-Type: application/json`

`X-API-Token: goth-store-token`

Body:

```json
{
  "nombre": "Anillo gotico",
  "precio": 9000,
  "categoria_id": 1
}
```

PUT `/api/productos/{id}`

Modifica un producto existente. Necesita token.

URL:

`http://localhost/nombre-carpeta/api/productos/1`

Headers:

`Content-Type: application/json`

`X-API-Token: goth-store-token`

Body:

```json
{
  "nombre": "Anillo gotico plateado",
  "precio": 11000,
  "categoria_id": 1
}
```

GET `/api/categorias`

Trae todas las categorias.

Ejemplo:

`http://localhost/nombre-carpeta/api/categorias`

GET `/api/categorias/{id}`

Trae una categoria puntual.

Ejemplo:

`http://localhost/nombre-carpeta/api/categorias/1`

Codigos que usa la API:

`200`: salio bien una consulta o modificacion.

`201`: se creo un producto.

`400`: faltan datos, algun parametro esta mal o el JSON esta mal armado.

`401`: falta el token o esta mal.

`404`: no se encontro el recurso pedido.

`500`: error de conexion con la base.

Con esto se cubre lo pedido: GET de una coleccion, GET por ID, ordenamiento ASC/DESC, POST, PUT, manejo de codigos 200/201/400/404 y documentacion de endpoints. Tambien agregue algunos opcionales: paginacion, filtro, busqueda, orden por varios campos y token para modificar datos.

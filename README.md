## Crear una API Web Básica

# Tecnologías 
* PHP7
* NGINX
* COMPOSER
* PDO
* MYSQL

## Identificar el VERBO
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
}

## Serializar un objeto
json_encode();

## Codificación de binarios 
base64_encode()
base64_decode()

## el standard psr-4

## Arquitectura Simple

```json

request-->api-->controler.method-->"orm"-->database
response<--(json)---<--api--(list)--<--"orm"--database

```
## VERBOS HTTP que se usaran   
* GET
* POST
> Ejemplo de mensaje:
```json

{
    "id":0,
    "nombre":"Archivo1",
    "contenido":"data:image/png;base64,sjsjjs"
}
```
> Ejemplo de un registro nuevo
POST: leo.api/Geo/paises

```json
{
    "id":0,
    "nombre":"Alemania"
}
```

# urls
```plain
index.php?c=[CONTROLLER_NAME]e=[ENDPOINT_NAME]&i=[IDENTIFIER]
leo.api/[CONTROLLER_NAME]/[ENDPOINT_NAME]/[IDENTIFIER]

http://localhost:80/leo.api/Geo/Paises/1
http://localhost:80/leo.api/Media/archivos/1
```
   
## mod rewrite settings(NGINX)
```plain
rewrite ^/leo.api/([^/]+)/([^/]+)/?$ /leo/index.php?c=$1&e=$2? last;
rewrite ^/leo.api/([^/]+)/([^/]+)/([^/]+)/?$ /leo/index.php?c=$1&e=$2&i=$3? last;   
``` 
## mod rewrite settings(APACHE)
```plain
RewriteEngine on
RewriteRule ^leo.api/(.*)/(.*)/(.*)$ leo/index.php?c=$1&e=$2&i=$3 [QSA]
RewriteRule ^leo.api/(.*)/(.*)$ leo/index.php?c=$1&e=$2 [QSA]
```

# Database scripts (mysql) 
```mysql
CREATE TABLE paises (
     id MEDIUMINT NOT NULL AUTO_INCREMENT,
     nombre VARCHAR(256) NOT NULL,
     PRIMARY KEY (id)
);

create table archivos(
     id MEDIUMINT NOT NULL AUTO_INCREMENT,
     nombre VARCHAR(256) NOT NULL,
     contenido longblob,
	 primary key(id)
	 )
```
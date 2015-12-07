# Warehouse V1

## Como Utilizar:

* Esta API necessita de uma chave de autenticação para poder ser utilizada. Basta informar ela no header 'auth'

### to send data:
> via POST: 
```
api.warehouse.io/v1/log
```
with json containing the following structure exemplified:
```javascript
{
  "identifier": "34234",
  "log_name": "stock_update",
  "level": "critical",
  "content": "Procuct sku: 34234 can not be updated",
  "site": "amazon-uk"
}
```
you must send the exact data, or this will not work.
level must be one of this: 'critical', 'warning', 'info'. anything else has to be changed on code and db scheme.

This is how we expect the data format (every field is required)
```javascript
{
  "identifier": "string|integer",
  "log_name": "string",
  "level": "string",
  "content": "string",
  "site": "string"
}
```


### to get data:
> via GET 
you have only one resource

#### get by site name
> /site/{site_here}

```
ex:
api.warehouse.io/v1/site/amazon-uk
```
>>
#### get with filters
>  Voce pode utilizar FILTROS para que possa pegar informações mais precisas

* Lista de Filtros
    * log_name  = [string]
    * identifier = [string | integer]
    * level = [string(info, warning, critical)]
    * limit = [integer]
    * order = [string(desc, asc)]
     
* Por padrão, se LIMIT não é definido, é retornado apenas os 25 ultimos registros enviados
* o filtro LIMIT irá retornar no máximo os 100 ultimos registros
* Por padrão, a ordem de retorno do metodo GET /site é DESC. ou seja, irá retornar dos registros mais recentes pros mais antigos

Some examples: 
##### getting all amazon-uk logs with critical level
```
api.warehouse.io/v1/site/amazon-uk?level=critical&identifier=abc123&order=ASC
```
or
##### getting only 3 amazon-uk logs with critical level 
```
api.warehouse.io/v1/site/amazon-uk?level=critical&log_name=product_update&limit=3
```


#
> ##### Support to MongoDb in development

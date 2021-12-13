## Video Club API
### API REST para la gestión de un videoclub.
#### Funciones
- Inventario de películas
- Calcula los precios de los alquileres
- Calcula los puntos de fidelización.

Todos los registros tienen un campo (boolean) enabled solo seran tomado en cuenta los registros con enabled definido en true

El precio de los alquileres está basado en el tipo de película y el número de días del alquiler. Los 3 tipos de películas definidos en el *SEED* son:
- *Nuevos lanzamientos* – El precio unitario por cada uno de los días de alquiler.
- *Películas normales* – Precio unitario por los tres primeros días. Cada día adicional supondrá un incremento del precio unitario por día.
- *Películas viejas* - Precio unitario por los cinco primeros días. Cada día adicional
   supondrá un incremento del precio unitario por día.

El precio unitario definido en el *SEED* es 3. 

El cliente consigue puntos de fidelización por alquilar películas. 

Los nuevos lanzamientos dan 2 puntos y con los otros tipos de película consigue 1 punto.
   
Operaciones expuestas:
- Listado de todas las películas.
- Listado de todas las películas por tipo.
- Alquiler para una o varias películas y cálculo del precio.
- Devolver los puntos de fidelización de un cliente.
____________________
## Installation
- Instalar el framework y dependencias `composer install`
- Generar el key de artisan `php artisan key:generate`
- Crear el archivo .env en la raiz usando el .env.example como ejemplo
- Correr las migraciones `php artisan migrate`
- Correr el seed para crear registros en la BD `php artisan db:seed`
____________________
## Video club API DOC

### Listado de todas las películas y por tipos: `get` /api/get-movies

#### params:
- *page (int):* (default: 1) Indica la pagina del listado a retornar
- *limit (int):* (default: 1) Indica la catidad de item por pagina a retornar 
- *movie_type (str):* (Nuevos lanzamientos|Películas normales|Películas viejas) Devuelve solo peliculas del tipo indicado si no se envia retorna todas las peliculas

### example response:
```
{
    "status": true,
    "message": "success",
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 2,
                "name": "tum6o6T5Wo",
                "price": "39.00",
                "stock": 25,
                "total_rented": 25,
                "movie_type": "Películas normales",
                "loyalty_points_rewards": 1,
                "normal_price_days_limit": 3,
                "additional_payment_charge": "10.00"
            },
            {
                "id": 6,
                "name": "lVAY9Mm874",
                "price": "30.00",
                "stock": 29,
                "total_rented": 30,
                "movie_type": "Películas normales",
                "loyalty_points_rewards": 1,
                "normal_price_days_limit": 3,
                "additional_payment_charge": "10.00"
            },
            {
                "id": 7,
                "name": "mPpE5z1CIs",
                "price": "17.00",
                "stock": 65,
                "total_rented": 65,
                "movie_type": "Películas normales",
                "loyalty_points_rewards": 1,
                "normal_price_days_limit": 3,
                "additional_payment_charge": "10.00"
            },
            {
                "id": 9,
                "name": "YsUoxQSAiB",
                "price": "25.00",
                "stock": 79,
                "total_rented": 20,
                "movie_type": "Películas viejas",
                "loyalty_points_rewards": 1,
                "normal_price_days_limit": 5,
                "additional_payment_charge": "15.00"
            },
            {
                "id": 14,
                "name": "g3V8NYjT0d",
                "price": "26.00",
                "stock": 61,
                "total_rented": 38,
                "movie_type": "Películas viejas",
                "loyalty_points_rewards": 1,
                "normal_price_days_limit": 5,
                "additional_payment_charge": "15.00"
            }
        ],
        "first_page_url": "http://localhost/api/get-movies?page=1",
        "from": 1,
        "last_page": 10,
        "last_page_url": "http://localhost/api/get-movies?page=10",
        "next_page_url": "http://localhost/api/get-movies?page=2",
        "path": "http://localhost/api/get-movies",
        "per_page": "5",
        "prev_page_url": null,
        "to": 5,
        "total": 46
    }
}
```

### Devolver los puntos de fidelización de un cliente: `get` /api/get-client-loyalty-points

#### params:
- *email (required|email):* Email del cliente a consultar los puntos de fidelización
### example response:
```
{
"status": true,
"message": "success",
    "data": {
        "loyalty_points": 73
    }
}
```

### Cálculo del precio de alquiler para una o varias películas `get` /api/get_budget
Solo calculara el budget de las peliculas solicitadas que esten habilitadas y aun se tenga disponibilidad en stock para alquilar.
#### params: 
- *client_email (required|email):* Email del cliente que alquilara peliculas
- *movies_id (required|array):* Arrays de ids de las peliculas a alquilar
- *date_ini (required|date|date_format:Y-m-d):* Fecha inicial del alquiler
- *date_end (required|date|date_format:Y-m-d|after:date_ini):* Fecha final del alquiler
### example response:
```
{
    "status": true,
    "message": "success",
    "data": [
        {
            "movie": "YsUoxQSAiB",
            "price": 405,
            "dateIni": "2021-12-12T00:00:00.000000Z",
            "dateEnd": "2021-12-24T00:00:00.000000Z"
        },
        {
            "movie": "g3V8NYjT0d",
            "price": 417,
            "dateIni": "2021-12-12T00:00:00.000000Z",
            "dateEnd": "2021-12-24T00:00:00.000000Z"
        }
    ]
}
```

### Alquiler para una o varias películas `post` /api/rent_movies
Solo alquilaran las peliculas solicitadas que esten habilitadas y aun se tenga disponibilidad en stock para alquilar.
#### params:
- *client_email (required|email):* Email del cliente que alquilara peliculas
- *movies_id (required|array):* Arrays de ids de las peliculas a alquilar
- *date_ini (required|date|date_format:Y-m-d):* Fecha inicial del alquiler
- *date_end (required|date|date_format:Y-m-d|after:date_ini):* Fecha final del alquiler
### example response:
```
{
    "status": true,
    "message": "success",
    "data": {
        "ticketsMovieRented": {
            "0": {
                "ticket": 47,
                "movie": "YsUoxQSAiB",
                "price": 405,
                "dateIni": "2021-12-12T00:00:00.000000Z",
                "dateEnd": "2021-12-24T00:00:00.000000Z"
            },
            "1": {
                "ticket": 48,
                "movie": "dauVolekgX",
                "price": 432,
                "dateIni": "2021-12-12T00:00:00.000000Z",
                "dateEnd": "2021-12-24T00:00:00.000000Z"
            },
            "totalLoyaltyPointsObtained": 3
        }
    }
}
```

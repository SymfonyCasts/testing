# Servicio GitHub: Implementación

Ahora que tenemos una idea de lo que necesitamos que haga el `GithubService`, vamos a añadir la lógica interna que obtendrá las incidencias del repositorio `dino-park` utilizando la API de GitHub.

## Añade el cliente y haz una petición

Para hacer peticiones HTTP, en tu terminal, instala el Cliente HTTP de Symfony con:

```terminal
composer require symfony/http-client
```

Dentro de `GithubService`, instala un cliente HTTP con`$client = HttpClient::create()`. Para hacer una petición, llama a `$client->request()`. Esto necesita 2 cosas. 1ª: qué método HTTP utilizar, como `GET` o `POST`. En este caso, debería ser `GET`. 2ª: la URL, que pegaré. Esto recuperará todos los "issues" del repositorio `dino-park` a través de la API de GitHub.

[[[ code('0478d205da') ]]]

## Analiza la respuesta HTTP

Bien, ¿y ahora qué? Mirando el repositorio `dino-park`, GitHub devolverá una respuesta JSON que contiene las incidencias que vemos aquí. Cada incidencia tiene un título con el nombre de un dino y si la incidencia tiene una etiqueta adjunta, también la obtendremos de vuelta. Así que, pon `$client->request()` en una nueva variable `$response`. A continuación, `foreach()`sobre `$response->toArray()` como `$issue`. Lo bueno de utilizar el cliente HTTP de Symfony es que no tenemos que molestarnos en transformar el JSON de GitHub en una matriz - `toArray()` hace ese trabajo pesado por nosotros. Dentro de este bucle, comprobamos si el título de la incidencia contiene el `$dinosaurName`. Así que`if (str_contains($issue['title'], $dinosaurName))` entonces `// Do Something`con esa incidencia.

[[[ code('959cf1b0b7') ]]]

Llegados a este punto, hemos encontrado la incidencia de nuestro dinosaurio. ¡Vaya! Ahora tenemos que hacer un bucle sobre cada etiqueta para ver si podemos encontrar el estado de salud. Para ayudarte, voy a pegar un método privado: puedes copiarlo del bloque de código de esta página.

[[[ code('fe4184164d') ]]]

Esto toma una matriz de etiquetas... y cuando encuentra una que empieza por `Status:`, devuelve el enum correcto `HealthStatus` basado en esa etiqueta.

Ahora, en lugar de `// Do Something`, decimos`$health = $this->getDinoStatusFromLabels()` y pasamos las etiquetas con `$issue['labels']`.

[[[ code('60e1f49c5b') ]]]

Y ahora podemos devolver `$health`. Pero... ¿qué pasa si un número no tiene una etiqueta de estado de salud? Hmm... al principio de este método, establece el valor por defecto `$health`a `HealthStatus::HEALTHY` - porque GenLab nunca se olvidaría de poner una etiqueta`Sick` en un dino que no se encuentra bien.

[[[ code('58effa5196') ]]]

Hmm... Bueno, ¡creo que lo hemos conseguido! Hagamos nuestras pruebas para estar seguros.

```terminal
./vendor/bin/phpunit
```

Y... ¡Vaya! Tenemos 8 pruebas, 11 aserciones, ¡y todas pasan! ¡Shweeet!

## Registra todas nuestras peticiones

¡Un último reto! Para ayudar a la depuración, quiero registrar un mensaje cada vez que hagamos una petición a la API de GitHub.

¡No hay problema! Sólo tenemos que conseguir el servicio de registro. Añade un constructor con`private LoggerInterface $logger` para añadir un argumento y una propiedad de una sola vez. Justo después de llamar al método `request()`, añade `$this->logger->info()` y pasa`Request Dino Issues` para el mensaje y también un array con contexto extra. ¿Qué tal una clave `dino` establecida en `$dinosaurName` y `responseStatus` en`$response->getStatusCode()`.

[[[ code('70f55f1a16') ]]]

¡Genial! Eso no debería haber roto nada en nuestra clase, pero vamos a ejecutar las pruebas para estar seguros:

```terminal-silent
./vendor/bin/phpunit
```

Y... ¡Ay! ¡Sí que hemos roto algo!

> Se han pasado muy pocos argumentos al constructor de GithubService. se esperaban 0 pasados 1.

¡Por supuesto! Cuando añadimos el argumento `LoggerInterface` a `GithubService`, nunca actualizamos nuestro test para pasarlo. Te mostraré cómo podemos hacerlo a continuación utilizando una de las super habilidades de PHPUnit: el mocking.

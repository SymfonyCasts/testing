# Configuración y desmontaje

Sigamos refactorizando nuestra prueba. En el método de prueba, creamos un `MockResponse`,`MockHttpClient`, e instanciamos `GitHubService` con un simulacro de `LoggerInterface`. Estamos haciendo lo mismo en esta prueba de arriba. ¿No dijo Ryan que había que DRY out nuestro código en otro tutorial? Bien... Supongo que le haremos caso.

Empieza añadiendo tres propiedades `private` a nuestra clase: una`LoggerInterface $mockLogger`, seguida de `MockHttpClient $mockHttpClient` y finalmente `MockResponse $mockresponse`. En la parte inferior de la prueba, crea un`private function createGithubService()` que requiera `array $responseData` y que devuelva `GithubService`. En el interior, di `$this->mockResponse = new MockResponse()` que `json_encode()`'s el `$responseData`.

Como crearemos el `MockResponse` después de instanciar el `MockHttpClient`, que verás en un segundo, necesitamos pasar nuestra respuesta al cliente sin utilizar el constructor del cliente. Para ello, podemos decir`$this->mockHttpClient->setResponseFactory($this->mockResponse)`. Finalmente devuelve un `new GithubService()` con `$this->mockHttpClient` y `$this->mockLogger`.

Podríamos utilizar un constructor para instanciar nuestros mocks y establecerlos en esas propiedades, pero PHPUnit sólo instanciará nuestra clase de prueba una vez, sin importar cuántos métodos de prueba tenga. Y queremos asegurarnos de que tenemos objetos simulados nuevos para cada prueba que se ejecute. ¿Cómo podemos hacerlo? En la parte superior, añade `protected function setUp()`. En el interior, di `$this->mockLogger = $this->createMock(LoggerInterface::class)` y luego`$this->mockHttpClient = new MockHttpClient()`.

Abajo, en el método de prueba, corta la matriz de respuesta, luego di`$service = $this->createGithubService()` y pega la matriz.

Veamos cómo van nuestras pruebas en el terminal...

```terminal
./vendor/bin/phpunit
```

Y... ¡Ya! ¡Todo va bien!

La idea es bastante sencilla: si tu clase de prueba tiene un método llamado `setUp()`, PHPUnit lo llamará antes de cada método de prueba, lo que nos proporciona mocks frescos al comienzo de cada prueba. ¿Necesitas hacer algo después de cada prueba? Lo mismo: crea un método llamado `tearDown()`. Esto no es tan común... pero podrías hacerlo si quieres limpiar algunos cambios en el sistema de archivos que se hicieron durante la prueba. En nuestro caso, no es necesario.

Además de `setUp()` y `tearDown()`, PHPUnit también tiene algunos otros métodos, como`setUpBeforeClass()` y `tearDownAfterClass()`. Estos se llaman una vez por clase, y hablaremos de ellos a medida que sean relevantes en futuros tutoriales. Y si te lo estás preguntando, estos métodos se llaman "Métodos de Fijación" porque ayudan a configurar cualquier "fijación" para poner tu entorno en un estado conocido para tu prueba.

En cualquier caso, volvamos a la refactorización. Para la primera prueba de esta clase, recorta la matriz de respuesta, selecciona todo este "código muerto", añade`$service = $this->createGithubService()` y luego pega la matriz. Podemos eliminar la variable `$service` a continuación. Pero ahora tenemos que averiguar cómo mantener estas expectativas que estábamos utilizando en el antiguo `$mockHttpClient`. Poder comprobar que sólo llamamos a GitHub una vez con el método HTTP `GET` y que estamos utilizando la URL correcta, es bastante valioso.

Afortunadamente, esas clases simuladas tienen un código especial sólo para esto. A continuación,`assertSame()` que `1` es idéntico a `$this->mockHttpClient->getRequestCount()`luego `assertSame()` que `GET` es idéntico a `$this->mockResponse->getRequestMethod()`. Finalmente, copia y pega la URL en `assertSame()` y llama a `getRequestUrl()` en`mockResponse`. Elimina la antigua `$mockHttpClient`... y las declaraciones de `use` que ya no utilizamos arriba.

Muy bien, es hora de comprobar las vallas...

```terminal-silent
./vendor/bin/phpunit
```

Y... ¡Vaya! ¡Todo sigue en verde!

Bueno, ahí lo tienes... Hemos ayudado a Bob a mejorar Dinotopia añadiendo algunas pequeñas funciones a la aplicación. Pero, lo que es más importante, hemos podido comprobar que esas funciones funcionan como pretendíamos. ¿Hay más trabajo por hacer? Por supuesto, vamos a llevar nuestra aplicación al siguiente nivel añadiendo una capa de persistencia para almacenar los dinos en la base de datos y aprender a escribir pruebas para ello. Estas pruebas, en las que utilizas conexiones reales a la base de datos o realizas llamadas reales a la API, en lugar de burlas, se denominan a veces pruebas de integración. Ese es el tema del próximo tutorial de esta serie.

Espero que hayas disfrutado de tu estancia en el parque, y gracias por mantener tus brazos y piernas dentro del vehículo en todo momento. Si tienes alguna pregunta o sugerencia, o quieres ir con Big Eaty en el Jeep, déjanos un comentario. Muy bien, ¡nos vemos en el próximo episodio!

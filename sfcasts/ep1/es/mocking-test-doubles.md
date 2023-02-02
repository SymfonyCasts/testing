# Burlas: Dobles de prueba

Así que ahora mismo, las pruebas están fallando porque necesitamos pasar una instancia de `LoggerInterface`al `GithubService` dentro de nuestra prueba. Podríamos crear un registrador y pasarlo. Pero... Eso puede ser un poco peliagudo. Instanciar un objeto logger puede ser sencillo... pero ¿y si no lo es? ¿Y si tuviéramos que instanciar un objeto con 5 argumentos constructores necesarios... y algunos de ellos son para otros objetos que también son difíciles de crear? ¡Un caos!

Afortunadamente, PHPUnit nos cubre las espaldas: ¡con sus súper habilidades de mocking!

## Un Logger falso

Dentro de `GithubServiceTest` crea una variable `$mockLogger` con el valor`$this->createMock(LoggerInterface::class)`. Pásala al servicio `GithubService`.

[[[ code('16bc592495') ]]]

Veamos qué ocurre ahora al ejecutar las pruebas.

```terminal
./vendor/bin/phpunit
```

Y... ¡HA! ¡Todas nuestras pruebas vuelven a pasar!

## ¿Pero qué es un Mock?

Entonces... ¿Qué es esa magia negra de `createMock()` que estamos utilizando?`createMock()` nos permite pasar una clase o interfaz y obtener una instancia "falsa" de esa clase o interfaz 
de esa clase o interfaz. Este objeto se llama simulacro.

Ahora ya sé lo que vas a preguntar... ¿Qué ocurre con el mensaje cuando llamamos al método `info()` en el simulacro `LoggerInterface`?

Pues nada... Internamente, PHPUnit crea básicamente una clase falsa que implementa `LoggerInterface`... excepto que todos los métodos están vacíos. No hacen nada y no devuelven nada.

A menos que le digamos que haga algo diferente. Pronto hablaremos de ello.

Por cierto, este falso registrador se llama en realidad doble de prueba. De hecho, nos encontraremos con diferentes nombres para los simulacros, como dobles de prueba, stubs y objetos simulados... Todos estos nombres significan efectivamente lo mismo: objetos falsos que sustituyen a los reales. Hay algunas diferencias sutiles entre los distintos nombres y te daremos una pista a lo largo del camino.

## Siempre debemos simular los servicios

Todavía tenemos un pequeño problema con nuestra prueba. Cada vez que la ejecutamos, estamos llamando a la API real de GitHub. Esto es un mal mojo... En una prueba unitaria, nunca debes utilizar servicios reales, como llamadas a la API o a la base de datos. ¿Por qué? El objetivo de una prueba unitaria es comprobar que el código dentro de `GithubService` funciona. Y, en el mejor de los casos, lo haríamos independientemente de cualquier otra capa de nuestra aplicación porque... simplemente no podemos controlar su comportamiento. Por ejemplo, ¿qué pasaría si la API de GitHub está desconectada por mantenimiento? O, mañana, GenLab cambia `Daisy` de enfermo a sano En este momento, ¡ambas cosas harían que nuestras pruebas fallaran! ¡Pero no deberían! La prueba unitaria para `GithubService` sólo debería fallar si contiene un error en su código, como por ejemplo que no esté analizando las etiquetas correctamente.

¿Cuál es la solución? Simular el `HttpClient`.

## Refactorizar HttpClient para que utilice DependencyInjection

Pero... no podemos hacer eso mientras estemos creando el cliente dentro de`GitHubService`. En su lugar, en el constructor, añade un argumento`private HttpClientInterface $httpClient` 

[[[ code('7d5a55c8fb') ]]]

Luego llama al método `request()` en `$this->httpClient` en lugar de `$client`. 
Como ahora estamos utilizando la inyección de dependencias, podemos eliminar la estática `$client` entera, junto con la declaración `use` anterior.

[[[ code('cf1edb2cab') ]]]

Aparte de las pruebas unitarias, ésta es simplemente una forma mejor de escribir tu código.

En la prueba, empieza dando a `GithubService` un cliente http sin mocking - `HttpClient::create()` - sólo para asegurarte de que todo funciona como se espera.

[[[ code('0948ad2237') ]]]

Prueba las pruebas:

```terminal-silent
./vendor/bin/phpunit
```

Y... ¡genial! No hemos roto nada...

## Simulando el HttpClient

Ahora podemos imitar el `HttpClient`. Debajo de `$mockLogger` añade,`$mockClient = $this->createMock()` y pasa en `HttpClientInterface::class`. 
Ahora pasa esto a nuestro servicio.

[[[ code('5f69f21efb') ]]]

Vuelve al terminal para ejecutar nuestras pruebas:

```terminal-silent
./vendor/bin/phpunit
```

Y... ¡Uf! Nuestra prueba `Sick Dino` 

> Falló al afirmar que las dos variables son iguales

Hmm... Para `Sick Dino`, estamos esperando un `HealthStatus::SICK` para `Daisy`. En nuestro servicio, estamos llamando al método `request()` en nuestro simulacro, haciendo una entrada en el registro, y luego haciendo un bucle sobre el array que se devolvió en nuestra respuesta... ¡JA! Ese es el problema. Recuerda: cada vez que PHPUnit crea un objeto falso, elimina toda la lógica de cada método dentro de ese falso. Sí, ¡estamos haciendo un bucle sobre nada!

En este caso, tenemos que enseñar al simulacro `HttpClient` a devolver una respuesta que contenga una cuestión coincidente con una etiqueta `Status: Sick`. Eso nos permitiría afirmar que nuestra lógica de análisis de etiquetas es correcta.

¿Cómo lo hacemos? Es lo que viene a continuación

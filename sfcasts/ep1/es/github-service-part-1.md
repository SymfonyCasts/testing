# Crea una prueba de servicio en GitHub

Ahora que podemos ver si un `Dinosaur` está aceptando visitas en nuestro tablero, necesitamos mantener el tablero actualizado en tiempo real utilizando las etiquetas de estado de salud que GenLab ha aplicado a varios temas de dino en GitHub. Para ello, crearemos un servicio que obtendrá esas etiquetas utilizando la API de GitHub.

## Prueba de nuestro servicio primero

Para probar nuestro nuevo servicio... que aún no existe, dentro de `tests/Unit/` crea un nuevo directorio `Service/` y luego una nueva clase: `GithubServiceTest`... que extenderá `TestCase`. Lo estoy creando en un subdirectorio `Service/` porque pienso poner la clase en el directorio `src/Service/`. Añade el método`testGetHealthReportReturnsCorrectHealthStatusForDino` y dentro,`$service = new GithubService()`. Sí, eso tampoco existe todavía...

Nuestro servicio devolverá un enum `HealthStatus` creado a partir de la etiqueta de estado de salud en GitHub, así que `assertSame()` que `$expectedStatus` es idéntico a`$service->getHealthReport()` y luego pasaremos `$dinoName`. Sí, utilizaremos un proveedor de datos para esta prueba... en el que aceptamos el nombre del dino para comprobar su estado de salud previsto.

Vamos a crearlo: `public function dinoNameProvider()` que devuelve un`\Generator`. Nuestro primer conjunto de datos para el proveedor tendrá la clave `Sick Dino`, que devuelve una matriz con `HealthStatus::SICK` y `Daisy` para el nombre del dino... porque cuando comprobamos GitHub hace un minuto, ¡Daisy estaba enferma!

A continuación, un `Healthy Dino` con `HealthStatus::HEALTHY` que resulta ser el único `Maverick`. Arriba, en el método de prueba, añade una anotación `@dataProvider` para que la prueba utilice `dinoNameProvider`... y luego añade los argumentos `HealthStatus $expectedStatus`y `string $dinoName`.

¡Hagamos esto! Busca tu terminal y ejecuta:

```terminal
./vendor/bin/phpunit
```

Y... ¡Sí! Tal y como esperábamos, tenemos dos errores porque

> No se encuentra la clase GithubService

## Crea el servicio que llamará a GitHub

Para solucionarlo, busca a un compañero de equipo y pídele amablemente que cree esta clase por ti! TDD - ¡desarrollo dirigido por el equipo!

Estoy bromeando: ¡lo tenemos! Dentro de `src/`, crea un nuevo directorio `Service/`. Entonces necesitaremos la nueva clase: `GithubService` y dentro, añade un método `getHealthReport()`
que tome un `string $dinosaurName` y devuelva un objeto `HealthStatus`.

Este es el plan: llamaremos a la API de GitHub para obtener la lista de incidencias del repositorio `dino-park`. Luego filtraremos esas incidencias para elegir la que coincida con `$dinosaurName`. Finalmente, devolveremos `GithubStatus::HEALTHY`, a menos que la incidencia tenga una etiqueta `Status: Sick`.

## Añade la declaración de uso en nuestra prueba

Antes de sumergirnos en la escritura de ese método, vuelve a nuestra prueba y corta el último par de letras de `GithubService`. Con un poco de magia de PHPStorm... en cuanto escribo la letra `i` y pulso intro, la declaración de uso se añade automáticamente a la prueba. ¡Gracias JetBrains!

Veamos cómo quedan las pruebas:

```terminal-silent
./vendor/bin/phpunit
```

Y... ¡Ja! En lugar de dos fallos, ahora sólo tenemos uno...

> Sick Dino ha fallado al afirmar que las dos variables hacen referencia al mismo objeto.

A continuación, añadiremos algo de lógica a nuestro `GithubService` para hacer que esta prueba pase

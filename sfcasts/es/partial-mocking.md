# Mocking parcial

Hagamos `LockDownHelper` más interesante. Digamos que, cuando termina un bloqueo, necesitamos enviar una petición API a GitHub. En nuestro primer tutorial, escribimos código que hacía peticiones a la API para obtener información sobre este repositorio `SymfonyCasts/dino-park`. Ahora, vamos a fingir que, cuando terminemos un bloqueo, necesitamos enviar una petición API para encontrar todas las incidencias con etiqueta "bloqueo" y cerrarlas. En realidad... no vamos a hacerlo, pero vamos a seguir los pasos para desencadenar una situación fascinante.

## Esta configuración: Hacer llamadas a la API desde nuestro Servicio

En ese primer tutorial, creamos un servicio GitHub que envuelve las llamadas a la API. Su único método obtiene un informe de salud de los dinosaurios. Añade un nuevo`public function` llamado `clearLockDownAlerts()`. Dentro, haz como si hiciéramos una llamada a la API -en realidad no es necesario-, pero, al menos, registra un mensaje.

[[[ code('850d2b684c') ]]]

¡Guay! Finge también que hemos probado este método de alguna manera: mediante una prueba unitaria o de integración. La cuestión es: estamos seguros de que este método funciona.

En `LockDownHelper`, para hacer nuestra falsa llamada a la API, autoconecta`GithubService $githubService`... y aquí abajo, después de `flush()`, di`$this->githubService->clearLockDownAlerts()`.

[[[ code('f27910e88c') ]]]

¡Vale! ¡Haz la prueba!

```terminal-silent
symfony php vendor/bin/phpunit tests/Integration/Service/LockDownHelperTest.php
```

No hemos cambiado nada y... sigue pasando. Eso tiene sentido. En nuestra prueba, pedimos a Symfony el `LockDownHelper` y él se encarga de pasar el nuevo argumento `GithubService`cuando crea ese servicio. Y como `GitHubService` no está haciendo realmente una llamada real a la API, todo va bien.

Pero, ¿y si `GithubService` contuviera una lógica real para hacer una petición HTTP a GitHub? Eso podría causar algunos problemas. En primer lugar, ralentizaría definitivamente nuestra prueba. Segundo, podría fallar porque, cuando compruebe el repositorio, puede que no tengamos ningún problema con la etiqueta `LockDown`. Y tercero, si encuentra problemas con esa etiqueta, podría cerrarlos en nuestro repositorio de producción real... aunque esto sea sólo una prueba.

Además -lo sé, estoy en racha-, si quisiéramos comprobar que se ha llamado realmente al método`clearLockDownAlerts()`, en una prueba de integración, la única forma de hacerlo es realizar una llamada a la API desde nuestra prueba para sembrar el repositorio con algunas incidencias (creando una incidencia con una etiqueta `LockDown` ), llamar al método y, a continuación, realizar otra petición a la API desde nuestra prueba para comprobar que se ha cerrado la incidencia. Caray. ¡Eso es demasiado trabajo para comprobar algo tan sencillo!

## ¿Mocking sólo en algunos servicios?

Espero que le estés gritando a tu ordenador:

> ¡Ryan! Este es el objetivo de Mocking: ¡lo que aprendimos en el primer tutorial!

Sí, ¡totalmente! Si burláramos `GitHubHelper`, evitaríamos cualquier llamada a la API y tendríamos una forma fácil de afirmar que se ha llamado al método. Así que, maldita sea, básicamente queremos burlarnos de una dependencia... pero utilizar los servicios reales para las otras dependencias. ¿Es posible? Pues sí Con algo que yo llamo "Mocking parcial".

## Inyectar un Mock en el Contenedor

Cuando pedimos al contenedor el servicio `LockDownHelper`, éste instancia los servicios reales que necesita y los pasa a cada uno de los tres argumentos. Lo que realmente queremos hacer es que pase el servicio real para `$lockDownRepository`y `$entityManager`, pero un simulacro para `$githubService`. ¡Y Symfony nos da una forma de hacerlo!

Compruébalo. Antes de pedir `LockDownHelperService`, crea un simulacro de `$githubService`configurado con `$this->createMock(GitHubService::class)`. Debajo de eso, di`$githubService->expects()` y, para asegurarte de que esto falla al principio, utiliza`$this->never()` y `->method('clearLockDownAlerts')`.

[[[ code('e29d741d61') ]]]

Si nos detenemos ahora y ejecutamos la prueba:

```terminal-silent
symfony php vendor/bin/phpunit tests/Integration/Service/LockDownHelperTest.php
```

Sigue pasando. Hemos creado un simulacro... pero nadie lo utiliza. Tenemos que decirle a Symfony:

> ¡Eh! Sustituye el `GitHubService` real del contenedor por este simulacro.

Hacerlo es sencillo: `self::getContainer()->set()` pasando el ID del servicio, que es `GithubService::class`, luego `$githubService`.

[[[ code('2f67150a93') ]]]

De repente, eso se convierte en el servicio del contenedor, y eso es lo que se pasará a `LockDownHelper` como tercer argumento.

¡Haz la prueba!

```terminal-silent
symfony php vendor/bin/phpunit tests/Integration/Service/LockDownHelperTest.php
```

Debido a `$this->never()`... ¡falla! No se esperaba que se llamara a `clearLockDownAlerts()`, pero se hizo... ya que lo estamos llamando aquí abajo. ¡Eso demuestra que se utilizó el simulacro!

Cambia la prueba de `$this->never()` a `$this->once()` e inténtalo de nuevo...

[[[ code('889e8270be') ]]]

```terminal-silent
symfony php vendor/bin/phpunit tests/Integration/Service/LockDownHelperTest.php
```

¡Pasa! Es una estrategia genial.

A continuación: Veamos cómo podemos probar si nuestro código ha provocado que ocurran determinadas cosas externas, empezando por probar los correos electrónicos.

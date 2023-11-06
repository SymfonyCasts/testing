# Configuración de la base de datos del entorno de prueba

Esta primera prueba ha sido demasiado fácil Así que escribamos otra más interesante. ¿Qué te parece, ejem, `public function testIsInLockDownReturnsTrueIfMostRecentLockdownIsActive()`. ¡Uf!

Empieza igual que antes: `self::bootKernel()`. Lo complicado de esta prueba es que necesitamos que la base de datos no esté vacía al principio. Necesitamos insertar un bloqueo activo en la base de datos... para que cuando finalmente llamemos al método y éste ejecute la consulta, encuentre el registro.

[[[ code('034159241e') ]]]

Esta es una parte habitual de las pruebas de integración, ya que con frecuencia hablan con la base de datos.

## Sembrar la base de datos

¡No hay problema! ¡Vamos a crear un bloqueo! Añade `$lockDown = new LockDown()`,`$lockDown->setReason()` para que sepamos por qué se produce el bloqueo, y`$lockDown->setCreatedAt()` a, qué tal, hace 1 día. Esa parte aún no es superimportante. Ah, y no necesitamos establecer el estado porque, si miras en la clase, aparece por defecto `ACTIVE`.

[[[ code('ad2b6aa68e') ]]]

Guardar esto también es sencillo. Coge el `$entityManager` con`self::getContainer()->get(EntityManagerInterface::class)`. Y haré nuestro truco `assert()` con `$entityManager instanceof EntityManagerInterface`para ayudar a mi editor. Termina con los habituales `$entityManager->persist($lockDown)` y`$entityManager->flush()`.

Para ver si esto funciona, aquí abajo, `dd($lockDown->getId())`.

[[[ code('a80db9ac16') ]]]

¡Vamos a probarlo! Ejecuta sólo las pruebas de este archivo:

```terminal-silent
./vendor/bin/phpunit tests/Integration/Repository/LockDownRepositoryTest.php
```

Y... oh... explota. Veamos... ¡Ah! ¡Tiene problemas para conectarse a la base de datos!

Olvidándonos por un momento de las pruebas, ¡éste es un problema familiar! La clave para conectar nuestra aplicación a la base de datos es la variable de entorno `DATABASE_URL`. Yo estoy utilizando Postgres, pero eso no importa.

## Manejo especial del .entorno para las Pruebas

Normalmente, cuando configuramos nuestro entorno local, personalizamos `DATABASE_URL`aquí en `.env`... o creamos un archivo `.env.local` y lo anulamos allí.

[[[ code('23a20ca312') ]]]

Y, en general, cuando arrancamos el núcleo en nuestras pruebas, todo funciona exactamente igual que cuando cargamos nuestra aplicación en el navegador. Sí que arranca nuestro código en un entorno Symfony llamado `test` en lugar de `dev`... y eso cambia algunas cosas, pero el 99% del comportamiento es el mismo.

Si te fijas en el error, la prueba está teniendo problemas para conectarse a `127.0.0.1` en el puerto `5432`. Eso tiene sentido: lo está leyendo de nuestro archivo `.env`. Todo muy normal.

Pero hay una diferencia importante en el entorno `test`. Si creas un archivo `.env.local`, anulas `DATABASE_URL`, y ejecutas tus pruebas (cambiaré este puerto por algo loco como `9999`), ¡no se utilizará! ¡Comprueba este error! Sigue buscando `port 5432`.

Sólo en el entorno `test`, el archivo `.env.local` no se carga. Así que si quieres configurar un `DATABASE_URL` específicamente para tu entorno `test`, tienes que ponerlo en `.env.test`: el archivo de variables específico del entorno.

Antes de continuar, asegúrate de borrar ese archivo `.env.local` para evitar confusiones.

## Lectura desde Docker en tus Pruebas

Pero en nuestro caso, no vamos a confiar en ninguno de estos archivos `.env`. Eso es porque, si has seguido las instrucciones de `README.md`, estamos utilizando Docker entre bastidores. Tenemos un archivo `docker-compose.yaml`, que inicia una base de datos Postgres. Y como estamos utilizando el binario Symfony como servidor web, configura el `DATABASE_URL`automáticamente para que apunte a ese contenedor.

Cuando actualizamos la página... no está utilizando el `DATABASE_URL` de mi `.env`: está utilizando el valor dinámico que establece el binario `symfony`. Esto es algo de lo que ya hablamos en nuestro tutorial de Doctrine.

Sin embargo, ¡es evidente que esa magia no se produce en nuestra prueba! El error hace evidente que está consultando el `DATABASE_URL` de `.env`. Y... ¡es cierto! Esto se debe a que el binario `symfony` no tiene la oportunidad de inyectar la variable de entorno`DATABASE_URL`. Para permitirlo, en lugar de ejecutar`./vendor/bin/phpunit`, ejecuta `symfony php vendor/bin/phpunit`... seguido de la ruta a la prueba

```terminal-silent
symfony php vendor/bin/phpunit tests/Integration/Repository/LockDownRepositoryTest.php
```

El comando `symfony php` es sólo una forma de ejecutar PHP... pero al hacer esto, permite que el binario `symfony` haga su magia.

Cuando probamos esto... vuelve a fallar. Pero ¡fíjate! Este es un error diferente. Ahora habla del puerto `58292`. Ese es el puerto aleatorio por el que aparentemente se puede acceder a mi base de datos Docker. También dice `database "app_test" does not exist`.

## Bases de datos de prueba con sufijos automáticos

Para ver de qué se trata, ejecuta:

```terminal
symfony var:export --multiline
```

Esto muestra todas las variables de entorno que el binario Symfony está inyectando. La más importante es `DATABASE_URL`. Esto apunta al contenedor Docker... que, en mi caso, se ejecuta en el puerto `58292`.

El detalle clave es esta parte `app`. Es el nombre de la base de datos que debe utilizarse. Entonces, si `DATABASE_URL` está apuntando a una base de datos llamada `app`, ¿por qué el error dice que una base de datos llamada `app_test` no existe?

Antes de responder a eso, tengo otra pregunta: cuando ejecutemos nuestras pruebas, ¿queremos que utilicen la misma base de datos que está utilizando nuestra aplicación local? Idealmente, ¡no! Tener una base de datos diferente para tus pruebas y para tu entorno de desarrollo normal es una buena idea. Por un lado... es simplemente molesto ejecutar tus pruebas y que manipulen tus datos mientras desarrollas. Y, afortunadamente, tener dos bases de datos diferentes es algo que ocurre automáticamente.

Abre `config/packages/doctrine.yaml`. En la parte inferior, tenemos este bloque especial`when@test`. Está configurado sólo para el entorno `test`. ¡Y fíjate en `dbname_suffix`! Está configurado como `_test`. Puedes ignorar el bit`%env(default::TEST_TOKEN)%`. Se refiere a una biblioteca llamada ParaTest y, en nuestro caso, estará vacía. Así que, efectivamente, es sólo `_test`.

[[[ code('2f8f4e37d5') ]]]

Así que, gracias a esta configuración, en el entorno `test`, toma la configuración `app`, le añade `_test` y, en última instancia, utiliza una base de datos llamada `app_test`.

¡Eso está muy bien! Y ahora que lo entendemos, todo lo que tenemos que hacer es crear esa base de datos.

## Crear la base de datos

En tu terminal, ejecuta `symfony console` - esto es sólo `bin/console`, pero permite que el binario `symfony` inyecte la variable de entorno `DATABASE_URL` -`doctrine:database:create --env=test`:

```terminal-silent
symfony binary doctrine:database:create --env=test
```

Y... ¡¡éxito!! También tenemos que crear la base de datos `schema`: `doctrine:schema:create`

```terminal-silent
symfony binary doctrine:schema:create --env=test
```

¡Genial! Haz la prueba ahora:

```terminal-silent
symfony php vendor/bin/phpunit tests/Integration/Repository/LockDownRepositoryTest.php
```

¡Ha funcionado! Ese `1`... viene del volcado de aquí abajo.

## Terminando la consulta

Vamos a terminar esta prueba. Para facilitarte las cosas, copia la línea del repositorio y crea un nuevo método privado: `private function getLockDownRepository()`. Pega, añade `return`, y luego el tipo de retorno. Ahora no necesitamos el `assert()` porque PHP lanzará un gran error si esto devuelve otra cosa por alguna razón.

[[[ code('193ca88f68') ]]]

Simplifica las cosas aquí con `$this->getLockDownRepository()->isInLockDown()`.

[[[ code('5271ef8d04') ]]]

Vuelve a hacer la prueba para asegurarte de que sigue pasando...

```terminal-silent
symfony php vendor/bin/phpunit tests/Integration/Repository/LockDownRepositoryTest.php
```

Pasa. Y, curiosamente, el ID es ahora `2`. Pronto hablaremos más de ello.

Sustituye el volcado por `$this->assertTrue()` que`$this->getLockDownRepository()->isInLockDown()`.

[[[ code('3b9eeaeb94') ]]]

En el repositorio, pegaré la consulta real. Esto busca un bloqueo que no haya terminado, y devuelve verdadero o falso.

[[[ code('7e06e79218') ]]]

¡Vamos a hacerlo!

```terminal-silent
symfony php vendor/bin/phpunit tests/Integration/Repository/LockDownRepositoryTest.php
```

Y... ¿la prueba falla? Oh, nuestra segunda prueba ha pasado, pero la prueba original falla de repente. ¿Cómo ha ocurrido?

Resulta que, gracias a la segunda prueba, cuando se ejecuta la primera, la base de datos ya no está vacía. De hecho, se va apilando con más y más filas cada vez que ejecutamos las pruebas. Observa, Ejecuta:

```terminal
symfony console dbal:run-sql 'SELECT * FROM lock_down' --env=test
```

¡Caramba! Éste es un problema crítico: tenemos que garantizar que la base de datos se encuentra en un estado predecible al comienzo de cada prueba. Vamos a sumergirnos en este problema tan importante a continuación.

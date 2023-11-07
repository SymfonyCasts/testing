# Reiniciar la base de datos

Es muy habitual que las pruebas de integración o las pruebas funcionales hablen con la base de datos. Y casi siempre necesitamos sembrar la base de datos antes de la prueba: añadir algunas filas a `LockDown` antes de hacer el trabajo y llamar a las aserciones.

En el primer tutorial, hablamos de una filosofía o patrón de pruebas llamado AAA: Organizar, Actuar y Afirmar. En una prueba de integración, el paso Organizar suele consistir en añadir filas a tu base de datos, el paso Actuar es donde llamas al método y luego Afirmar es, por supuesto, las aserciones del final.

## ¿Cargar dispositivos?

Hay dos enfoques para cargar tu base de datos en una prueba. El primero es escribir código dentro de la prueba para insertar todos los datos que necesites. La segunda es crear y ejecutar un conjunto de fijaciones.

Y nuestra aplicación tiene fixtures que alimentan nuestro sitio local. ¿Deberíamos... cargarlos desde dentro de nuestra prueba para que comience con algunos datos en un estado predecible?

¡Suena bien! Pero... ¡no lo hagas! No cargues accesorios en tus pruebas. ¿Por qué? Porque una buena prueba se lee como una historia: deberías poder leer qué datos se añaden, a qué método se llama y qué comportamiento se espera.

Si cargas un conjunto de fixtures... y de repente afirmas que estamos en un bloqueo, no es superobvio por qué estamos en un bloqueo... ¡ni siquiera qué estamos probando! Tienes que indagar en los accesorios de la aplicación para encontrar qué registros LockDown hay... y averiguar qué está pasando. Eso no me gusta.

Así que, aunque te parezca un poco más de trabajo, la mejor estrategia es insertar los datos que necesitas dentro de cada método de prueba. Y después del próximo capítulo, en realidad no será mucho trabajo.

## Borrar los datos

Y lo que es aún más importante, independientemente de cómo siembres tu base de datos, tenemos que asegurarnos de que, antes de que comience cada prueba, la base de datos esté vacía. Y acabamos de ver por qué.

Nuestra prueba original pasó... hasta que nuestra segunda prueba insertó una fila... lo que hizo que la primera fallara de repente. Buf. A menos que tu base de datos esté en un estado perfectamente predecible al inicio de cada prueba, ¡no puedes confiar en ellas! ¡Y la mejor forma de ser predecible es empezar vacío!

Podríamos anular el método `setUp()` y ejecutar aquí un código que hiciera eso. Afortunadamente, no necesitamos hacerlo porque hay múltiples bibliotecas que ya resuelven este problema. Mi favorita es Foundry.

## Instalar zenstruck/foundry

Ejecuta:

```terminal-silent
composer require zenstruck/foundry --dev
```

Si has visto nuestro tutorial sobre Doctrine, ¡recordarás Foundry! Pero puede que no conozcas sus superpoderes de prueba... que es donde realmente brilla.

El principal objetivo de esta biblioteca es ayudar a crear datos ficticios, y de eso hablaremos pronto. Pero también viene con una forma superfácil de vaciar tu base de datos entre cada prueba.

Para utilizarlo, en la parte superior de tu clase de prueba, di use `ResetDatabase`... y también otro trait llamado `Factories`.

[[[ code('e2daa9c1e9') ]]]

Ejecuta las pruebas:

```terminal
symfony php vendor/bin/phpunit tests/Integration/Repository/LockDownRepositoryTest.php
```

¡Pasan! ¡Podemos ejecutarlas una y otra vez! Antes de cada método de prueba individual, ¡vacia la base de datos!

Por cierto, hay otra biblioteca que hace lo mismo llamada`dama/doctrine-test-bundle`, que puede ser incluso más rápida que `ResetDatabase` de Foundry. Siéntete libre de instalarla, y luego utiliza Foundry sólo para las cosas de fábrica de las que hablaremos pronto. Funcionan muy bien juntos.

## Silenciar las desapropiaciones con symfony/phpunit-bridge

Antes de seguir adelante, ¡probablemente te hayas dado cuenta de que tenemos un montón de desaprobaciones! Ver desaprobaciones es genial... pero una desaprobación indirecta significa que no es nuestro código el que está provocando la desaprobación: es una biblioteca llamando a un método desaprobado en otra biblioteca.

No me preocupan demasiado... así que vamos a silenciarlos durante el resto del tutorial. Estos avisos de desaprobación provienen del paquete phpunit-bridge de Symfony, y podemos controlar cómo funcionan.

Abre `phpunit.xml.dist`. Aquí abajo, dentro de la sección `php`, añade `env`para establecer una variable de entorno llamada `SYMFONY_DEPRECATIONS_HELPER`. Para el valor, una forma fácil de silenciar estas advertencias es enviarlas a un archivo de registro en su lugar:`logFile=var/log/deprecations.log`.

[[[ code('0dcdd9d33e') ]]]

Ciérralo. Ahora, cuando ejecutemos las pruebas:

```terminal-silent
symfony php vendor/bin/phpunit tests/Integration/Repository/LockDownRepositoryTest.php
```

¡Limpio y ordenado! Y las deprecaciones siguen esperándonos en el archivo de registro.

A continuación: ¡aprovechemos las Fábricas de Foundry para que sembrar nuestra base de datos sea una absoluta delicia!

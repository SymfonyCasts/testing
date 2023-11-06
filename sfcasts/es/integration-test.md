# ¡Hola Pruebas de Integración!

¡Hola, gente! Bienvenidos al segundo episodio de nuestra serie de pruebas, que trata sobre las pruebas de integración. En el episodio 1, Anakin activó accidentalmente el piloto automático de un caza estelar... ¡lo que nos enseñó todo sobre las pruebas unitarias! ¡Qué suerte!

Las pruebas unitarias son la forma más pura de prueba, en la que pruebas clases y los métodos de esas clases. Y si una clase requiere otras clases, simulas esas dependencias. Es genial y bonito... y no conduce al lado oscuro, te lo prometo.

En este tutorial, las cosas se complican, ¡pero también son más útiles en las situaciones adecuadas! En lugar de simular dependencias, vamos a probar con servicios reales... lo que a veces significa que nuestras pruebas harán que ocurran cosas reales, ¡como consultas reales a la base de datos! Esto conlleva todo tipo de emocionantes complicaciones Y vamos a sumergirnos en todas ellas.

## Configuración del proyecto

Pero primero, ¡activemos nuestro propio piloto automático y pongamos en marcha nuestra aplicación! Probar es divertido, así que descárgate el código del curso de esta página y codifica conmigo. Después de descomprimir el archivo, encontrarás un directorio `start/` con el mismo código que ves aquí, incluido este ingenioso archivo `README.md`. Contiene todas las instrucciones de configuración, incluida la configuración de la base de datos, porque en este curso tenemos una base de datos. Si estuviste con nosotros en el episodio uno -bienvenido de nuevo- y asegúrate de descargar el código de este curso porque hemos cambiado algunas cosas, como añadir una base de datos y actualizar algunas dependencias.

Ah, y este tutorial utiliza PHPUnit 9, aunque ya está disponible PHPUnit 10. Eso está bien porque no hay muchos cambios de cara al usuario en PHPUnit 10.

El último paso del LÉEME es buscar tu terminal, entrar en el proyecto y ejecutar

```terminal
symfony serve -d
```

para iniciar un servidor web local en https://127.0.0.1:8000. Haz clic en eso y... ¡aquí estamos! Dinotopia: La aplicación en la que podemos ver el estado de los dinosaurios de nuestro parque. Y ahora, estos dinosaurios proceden de la base de datos. No es nada del otro mundo, pero tenemos una entidad `Dinosaur`. Y dentro de nuestro único controlador, consultamos todos los dinosaurios... y eso es lo que pasamos a la plantilla... que es lo que vemos aquí.

## Comprobando un "Bloqueo"

Todo en la aplicación funciona de maravilla. Bueno... excepto por ese pequeño problema. Verás, a veces Big Eaty (que es nuestro T-Rex residente) se escapa, y no tenemos forma de bloquear el parque y avisar a la gente. Básicamente, a la dirección le preocupa que se coma a demasiados huéspedes. Así que la primera función que vamos a construir es un sistema para iniciar un cierre... ¡y ya tenemos una entidad para ello! Se llama, creativamente, `LockDown`... con `$createdAt`, `$endedAt`, y `$status` (que es un `Enum`). Dentro de `Enum`, hay tres casos: `ACTIVE`,`ENDED`, o `RUN_FOR_YOUR_LIFE`. Intentemos... evitar el último...

[[[ code('9e7e71425e') ]]]

[[[ code('4fff40797d') ]]]

En nuestro `MainController` (nuestra página de inicio), si el registro de bloqueo más reciente de la base de datos tiene un estado `ACTIVE` o `RUN_FOR_YOUR_LIFE`, tenemos que mostrar un mensaje de advertencia gigante en la pantalla.

[[[ code('a921460f85') ]]]

Para ello, abre `src/Repository/LockDownRepository.php`. Para saber si estamos en un bloqueo, añade un nuevo método llamado `isInLockDown()` que devolverá un `bool`. Por ahora, sólo `return false`.

[[[ code('d4a708c7ce') ]]]

## Crear la prueba

¡Utilicemos un poco de desarrollo dirigido por pruebas! Antes de escribir esta consulta, añadamos una prueba para ella. Aún no tenemos una prueba para la clase `LockDownRepository`, así que abre`tests/`. En el primer tutorial, creamos un directorio `Unit/` y emparejamos la estructura de directorios dentro de `src/` para todas las clases que necesitamos probar.

Esta vez, crea un directorio llamado `Integration/`. No es necesario que organices las cosas así, pero es bastante habitual tener pruebas unitarias en un directorio y pruebas de integración en otro. Aún no hemos hablado de lo que es una prueba de integración, pero lo veremos en un minuto.

Dentro de `Integration/`, seguiremos la estructura de directorios. Crea un directorio `Repository/` ya que esta clase vive en `src/Repository/`... y dentro, una nueva clase PHP llamada `LockDownRepositoryTest`.

Empieza como siempre: extiende `TestCase` de PHPUnit. Llama al primer método`testIsInLockDownWithNoLockdownRows()`. Esto probará que, si la tabla de bloqueo está vacía, el método debe devolver `false`.

[[[ code('c2ab2012a1') ]]]

Vale, sigamos fingiendo que vivimos en el mundo de las pruebas unitarias e intentemos probar esto... como hicimos en el tutorial anterior. Para ello, digamos`$repository = new LockDownRepository()`.

## ¡Uh Oh, Instanciar este Objeto es Difícil!

Pero, hmm. `LockDownRepository` extiende `ServiceEntityRepository`, que extiende otra clase de Doctrine. Si te fijas, para instanciarlo, necesitamos pasarle un `ManagerRegistry` de Doctrine. Y si mantienes pulsado "comando" o "control" y haces clic en esto... y vas a la clase base, se complica. Llama a`$registry->getManagerForClass()` para obtener el gestor de entidades... y se lo pasa al padre. Así que ya, vamos a necesitar burlarnos del registro... para que cuando se llame a `getManagerForClass()`, devuelva un gestor de entidades burlado.

Dentro de nuestro repositorio, acabaremos llamando a `$this->createQueryBuilder()`. Si te sumerges en eso, utiliza la propiedad `_em` (que es ese gestor de entidades que pensamos burlar) y llama a `createQueryBuilder()`, que devuelve un `QueryBuilder`. Así que también tenemos que burlar este método en `EntityManager` para que devuelva un`QueryBuilder` burlado .

¡Esto se está volviendo una locura! Tenemos un simulacro, para devolver un simulacro, para devolver otro simulacro. Y al final, ¿qué afirmaríamos? ¿Aseguraríamos que nuestro código llama al método `->andWhere()` del QueryBuilder con los argumentos correctos? ¿O vamos a... hacer que el QueryBuilder genere una cadena de consulta real... y luego afirmar que la cadena... nos parece correcta?

## Por qué una prueba unitaria es la herramienta equivocada

No: no vamos a hacer nada de eso. Lo que estamos viendo es una situación en la que una prueba unitaria no es la herramienta adecuada. Y hay dos razones. En primer lugar, ¡es demasiado compleja! Crear una prueba unitaria requeriría una serie aparentemente interminable de simulacros. Y segundo, ¡una prueba unitaria no sería útil! Si estamos creando una consulta compleja dentro de `LockDownRepository`, para que sea una prueba realmente útil, necesitamos ejecutar realmente esa consulta y asegurarnos de que devuelve los resultados que esperamos de la base de datos.

Así que, en lugar de crear un `LockDownRepository` nuevo con un montón de mocks, vamos a pedir a Symfony que nos proporcione el `LockDownRepository` real: el que utilizaríamos en nuestro código normal. El que, cuando llamemos a un método en él desde nuestra prueba, ejecutará una consulta real a la base de datos.

Eso se llama "prueba de integración", y te mostraré cómo hacerlo a continuación.

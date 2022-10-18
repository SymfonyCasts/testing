# Testing Class Methods

As a reminder, the class is currently pretty simple: we pass some data to the constructor...
and then we can *read* that data via some methods. Instead of just "hoping" this all works,
let's go ahead and make sure that our `Dinosaur` class is *really* bug-free with some tests!

In `DinosaurTest`, remove these two tests and replace them with
`public function testCanGetAndSetData()`:

[[[ code ('75b6e2188d')]]]

Inside... we're literally going to play with the object by instantiating it and trying some methods.

So, `$dino = new Dinosaur()`.... and pass in some data.
For the name, eh - let's use `Big Eaty`: he's our resident `Tyrannosaurus` who happens to
be `15` meters in length. And Big Eaty is currently living in `Paddock A`:

[[[ code('072fb358a9') ]]]

Now that we have our `Dinosaur` object, we can write a few assertions. `self::assertSame()`
that `Big Eaty` is identical to `$dino->getName()`, `assertSame()` that `Tyrannosaurus` is
identical to `$dino->getGenus()`, `assertSame()` that `15` is identical to `getLength()`,
and last but not least, `assertSame()` that Big Eaty is *still* in `Paddock A` when we
call `getEnclosure()`... and not running wild around the island:

[[[ code('431cb38e37') ]]]

Let's try it! Move back to your terminal and run:

```terminal
./vendor/bin/phpunit
```

## Should I Test that Method?

And... YES! We have one test with four assertions. But... looking back at our
`Dinosaur` class, we're not really doing a whole heck of a lot in here. We're
requiring a few arguments in our constructor, setting them on properties, and
exposing those properties with getter methods. Nothing complex at all. So while
our `DinosaurTest` is *perfectly* acceptable, it's not the *most* useful.
Because the odds of these methods having a bug are low. And besides, if there
*were* a bug, we'll probably catch it while testing *other* parts of our app
that *call* these.

The point is: while you can do whatever you want, this probably
isn't a test that I would write in a *real* project. My rule of thumb is:
if a method scares, it's worth a test. And if you're not sure, it's always
safe to add a test.

## The Order of the assert() Method Arguments

By the way: the argument *order* for the assert methods is important.

The first argument should always be the *expected* argument - like
`Big Eaty` - and the second should be the *actual* value we get - like `$dino->getName()`.
This isn't a huge deal for the assertions we're using here... though if
you reverse this, the error message will be confusing.

It *is* more important for other assertions, like `assertGreaterThan()`... which we
can use to test that `$dino->getLength()` is greater than `10`.

[[[ code('78cbac661b') ]]]

When we try this:

```terminal-silent
./vendor/bin/phpunit
```

Yup! One failure in `DinosaurTest`:

> Failed asserting that 10 is greater than 15.

Whoops! Looking back in our `DinosaurTest`, this test failed because
we passed the *actual* value first instead of our *expected* value.

## The Assert Message

Before we clean this up, let's pass a 3rd *optional* argument:

> Dino is supposed to be bigger than 10 meters.

[[[ code('f22586ee8b') ]]]

To see what this does, run the tests again:

```terminal-silent
./vendor/bin/phpunit
```

And... sweet! The test still fails but now we *also* see the message, which
can sometimes help us more quickly understand *what* failed and why. Every
assert method has this "message" argument and I like to use it when a complex
test could use a bit more explanation.

## Naming Conventions

I want to circle back to the *name* of our first test method: `testCanGetAndSetData`.

[[[ code('c7532cd075') ]]]

In standard PHP, we try to create methods that are descriptive... but not necessarily
*super* long... since we'll need to call them in our code. Good examples
are `getGenus()` and `getName()` in the `Dinosaur` class. But when it comes to testing,
keeping things short is *not* a benefit.

Check it out: I change the name of our test method to `testDinosaur()`...
and then run our tests again.

```terminal-silent
vendor/bin/phpunit
```

PHPUnit tells us that `DinosaurTest::testDinosaur()` failed asserting that 10 is
greater than 15. Ok... but *what* are we testing? The method name - `testDinosaur()` -
tells us nothing... especially since we're *inside* of a class called `DinosaurTest`!
Yea, I get it: we're testing dinosaurs!

The *name* of each test method is *your* chance to describe *exactly* what you're
testing, and even sometimes *why*. Change the test name back to `testCanGetAndSetData()`,
which does a *much* better job of explaining the *purpose* of this test. Notice that
it almost reads like a sentence. That's great! And some people even take this further
by including the word "it", like `testItCanGetAndSetData()`. The point is: be descriptive,
there's no downside to long test names.

## Descriptive Testdox Output

Let me show you one more cool trick with PHPUnit. Move back to the terminal and run our tests
again... but *this* time pass a `--testdox` flag:

```terminal-silent
./vendor/bin/phpunit --testdox
```

And... Wooah! The output is different. Most importantly, it turned the method name
into a human-readable sentence... which is minor, but cool.

By the way, the `phpunit` executable has a lot more options and arguments available.
Run PHPUnit with the `help` flag to see them.

```terminal-silent
./vendor/bin/phpunit --help
```

We'll talk about the most useful of these throughout the tutorial.

Before we keep going, we need to cleanup our test. Remove this `testGreaterThan()` assertion...

[[[ code('e6fec8277d') ]]]

and run our tests again:

```terminal-silent
./vendor/bin/phpunit --testdox
```

And... YES! All of our tests are passing. Coming up next, we're going to get
philosophical and take a look at Test Driven Development or simply - TDD.

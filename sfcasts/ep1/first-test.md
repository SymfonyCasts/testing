# Our First Test

We already have this `Dinosaur` class... and it's *pretty* simple. But when it comes to
dinosaurs, bugs in our code can be, mmm, a bit painful. So let's add some basic tests!

## Creating the Test Class

Mmmm... where do we put this new test? We can *technically* put our tests *anywhere* within our project.
But when we installed `symfony/test-pack`, Flex created a `tests/` directory which,
no surprise, is the *recommended* place to put our tests.

Remember that, in this tutorial, we're only dealing with
Unit tests. So, inside of `tests/`, create a new directory called `Unit`.
And because our `Dinosaur::class` lives in the `Entity` namespace - create an `Entity`
directory inside of *that* at the same time.

All of this organization is *technically* optional: you can organize the `tests/`
directory *however* you want. *But*, putting all of our unit tests into a `Unit`
directory is just... nice. And the reason we made the `Entity`
directory is because we want the file structure inside of `Unit` to mirror our `src/`
directory structure. That's a best practice that keeps our tests organized.

Finally, create a new class called `DinosaurTest`. Using that `Test` suffix makes
sense: we're testing `Dinosaur`, so we call this `DinosaurTest`! But it's also
a requirement: PHPUnit - our testing library - *requires* this. It also requires
that each class extend `TestCase`:

[[[ code('a18a9e97ac') ]]]

Now let's go ahead and write a simple test to make sure everything is working.

Inside our `DinosaurTest` class, let's add `public function testIsWorks()`... where
we'll create the most *exciting* test ever! If you like return types - I do! - use
`void`... though that's optional

Inside call `self::assertEquals(42, 42)`:

[[[ code('c65f8a62e5') ]]]

That's it! It's not a very *interesting* test - if our computer thinks that
42 doesn't equal 42, we have bigger problems - but it's *enough*.

## Executing PHPUnit

How do we *execute* the test? By executing PHPUnit. At your terminal, run:

```terminal
./vendor/bin/phpunit
```

And... awesome! PHPUnit saw *one* test - for our one test method - and one
*assertion*.

We could also say `bin/phpunit` to execute our tests, which is basically just a 
shortcut to run `vendor/bin/phpunit`.

But, I'm sure your curious... What's... an assertion?

Looking back at `DinosaurTest`, the one assertion refers to the `assertEquals()`
method, which comes from PHPUnit's `TestCase` class. If the *actual*
value - 42 - doesn't match the *expected* value, the test would fail.
PHPUnit has a *bunch* more assertion methods... and we can see them
all by going to https://phpunit.readthedocs.io. This is *full*
of goodies, including an "Assertions" section. And... *wow*! Look at them all...
We'll talk about the most important assertions throughout the series.
But for now, back to the test!

## Test Naming Conventions

Because, I have a question: how did PHPUnit *know* that this is a test? When we call
`vendor/bin/phpunit`, PHPUnit does three things. First, it looks for its configuration
file, which is `phpunit.xml.dist`:

[[[ code('93d0379590') ]]]

Inside, it finds `testsuites`... and the `directory` part says:

[[[ code('2f4b344a62') ]]]

> Hey PHPUnit: go look inside a `tests/` directory for tests!

Second, it finds that directory and *recursively* looks for every class that ends with the word
`Test`. In this case, `DinosaurTest`. Finally, once it finds a test class, it gets
a list of all of its public methods.

So... am I saying that PHPUnit will execute *every* public method as a test? Let's find out!
Create a new `public function itWorksTheSame(): void`

[[[ code('67cfb1a4ef') ]]]

Inside we are going to `self::assertSame()` that 42 is equal to 42. `assertSame()` is
*very* similar to `assertEquals()` and we'll see the difference in a minute.

[[[ code('31924251b4') ]]]

Now, move back to your terminal and let's run these tests again:

```terminal-silent
./vendor/bin/phpunit
```

Huh? PHPUnit *still* says just one test and one assertion. But inside our
test class, we have *two* tests and *two* assertions. The problem is that
PHPUnit *only* executes public methods that are prefixed with the word `test`.
You *could* put the `@test` annotation above the method, but that's
not very common. So let's avoid being weird, and change this to
`testItWorksTheSame()`.

[[[ code('0d3b2cd0c0') ]]]

Now when we run the test:

```terminal-silent
./vendor/bin/phpunit
```

PHPUnit sees 2 tests and 2 assertions! Shweeeet!

## Testing Failures 😱

What does it look like when a test fails? Let's find out! Change our expected `42` to a
*string* inside `testItWorks()`... and do the same inside `testItWorksTheSame()`. Yup,
one of these *won't* work.

[[[ code('729d779ffd') ]]]

This time when we try it:

```terminal-silent
./vendor/bin/phpunit
``` 

Oh no! One failure!

> `DinosaurTest::testItWorksTheSame()` failed asserting that `42` is identical to `42`.

So... `assertEquals()` *passed*, but `assertSame()` failed. That's because
`assertEquals()` is the equivalent to doing an if 42 `==` 42: using the
double equal sign. But `assertSame()` is equivalent to 42 `===` 42: with *three* equal signs.

And since the string 42 does *not* triple-equals the integer 42, that test fails
and PHPUnit yells at us.

Ok, we've got our first tests behind us! Though... testing that the answer to life
the universe and everything is equal to the answer to life the universe and everything...
isn't very interesting. So next: let's write *real* tests for the `Dinosaur` class.

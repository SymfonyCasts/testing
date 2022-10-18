# TDD Part 2: Finish & Refactor

Before we move on to the *last* step in TDD, I think we need to add a couple more
size description tests for medium and small dinosaurs. 

## A few more tests

In our `DinosaurTest::class` copy our
`testDino10MetersOrGreaterIsLarge` method and rename it to
`testDinoBetween5And9MetersIsMedium()`. Inside, change the
`length` of our `$dino` from `10` to `5`, use `Medium` for the expected value, and
update the message to `Medium` as well.
*Finally*, paste the method again for our small dino test,
using the name `testDinoUnder5MetersIsSmall()`. Set the
length to `4`, assert that `Small` is identical to `getSizeDescription()` and *also*
update the message.

[[[ code('ebded3950d') ]]]

Back in our terminal, run the tests again:

```terminal-silent
./vendor/bin/phpunit --testdox
```

And... they're failing! But not because our method returns the wrong result. They're
failing due to a type error on `getSizeDescription()`:

> The return value must be of type string and none is returned.

Do you remember earlier we ran our large dinosaur test *before* writing the
method and we didn't see our "this is supposed to be a large dino" message?
Well, we don't see it here either... That's because PHP threw an error... and
so the `getSizeDescription()` message explodes *before* PHPUnit can run the
`assertSame()` method. It's no big deal and we can still use the stack trace to
see exactly where things went wrong.

Alrighty, back to the `Dinosaur` class. Lets fix these tests by adding
`if ($this->length)` is less than `5`, `return 'Small'`:

[[[ code('f69c71ac08') ]]]

And `if ($this->length)` is less than `10`, `return 'Medium'`

[[[ code('987634ca95') ]]]

Back to our terminal, run the test again:

```terminal-silent
./vendor/bin/phpunit --testdox
```

And... alright alright alright... they're passing.

## Step 4: Refactoring

So let's move on to the *last* step of TDD... and a fun one! Refactoring our code.

Looking at our `getSizeDescription()` method, I think we can clean this up a bit.
And the great news is that, because we've covered our method with tests, if we
mess something up during refactoring, the tests will tell us! We get to be reckless!
It also means that we didn't really need to worry about writing *perfect* code earlier.
We just needed to make our tests pass. NOW we can improve things...

Let's change this middle condition to `if ($this->length)` is greater than or equal
to `5`, return `Medium`. We can get rid of this last conditional altogether and
just return `Small`:

[[[ code('145e07489e') ]]]

I like that! To see if we messed up, move back to the terminal and run our
tests again.

```terminal-silent
./vendor/bin/phpunit --tesdox
```

And... we've done it! That's TDD - write the test, see the test fail,
write simple code to see the test pass, then refactor our code. Rinse and repeat.

TDD Is interesting because, by writing our test first, it forces us to think about
*exactly* how a feature should work... Instead of just blindly writing code and
seeing what comes out. It also helps us focus on *what* we need to code... Without
making things too fancy. Yes, I'm guilty of that too... Get your tests to pass,
then refactor... Nothing more is needed.

## Use the Size Description in our Controller

And now that we have our fancy new method - built via the powers of TDD - let's
celebrate by *using* it on the site!

Close up our terminal and move to our template: `templates/main/index.html.twig`.
Instead of showing the dino's with `dino.length`, change this to
`dino.sizeDescription`. Save it, go back to our browser and... refresh.

[[[ code('004503ef83') ]]]

Awesome. We have large, medium, and small for the dinosaur's size instead of a number.
No way Bob will accidentally wander into the T-Rex enclosure again!

We've just used TDD to make our app a bit more human-friendly. Coming up next,
we'll use some of the TDD principles we've learned here to clean up our tests with
PHPUnit's data providers!

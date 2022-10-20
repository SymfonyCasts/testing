# Data Providers

We treat our source code as a first-class citizen. That means, among other things,
we avoid duplication. Why not do the same with our
tests? Our three tests for the size are... repetitive. They test the same thing
just with *slightly* different input and then a different assertion. Is there
a way to improve this? Absolutely: thanks to PHPUnit Data Providers.

## Refactor our tests

Move to the bottom of `DinosaurTest` and add
`public function sizeDescriptionProvider()`. Inside, `yield` an array with `[10, 'Large']`,
then `yield [5, 'Medium']`, and finally `yield [4, 'Small']`:

[[[ code('fa183eb1dc') ]]]

Yield is just a fancy way of returning arrays using PHP's built-in Generator function. 
As you'll see in a minute, these values - like `10` and `large` 
will become *arguments* to our test.

Alrighty, up in our test method, add an `int $length` argument and then
`string $expectedSize`:

[[[ code('9c18fe2527') ]]]

Now instead of Big Eaty's length being `10`, use `$length`. And for our assertion, 
use `$expectedSize` instead of `Large`:

[[[ code('b79f48e5de') ]]]

We do not need the medium and small tests anymore, so we can remove *both* of them.

Ok! Move back to your terminal and run our tests:

```terminal
./vendor/bin/phpunit --testdox
```

Uh oh... Our test is failing because! It says:

> ArgumentCountError - Too few arguments were provided. 0 passed and exactly 2 expected.

## Tell our test to use the Data Provider

Oops, we never told our test method to *use* the data provider. Move back into our
test and add a DocBlock with `@dataProvider sizeDescriptionProvider`:

[[[ code('20801d2e3a') ]]]

When PHPUnit 10 gets released, we'll be able to use a fancy `#[DataProvider]` attribute 
instead of this annotation.

Back to the terminal! Run the tests again:

```terminal-silent
./vendor/bin/phpunit --testdox
```

And... Yes! Our tests are passing!

## Message Keys instead of Arguments

In the output, we see that each test ran with datasets 0, 1, & 2. Those are the
arrays from the data provider. We can spruce this up a bit... because it's not
going to be very helpful later if PHPUnit tells us that dataset `2` failed. Which
one is that?

Move back to our test and, down here after the first `yield` statement, add the message
key `'10 Meter Large Dino' =>`. Copy and paste this for our medium dino with `5`
instead of `10` and this needs to be `Medium`. Do the same for our small dino
with `4` and `Small`:

[[[ code('dc69fe001b') ]]]

Back in our terminal, let's see our tests now:

```terminal-silent
./vendor/bin/phpunit --testdox
```

And... Cool Beans! We now have

> Dino 10 meters or greater is large with 10 Meter Large Dino

This looks a lot better than just seeing data set 0... though we do need to fix
one more thing. That test method name doesn't make sense anymore.
Change it to `testDinoHasCorrectSizeDescriptionFromLength()`.

And, looking at our assertion, the message argument isn't very useful anymore... so let's
remove it.

[[[ code('dc69fe001b') ]]]

# Return Types Everywhere!

Finally, although not required... We can use either `array` or
`\Generator` as the return type for the data provider. Let's go with
`\Generator`- after all, we may need those for the park fences one day...

[[[ code('af4164141c') ]]]

To make sure this didn't break anything, try the tests one more time:

```terminal-silent
./vendor/bin/phpunit --testdox
```

Ummm... Awesome! Green Checks Everywhere!

And there you have it, with a little TLC, our tests are now nice and tidy...
Coming up next, let's figure out how we can get our Dino's health status from
GitHub and use it in our app...

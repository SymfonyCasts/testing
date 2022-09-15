# Data Providers

We treat our source code as first-class citizens. Why not do the same with our
tests? Our three tests for the size are... repetitive. They test the same thing
just with slightly different input and then a different assertion. We can improve
this by using PHPUnit's Data Providers. Which run the same test over and over
with different sets of data.

Move down to the bottom of our `Dinosaur` class and add
`public function sizeDescriptionProvider()`. Inside, `yield` an array `[10, 'Large']`,
then `yield [5, 'Medium']`, and finally `yield [4, 'Small']`. Yield is just a fancy
way of returning arrays using PHP's built-in Generator function.

Alrighty, move back to our test and add the `int $length` argument and then
`string $expectedSize`. Now instead of Big Eaty's length being `10`, we'll use
`$length`. For our assertion, use `$expectedSize` instead of `Large`. We do not
need the medium and small tests anymore, so remove both of those methods too.

Move back to your terminal and run our tests:

```terminal
./vendor/bin/phpunit --testdox
```

Uh oh... Our test is failing because:

> Dino 10 Meters Or Greater Is Large expected 2 arguments and 0 were passed.

Oops, we never told our test method to use the data provider. Move back into our
test and add a DocBlock with `@dataProvider sizeDescriptionProvider`. When PHPUnit
10 gets released, we'll be able to use a dataProvider attribute instead of this
annotation.

Back to the terminal and run the tests again:

```terminal-silent
./vendor/bin/phpunit --testdox
```

And... Yes! Our tests are passing!

In the output, we see that each test ran with datasets 0, 1, & 2. Those are our
arrays from the data provider. We can spruce this up a bit.

Move back into our test and down here after a first yield statement, add the message
key `'10 Meter Large Dino' =>`. Copy and paste this for our medium dino with `5`
instead of `10` and this needs to be `Medium`. Do the same for our small dino
with `4` and `Small`.

Back in our terminal and see our tests now:

```terminal-silent
./vendor/bin/phpunit --testdox
```

And... Cool Beans! We now have

> Dino 10 meters of greater is large with 10 Meter Large Dino

This looks a lot better than just seeing data set 0... We do however need to fix
one more thing. That test method name doesn't fit in with our other 2 data providers.
In our test, change the method name to `testDinoHasCorrectSizeDescriptionFromLength()`.

Looking at our assertion, the message argument isn't very useful anymore... Let's
remove it. We can always add it back in using our data provider later if we need
to.

Lastly, although not required... We can use either `array` or
`\Generator` as the return type for our data provider. Let's go with
`\Generator`- after all, we may need those for the park fences one day...

Move back into the terminal and run our tests one last time:

```terminal-silent
./vendor/bin/phpunit --testdox
```

Ummm... Awesome! Green Checks Everywhere!

And there you have it, with a little TLC, our tests are now nice and tidy...
Coming up next, let's figure out how we can get our Dino's health status from
GitHub and use it in our app...

# TDD - Test Driven Development

All right. So one of the problems is that when Bob, our park ranger, sees the
dinosaur size... he can't remember if these are in meters... or centimeters...
which makes a big difference when you step into a cage.

A better way might be to just use words like small, medium, or large. So... let's
do that!

## What is TDD?

*But*, to add this feature, we're going to use a philosophy called
Test Driven Development or TDD. TDD is basically a buzzword that describes a
4-step process for writing your tests first.

Step 1: Write a test for the feature. Step 2: Run your test and watch it fail...
since we haven't created that feature yet! Step 3: Write as little code as
possible to get our test to pass. And Step 4: Now that it's passing, refactor
your code if needed to make it more awesome

So, to get the Small, Medium, or Large text, I think we should add a new
`getSizeDescription()` method to our `Dinosaur` class. *But*, remember, we're
going to do this the TDD way, where Step 1 is to write a *test* for that method...
even though it doesn't exist yet. Yes, I know that's weird... but it's kinda awesome!

## Step 1: Write a test for the Feature

Add `public function` and let's first test that a dinosaur that's over 10 meters
or greater is large:

[[[ code('5ce160fd51') ]]]

Inside, say `$dino = new Dinosaur()`, give him a name,
let's use Big Eaty again, since he's a cool dude, and set his length to 10.

Then, `assertSame()` that `Large` will be identical to `$dino->getSizeDescription()`.
For our failure message, let's use `This is supposed to be a Large Dinosaur`.

[[[ code('45edace3e2') ]]]

Yes, we're *literally* testing a method that doesn't exist yet. That's TDD.


## Step 2: Run the test and watch it fail

Ok, step 1 is done. Step 2 is to run our test and make sure it fails.
Open up a terminal and then run `./vendor/bin/phpunit`.

```terminal
./vendor/bin/phpunit
```

And... great! 2 tests, 4 assertions, and 1 error.
Our new test failed because, of course, we called an undefined method! We kind
of knew this would happen. Hm... Did you notice that our
"this is supposed to be at large dinosaur" message isn't showing up here? I'll
explain why in just a minute.

## Step 3: Write simple code to make it pass

Time for step 3 of TDD: write simple code to make this test pass.
This part, taken literally, can get kinda funny. Watch:
back in our `Dinosaur` class add a new `public function getSizeDescription()`
which will return a `string`. Inside... `return 'Large'`:

[[[ code('c6d7d5f8cc') ]]]

Yup, that's it! Move back to your terminal and re-run the tests.

```terminal-silent
./vendor/bin/phpunit --testdox
```

And... Awesome - They Pass! Well... of *course* the test passed - we hard coded
the result we wanted! But, that's *technically* what TDD says: write the *least*
amount of code possible to get your test to pass. If your method is too simple
after doing this, it means you're missing more tests - like for small or medium
dinosaurs - that would force you to *improve* the method. We'll see that in a
minute.

But let's be a *bit* more realistic. Say:
`if ($this->length >= 10) {`, then `return 'Large'`:

[[[ code('109038eb53') ]]]

Run the tests *one* more time to make sure they're still passing:

```terminal-silent
./vendor/bin/phpunit --testdox
```

And... yes! We're still good to go!

Next, let's finish this method the TDD-way: by writing more tests for the missing
features first. Then we'll move onto the final - and most fun step of TDD: Refactoring!

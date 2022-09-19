# Dino Health

Bob has just told us he needs to display which dinos are accepting lunch in our
app... Ha... I mean visitors. If a dino is feeling good, park guests should be able
to see them. But if they are sick - no visitors are allowed. *Eventually*, we'll
call GitHub's API to get a list of sick dinos from GenLab. But for now, we'll 
assume that all dinos are healthy.

## Are they accepting visitors?

To kick this off, in our `DinosaurTest` add
`public function testIsAcceptingVisitorsByDefault()`. Inside, `$dino = new Dinosaur()`
and let's call him `Dennis`.

Now we want to `assertTrue()` that Dennis `isAcceptingVisitors()`.

Move to our terminal to run our test:

```terminal
./vendor/bin/phpunit --testdox
```

And... great! We have 5 tests, 7 Assertions, & 1 Error because:

> isAcceptingVisitorsByDefault() calls an undefined method.

To fix this, move to our `Dinosaur` class and at the bottom, add
`public function isAcceptingVisitors()` that returns `bool`. Inside, return `true`.

Move back to the terminal and run our tests again...

```terminal-silent
./vendor/bin/phpunit --testdox

```

And... Yes! `Is accepting visitors by default` is now passing!

## Sick Dino's - Stay Away!

Now let's take care of our sick dino's by adding
`public function testIsNotAcceptingVisitorsIfSick(): void`.
Inside we'll create a `$dino` with the name `Bumpy`. And then `assertFalse()` that
`$dino->isAcceptingVisitors()`.

Let's see this test fail in our terminal...

```terminal-silent
./vendor/bin/phpunit --testdox
```

Hmm... Yup!

< Is not accepting visitors if sick failed asserting that true is false.

This is *exactly* what we were expecting...

Looking at our `Dinosaur` class, we need to
do 2 things. `isAcceptingVisitors()` should return true if the dino is healthy. *And* we
need a way to set a health status on our object.

With a quick peek at the issues on GitHub - GenLab is using labels for "Sick" and
"Healthy" dino's. I'm thinking we can use those labels on our dino objects too.

# Enums are cool for health labels

Instead of setting `Healthy` or `Sick` on a property in our `Dinosaur` class. Let's
be a bit more modern than Dennis & his buddy Bumpy by creating a new `Enum/` folder
inside the `src` directory. Now create a new class - `HealthStatus` and for the 
template, select `Enum`. We need `HealthStatus` to be backed by a `: string`. 
Inside... add a `case` for `HEALTHY` that returns `Healthy'` and do the same for
`SICK`.

Over in our `Dinosaur`, add a new `private HealthStatus $health` property
that defaults to `HealthStatus::HEALTHY`. And down in our `isAcceptingVisitors()`
method, only return true if `$this->health === HealthStatus::HEALTHY`.

Back to the terminal and make sure we still have just the one failure.

```terminal-silent
./vendor/bin/phpunit --testdox
```

And... Great! We didn't break anything.

Back to our failing test, call `$dino->setHealthStatus()` and pass in `HealthStatus::SICK`.
We *could* run this test, but we already know it would give us an undefined method error,
so let's skip that and in our `Dinosaur` class just add `public function setHealthStatus()`.
This method will accept a `HealthStatus $health` argument and returns nothing.
Inside, `$this->health === $health`

*NOW* move back to our terminal and run our tests again...

And... WooHoo! Our 6 tests and 9 assertions are all passing!

## Show which exhibits are open

Alrighty... The last thing we need to do is use our health status in the app. Open
our `index.html.twig` template here in the `main/` and add a `Accepting Visitors`
heading to our table. In the dino loop, call `dino.acceptingVisitors` and we'll
show `Yes` if this is true or `No` if we get false.


Move into our browser, and refresh our status page... And... WooHoo! All
of our dinos are accepting visitors!

But... We already know from looking at GitHub earlier, that some of our dinos 
*are* sick and should *not* be accepting visitors... We *could* set the health 
status for our sick dinos in the `MainController`. But, why not just get that 
information from GitHub instead. We'll do that next by creating a GitHub Service
to keep our dino table updated automatically.

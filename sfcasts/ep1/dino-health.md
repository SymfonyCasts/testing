# Dino Health

Bob has just told us he needs to display which dinos are accepting lunch, erm... I mean visitors
in our app. If a dino is feeling good, they're accepting visitors, if they are sick -
no visitors allowed. Eventually, we'll call githubs API to get a list of sick dinos
from genlab. But for now, we'll assume all dinos are healthy.


To kick this off, lets create a new test in our `DinosaurTest`...
`public function testIsAcceptingVisitorsByDefault()`. Inside, `$dino = new Dinosaur()`
and let's call him `Dennis`.

Now we want to `assertTrue()` that `$dino->isAcceptingVisitors()`.

Move to our terminal and run our test:

```terminal
./vendor/bin/phpunit --testdox
```

And... great. We have 5 tests, 7 Assertions, & 1 Error because:

> isAcceptingVisitorsByDefault() calls an undefined method.

Move to our `Dinosaur` class and at the bottom, add `public function isAcceptingVisitors(): bool`.
Inside, return `true`.

Move back to the terminal and run our tests again...

```terminal-silent
./vendor/bin/phpunit --testdox

```

And... Yes! `Is accepting visitors by default` is now passing!

Back in our test, add a `public function testIsNotAcceptingVisitorsIfSick(): void`.
Inside we'll create a `$dino` with the name `Bumpy`. And then `assertFalse()` that
`$dino->isAcceptingVisitors()`.

Let's see this test fail in our terminal

```terminal-silent
./vendor/bin/phpunit --testdox
```

Hmm... Yup...

< Is not accepting visitors if sick failed asserting that true is false.

Is exaclty what we were expecting... Looking at our `Dinosaur` class, we need to
do 2 things. Add a way to set the dinos health status and refactor our `isAcceptingVisitors()`
to return false if the dino is sick. Over here on GitHub, we can see that GenLab is
using `Sick` and `Healthy` on a few dino issues. So lets use those
on our dino objects too.


Instead of just using a string for the health status, create a new folder `src/Enum/`
and then create a new class - `HealthStatus`. For the template, select `Enum`.
Enum that is backed with a `string`. Inside... add
a `case` for `HEALTHY = 'Healthy'` and do the same for `SICK`.

Over in our `Dinosaur`, add a new `private HealthStatus $health` property
that defaults to `HealthStatus::HEALTHY`. And down in our `isAcceptingVisitors()` method,
change return `true` to return `$this->health === HealthStatus::HEALTHY`.

Back to the terminal and lets make sure we still have just the 1 failure.

```terminal-silent
./vendor/bin/phpunit --testdox
```

And... Great! We didn't break anything.

Back in our test, call `$dino->setHealthStatus()` and pass in `HealthStatus::SICK`.
We *could* run this test, but we already know it would give us an undefined method error,
so let's skip that and just add `public function setHealthStatus()` in our `Dinosaur` class.
This method will accept a `HealthStatus $health` argument and returns `void`.
Inside, `$this->health === $health`

*NOW* move back to our terminal and run our tests again...


And... WooHoo! Our 6 tests and 9 assertions are all passing!


Alrighty... The last thing we need to do is use our health status in the app. Open
our `index.html.twig` template here in the `main/` and add a new `Accepting Visitors`
heading to our table. In the dino loop, call `dino.acceptingVisitors` and we'll
show `Yes` if this is true or `No` if we get false.


Move into our browser, and refresh our status page... And... WooHoo! All
of our dinos are accepting visitors!

But... We already know from looking at GitHub earlier, some of our dinos *are* sick
and should not be accepting visitors... We *could* set the health status for our sick
dinos in the `MainController` now. but, why not just get that information from
GitHub instead. We'll do that next by creating a GitHub Service to keep our dino
table updated automatically.

# Dino Health

Bob has just told us he needs to display which dinos are accepting lunch in our
app... Ha... I mean visitors. GenLab has strict protocols in place for dinos that
are feeling under the weather. That means no park guests are allowed... To help 
display this we need to store the health status for each of our dinos and have 
an easy way to figure out whether or not this means they're accepting visitors...

GenLab has a long standing tradition - if they havent labeled a dino as sick on
GitHub, the dino is healthy.

If a dino is feeling good, park guests should be able
to see them. But if they are sick - no visitors are allowed. *Eventually*, we'll
call GitHub's API to get a list of sick dinos from GenLab. But for now, we'll 
assume that all dinos are healthy.

## Are they accepting visitors?

Lets start by adding a method - `isAcceptingVisitors()` to our `Dinosaur`. But,
we'll do this the TDD way by writing the test first. In `DinosaurTest` add
`public function`, Hmm... if only sick dinos cannot have guests then we should name
this `testIsAcceptingVisitorsByDefault()`. Inside, `$dino = new Dinosaur()`
and let's call him `Dennis`.

By default, if we simply instantiate a `Dinosaur` and do nothing else, the `dino`
*should* be accepting visitors, so let's `assertTrue()` that Dennis `isAcceptingVisitors()`

Move to our terminal to run our test:

```terminal
./vendor/bin/phpunit --testdox
```

And... great! We have 5 tests, 7 Assertions, & 1 Error because:

> isAcceptingVisitorsByDefault() calls an undefined method.

To fix this, move to our `Dinosaur` class and add
`public function isAcceptingVisitors()` that returns `bool`. Inside, return `true`.

Move back to the terminal and run our tests again...

```terminal-silent
./vendor/bin/phpunit --testdox

```

And... Yes! `Is accepting visitors by default` is now passing!

## Sick Dino's - Stay Away!

Now let's take care of our sick dino's by not allowing visitors. Add
`public function testIsNotAcceptingVisitorsIfSick(): void` and
inside we'll create a `$dino` with the name `Bumpy`. 

We'll need a way to set a dino's health status... With a quick peek at the issues
on GitHub - GenLab is using labels for "Sick" and
"Healthy" dino's. We can probably use those labels too on our dino objects by
calling `$dino->setHealth('Sick')`. Now we'll want to `assertFalse` that Bumpy
`isAcceptingVisitors()`.

Let's see this test fail in our terminal...

```terminal-silent
./vendor/bin/phpunit --testdox
```

Hmm... Yup!

< !!!!!!!!!!!!!! ERRRRRROR MEESSAGE HERE !!!!!!!!!!!!!!!!!!!!!!!!!

This is *exactly* what we were expecting...

Back in `Dinosaur`, add a new `setHealth()` method that accepts a `string $health`
argument and returns `void`. Inside, set the `$health` on `$this->health`. Up top,
add a `private string $health` property that defaults to `'Health'`.

Cool! The last we need to do is change the return value in `isAcceptingVisitors()`.
Instead of `true`, lets return `$this->health === $healthy`.

In the terminal, run our tests again.

```terminal-silent
./vendor/bin/phpunit --testdox
```

WOOOHOOOOOOOOO! ITS WORKING.. (IM ALL CAPS, NEED TO GET THE ACTUAL OUTPUT HERE) !!!!!!!!!!!!!!!!!!!!!!

# Enums are cool for health labels

!!!!!!!!!! LETS ENSURE THAT WE ONLY ACCEPT HEALTHY OR SICK BY USING AN ENUM!!!!!!!!!!!!!!!!!11


Instead of setting `Healthy` or `Sick` on a property in our `Dinosaur` class. Let's
be a bit more modern than Dennis & his buddy Bumpy by creating a new `Enum/` folder
inside the `src` directory. Now create a new class - `HealthStatus` and for the 
template, select `Enum`. We need `HealthStatus` to be backed by a `: string`. 
Inside... add a `case` for `HEALTHY` that returns `Healthy'` and do the same for
`SICK`.

Over in our `Dinosaur` health property, use `HealthStatus::HEALTHY` instead of
`'Healthy`. And down in our `isAcceptingVisitors()`
method, true if `$this->health === HealthStatus::HEALTHY`.

Last thing todo is use `HealthStatus::SICK` in our test.

Run our tests again to make sure nothing is borked up.

```terminal-silent
./vendor/bin/phpunit --testdox
```

And... Great! We didn't break anything.

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

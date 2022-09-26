# Incomplete Tests and Dancing Dino's

!!!!!!! Start with screen on the app !!!!!!!!! <----- Note to self while recording, to be removed before merge...

Bob has just told us he needs to display which dinos are accepting lunch in our
app... Ha... I mean visitors. GenLab has strict protocols in place - park guests
can visit with healthy dinos but if they're sick, no visitors are allowed.
To help display this, we need to store the health status of each of our dinos *and*
have an easy way to figure out whether or not this means they're accepting visitors...

## Let's skip a test...

Lets start by adding a method - `isAcceptingVisitors()` to our `Dinosaur`. But,
we'll do this the TDD way by writing the test first. In `DinosaurTest` add
`public function testIsAcceptingVisitorsByDefault()`. Inside, `$dino = new Dinosaur()`
and let's call him `Dennis`.

If we simply instantiate a `Dinosaur` and do nothing else, the dino *should* be 
accepting visitors until GenLab tells us otherwise, so `assertTrue()` that Dennis
`isAcceptingVisitors()`.

Below this test, add another called `testIsNotAcceptingVisitorsIfSick()`. And
for now, we'll just call `$this->markTestImcomplete()`.

Move to our terminal to run our tests:

```terminal
./vendor/bin/phpunit --testdox
```

And... great! Our `isAcceptingVisitors()` test is failing because of a

> Call to an undefined method.

But, our *next* test has this weird circle `∅`... That's because we marked the test
as incomplete. I use this sometimes when I know I'll need a test for a feature,
but I'm not ready to *complete* the test yet. There also is a `markSkipped()` method
that can be used when you need to skip tests under certain conditions. For instance,
if the test only should run on PHP 8.1.

## Are they accepting visitors?

Anywho, let's get back to coding, shall we... In our `Dinosaur` class, add a method
`isAcceptingVisitors()` that returns a `bool`, and inside we'll return `true`.

Let's see what happens when we run our tests now...

```terminal-silent
./vendor/bin/phpunit --testdox

```

And... Yes! `Is accepting visitors by default`... is now passing! Check it out,
even though we still have our "sick dino" test marked as incomplete, *all* of our tests
are *technically* passing. This is super cool when using a continuous integration
service, like GitHub Actions, where we wouldn't want a skipped or incomplete test
to show that all of our tests have failed.

## Sick Dinos - Stay Away!

Alrighty, time to take care of this incomplete test... a quick peek at the issues
on GitHub - GenLab is using labels for "Sick" and "Healthy" dinos. We can probably
use those labels too on our dino objects.

Inside our test, remove `markAsIncomplete()` and create a `$dino` with the name
`Bumpy`. Now call `$dino->setHealth('Sick')` and then `assertFalse()` that
Bumpy `isAcceptingVisitors()`. Hmm... PHPStorm is telling us

> Method setHealth() is not found inside Dinosaur

So... let's skip running the test and in `Dinosaur` add a new `setHealth()` method that
accepts a `string $health` and returns `void`. Inside, set the `$health`
on `$this->health`... Then up top, add a `private string $health` property that
defaults to `Health`.

Cool! Now we just need the return value in `isAcceptingVisitors()` to return
`$this->health === $healthy` instead of `true`.

Fingers crossed our tests are now passing...

```terminal-silent
./vendor/bin/phpunit --testdox
```

And... Mission Accomplished!

# Enums are cool for health labels

But I'm thinking that we should refactor the `setHealth()` method to only allow
`Sick` or `Healthy` and not something like `Dancing`... Create a new `Enum/` folder
inside the `src` directory then a new class - `HealthStatus`. For the template,
select `Enum` and click `OK`. We need `HealthStatus` to be backed by a `: string`... 
And our first `case HEALTHY` will return `Healthy`, then `case SICK` will return 
`Sick`.

On the `Dinosaur::health` property default to `HealthStatus::HEALTHY`. And down
in our `isAcceptingVisitors()` method, return true if
`$this->health === HealthStatus::HEALTHY`.

The last thing todo is use `HealthStatus::SICK` in our test. Now we can run our 
tests again...

```terminal-silent
./vendor/bin/phpunit --testdox
```

And... Ya! We didn't break anything...

## Show which exhibits are open

To fulfill Bob's wishes, open our `index.html.twig` template here in the `main/` 
templates directory and add a `Accepting Visitors` heading to our table. In the
dino loop, create a new `<td>` and call `dino.acceptingVisitors`. We'll show 
`Yes` if this is true or `No` if we get false.

In the browser, refresh the status page... And... WooHoo! All of our dinos are
accepting visitors!

But... We already know from looking at GitHub earlier, that some of our dinos
*are* sick and per GenLab's protocols they should *not* be accepting visitors...
We *could* set `HealthStatus::SICK` for our sick dinos in the `MainController`. But,
why not just get the health labels from the issues on GitHub instead? We'll do
that next by creating a GitHub Service to keep our dino table updated automatically.

# Incomplete Tests and Dancing Dino's

Bob just told us he needs to display which dinos are accepting lunch in our
app... I mean accepting *visitors*. GenLab has strict protocols in place: park guests
can visit with *healthy* dinos... but if they're sick, no visitors allowed.
To help display this, we need to store the health status of each dino *and*
have an easy way to figure out whether or not this means they're accepting visitors...

## Let's skip a test...

Let's start by adding a method - `isAcceptingVisitors()` to `Dinosaur`. But,
we'll do this the TDD way by writing the test first. In `DinosaurTest` add
`public function testIsAcceptingVisitorsByDefault()`. Inside, `$dino = new Dinosaur()`
and let's call him `Dennis`:

[[[ code('a16abdacf3') ]]]

If we simply instantiate a `Dinosaur` and do nothing else, GenLab policy states
that it *is* ok to visit that Dinosaur. So `assertTrue()` that Dennis
`isAcceptingVisitors()`:

[[[ code('560a2166c2') ]]]

Below this test, add another called `testIsNotAcceptingVisitorsIfSick()`. And
for now, let's be lazy and just say `$this->markTestIncomplete()`:

[[[ code('f091e321f6') ]]]

Ok, let's try the tests:

```terminal
./vendor/bin/phpunit --testdox
```

And... no surprise! Our first new test is failing:

> Call to an undefined method.

But, our *next* test has this weird circle `∅` because we marked the test
as *incomplete*. I use this sometimes when I know I need to write a test...
I'm just not ready to *do* it quite yet. PHPUnit also has a `markSkipped()` method
that can be used to skip tests under certain conditions, like if a test
should run on PHP 8.1.

## Are they accepting visitors?

Anywho, let's get back to coding, shall we... In our `Dinosaur` class, add a
`isAcceptingVisitors()` method that returns a `bool`, and inside we'll return `true`.

[[[ code('4659aadb65') ]]]

Let's see what happens when we run our tests now...

```terminal-silent
./vendor/bin/phpunit --testdox
```

And... Yes! `Is accepting visitors by default`... is now passing! We still have
one *incomplete* test as a reminder, but it's not causing our whole test suite to fail.

## Sick Dinos - Stay Away!

Let's finish that now. If we peek at the issues
on GitHub - GenLab is using labels to identify the "health" of each dino: "Sick" versus
"Healthy". Pretty soon, we're going to *read* these labels and use them in our app.
To prep for that, we need a way to store the current *health* on each `Dinosaur`.

Inside the test, remove `markAsIncomplete()` and create a `$dino` named
`Bumpy`... he's a triceratops. Now call `$dino->setHealth('Sick')` and then `assertFalse()`
that Bumpy `isAcceptingVisitors()`. He's cranky when he's sick.

[[[ code('eb240d5ae4') ]]]

But, no surprise, PHPStorm is telling us:

> Method setHealth() not found inside Dinosaur

So... let's skip running the test and head straight to `Dinosaur` to add a `setHealth()` method that
accepts a `string $health` argument... and returns `void`. Inside, say `$this->health = $health`...
then up top, add a `private string $health` property that
defaults to `Healthy`:

[[[ code('390f3f8422') ]]]

Cool! Now we just need to update `isAcceptingVisitors()` to return
`$this->health === $healthy` instead of `true`:

[[[ code('81141c7b9b') ]]]

Fingers crossed our tests are now passing...

```terminal-silent
./vendor/bin/phpunit --testdox
```

And... Mission Accomplished!

## Enums are cool for health labels

Now that the tests are passing, I'm thinking we should refactor the `setHealth()` method to only allow
`Sick` or `Healthy`... and not something like `Dancing`... Inside `src/`, create a new `Enum/`
directory then a new class: `HealthStatus`. For the template,
select `Enum` and click `OK`. We need `HealthStatus` to be backed by a `: string`...

[[[ code('f0b8afa53b') ]]]

And our first `case HEALTHY` will return `Healthy`, then `case SICK` will return
`Sick`.

[[[ code('333f147537') ]]]

On the `Dinosaur::$health` property, default to `HealthStatus::HEALTHY`. And
change the property type to `HealthStatus`. Down in `isAcceptingVisitors()`, 
return true if `$this->health === HealthStatus::HEALTHY`. Below in `setHealth()`,
change the argument type from `string` to `HealthStatus`.

[[[ code('a9a2043218') ]]]

The last thing to do is use `HealthStatus::SICK` in our test.

[[[ code('f7e4ecb955') ]]]

Let's see if we broke anything!

```terminal-silent
./vendor/bin/phpunit --testdox
```

And... Ya! We *didn't* break anything... I'm only a little surprised.

## Show which exhibits are open

To fulfill Bob's wishes, open the `main/index.html.twig` template
and add an `Accepting Visitors` heading to the table. In the
dino loop, create a new `<td>` and call `dino.acceptingVisitors`. We'll show
`Yes` if this is true or `No` if we get false.

[[[ code('c21565ec8c') ]]]

In the browser, refresh the status page... And... WooHoo! All of our dinos *are*
accepting visitors... because we haven't set any as "sick" on our code!

But... We already know from looking at GitHub earlier, that some of our dinos
*are* sick. Next: let's use GitHub's API to read the labels from our GitHub
repository and set the *real* health on each `Dinosaur` so that our dashboard
will update in real-time.

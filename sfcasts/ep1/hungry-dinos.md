# Filtering Out Hungry Dino's

Instead of seeing our dinos on the dashboard, we're seeing a `TypeError` for
`GithubService`:

> Return value must be of type `HealthStatus`, `null` returned

That's not doing a great job of telling us *what* the problem really is. Thanks
to the stack trace, it looks like it's being caused by a `Status: Hungry` label.
Yup! On GitHub, it looks like Dennis is hungry again after finishing his daily 
exercise routine.

## Our Enum Is Hungry Too

Looking at `HealthStatus`, we don't have a case for hungry dinos:

[[[ code('f206435c2b') ]]]

So add `case HUNGRY` that returns `Hungry`... then refresh the dashboard.

[[[ code('6155f75ab1') ]]]

And... Ya! No more errors...

But, wait... It says that `Dennis` is *not* accepting visitors. He isn't *sick*,
just *hungry*. GenLab said only sick dino's should *not* be on exhibit. Besides, 
who doesn't want to see what happens to the goat?

## Test Hungry Dinos Can Have Visitors

In `DinosaurTest`, we need to assert that hungry dino's *can* have visitors.
Hmm... I think we might be able to use `testIsNotAcceptingVisitorsIfSick()` for this.
Yup, that's what we'll do. Below, add a `healthStatusProvider()` that returns 
`\Generator` and for the first dataset `yield 'Sick dino is not accepting visitors'`. 
In the array say `HealthStatus::SICK`, and `false`. Next, 
`yield 'Hungry dino is accepting visitors'` with `[HealthStatus::HUNGRY, true]`:

[[[ code('6ad9401fac') ]]]

Above, add the `@dataProvider` annotation so we can use `healthStatusProvider()`.
While we're here, rename the method to `testIsAcceptingVisitorsBasedOnHealthStatus` 
then add the arguments `HealthStatus $healthStatus` and `bool $expectedVisitorStatus`:

[[[ code('62a155f67d') ]]]

Inside set the health with `$healthStatus` then replace `assertFalse()` with 
`assertSame($expectedStatus)` is identical to `$dino->isAcceptingVisitors()`:

[[[ code('2180c8cad5') ]]]

Phew, that was a lot of work!

## Filtering Tests

Let's see if that did the trick. Run:

```terminal
./vendor/bin/phpunit --filter testIsAcceptingVisitorsBasedOnHealthStatus
```

See what I did there? To focus on *just* this test, we can add the `--filter`
set to the complete or partial name of a test class, method, or anything in between.
This comes in really handy when you have a large test suite and only want to run
one or a few tests.

Anywho, Hungry dino is not accepting visitors is failing:

> Failed asserting that false is true.

Looking at `Dinosaur::isAcceptingVisitors()`, to account for hungry dino's,
we need to return `$this->health` does not equal `HealthStatus::SICK`:

[[[ code('326a577dd2') ]]]

Let's see what happens when we run:

```terminal
./vendor/bin/phpunit --filter "Hungry dino is accepting visitors"
```

And... boom! Our hungry dino test is now passing, ha! Yup, we can use data provider
keys with the `filter` flag too. But to make sure we didn't stop healthy dino's
from having visitors, run:

```terminal
./vendor/bin/phpunit
```

Um... Yes! All dots and no errors. Shweet! We didn't wreck the park. Take a look
at the dashboard, refresh, and ya! Dennis is able to eat his lunch with park guests
once again. Though, I think we should be proactive and throw a more clear exception
in case we ever see any future status labels that we don't know about. Let's do 
that next.

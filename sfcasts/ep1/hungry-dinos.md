# Filtering Out Hungry Dino's

Instead of seeing our dinos on the dashboard, we're seeing a TypeError:

> `GithubService::getDinoStatusFromLabels()` Return value must be of type
`App\Enum\HealthStatus`, `null` returned

Which isn't doing a good job at telling us *what* the problem really is. Thanks
to the Stack Trace that Symfony is giving us, we see this is being caused by a
`Status: Hungry` label and here on GitHub, Dennis is hungry again after finishing
his daily exercise routine.

## Our Enum Is Hungry Too

Looking at `HealthStatus`, we don't have a case for hungry dinos. So add
`case HUNGRY` that returns `Hungry`... and then refresh to see if we fixed the
problem.

And... Ya! No more errors...

But, wait... It says that `Dennis` is *not* accepting visitors. He isn't *sick*,
just *hungry*. GenLab said only sick dino's should not be on exhibit. Beside, who
doesn't want to see what happens to the goat?

## Test Hungry Dinos Can Have Visitors

In `DinosaurTest`, we need to test that hungry dino's *can* have visitors.
Hmm... I think we might be able to use `testIsNotAcceptingVisitorsIfSick()` for this.
Yup, that's what we'll do. Below, add a
`healthStatusProvider()` that returns `\Generator` and for the first dataset
`yield 'Sick dino is not accepting visitors'`. In the array say `HealthStatus::SICK`,
and `false`. Next, `yield 'Hungry dino is accepting visitors'` with
`HealthStatus::HUNGRY` and `true`. Above the test method, add the `@dataProvider`
annotation so we can use `healthStatusProvider()`.
We should also
rename the test method name to `testIsAcceptingVisitorsBasedOnHealthStatus` and
the arguments `HealthStatus $healthStatus` then `bool $expectedStatus`.
Inside we'll set the health using `$healthStatus` then replace `assertFalse()` with
`assertSame($expectedStatus)` is identical to `$dino->isAcceptingVisitors()`.

## Filtering Tests

Alrighty, let's see if that did the trick. But this time say:

```terminal
./vendor/bin/phpunit --filter testIsAcceptingVisitorsBasedOnHealthStatus
```

And... Wooah! PHPUnit only ran 2 tests! With the `filter` flag, we're telling
PHPUnit that we only want to run tests that match the *pattern* provided. We can
use the complete or partial name of a test class, method, or anything in between.
This comes in really handy when you have a large test suite and you only need to
run a select number of tests.

Anywho, Hungry dino is not accepting visitors is failing because:

> Failed asserting that false is true.

Looking at `isAcceptingVisitors()` in `Dinosaur` class. We need to return
`$this->health` does not equal `HealthStatus::SICK`.

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
once again. Though, we still need to be proactive and customize the exception for
status labels that we don't know about. We'll do that next.

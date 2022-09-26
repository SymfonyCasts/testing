# Hungry Dino's

All right. So we're using our service and our controller now, but we're getting this
type error

And our GitHub service get Dyna status from labels. Method is the return value must
be of type E uh, enum health status, but no is returned instead. And we can see here
based on the exception, uh, output that Symfony's given us is that when our service
tries to guess the name, uh, of the health status from the label, it's returning
null, obviously instead of actually giving us healthy or sick, if we look at our
issues, I think it's because Dennis finished his daily exercise routine and his
status is hungry. We know we have the status, sick, I status healthy labels accounted
for, but I don't think we have hungry in there.


In `HealthStatus`, add a new case for `HUNGRY` that returns `Hungry`. Back to the
browser to see if the app is working. And... Ya! No more errors...

But, wait... It says that `Dennis` is *not* accepting visitors. He isn't sick,
just hungry. GenLab said only sick dino's should not be on exhibit. Beside, who
doesn't want to see what happens to the goat?

Back in `DinosaurTest`, let's see here - we need add a test to ensure hungry dino's
can have visitors. Hmm... I think we can modify our existing
`testIsNotAcceptingVisitorsIfSick()` test, to *also* test hungry dino's *can* have
visitors. Add a new data provider called `healthStatusProvider()` that returns a
`\Generator`. For our first dataset, `yield Sick dino is not accepting visitors`
and return an array with `HealthStatus::SICK`, and we will assert `false` is the
same as `isAcceptingVisitors()`.

Next we'll `yield Hungry dino is accepting visitors` and for the array,
`HealthStatus::HUNGRY` and we will expect `true`. Now we can add the `@dataProvider`
annotation to tell our test to use the `healthStatusProvider()`. We should also
rename the test method name to... `testIsAcceptingVisitorsBasedOnHealthStatus` and
add a `HealthStatus $healthStatus` argument and finally `bool $expectedStatus`.
Inside we'll set the health using `$healthStatus` then replace `assertFalse` with
`assertSame($expectedStatus)` is identical to `$dino->isAcceptingVisitors()`.

Alrighty, to the terminal we shall go. This time when we run out tests, add a
`filter` flag along with the test name:

```terminal
./vendor/bin/phpunit --filter testIsAcceptingVisitorsBasedOnHealthStatus
```

And... Wooah! PHPUnit only ran 2 tests! With the `filter` flag, we're telling
PHPUnit that we only want to run tests that match the pattern we provider. We can
use the complete or partial name of a test class, method, or anything in between.
That comes in really handy when you have a large test suite and you only need to
run a select number of tests. Anywho, our Hungry dino is not accepting visitors
test is failing because:

> Failed asserting that false is true.

Take a look at the `isAcceptingVisitors()` method in the `Dinosaur` class. Because,
we've added `HUNGRY` as a `HealthStatus`, we need to return `$this->health` does
not equal `HealthStatus::SICK`.

Let's see if that did the trick. Run:

```terminal
./vendor/bin/phpunit --filter "Hungry dino is accepting visitors"
```

And... boom! Our hungry dino test is now passing! As you see, we can also use
data provider keys with the `filter` flag. But let's make sure that all of our tests
are passing by running:

```terminal
./vendor/bin/phpunit
```

And... Yes! We're all green!

COMINIG UP NEXT WE'LLL DO WHAT???

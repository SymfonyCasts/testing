# Create a GitHub Service Test

Now that we can see if a `Dinosaur` is accepting visitors on our dashboard, we
need to keep the dashboard updated in real-time by using the health status labels
that GenLab has applied to several dino issues on GitHub. To do that we'll create
a service that will grab those labels using GitHub's API.

## Test for our Service First

To test our new service... which doesn't exist yet, inside of `tests/Unit/` create
a new `Service/` directory and then a new class: `GithubServiceTest`... which will
extend `TestCase`:

[[[ code('c21565ec8c') ]]]

I'm creating this in a `Service/` sub-directory because I'm
planning to put the class in the `src/Service/` directory. Add method
`testGetHealthReportReturnsCorrectHealthStatusForDino` and inside,
`$service = new GithubService()`. Yup, that doesn't exist yet either...

Our service will return a `HealthStatus` enum that's created from the health status
label on GitHub, so we'll `assertSame()` that `$expectedStatus` is identical to
`$service->getHealthReport()` and then pass `$dinoName`. Yup, we'll be using a
data provider for this test... where we accept the *name* of the dino to check
for their expected health status.

Let's go create that: `public function dinoNameProvider()` that returns a
`\Generator`. Our first dataset for the provider will have the key `Sick Dino`,
which returns an array with `HealthStatus::SICK` and `Daisy` for the dino's name...
because when we checked GitHub a minute ago, Daisy was sick!

Next up is a  `Healthy Dino` with `HealthStatus::HEALTHY` who happens to be the
one and only `Maverick`. Up on the test method, add a `@dataProvider` annotation
so the test uses `dinoNameProvider`... and then add `HealthStatus $expectedStatus`
and `string $dinoName` arguments.

[[[ code('71aa11b21e') ]]]

Let's do this! Find your terminal and run:

```terminal
./vendor/bin/phpunit
```

And... Yup! Just as we expected, we have two errors because:

> The GithubService class cannot be found

## Create the service that will call GitHub

To fix that, find a teammate and ask them nicely to create this class for you!
TDD - team-driven-development!

I'm kidding: we got this! Inside of `src/`, create a new `Service/` directory. Then
we'll need the new class: `GithubService` and inside, add a method: `getHealthReport()`
which takes a `string $dinosaurName` and gives back a `HealthStatus` object.

[[[ code('0db5295d46') ]]]

Here's the plan: we'll call GitHub's API to get the list of issues for the `dino-park`
repository. Then we'll filter those issues to pick the one that matches `$dinosaurName`.
Finally, we'll return `HealthStatus::HEALTHY`, unless the issue has a `Status: Sick`
label.

## Add the use statement in our test

Before we dive into *writing* that method, jump back into our test and chop off the
last couple of letters for `GithubService`. With a little PHPStorm Magic... as soon
as I type the letter `i` and hit enter, the use statement is automatically added
to the test. Thank you JetBrains!

[[[ code('f4ec616f0c') ]]]

Let's see how the tests are looking:

```terminal-silent
./vendor/bin/phpunit
```

And... Ha! Instead of two failures, we now only have one...

> Sick Dino failed asserting that the two variables reference the same object.

Coming up next, we'll add some logic to our `GithubService` to make this test pass!

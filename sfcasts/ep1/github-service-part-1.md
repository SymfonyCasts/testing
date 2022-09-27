# Create a GitHub Service Test

Now that we can see if a `Dinosaur` is accepting visitors on our dashboard, we
need to keep the dashboard updated in real time by using the health status labels
that GenLab has applied to several dino issues on GitHub. To do that we'll create 
a service that will grab those labels from the issues using GitHub's API.

## Test for our service first

To test our new service, inside of `tests/Unit/` create a new `Service/` directory 
and then a new class: `GithubServiceTest` which will extend `TestCase`. Add method
`testGetHealthReportReturnsCorrectHealthStatusForDino` and inside, even
though it doesn't exist yet, `$service = new GithubService()`.

Our service will return a `HealthStatus` enum that is created from the health status
label on GitHub, so we'll `assertSame()` that `$expectedStatus` is identical to 
`$service->getHealthReport()`. Yes, we'll be using a data provider for this test...
And because our service will return a *single* `HealthStatus` object, let's tell 
the method which dino we need a health report for by passing in a `$dinoName` to
`getHealthReport()`.

Create a new method: `dinoNameProvider()` that returns a `\Generator`. Our first 
dataset for the provider will have the key `Sick Dino`, which returns an array 
with `HealthStatus::SICK` and `Daisy` for the dino's name. Next up is a 
`Healthy Dino` with `HealthStatus::HEALTHY` who happens to be the one and only
`Maverick`. On the test method, add a `@dataProvider` annotation so the test uses 
the `dinoNameProvider` and we'll also pass in a `HealthStatus $expectedStatus`
argument and a `string` called `$dinoName`.

Move to your terminal and let's check out our tests:

```terminal
./vendor/bin/phpunit
```

And... Yup! Just as I expected, we have two errors because: 

> The GithubService class cannot be found

## Create the service that will call GitHub

To fix those errors, inside of `src/`, create a new `Service/` directory. Then 
we'll need a new class: `GithubService` and inside, add a method: `getHealthReport()`
which takes a `string $dinosaurName` and gives back a `HealthStatus` object.

Hmm... Alrighty, the first thing we will do is call GitHub's API to get the list
of issues for the `dino-park` repository. Then we'll filter those issues to pick the 
one that matches `$dinosaurName`. And last but not least, return 
`GithubStatus::HEALTHY`, unless we find an issue with a `Status: Sick` label that
matches our dino's name.

## Add the use statement in our test

Before we flesh out our service, jump back into our test and chop off the last
couple of letters for `GithubService`. With a little PHPStorm Magic... as soon 
as I type the letter `i` and hit enter, the use statement is automatically added 
to the test. Thank you JetBrains!

Let's see how are tests are looking:

```terminal-silent
./vendor/bin/phpunit
```

And... Ha! Instead of two failures, we now only have one...

> Sick Dino failed asserting that the two variables reference the same object.

Coming up next, we'll add some logic to our `GithubService` and make this test pass!

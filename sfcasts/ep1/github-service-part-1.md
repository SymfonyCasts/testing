# Create a GitHub Service Test

Now that we can see if a `Dinosaur` is accepting visitors based on its
current health. We need to keep our dino's health updated automatically using the
health status labels that GenLab has applied to several issues on GitHub. We'll
create a new service that will grab those labels from the issues using GitHub's API.

## Test for our service first

To test our new service, create a `Service/` directory inside of `Unit/` and then
create a new `GithubServiceTest` which will extend `TestCase`. Add a method
called `testGetHealthReportReturnsCorrectHealthStatusForDino`. Inside, even
though it doesn't exist yet, `$service = new GithubService()`.

Our service will return a `HealthStatus` enum that is created from the health status
label on GitHub, so we'll `assertSame()` that `$expectedStatus` is identical to 
`$service->getHealthReport()`. Yes, we'll be using a data provider for this test...
Because our service will return a single `HealthStatus` object, let's tell the method
which dino we need a health report for by passing in a `$dinoName` argument to
`getHealthReport()`.

Create a `dinoNameProvider()` which returns a `\Generator` and our first dataset
will be a `Sick Dino` with `HealthStatus::SICK` then `Daisy` for the dino's name.
Next up is a `Healthy Dino` with `HealthStatus::HEALTHY` and we'll use `Maverick`
this time. Now add the `@dataProvider` annotation so our test uses the
`dinoNameProvider` and we'll pass in a `HealthStatus $expectedStatus`
argument and a `string` called `$dinoName`.

Move to your terminal and run:

```terminal
./vendor/bin/phpunit
```

And... Yup - we have two errors. The `GithubService` class cannot be found for
both of our tests which is to be expected.

## Create the service that will call GitHub

Let's fix that by creating a new `Service/` directory inside our `src/ and then we'll need a new
`GithubService` class. Inside, we'll add a `getHealthReport()` method which takes
a `string $dinosaurName` and gives back a `HealthStatus`.

Hmm... Alrighty, the first thing we will do is call GitHub's API to get the list
of issues for `dino-park`, then filter those issues to pick the one that matches
`$dinosaurName` and last but not least... return `GithubStatus::HEALTHY` unless
we find GenLab has added a `Status: Sick` label to an issue that matches our dinos
name.

## Add the use statement in our test

Jump back into our test and chop off the last couple of letters for `GithubService`,
and with a little PHPStorm Magic... as soon as I type the letter `i`, we now have the use statement automatically
added to the test. Run our tests again over in the terminal:

```terminal-silent
./vendor/bin/phpunit
```

And... Ha! Instead of two failures, we now only have one...

> Sick Dino failed asserting that the two variables reference the same object.

Coming up next, we'll add some logic to our `GithubService` and make this test pass!

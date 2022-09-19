# Create a GitHub Service Test

Alrighty. So now that a dinosaur object is able to persist the health status and
tell us if the dinosaurs are able to accept visitors, we need to go ahead and get
these, uh, labels for each of our S and the GitHub repo and pull 'em into our app.
Instead up on our objects accordingly, to do that, we're going to create a new GitHub
service that will use Symfony's HTTP client to call up, uh, GitHub's API, fetch those
issues and figure out which1s apply to our dinosaurs.

First thing we're going to do is create a `Service/` directory for a new
`GithubServiceTest`, which extends `TestCase`, and add a test -
`testGetHealthReportReturnsCorrectHealthStatusForDino`. Inside, even though it doesnt
exist yet - `$service = new GithubService()`. Now we want to `assertSame()` that
our `$expectedStatus` is identical to `$service->getHealthReport()`. We also need
to pass in `$dinoName` to this method.

As you may have already guessed, create a `dinoNameProvider()` which
returns a `\Generator`. Our first dataset will be a `Sick Dino` with `HealthStatus::SICK`,
and `Daisy` for the dino name. Next up is a `Healthy Dino` with `HealthStatus::HEALTHY`
and we'll use `Maverick` this time. Now lets add the `@dataProvider` so our test
uses the `dinoNameProvider`. We'll pass in a `HealthStatus $expectedStatus`
argument and a `string` called `$dinoName`.

Move to your terminal and run:

```terminal
./vendor/bin/phpunit
```

And... Yup - we have two errors. The GithubService class cannot be found for
both of our tests which is to be expected. Let's go fix that...

Back in our code, create a new `src/Service/` directory and then we'll need a new
`GithubService` class. Inside, we'll add a `getHealthReport()` method which takes
a `string $dinosaurName` and gives back a `HealthStatus`.

Hmm... Alrighty, first thing we need to do is Call GitHubs API to get the list
of issues for `dino-park`, then filter those issues to pick the one that matches
`$dinosaurName` and last but not least... return `GithubStatus::HEALTHY` unless
we find GenLab has added a `Status: Sick` flag to an issue that matches our dinos
name.

Jump back into our test and chop off the last couple of letters for `GithubService`,
and with a little PHPStorm Magic... we now have the use statement automatically
added to the test. Move to the terminal and run our tests again:


```terminal-silent
./vendor/bin/phpunit
```

And... Ha! Instead of two failures, we now only have one...

> Sick Dino failed asserting that the two variables reference the same object.

Coming up next, we'll add some logic to our `GithubService` and make this test pass!

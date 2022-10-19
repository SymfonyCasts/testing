# GitHub Service: Implementation

Now that we have an idea of what we need the `GithubService` to do, let's add
the logic inside that will fetch the issues from the `dino-park` repository
using GitHub's API.

## Add the client and make a request

To *make* HTTP requests, at your terminal, install Symfony's HTTP Client with:

```terminal
composer require symfony/http-client
```

Inside of `GithubService`, instantiate an HTTP client with
`$client = HttpClient::create()`. To make a request, call `$client->request()`.
This needs 2 things. 1st: what HTTP method to use, like `GET` or `POST`. In this
case, it should be `GET`. 2nd: the URL, which I'll paste in. This will fetch all 
of the "issues" from the `dino-park` repository via GitHub's API.

[[[ code('0478d205da') ]]]

## Parse the HTTP Response

Ok, now what? Looking back at the `dino-park` repo, GitHub will return a JSON 
response that contains the issues we see here. Each issue has a title with a 
dino's name and if the issue has a label attached to it, we'll get that back too.
So, set `$client->request()` to a new `$response` variable. Then, below, `foreach()`
over `$response->toArray()` as an `$issue`. The cool thing about using Symfony's
HTTP Client is that we don't have to bother transforming the JSON from GitHub 
into an array - `toArray()` does that heavy lifting for us. Inside this loop,
check if the issue title contains the `$dinosaurName`. So
`if (str_contains($issue['title'], $dinosaurName))` then we'll `// Do Something`
with that issue.

[[[ code('959cf1b0b7') ]]]

At this point, we've found the issue for our dinosaur. Woo! Now we need to
loop over each label to see if we can find the health status. To help, I'll
paste in a private method: you can copy this from the code block on this page.

[[[ code('fe4184164d') ]]]

This takes an array of labels... and when it finds one that starts with `Status:`,
it returns the correct `HealthStatus` enum based on that label.

Now instead of `// Do Something`, say
`$health = $this->getDinoStatusFromLabels()` and pass the labels with `$issue['labels']`.

[[[ code('60e1f49c5b') ]]]

And now we can return `$health`. But... what if an issue doesn't *have* a health
status label? Hmm... at the beginning of this method, set the default `$health`
to `HealthStatus::HEALTHY` - because GenLab would *never* forget to put a
`Sick` label on a dino that isn't feeling well.

[[[ code('58effa5196') ]]]

Hmm... Welp, I think we did it! Let's run our tests to be sure.

```terminal
./vendor/bin/phpunit
```

And... Wow! We have 8 tests, 11 assertions, and they're all passing! Shweeet!

## Log all of our requests

One last challenge! To help debugging, I want to log a message each time we make
a request to the GitHub API.

No problem! We just need to get the logger service. Add a constructor with
`private LoggerInterface $logger` to add an argument and property all at once.
Right after we call the `request()` method, add `$this->logger->info()` and pass
`Request Dino Issues` for the message and also an array with extra context. How 
about a `dino` key set to `$dinosaurName` and `responseStatus` to
`$response->getStatusCode()`.

[[[ code('70f55f1a16') ]]]

Cool! That *shouldn't* have broken anything in our class, but let's run the tests
to be sure:

```terminal-silent
./vendor/bin/phpunit
```

And... Ouch! We *did* break something!

> Too few arguments passed to the constructor in GithubService. 0 passed 1 expected.

Of course! When we added the `LoggerInterface` argument to `GithubService`, we 
never updated our test to pass that in. I'll show you how we can do that next 
using one of PHPUnit's super abilities: mocking.

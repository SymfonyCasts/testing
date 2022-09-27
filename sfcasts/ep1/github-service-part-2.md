# GitHub Service: Implementation

Now that we have an idea of what we need the `GithubService` to do, let's add
the logic inside that we'll use to fetch the issues from the `dino-park` repository
using GitHub's API.

## Add the client and make a request

In the terminal, add Symfony's HTTP Client to our app with:

```terminal
composer require symfony/http-client
```

Which we'll use to call the API. Inside of `GithubService`, instantiate an HTTP 
client with `$client = HttpClient::create()`. To make a request, the
`$client->request()` method. We need to tell the client 2 things, 1st: what HTTP
method to use, like `GET` or `POST`. In this case, the `method` will be `GET`. 
2nd: the `url` that we'll call is 
`https://api.github.com/repos/SymfonyCasts/dino-park/issues` will get all the 
"issues" from the `dino-park` repository that our service needs.

## Parse the HTTP Response

After we call the `request()` method, what happens next? Looking back at the 
`dino-park` repo, GitHub will return a JSON response that contains the issues we
see here. Each issue has a title with a dino's name and if the issue has a label 
attached to it, we'll get that back too. So, in the service, let's set the 
`HttpResponse` object returned from by the `request()` method on to `$response`.

To filter the issues from the response, add a `foreach()` that loops each
`$response->toArray()` as an `$issue`. The cool thing about using Symfony's 
HTTP Client... we don't have to bother transforming the JSON from GitHub into an 
array - `toArray()` does that heavy lifting for us. Inside this loop, we need to 
check if the issue title contains the `$dinosaurName`. So 
`if(str_contains($issue['title'], $dinosaurName))` then we'll `// Do Something`
with that issue.

I'm going to copy in a private method that will take an array of labels... If 
one of the labels is a health status label, the method will return the correct
`HealthStatus` enum based on that label. Now instead of `// Do Something`, we can
add `$health = $this->getDinoStatusFromLabels()`. And to get the labels for the
issue, we'll pass in `$issue['labels']`. 

And now we can return `$health`. But... what if an issue doesn't have a health 
status label? Hmm... at the beginning of this method, set the default `$health` 
to `HealthStatus::HEALTHY` - because GenLab would *never* forget to put a
`Sick` label on a dino that isn't feeling well.

Let's check out our tests:

```terminal
./vendor/bin/phpunit
```

And... Wow! We have 8 tests, 11 assertions and they're all passing! Shweeet!

## Log all of our requests

The last thing that we need to do... Add a `__constructor()` to `GithubService`
and create a `private LoggerInterface $logger` property for the service. Right
after we call the `request()` method, lets add a log entry for each request by calling
`$this->logger->info()`. We'll use `Request Dino Issues` for the log message and 
to add some context to the log entry, create an array with `dino` as the key,
whose value is `$dinosaurName`. It would probably be a good idea to know *if* the 
API request was successful or not, so add a `responseStatus` key which tell's us
the `$response->getStatusCode()`. 

In the terminal, let's run the tests:

```terminal-silent
./vendor/bin/phpunit
```

And... Ouch! We broke our tests...

> Too few arguments passed to the constructor in GithubService. 0 passed 1 expected.

When we added the `LoggerInterface` to the `GithubService`, we never changed our
test to create a logger instance for each test. I'll show you how we can
do that next using one of PHPUnit's super abilities next...

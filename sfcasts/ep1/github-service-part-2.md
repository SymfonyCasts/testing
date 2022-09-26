# GitHub Service: Implementation

Now that we have an idea of what we need to do in our new service. Let's add
some logic inside the service to fetch the issues in the `dino-park` repository using
GitHub's API.

Move into the terminal and run

```terminal
composer require symfony/http-client
```

to add Symfony's Http Client to our app which we'll use to call the API.
Inside of `GithubService`, instantiate the client with `$client = HttpClient::create()`.

Now we we'll make an actual HTTP request by calling `$client->request()`, which accepts
two arguments, the first being `method`. This tells the client which HTTP Method,
like `GET` or `POST`, that we want to use. In this case we're performing a `GET`
request. Now we'll tell the client what `url` we are going to call to retrieve a
list of issues from the `dino-park` repository by passing in
`https://api.github.com/repos/SymfonyCasts/dino-park/issues`.

After we call the `request()` method, what happens next?

Looking back at the `dino-park` repo, GitHub will return a JSON response that
contains these 4 issues. Each issue has a title with a dino's name and if the issue
has a label attached to it, we'll get that back too. So, in our service, let's
set the `HttpResponse` returned from the `request()` method on `$response`.

To filter the issues in our response - Symfony's HTTP Client will automatically turn
the JSON we get back from GitHub into an array when we call the `toArray()` method.

So now we can add a `foreach()` loop, and for each `$response->toArray()` as
`$issue` will check that `if()` `str_contains($issue['title'])` of our `$dinosaurName`,
we'll `// Do Something`.

I'm going to copy in a private method that will take an array of labels from our
issue, and if one of the labels is a health status label, it will return the correct
`HealthStatus` enum.

Now if the title contains the dino's name, then `$health` will equal
`$this->getDinoStatusFromLabels()` and we'll pass in any labels for our issue has
with `$issue['labels']`. For our `return`, we will return `$health`. But what if
an issue doesn't have a health status label? No worries, back up top, we'll set
`$health = HealthStatus::HEALTHY` because GenLab would *never* forget to put a
`Sick` label on a dino that wasn't feel well.

Move back into the terminal and run our tests:

```terminal
./vendor/bin/phpunit
```

And... We 8 tests and 11 assertions that are all passing! Shweeet!

We have one last thing we need to do... Add a constructor to our `GithubService`
and create a `private LoggerInterface $logger` property for the service. Right
after we call the `request()` method, lets add a log entry for each request by calling
`$this->logger->info()`. For the message, `Request Dino Issues` and then pass an
array that will have the `dino`'s name as `$dinosaurName` then the `responseStatus`
as `$response->getStatusCode()`. This will come in handy if we ever need
to look back and see if an API request was successful or not.

Run the tests again:

```terminal-silent
./vendor/bin/phpunit
```

And... Ouch! We broke our tests...

> Too few arguments passed to the constructor in GithubService. 0 passed 1 expected.

When we added the `LoggerInterface` to the `GithubService`, we never changed our
test to create a logger instance for each test. We'll do that next.

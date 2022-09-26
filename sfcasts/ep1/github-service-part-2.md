# GitHub Service: Implementation

Now that we have an idea of what we need to do in our `GithubService`. Let's add
some logic inside the service to fetch the issues from the `dino-park` repository
using GitHub's API.

Move into the terminal and run

```terminal
composer require symfony/http-client
```

to add Symfony's Http Client to our app which we'll use to call the API.
Inside of `GithubService`, instantiate the client with `$client = HttpClient::create()`.
Then to make an actual HTTP request, call `$client->request()`. This method requires
two arguments, the first being `method`, which tells the client what HTTP Method,
like `GET` or `POST`, that we want to use. So pass in `GET`. Then for the second
argument, we'll tell the client what `url` we are going to call to retrieve a
list of issues from the `dino-park` repository by passing in
`https://api.github.com/repos/SymfonyCasts/dino-park/issues`.

After we call the `request()` method, what happens next?

Looking back at the `dino-park` repo, GitHub will return a JSON response that
contains these 4 issues. Each issue has a title with a dino's name and if the issue
has a label attached to it, we'll get that back too. So, in our service, let's
set the `HttpResponse` returned from the `request()` method on `$response`.

To filter the issues in our response, add a `foreach()` loop, and for each
`$response->toArray()` as `$issue` will check that `if()` `str_contains($issue['title'])`
of our `$dinosaurName`, we'll `// Do Something` with that issue. The cool thing
about using Symfony's HTTP Client we don't have to bother transforming the JSON
from GitHub into an array - the `toArray()` method does that heavy lifting
for us.

I'm going to copy in a private method that will take an array of labels and if 
one of the labels is a health status label, it will return the correct`HealthStatus` 
enum.

Now if the title contains the dino's name, then `$health` will equal
`$this->getDinoStatusFromLabels()`. To get the labels applied to the issue, pass
in `$issue['labels']`. For our `return`, use `$health`. But what if an issue 
doesn't have a health status label? No worries, back up top, we'll set
`$health = HealthStatus::HEALTHY` because GenLab would *never* forget to put a
`Sick` label on a dino that wasn't feeling well.

From the terminal and run the tests:

```terminal
./vendor/bin/phpunit
```

And... We 8 tests and 11 assertions that are all passing! Shweeet!

We have one last thing we need to do... Add a constructor to our `GithubService`
and create a `private LoggerInterface $logger` property for the service. Right
after we call the `request()` method, lets add a log entry for each request by calling
`$this->logger->info()`. We'll use `Request Dino Issues` for the log message and 
to add some context to the log entry, create an array with `dino`'s name as the key,
whose value is `$dinosaurName`. It would probably be a good idea to know if the 
API request was successful or not, so add another key `responseStatus` which is a
`$response->getStatusCode()`. This will come in handy if we ever need to look
back and see if an API request was successful or not.

Run the tests again:

```terminal-silent
./vendor/bin/phpunit
```

And... Ouch! We broke our tests...

> Too few arguments passed to the constructor in GithubService. 0 passed 1 expected.

When we added the `LoggerInterface` to the `GithubService`, we never changed our
test to create a logger instance for each test. I'll show you how we can
do that next using one of PHPUnit's strong points - Mocks!

# Setup and Tearing It Down

Let's continue refactoring our test. In the test method, we create a `MockResponse`,
`MockHttpClient`, and instantiate `GitHubService` with a mock `LoggerInterface`.
We're doing the same thing in this test above. Didn't Ryan say to DRY out our code
in another tutorial? Fine... I suppose we'll listen to him.

Start by adding three `private` properties to our class: a
`LoggerInterface $mockLogger`, followed by `MockHttpClient $mockHttpClient` and
finally `MockResponse $mockresponse`:

[[[ code('ef02047cf4') ]]]

At the bottom of the test, create a `private function createGithubService()` 
that requires `array $responseData` then returns `GithubService`. Inside, 
say `$this->mockResponse = new MockResponse()` that `json_encode()`'s the `$responseData`:

[[[ code('1562c787cd') ]]]

Since we'll be creating the `MockResponse` *after* we instantiate the `MockHttpClient`,
which you'll see in a second, we need to pass our response to the client without 
using the client's constructor. To do that, we can say 
`$this->mockHttpClient->setResponseFactory($this->mockResponse)`. Finally return
a `new GithubService()` with `$this->mockHttpClient` and `$this->mockLogger`.

[[[ code('e17536efdd') ]]]

We *could* use a constructor to instantiate our mocks and set them on those properties.
But PHPUnit will only instantiate our test class *once*, no matter how many test
methods it has. And we want to make sure we have fresh mock objects for *each* 
test run. How can we do that? At the top, add `protected function setUp()`. Inside,
say `$this->mockLogger = $this->createMock(LoggerInterface::class)` then
`$this->mockHttpClient = new MockHttpClient()`.

[[[ code('0a9d5b26d9') ]]]

Down in the test method, cut the response array, then say 
`$service = $this->createGithubService()` and paste the array.

[[[ code('c487d18a05') ]]]

Let's see how our tests are doing in the terminal...

```terminal
./vendor/bin/phpunit
```

And... Ya! Everything is looking good!

The idea is pretty simple: if your test class has a method called `setUp()`, PHPUnit
will call it before *each* test method, which gives us fresh mocks at the start
of every test. Need to do something *after* each test? Same thing: create a method
called `tearDown()`. This isn't as common... but you might do it if you want to
clean up some filesystem changes that were made during the test. In our case, there's
no need.

In addition to `setUp()` and `tearDown()`, PHPUnit also has a few other methods, like
`setUpBeforeClass()` and `tearDownAfterClass()`. These are called once per *class*,
and we'll get more into those as they become relevant in future tutorials. And if 
you were wondering, these methods are called "Fixture Methods" because they help
setup any "fixtures" to get your environment into a known state for your test.

Anyhow, let's get back to refactoring. For the first test in this class, cut out
the response array, select all of this "dead code", add
`$service = $this->createGithubService()` then paste in the array. We can remove
the `$service` variable below:

[[[ code('130ec24080') ]]]

But now we need to figure out how to keep these expectations that we were using on 
the old `$mockHttpClient`. Being able to test that we only call GitHub *once* with 
the `GET` HTTP Method and that we're using the right URL, is pretty valuable.

Fortunately, those mock classes have special code *just* for this. Below, 
`assertSame()` that `1` is identical to `$this->mockHttpClient->getRequestCount()`
then `assertSame()` that `GET` is identical to `$this->mockResponse->getRequestMethod()`.
Finally, copy and paste the URL into `assertSame()` and call `getRequestUrl()` on
`mockResponse`. Remove the old `$mockHttpClient`... and the `use` statements 
that we're no longer using up top.

[[[ code('f6e3e73d18') ]]]

Alrighty, time to check the fences...

```terminal-silent
./vendor/bin/phpunit
```

And... Wow! Everything is still green!

Welp, there you have it... We've helped Bob improve Dinotopia by adding a few
small features to the app. But more importantly, we're able to test that those
features are working as we intended. Is there more work to be done? Absolutely!
We're going to take our app to the next level by adding a persistence layer to
store dinos in the database and learn how to write tests for that too. These
tests, where you use *real* database connections or make *real* API calls, instead
of mocking, are sometimes called integration tests. That's the topic of the next
tutorial in this series.

I hope you enjoyed your time here at the park - and thanks for keeping your arms
and legs inside the vehicle at all times. If you have any questions, suggestions, 
or want to ride with Big Eaty in the Jeep - just leave us a comment. Alright,
see you in the next episode!

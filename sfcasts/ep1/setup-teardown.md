# Setup and Tearing It Down

Let's continue on with refactoring our test. In the test method, we create a `MockResponse`,
`MockHttpClient`, and instantiate `GitHubService` with a mock `LoggerInterface`.
We're doing the same thing in this test above. Didn't Ryan say to DRY out our
code in another tutorial? Fine... I suppose we'll listen to him.

We'll start by adding three `private` properties to our class. First, a
`LoggerInterface $mockLogger`, followed by `MockHttpClient $mockHttpClient` and finally
`MockResponse $mockresponse`. At the bottom of the test, create a 
`private function createGithubService()` that requires `array $responseData` then
returns `GithubService`. Inside, say
`$this->mockResponse = new MockResponse()` that `json_encode()`'s the `$responseData`.

Since we'll be creating the `MockResponse` *after* we instantiate the `MockHttpClient`,
which you'll see in a second, we need to pass our response to the client without 
using the client's constructor. To do that say 
`$this->mockHttpClient->setResponseFactory($this->mockResponse)` and finally 
return a `new GithubService()` with `$this->mockHttpClient` and `$this->mockLogger`.

We *could* use a constructor to instantiate our mocks and set them on their properties,
but we want to make sure that we have a fresh mock every time a test runs. How?
At the top add `protected function setUp()` and inside say 
`$this->mockLogger = $this->createMock(LoggerInterface::class)`. Next, create a 
`MockHttpClient()` on `$this->mockHttpClient`.

Down in the test method, cut the response array, then we'll say 
`$service = $this->createGithubService()` and paste the array.

Let's see how our tests are doing in the terminal...

```terminal
./vendor/bin/phpunit
```

And... Ya! Everything is looking good!

But... Why *didn't* we just use the constructor to initialize the properties? Welp,
we want to make sure we aren't re-using our mocks in each test. PHPUnit calls the
`setUp()` *before* it calls each test method which gives us fresh mocks at the start
of each test. But what goes up must come down, which is what the `tearDown()` method 
does at *end* of each test case. We don't have a need for that here since we are 
just creating relatively "simple" objects. In the next tutorial when we need to
say, close database connections, `tearDown()` will do that spectacularly.

In addition to `setUp()` and `tearDown()`, PHPUnit also has methods, like
`setUpBeforeClass()` and `tearDownAfterClass()` that it invokes at different stages
in the life of a test. We'll get more into those as they become relevant in future
tutorials. And if you were wondering, all of these methods are actually called 
"Fixture Methods" that help control the known state of your test.

Anyhow, let's get back to refactoring. For the first test in this class, cut out
the response array, select all of this "dead code", and add
`$service = $this->createGithubService()` then paste in the array. We can remove
the `$service` variable below. But, now we need to figure out how to keep these
expectations that we were using on the old `$mockHttpClient`. Being able to test
that we only call GitHub *once* with the `GET` HTTP Method and that we're using the
right URL, is pretty valuable.

So below, `assertSame()` that `1` identical to `$this->mockHttpClient->getRequestCount()`
then we can `assertSame()` that `GET` is identical to `$this->mockResponse->getRequestMethod()`.
Finally, we can copy and paste the URL into `assertSame()` and call `getRequestUrl()` on
the `mockResponse`. Remove the old `$mockHttpClient`, and the `use` statements 
that we're no longer using up top.

Alrighty, time to check the fences...

```terminal-silent
./vendor/bin/phpunit
```

And... Wow! Everything is still green!

Welp, there you have it... Coming soon, we'll store our dino's in the database and write
a few tests to make sure they stay there. See you soon!


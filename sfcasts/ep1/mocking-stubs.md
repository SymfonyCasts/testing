# Mocking: Stubs

Let's take a quick look back at `GithubService` to see *exactly* what it's doing.
First, the constructor requires an `HttpClientInterface` object that we use to
call GitHub. In return, we get back a `ResponseInterface` that has an array of
issue's for the `dino-park` repository. Next we call the `toArray()` method on
the response, and iterate over each issue to see if the title contains the 
`$dinosaurName`, so we can get its status label.

[[[ code('8232c3dd4b') ]]]

To get our tests to pass, we need to *teach* our fake `httpClient` that when we
call the `request()` method, it should give back a `ResponseInterface` object
containing data that *we* control. So... let's do that.

## Training the Mock on what to Return

Right after `$mockHttpClient`, say `$mockResponse = $this->createMock()` using
`ResponseInterface::class` for the class name. Below on `$mockHttpClient`, call,
`->method('request')` which `willReturn($mockResponse)`. This tells our mock client
that hey, anytime we call the `request()` method on our mock, you need to return
*this* `$mockResponse`.

[[[ code('db50103536') ]]]

We *could* run our tests now, but they would fail. We taught our mock client
*what* it should return when we call the `request()` method. *But*, *now* we need
to teach our `$mockResponse` what *it* needs to do when we call the `toArray()`
method. So right above, lets teach the `$mockResponse` that when we call,
`method('toArray')` and it `willReturn()` an array of issues. Because that's what 
GitHub returns when we call the API.

[[[ code('c9bc8019ab') ]]]

For each issue, GitHub gives us the issue's "title", and among other things,
an array of "labels". So let's mimic GitHub and make this array include one
issue that has `'title' => 'Daisy'`.

And, for the test, we'll pretend she sprained her ankle so add a `labels` key set
to an array, that includes `'name' => 'Status: Sick'`.

Let's also create a healthy dino so we can assert that our parsing checks *that*
correctly too. Copy this issue and paste it below. Change `Daisy` to `Maverick`
and set his label to `Status: Healthy`.

[[[ code('6ae7eb1f96') ]]]

Perfect! Our assertions are already expecting `Daisy` to be sick and `Maverick`
to be healthy. So, if our tests pass, it means that all of our label-parsing
logic *is* correct.

Fingers crossed, let's try it:

```terminal
./vendor/bin/phpunit
```

And... Awesome! They *are* passing! And the best part about it, we're no longer 
calling GitHub's API when we run our tests! Imagine the panic we would cause if 
we had to lock down the park because our tests failed due to the api being 
offline... or just someone changing the labels up on GitHub, Ya... I don't want 
that headache either...

## Stubs? Mocks?

Remember when we were talking about the different names for mocks? Welp, both
`mockResponse` and `mockHttpClient` are now officially called stubs... That's a 
fancy way of saying fake objects where we *optionally* take control of the values it
returns. That's exactly what we are doing with the `willReturn()` method. Again,
the terminology isn't too important, but there you go. These are stubs. And yes,
every time I teach this, I need to look up these terms to remember exactly what 
they mean.

Up next, we're going to turn our *stubs* into full-blown mock objects by also 
testing the data passed *into* the mock.

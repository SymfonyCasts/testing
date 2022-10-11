# Mocking: Stubs

Let's take a quick look back at our `GithubService` and see *exactly* what it's
doing. First, the constructor requires an `HttpClientInterface` object that we use
to call GitHub. In return, we get back a `ResponseInterface` that has an array
of the issue's for the `dino-park` repository. Next we call the `toArray()` method
on the response object, and iterate over each issue to see if the title contains
the `$dinosaurName` so we can get it's status label.

To get our tests to pass, we need to *teach* our fake `httpClient` that when we
call the `request()` method, it gives back a `ResponseInterface` object
containing data that *we* control. So let's do that...

Right after the `$mockHttpClient`, say `$mockResponse = $this->createMock()` using
`ResponseInterface::class` for the class name. Below on `$mockHttpClient`, call,
`method('request')` and `willReturn($mockResponse)`. This tells our mock client
that hey, anytime we call the `request()` method on our mock, you need to return
*this* `$mockResponse`.

!!!!!!!!! This chapter is short enough, we probably should run the tests !!!!!!!!!
We *could* run our tests now, but they would fail. We taught our mock client
*what* it should return when we call the `request()` method. *But*, we need
to teach our `$mockResponse` what *it* needs to do when we call the `toArray()`
method. So right above, lets teach the `$mockResponse` that when we call the,
`method('toArray')`, it `willReturn()` an array of issues. As that's what GitHub
returns when we call the API.

For each issue, GitHub gives us the issue's "title", and among other things,
an array of "labels" for that issue. So let's mimic GitHub and make this array
include one issue that has the, `'title' => 'Daisy'`. Also give this issue some
`labels` set to an array that includes one: `'name' => 'Status: Sick'`

Foreach sick dino, there *has* to be a healthy one... copy this issue and paste
it below. Change `Daisy` to `Maverick`. And because he has a test flight at noon,
is status label is `Status: Healthy`.

Let's see how are tests are doing:

```terminal
./vendor/bin/phpunit
```

And... Awesome! All of our tests are passing! And the best part about it, we're
no longer calling GitHub's API when we run our tests! Imagine the panic we would
cause if we had to lock down the park because our tests failed due to an api being
offline... Ya... I don't want that head either...
!!!!DIFF!!!!
Imagine what GitHub would
say if they knew we're calling their API a thousand times a day just to run our
tests...

Remember when we were talking about all of the names for mocks? Welp, both the
`mockResponse` and `mockHttpClient` are called stubs... Which is a fancy way of
saying fake objects that we optionally take control of the values it returns. Thats
exactly what we are doing with the `willReturn()` method. Again, the terminology isn't
too important, but there you go. These are stubs. And yes, every time I teach this,
I need to look up these terms to remember exactly what they mean.

Up next, we're going to turn our stubs into full blown Mock Objects, by testing
the data that is passed *into* the mocks as well.

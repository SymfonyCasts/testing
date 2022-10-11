# Mocking: Stubs

Let's take a quick look back at our `GithubService` and see exactly what we're
doing in here. First, we're passing in an `HttpClientInterface` into the service,
then we make a HTTP Request, and we ultimately expect an `HttpResponseInterface`
object with our GitHub data. We transform that data into an array, iterating
over each item within that array to get the health status of a specific dino using
its name.

To get our tests to pass, we need to *teach* our fake `HttpClient` that when
we call the `request()` method, it should return a `Response` object containing
data that we control. So let's do that...

Right after the `$mockHttpClient`, add `$mockResponse = $this->createMock()` using
`ResponseInterface::class` for the name. Below on `$mockHttpClient`, call 
`method('request')` and `willReturn($mockResponse)`. This tells our mock client
that hey, anytime we call the request method on our mock, you need to return *this*
mock response.

We *could* our tests now, but they would fail. We taught our mock http client
*what* it should return when we call the `request()` method. *But*, we need
to teach our `$mockResponse` what *it* needs to do when we call the `toArray()`
method. So right above, lets teach the `$mockResponse` that when we call the
`method('toArray')`, it `willReturn()` an `[]` of issues. As that's what GitHub
returns when we call the API.

For each issue, GitHub gives us, among other things, the issue's "title" along with
an array of "labels" for that issue. So let's mimic GitHub and add an issue `[]`
that has the `'title' => 'Daisy'`. Then for the `'labels' => []`, add a label `[]`
with `name => Status: Sick`.

To be realistic, copy this issue and paste it below. Change `Daisy` to `Maverick`.
And because he has a test flight at noon, he has the `Status: Healthy` label.

Let's see how are tests are doing:

```terminal
./vendor/bin/phpunit
```

And... Awesome! All of our tests are passing! And the best part about it, we're
no longer calling GitHub's API when we run our tests! Imagine what GitHub would say
if they knew we're calling their API a thousand times a day just to run our tests...

Remember when we were talking about all of the names for mocks? Welp, both the
`mockResponse` && `mockHttpClient` are called stubs... Which is a fancy way of saying
fake objects we you optionally take control of the values it returns. Thats exactly
what we are doing with the `willReturn()` method. Again, the terminology isn't
too important, but there you go. These are stubs. And yes, every time I teach this,
I need to look up these terms to remember exactly what they mean.

Coming up next, we're going to turn our stubs into full blown Mock Objects,
which is basically the same thing as we are doing here *except* we're also going
to control the data that we are passing *into* the mocks instead of just the data
they return.

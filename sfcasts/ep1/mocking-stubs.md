# Mocking: Stubs

Let's take a quick look back at our `GithubService` and see exactly what we're
doing in here. First, we're passing in an `HttpClientInterface` into the service,
then we make an HTTP Request, and we ultimately expect an `HttpResponseInterface`
object with our GitHub data. We transform that data into an array, iterating
over each item within that array to get the health status of a specific dino using
its name.

To get our tests to pass, we need to *teach* our fake `HttpClient` that that when
we call the `request()` method, it should return a `Response` object containing
data that we control. So let's do that...

Under the `$mockHttpClient`, `$mockResponse` equals `$this-createMock()` for a
`ResponseInterface::class`. Now we'll configure the `$mockHttpClient` that when
we `method()` `request`, it `willReturn()` `$mockResponse`.

If we ran our tests now, the tests would *still* fail. We taught our mock http client
*what* it should return to use when we call the `request()` method. *But*, we need
to teach our `$mockResponse` what *it* needs to do when we call the `toArray()` method.

So right above, lets teach the `$mockResponse`, that when we call `method()` `toArray`,
it `willReturn()` an `[]`.

So what exactly is this array supposed to return? Well, we want
it to return a list of issues, and because we're only concerned with the title of
our issues when we're fetching them, we'll set the `title` key on the first issue
with `Daisy` as the value. We also need to get the array of `lables` for this issue.
And in that array, we will return a label with the `name` as `Status: Sick` to match
the label on GitHub. Copy this "issue" and paste it below. Use `Maverick` as the
`name` and because he has a test flight at noon, his label is `Status: Healthy`.

Now lets see what are tests are doing now:

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

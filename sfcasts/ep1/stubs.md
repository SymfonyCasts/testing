All right. Let's take a quick look back at our GI hub service real quick and see
exactly what we're doing in here. First, we're using our HT HTTP client interface and
we're calling the request method, which ultimately gives us a response interface
object. We're taking that response object and we're turning the, uh, response data
that's J on from GitHub. And we're transforming that into an array and then iterating
over each item within that array to set the health status, uh, or to get the health,
the, to get the health of the Dino from our, uh, GI hub labels. Let's go back to our
test because now that now that we know what we're doing, we need to teach our fake
HTTP client here, that when we call the request method on it, it should return a
response object, continuing data that we control. So let's go ahead and do that.
Let's create first a mock response

<affirmative>

And this will create a mock that is a response interface. All right. And then now
that we've created that we can configure our mock HTTP client so that when we call
the method request, it's going to return the mock response. At this point, if we ran
our test again, the tests are going to fail. Cause we're still having the same
problem as before, as we don't have, we're not actually providing a response to our
service. And that's because we haven't told PHP unit what we want the, uh, mock
response to do when we call the two array method. So let's go ahead and do that right
above here. We've created our mock now mock response. We want the method to array
will return an array. So what exactly is this array supposed to return? Well, we want
it to return a, a list of issues and because we're only concerned with the title of,
uh, our issues, when we're fetching them, we'll set the title key and we'll give it,
pass it, Daisy. And then the labels array for this issue is going to be, it's an
array of labels and we of course are passing name

And status six. Let's copy this and come on down here and we'll paste it. Let's
change the second one to Maverick and Maverick. Remember he's taking his test flight,
so he is healthy today. Ah, let's go ahead and move back to the terminal run vendor,
bend PHP unit, and awesome. Our test pass. The best thing about this is we're no
longer calling GitHubs API, ever retirement, run our tests. Imagine what that would
do in a, a continuous integration environment, such as getting up actions, where if
you're running this test, thousands of times a day, it's calling that API over and
over again. Remember when we were talking about, uh, all the different names for
mock, well, both a response or a mock response, and our mock client here are, uh, are
called stubs. Stubs are fake objects where you optionally take control of its return
values, which we're doing in this case, when we're saying will return for both of
these objects. Again, the terminology isn't too important, but there you go. These
are stubs. And yes, every time I teach this, I need to look up these terms to
remember exactly what they mean

Coming up next. We're going to take our coming up next. We're going to return our
stubs into mock objects, which is basically the same thing, but we're also going to,
uh, control the data that we are passing into these, to the two array and the request
method.

Yeah.


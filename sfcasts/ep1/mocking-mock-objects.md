# Mocking: Mock Objects

Right. So we have pass and tests now, but let's think about this for a second.
Anytime we call a request, uh, method on our HTTP client, we're going to get back our
JSON string, which we turn into an array. Something that is equally as important as
the data we're getting back from GitHub is how we're asking for that data. So in our
request, uh, method call we're performing. We were passing the method, get, and we're
passing the API endpoint for GitHub. We look at our test. If we look, if we look back
here in our test and we scroll down to our mock HTTP client, we can add a method call
to the HTTP client mock. And with that, we, we can use expects the expects method.
Uh,

This

Is required.

Okay.

The expect expects method requires that you pass a rule to it. So the test class, uh,
or test class that we're extending from PHP unit has a number of different rules that
we can pass. Never wants any, at least, at least once at most exactly right now.
We're just going to give it once we run our test back in the terminal bender bend PHP
unit and Austin, we have th eight tests and 13 assertions. Now what's happening here
is that we're telling the mock that we expect one time, the method request to be
called, and it's going to return our response object or our mock response that we
created earlier. We're still not telling this. Uh, we're still telling our mock that
we expect that our GI method and the URL for our client needs to be passed. So below
the method, uh, or method call for the, uh, mock, we can pass, uh, width, but width
is going to do, uh, we're going to pass with and with takes an unlimited number of
arguments. So we look at our service again, we're passing in the method, get and the
URL to the request method. So their test that's passing get

So blah. So in our test, we expect that request to be called one time with the
argument get and our whoops, and our URL. Move back into a terminal and vendor bend
PHP unit, huh? What's this? We have two failures. Our get up service test test, get
health report returns, correct health status for Dino with data six, six Dino
expectation fail for method. Uh, method name is request when invoked one time
parameter one for invocation does not match expected value failed asserting that the
true strings are equal. We expected that HTTPS, uh, api.com/repost slashy CA /Dyna
park. This is what our, we told our test we wanted. And instead we got, uh, the same
string, but with the issues <laugh> silly me, we're getting the same failure again
for healthy Dino, uh, set as well. Let's move back into our test. And in this
particular case, our URL is supposed to end with issues. So we'll go ahead and add
that. Move back into our terminal. And let's run our tests again with vendor bend PHP
unit. Great. They're passing. Now we have eight tests and 13 assertions, but what if
I actually wanted to make sure that this method is called more than once? Oh, well,
we can do that.

Aside from the once, uh, aside from the once rule for the expects method we can pass
in never any, at least, at least once at most. And exactly let's say we never wanted
this call, uh, this method to be called. Let's change that once to never move back to
our terminal and we'll run our tests again. And we can see here that we have two
failures that are service test with a sick Dino data set. Uh, it failed because the
HTTP interface request method was not expected to be called.

Let's

Go ahead and move back here. And we'll change this never back to once again, run our
test one more time to make sure that they're passing which they are. And there we
have it. We could do the same thing, uh, for our mock response. And that is tell that
our mock, that we expect the method two array to be called at least one time, but we
wouldn't have to worry about, uh, because two array doesn't accept any parameters. We
wouldn't have to worry about adding the, or configuring this mock to use the width,
uh, arguments, uh, method. But the response, once we get it, we don't really care
what we're doing with that because we have other tests in place that are going to
cover this. So if it was really important, we could tell the mock response to accept
this mythical once, but let's just pick and choose our battles and not worry about
that. All right. So now that we have our service, uh, working and we're able to
ensure that things are working as expected, let's go ahead and use it in our
controller. So open up the main controller and here at the public function index,

Let's go ahead and pass in a GitHub service argument and we'll call it GitHub. Thanks
to Symfony the service, be configured automatically and inject it into this method.
Anytime it's called right below our dinos array, we're going to create a for each
loop and for each dinos as Dino, we want to set the health of the Dino by doing Dino
set health and to set the health, we're going to call our GitHub service, get health
report. And of course we have to pass in the name of our Dino to the GI health report
method. And that's it moving back to the browser refresh and oh, no, the return value
must be of type health status and NOLA was returned. Hmm. Let's take a look back at
our code and we'll look here, uh, our get health report method. Pause.

All right. So we move back to our browser. We refresh and oh, no. All right. So get,
get Dyna status from labels. Return value must be of type health status and no is
returned later on. We can actually write a test for this, uh, in a future episode,
but for right now will, let's go back and we can see here, uh, for our type area,
that line 54 return health status try from status is returning Noll. I bet this has
something to do with, uh, an issue in GitHub. Let me look at the issues real quick.
And we have, huh, Dennis, Dennis just finished his daily exercise routine and he is
hungry. The reason why we're getting this error is if we go and look in our GitHub
service, we'll scroll down to the GI dinosaur status from labels method. And we can
see that hungry, uh, that we're passing the sta status from GitHub to our health
status, try from method. This is our enum. And if we look in our health status, uh,
enum, we don't have a case for hungry. Ha go figure coming up next. We're going to
figure how to, uh, we're going to figure that one out.

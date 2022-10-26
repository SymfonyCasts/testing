Let's continue on the trend of refactoring or test. And if we look at this test
method, we create a mock response object, a mock HTTP client. We are mocking the
logger interface and then we create a GitHub service. I think what we can do, if we
look up here in our other test method, we are doing the same thing, just different
objects. Let's try out this test a little bit and add private logger interface, mock
logger and blow. Create a private T mock HTTP client and mock HTB client. Then
private mock spots. Mock spots coming back down here to the bottom. Let's add a at a
new public function and we will call it create it hub service and it will take an
array of response data. Oops. We'll take an array of response data. You'll give us
back a GitHub service. Then inside, say this mock response = oops = new mock
response. And we will JSON and code response data. Next we will this mock http
client, we will set the response,

Oops,

Nope, I'm not going to do that. This mock response. Then finally we'll return a new
GitHub service with this mock HTTP client and this mock logger. Right now we just
have to init, uh, instantiate the mock HTP client and the logger. So back to the top.
We're going to do that with a built in, uh, a method that test case gives us called
setup. Inside setup. Say this mock logger = this create, oops, this create mock
logger interface. Then below this mock http client = new mock http client. Yeah, that
sounds good. Okay. Now back in our test method, we can cut a response and then we
will say, service = this. Create get held service. Passing in our response data and
yes, move to the terminal refresh and yes. Oops, no. Yeah, lyric. And then we'll do
vendor pin PHP unit and Awesome. All of our tests are still passing, but look how
much code we removed. Well, okay, let's see, what am I doing now? Well, oh yeah,
yeah, yeah. We're going to remove some more stuff. Okay, so now we've done that.
Let's come up here to our first test method and we're going to do the same thing. So

It's

Cut out the response here and say, Service = this criteria of service. Let's keep
doing that. Pass in the response data and then down here, let's go ahead and remove
the service altogether. We still have one problem. So before we were adding in, uh,
expectation that this, that the request method was called one time. We still want to
do that. We were also making sure that get, uh, http method was passed in along with
the url. So let's do that first. We will a search, same that one to the same as this.
Um, mock HTT you provide. Get request count. Then we can remove this. Actually, no, I
don't want to do that. Keep that. Yeah, yeah, yeah.

There's

Too much back and forth. Then will a search same that get was the, this mock spot?
Oops. No. Stop doing that was the mock response. Get request url. I'm screwing this
part up. And start from the top insert. That should start from the top again. Assert
that get is the same as this mock response get request method. And then last thing we
want to do is a copy the URL here. Hey, hush. Copy the URL and then we'll do a
search. Same that the, uh, stop, right? Well a search. Same. Geez, Jesus.

Okay.

Well the search, same that the URL is the same as this mock response hip request url.
We can go ahead and remove these. Then scroll back up here to the top and in our use
statements, let's go ahead and remove our two interfaces as well. Cool. Move back
into the tests and run vendor bend PHP unit again and, Great. We have 10 tests and 20
assertions. Cool. Hey, what, what up? Oh, okay. Sorry, I was recording. I couldn't
open the door. Me, I know.

Now, now that we have our service, we'll need a way to call GitHub's API from within
our service. So we can grab the list of issues for our Dyna park repository on
GitHub. We're going to use Symfony's HTTP client for that. So move over to your
terminal. And let's composer require Symfony HTTP client. Now that we have that,
let's use it in a wrap. So the first thing we need to do is, uh, initialize the
client and we can do that by creating a client variable that = HTTP client, and we
are going to create next. We're going to do alright. Yeah, next we're going to
actually call the, uh, GitHub's API. So we'll do that with client, uh,

Think

Right. Expert on call, get hubs API with their client. So we'll do client request and
the request method for the HTTP client accepts two arguments. The first is method,
and this refers to the HTTP method, like get or post that we're going to use in this
case, we're going to use GI. And the next argument for the request method is URL. And
this is the actual URL that we're going to call to retrieve the list of issues from
our Dyna park repository. All right,

Al, when we call the re uh, when we call the API endpoint for GitHub, GitHub's going
to return to us a list of J O but what does that look like? Let's move back to our
browser real quick, take a look at these Symfony issues or the Dino park issues. And
we can see that we have four issues total within this repository. And we have
dentists finished his daily exercise routines. Big ed has a toothache. Maverick has a
test flat at noon and Daisy sprained her leg. Each of these has a status label next
to him, and this is what we need. This is what we're going to use to set the health
under Dino objects. So for instance, Daisy sprained, her leg and her status is sick.
Whereas Maverick has a test flight at noon. His status is healthy. So let's move back
into our, let's moved back into our service and the, when we call client request,
that's going to return a response object, an HTTP response object. So we'll set
client request over set response = client request, and then down here, we need to
filter our issues out. So to do that,

Oops,

Uh, so to do that, we're going to, uh, create a four each loop and Symfony's HTTP
client has a really cool, uh, way of turning J on into a Ray for us automatically by
calling response to array. And then we're going to do, uh, for each yeah, for each
response to array as issue

Oops,

As issue and inside what we need to make sure is that the string that the no, and
inside, we need to make sure that if string contains our issued title and that's
going to be title matches the dinosaur name that we're provided here in our, uh, GI
health report method, then we'll need to do something with, uh, this issue from
GitHub, right? So what are we going to do with that? Well, I'm going to copy in just
a little bit of, uh, I'm going to copy in a private method and I'll put that right
below here, right there, put it right below our service method.

Stop.

Ah, come on. All right. So I'm going to copy in a private method and what this is
doing. It's going to take an array of our labels that, uh, exist on our issue object.
And it's going to loop over them, grab the name from the label. If the name of the
label starts with status, or if the name of the label does not start with status,
it's going to continue over, uh, the array. If it does begin with status, it's going
to trim off that status, prefix remove any white space and then give us the label.
Well, that's just stupid. Uh,

I'm going to have to back up and paste this one more time. All right. So I'm going to
paste in a private method here. And what this method is going to do is get Dyna
status from labels, which it accepts an array of labels that we're going to get off
of our issue, uh, off of our GitHub issue. And it's going to give us a health status
and side. All it's doing is just going over the array of labels, looking for labels
that start with the word status, removing that, and then trying to get a health
status, uh, trying to return a health status object from the status, uh, name of the
label. So up here in our, for each loop, uh, in our GI health report method, let's go
ahead and to do something. We are going to, uh, health = this GI dinosaur status from
labels, and we need to return, or we need to pass it the, uh, array of labels from
our issue object.

Alright,

Now, instead of returning just healthy all the time, we're going to return the health
variable, which should be a health status from our private method. And just in case
that either one, the issue does not have any labels on it or two, it cannot find a,
uh, health status label. We're going to come up here to the top and we're going to
set health = health status healthy. This is because anytime that a dinosaur does not
have a sick label, we have to assume that the dinosaur is healthy. Uh, move back into
her terminal and let's test this out vendor bend PHP unit. Awesome. We have eight
tests and 11 assertions. Our service is working beautifully. We're going to do one
last thing here. And that is at the top of our GitHub service. We want to log all of
these HTTP requests we make to GitHub. This will come handy in production. So let's
create a public function construct, and we're going to create a private logger
interface and we'll name it logger. And then, uh, right before a four each loop after
we call the respo or after we call the, uh, GitHub API, let's go ahead and

Let's go ahead and do this logger. We want to log as info and our message will be
request Dino issues. And we're going to add a little extra cont context to this
message. And by passing an array, which will contain the Dino's name. So Dino, uh,
for the key and the name is dinosaur name at what we always know, which, uh, Dino we
were trying to get, uh, an issue from. And then below that, let's go ahead and throw
in the response status. Ah, we're going to throw in the response status that was
given back from our, uh, HTTP request. So response get status code, and we're going
to return, uh, move back to her terminal. Let's run this test one more time. Oh no,
we got two errors now. So both of our GitHub service test test get health report
returns, correct health status for Dino with data sets, sick Dino and a healthy Dino
are both saying that we have an argument count error, two few arguments to function
at GitHub service construct, zero pass. And exactly one were expected looking at our,
uh, service here. That's because we're supposed to be passing in from our test a
logger instance, and we are not doing that yet. So coming up next, we'll show you how
to do that. And we'll fix this.


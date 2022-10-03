# Create a GitHub Service Test

All right. So now that a dinosaur object is able to persist the health status and
tell us if the dinosaurs are able to accept visitors, we need to go ahead and get
these, uh, labels for each of our S and the GitHub repo and pull 'em into our app.
Instead up on our objects accordingly, to do that, we're going to create a new GitHub
service that will use Symfony's HTTP client to call up, uh, GitHub's API, fetch those
issues and figure out which1s apply to our dinosaurs. First thing we're going to do
is create a new unit, uh, service directory inside. We're going to create a new
GitHub service test, and of course, this needs to extend, uh, PHP units test case,
and now let's create a new public function test get test. Dang it. Test get health
report returns, correct health status for D all right, and this isn't going to return
anything. Now inside let's do service = new GitHub service. And of course this
doesn't exist yet, but that's okay. And then for the test, we want to self assert
same that our expected status matches our service get health report. And we're going
to pass in the Dino's name to get that report. Now, obviously we're going to be using
a data provider. So let's go ahead and write that out.

And this would be public function, Dino name provider, cause that's how we Fe our,
uh, uh, health reports. This is going to return to generator, and now we're going to
yield sick Dino, and this will return of course, health status.

Oops,

This will return health status, uh, sick. And let's take a quick peek back on GitHub
to see what the Dino's name but was sick. And that was Daisy. She has a sprained her
leg. So Daisy, and then for our healthy Dino, we're going to yield healthy Dino, and
this will be health status healthy. And I believe we're going to use, uh, yes,
Maverick. He has a test blood at noon, so let's come back here, Maverick, and there's
our data provider. Now back up here, let's go ahead and tell our test method, user
provider. And we're going to do that with a at data provider annotation and the data
provider is Dino name provider.

Oops,

Let's come back here and expand that out. And then of course we need to pass the
health status and this is our expected, expected status and a string, which is the
dinos name.

All cool. We have our test set up. Let's go ahead and move to your terminal vendor,
then PHP unit, and we have two errors. GitHub service test test get health report
returns, correct health status for Dino with dataset sick Dino, uh, is given us an
error because the class GitHub service is not found. Of course we're getting the same
thing for our healthy Dino as well, which is to be expected. So let's go fix that
back in our code. Let's come up here to our source directory, create a new folder and
we'll call this service and inside create a new PHP class GI service. All right, our
new service let's create our new public function, GI health report. And this of
course accepts a string dinosaur name, and it's going to return a health status
status. I'm going to keep saying status. All right. Now, instead the method we're
going to first call GitHub's API. We're going to first call GitHubs, uh, API. Then
we're going to filter the issues. And last we are always going to return a health
status of healthy or sick. Now let's go back to our test. Oops, let's go back to our
test and we need to add the use statement for our GitHub service. So the best way to
do this is chop off the last couple of letters and there we go. GitHub service app
service. That's the one we want. And PHP storm adds the use statement for us.

We can go back to our terminal and let's run the test one more time. Make sure that
yes, instead of two failures, we only have one. Our GitHub service test test GitHub
test get health report returns, correct health status for Dino with data set sick
Dino, it failed to asserting that two variables reference the same object. And it's
of course doing that because we are only returning a healthy status for get health
reports. Now that we have this kind of wired up, let's go ahead and implement the
service.


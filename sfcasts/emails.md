# Emails

Coming soon...

All right, team, we've covered all the main parts of integration testing, woohoo.
It's really delightfully simple. It's just a strategy to grab the real services from
a container and test them, which ultimately gives us a more realistic test. The
downsides of integration tests are that they run slower than unit tests. And they're
often more complex because you need to think about things like clearing the database
and seeding the database. And sometimes you really do want to avoid some real things
from happening like API calls. So in this case, we actually had to use mocking to
avoid that. But like anything else, the right tool, unit testing or integration
testing, just depends on the situation. Use both. All right, to finish the tutorial,
let's show off how we can test a few more complex parts of our system, like testing
whether emails were sent, or messenger messages were dispatched. To do this, we need
to give Bob a new superpower, the ability to put the park into a lockdown. When he
does this, our app should also send out an email to all park staff saying, hey, watch
out for the dinosaurs. All right, so to help with this, I'm going to go into lockdown
helper service. Lockdown helper. And create a new method. This will be what we call
the put the park into lockdown. It's a public function. How about dino escaped? So
it'll be avoid return type. And I'm just going to put some comments here for what
we're actually going to do. So we just need to persist the lockdown the database and
send an email to actually call this code and trigger lockdown. Let's create a new
console command because those are fun. So I've been console make command. Let's call
it app lockdown start. Simple enough that creates a single class. Let's go and check
that out. Oh, there it is. And in here, we're going to inject the private lockdown
helper lockdown helper. Make sure you call the parent constructor. That's good. And
I'm going to delete a bunch of logic in here because this is going to be really
simple. We're just going to say this dot lockdown helper dot dino escaped. And then
how about IO caution? Lockdown started. Sweet. So this method doesn't do anything
yet, but we can already try our command cheat and copy my command name from bin
console app lockdown start. And we've got it sweet. So before we fill the logic into
our lockdown helper, let's write a test for it. So we have one test for tests in
current lockdown. And actually, before we even do there, let's do that trick where we
add a private function to help us get out the service we're testing. So I'll say
private function. Get lockdown helper, it's going to return a lockdown helper. And
inside of here, we'll just copy the code from above and return it. And then the code
up here can simplify to use just this arrow, get lockdown helper error and current
lockdown. Alright, so now let's create our new test method. So our function test dino
escaped persists lockdown inside, we're gonna start the same way that we always do by
booting the kernel. And then we can call our method this arrow get lockdown helper,
arrow dino escaped. Now we can run this test right now, if we want to, we could run
symphony, PHP bin slash vendor bin PHP unit. I'll run that entire class. And it
doesn't fail, we get risky because we haven't actually performed any assertions. But
at least this isn't blowing up. What we want to assert is that this did insert a row
into the database. So how can we do that? Well, of course, we can grab the entity
manager or our repository service, make a query and then do some assertions on that.
However, foundry comes with a nice little trick for this. So after we call our
method, we can say lockdown factory. And usually we say things like create or create
many. But this also has a method on it called repository. This is an object that's
from foundry that wraps your repository. And you can actually call different methods
on it. So we can actually call find most recent, we can treat it as if it's our real
repository find most recent or is in lockdown. But it also has this assert thing on
it. So we can say assert count one. So we're making sure that the there is one record
in the database and we could go further and fetch that record and make sure its
status is active, but I'm not going to worry about that. Alright, so let's run the
test. This should fail and perfect it does. So let's go real quick. And I'll paste in
some code that creates the lockdown and saves it. Easy enough. Good, boring code
there. And now our test passes. Alright, so let's look at sending that email now
before we do that, I'm actually going to add an assertion for that. So just like how
we can assert things in the database, simply gives us some tools for asserting things
like was an email sent, which would normally be kind of a hard thing to test for. We
can do that by just saying this arrow assert email count. And we're not going to talk
about any of these other methods because there's actually assert email address
contains attachment contains HTML body contains, you can assert lots of things via
email, I'm just going to assert that we sent one email during this request. And of
course, since we're not sending that, it fails. But actually it fails, even because
we don't have we don't even have mailer installed. So let's actually get that
installed. To require symphony slash mailer Whoa, that's not what we want.

So this composer requires Symfony Mailer, and if it asks you about docker
configuration it's up to you. But I'm going to say yes permanently, we'll talk about
what that did in a second, it doesn't really matter. Now just like with a database,
of course with Mailer you're going to have to configure your Mailer connection
parameters, and that's done in .n via Mailer underscore dsn. So I'm going to go ahead
and uncomment this. Null is actually a great default, it means that your emails won't
actually send in the dev or test environments, then you can override on your
production environment to send to somewhere real. But null is a great thing. But if
you did want to change this in .env to something else, I'd probably add this null
transport to .env.test because it's really nice to not send any emails in the test
environment. Alright so now let's try our test again. Because we have not sent any
emails. So let's do that over in Lockdown Helper. Let's inject one more service in
here, private Mailer interface, Mailer. And then down here since this isn't a Mailer
tutorial, I'm just going to call a new function called send email alert. And then I'm
going to paste that in. So perfect. And then I'll hit alt enter on this email to add
the Symfony Component MIME use statement. And should be good. And got it, the test
passes. By the way, it's not really related to testing. But one of the cool things is
that if you use the Docker integration, when we installed Mailer, it actually added
this little mail catcher service. So if you run Docker Compose down, and then let's
go back up dash D, it actually creates a little mail catcher service here. And the
cool thing is, we run our test again. Instead of the email, the email didn't
actually, normally the email doesn't actually send, it uses the null transport. But
because we have that running in the background, and we're using the Symfony binary,
we can actually run Symfony open localhost. Symfony open local webmail. And that's
actually catching all of our email things in the background, being caught here. And
if we sent an email is actually using our application, they would actually go here as
well. In fact, watch this. Let's run Symfony console. app lockdown start. That's our
lockdown. If you check out over here, ah, we have two messages. Pretty sweet. But
anyways, before we stop talking about testing, I want to show you one other really
quick tool for testing email messages. And that is actually another library from
Zenstruck. So run composer require Zenstruck slash mailer dash dash dev. So Symfony
as we saw, oops, slash mailer test dash dash dev. So as we saw, Symfony has built in
tools for testing emails, and they work great. The mailer kind of gives you even this
middle test library gives even more ways to test your emails. So it's just kind of a
nice thing and you can totally use it. And it's simple enough, you're going to use a
another trait. So use interacts with mailer. And then down here, instead of assert
email count, you can say things like this arrow mailer arrow. And then you have a ton
of different asserts assert email. Sent to assert sent email count one, let's say
this arrow mailer arrow. So there's a lot of different email. Assert email sent to
and we sent it to staff at dinotopia.com with subject line park lockdown. Oops, not
email count. Email sent to. There we go. So you can see this is the expected to and
then this is a callable where you can assert more things, or you can just pass the
subject there that I'm just scratching the surface. It looks small, but it's actually
a lot that you can do with this mailer test library. And as always, there's really
great documentation on there about all the cool things that you can do. But anyways,
we've run our test. It's still passes. All right, next up, let's talk about testing
messenger.

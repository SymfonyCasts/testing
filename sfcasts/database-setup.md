# Database Setup

Coming soon...

Alright, this first test was too easy. Almost cheating, really. We're returning false
and isInLockDown. So let's write another more interesting test. How about public
function test isInLockDown returns true if most recent lockdown is active. Cool. And
we'll start kind of the same way with self-boot kernel so we have things going on in
the background. Now the tricky thing here is that we need the database to not be
empty at the start. We need to be able to insert a lockdown record into the database
that's active so that when we finally call our method and it executes the query, it
will find that record. So this is a critical part of integration tests just because
integration tests so commonly talk to the database. We need to make sure our database
is in a known state before the test. And really, this is no problem, okay? We need to
create some lockdowns. Let's lockdown and save it. Let's do it. lockdown equals new
lockdown. I'll say lockdown, you know, set a reason here. And we'll set the created
at to be like one day ago. That doesn't really matter yet. And I won't need to set
the status because if we kind of peek in there, you can see that the status is set to
active by default. Cool. So we created the entity object, nothing special about that.
Now we need to save it. And there's also nothing special about that. We can get the
entity manager out by saying self colon git, colon git container, arrow git, and then
entity manager interface colon colon class. I'll do my funny assert trick just to
help my editors auto complete. And you guys know the drill at this point entity
manager arrow persist lockdown entity manager arrow flush. Just like we would do in
our application code. Now to see if this is working, I'm just going to do a DD
lockdown here, DD lockdown arrow get ID. Cool. Let's try this test. I'm going to
focus my test a little bit and run test integration repository lockdown repository
test just to run stuff from this class. And oh, it explodes. Let's see here. Ah, it
apparently it's having problems connecting to my database on my database. Okay, so on
the surface, this is just like, this problem is because this is a very known problem.
Whenever we start our application, we need to configure the database so that
everything works. And the key behind this, of course, is the database underscore URL
environment variable. I'm using Postgres, but it doesn't really matter. So just
ignoring testing for a second, when you normally set up your local environment,
you're going to customize this database URL under here and dot env, or you could
create a dot env dot local file. And you could override this there and point it to
whatever your local database connection stuff is. And whatever you have as database
URL is what is going to be using your test, you can see here it's having problems
connecting to 127.0.1.5432. Because it's reading that right from my .env file. Now,
when, so this is all just normal database setup stuff, with one important but small
difference in the test environment. And that's this. If you create a dot env dot
local file, for example, and you override database URL and run your tests, it's going
to change this port to something crazy. It is not going to be used. Check out this
error. It's still looking for port 5432. This is a special thing in the test
environment. Test environment ignores your dot env dot local. So if you wanted to
configure a database URL specifically for your test environment, you need to put it
into dot env dot test the environment specific test file. So just watch out for that
gotcha. That dot env dot local is not read in the test environment. Now I'm going to
delete that dot env dot local just to avoid any confusion. Now for us, we are not
going to rely on any of these dot env files. And that's because if you follow the
readme.md instructions to set up a project, you've noticed that we're using docker
behind the scenes, we have a docker compose, which loads a Postgres database for us.
And because we're using the symphony binary as a web server, it's setting the
database underscore URL automatically for us. So when I go over here and refresh the
page, it's not using this database URL from my dot n that's actually being overridden
automatically to point to the docker container that I have running. This is something
that we talked more about in our doctrine tutorial. However, it looks like that is
not happening inside of our test when we run our test looks like the database URL is
just pointing to what we have in dot n. And that's true. That's because the symphony
binary doesn't have a chance to inject the database underscore URL environment. So to
allow that instead of dot slash vendor bin slash PHP units, we're going to run
symphony PHP, and then vendor bin PHP units. And we'll just run that specific test.
So symphony PHP, symphony PHP is just a way to run execute PHP. But by doing this,
it's going to inject that database underscore URL variable. And that and when we try
it, it fails again. But check it out. This is a different error. This time,
apparently, it's talking to port 58292. That might be different on your computer.
That's a random port that our Docker database can be released on can be reached on.
But it says database app underscore test does not exist. Huh? So to see what's going
on, we can actually run symphony var export dash dash multiline. What this is going
to show us is all the environment variables that the symphony binary is injecting.
And the most important one is it's injecting database URL. And here's where you can
see it pointing at our Docker container, which for me is running on this port 58292.
The important thing to notice here is this app. That's the name of the database that
it's pointing to. So if our database underscore URL is pointing to a database named
app, why did the error just say that there's no database called app underscore test?
Before we answer that, I have another question. Do we when we're running our tests,
do we want our tests to use the same database that our local application is running?
Ideally, no. Having a different database for your test versus your normal development
environment is a good idea. For one, it's just annoying to run your tests and have it
mess with your data while you're developing. And fortunately, having two different
databases is something that happens out of the box. Open up config packages
doctrine.yaml. And down at the bottom, we have this special when at test blocks, this
is configuration only for the test environment. And check out this DB name suffix, it
is set to underscore test, you can ignore this percent and test token thing that
relates to a library called para test. And in our case, that's going to be empty. So
effectively, it's this. Thanks to this config in the test environment, it's going to
take our app, and it's actually going to look for a database called app underscore
test, which is just really nice. And that explains why that database didn't exist. So
all we need to do is just create that database. So go back here, we can say symphony
console. So this is running bin console, but through our symphony binary so that I
can inject the database underscore URL environment variables. And we'll say doctrine
database colon create, and then dash dash n equals test. So to run this in the test
environment, that way the databases app underscore test, and then it will try to
create that. And it works. And then we're also going to need to create our schema.
And that works as well. This project does have some data fixtures in it. So should we
also execute our fixtures in the test environment? The answer is no. But we're going
to talk a lot more about that soon. Right now, let's run our test again. And yes, it
hit our dump one, that one is coming from our dump down here. So let's finish this
test. I'm going to need the lockdown repository again, and to avoid repeating myself,
I'm actually going to copy this and create a new private method down here, I'll say
private function, get lockdown repository. Paste that code in there return, and I can
use the type in lock and repository return type lock and repository. And now I don't
need the assert because PHP will throw a big error if this returns something else for
some reason. And this just simplifies thing up here quite a bit, we're going to say
this arrow get lockdown repository arrow is in lockdown. And if we try the test
again, just to make sure that still passes, it does. And interestingly, you can see
the ID is now two in the database for our ID down here. So let's replace that. In
this case, we're going to want this arrow assert true that this arrow get lockdown
repository arrow is in lockdown. And of course, if we try that now it would fail
because this is just empty. So I'm going to quickly write out the query for this. So
query builder with the alias lockdown. And we're locked on that status does not equal
ended status. And set parameter ended status set to lockdown status. Ended set max
results one arrow get query arrow get one or no results. If this finds something,
then we are in lockdown. If not, we're not locked down. So we can say does not equal
null to finish that. All right, try the test. And it fails. No, our second test
passed now. But our first our original test is suddenly failing. How did that happen?
Well, it turns out that thanks to our second test, when our first test runs, the
database is no longer empty. In fact, it's piling up with more and more rows each
time we run our tests. I can prove it run symphony console, D ball run SQL, select
star from lockdown, dash dash and vehicles test. There it is. So this is a critical
problem, we need to make sure that we know exactly guarantee the database is in a an
exact predictable state at the beginning of every single test. Let's dive into this
very important problem next.

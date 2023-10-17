# Reset Database

Coming soon...

It's really common with integration tests or functional tests to talk to the
database. And when you do, it's also super common to need to seed the database with
data before your test, to add some rows like our LockDown before doing your work and
writing your assertions. In the first tutorial, we even talked about a kind of a
philosophy or a pattern for your tests called AAA, which stands for Arrange, Act, and
Assert. So in this case, with an integration test, commonly the Arrange step means
seeding your database with data. Then your Act step is when you call the method, and
your Assert is, of course, the assert or asserts that you have at the end. Anyway,
there are two basic approaches to seeding your database and test. The first is to
write code inside your test to insert all the data that you need. The second is by
creating some sort of fixtures, and we do have fixtures that are powering our actual
local website, and loading those fixtures from right inside your test. Well to make
your life easy, I strongly advise you to not load your fixtures. Why? Because your
test is a story, and that includes seeing which data you have in the database. If you
instead load all of your app fixtures, and then suddenly assert that we're in a
lockdown, it's not super obvious why we're in a lockdown or what we're testing. You
need to go dig into the app fixtures to figure out what LockDown records we have and
figure out what's actually going on here. I really do not like that. So it might feel
like a little extra work, but seeding your database right inside your test methods,
that's the way to go. But either way, there's kind of a bigger thing here. No matter
how you seed your database, you need to make sure that before your test starts, your
database starts empty. We just saw why. Our original test passed, but as soon as our
second test inserted a row to the database, our first test started failing. Boo.
Unless your database is in a perfectly predictable state, you can't trust your tests.
So let's think about this. Before each test, we want to clear the database. So we
could do something where we override the setup method here and we run some code here
that clears the database before each test, and that would totally work. Fortunately,
we don't need to do that because there are actually multiple libraries that already
solve this problem. My favorite is Foundry. So run a composer require zenstruck slash
foundry dash dash dev. If you've watched our document tutorial, you might be familiar
with Foundry, but you may not be familiar with some of its testing superpowers, which
is really where it shines. Now the main point of this library is to help you create
dummy data, and we are going to talk about that soon, but it also comes with a super
easy way to empty your database between each test. And it looks like this. At the top
of your test class, say use reset database, and also use another thing called
factories from that same library. All right, now run all of your tests. They pass. We
can run them over and over and over again. What's happening is before each individual
test method, it is clearing the database. By the way, there is one other library for
clearing the database called Dama, which can be faster, so I'd welcome you to use
that. It works really well with Foundry. There's integration for it, and in that
case, if we were using Dama, you would keep the use factories. You would just remove
the use reset database since Dama was handling that instead. Now before we move on,
unrelated, you'll notice that I have quite a few deprecations here. These are called
indirect deprecations. Seeing what deprecations you're using is great, and indirect
deprecation means it's not our code that is triggering the deprecation. It's that we
have some library, and that library is calling deprecated code on some other library.
This is actually not something we need to worry about, and it's going to be really
annoying to keep seeing this throughout the entire tutorial, so we are going to move
those deprecations to a log file. To do that, we can open up phpunit.xml.dist, and
down here, instead of PHP, here's a way we can set environment variables, so I'm
going to put env here. There's not much difference between env and server. We can set
one called symphony deprecations helper. This is specific to the symphony phpunit
bridge, which is what gives us all these deprecation errors. We can say value equals,
and an easy way to kind of silence those, to move those, is to set a log file. So we
can say log file equals var slash log slash deprecations dot log, and then we'll
close that up. So now when we run our test, it's nice and clean, and if you want to
see if you're actually hitting any deprecated code paths, you can always go and look
at your deprecations dot log, and there they are right there. Alright, next, let's
leverage Foundry Factories to make seeding our database an absolute delight.

# Messenger

Coming soon...

Alright, let's complicate our LockDownHelper even further. When we create a LockDown,
instead of sending the email message from right inside here, we're going to dispatch
a message to Messenger and then have it send the email. We might do this for
performance reasons, though there are different ways. But you can make emails send
via Messenger automatically, but the point is we're going to dispatch a message from
inside of here. So, let's start by installing Messenger. Composer requires Symfony
slash Messenger. Awesome. Cool. And the key thing is that this is going to create a
.env with a Messenger transport DSN, which by default is going to use the Doctrine
transport. It doesn't matter what transport you use, Doctrine, Redis, I don't care.
And as you'll see, in the test environment, we're not actually going to send to any
transport. So, it won't matter for testing. In fact, to make testing easier, let's
immediately require another package called ZenStruck. Yep, them again. MessengerTest
dash dash dev. Cool. What this MessengerTest library does is it gives you a special
transport called test. So, we're using the Doctrine transport here. There's a new one
called test, which is going to make it really easy to control the behavior of our
Messenger transports in the test environment. So, open up config packages at
Messenger.yaml. And so, you can see here we have transports. Let's uncomment out
async, so we have an async transport. And that's using Messenger transport DSN. Then
down here for when at test, what we're doing here is we're overriding that async
transport and we're setting it to in-memory. And I need to get rid of one extra space
there. Now, in-memory is a transport that comes from Symfony. It's nice, but it's not
as nice as the one that comes from the mailer library, the testing library. So,
change this to test colon slash slash. And you'll see what that does. To use this in
our test, so before we actually dispatch a message inside of our helper, let's go
into our test because here I want to assert that we sent a message to Messenger. So,
surprise, surprise, we're going to use another trait called interacts with Messenger.
And then down here. Right before we call the method, we could say this arrow,
transport, and you can give it the name of the transport or not queue arrow assert
empty. Like with the mailer library, there's a lot of different things that you can
check here about your messages. We're going to keep things nice and simple. So, I'm
going to assert that we start empty. We don't really need to do that, but it's nice.
And then down here at the end, we'll assert count that there is one message that's
been sent. All right, so we run our test now. I'll keep running all of my tests from
lockdown helper. It fails with exactly the message you want. Expected one messages,
but zero messages found. Sweet. All right, so let's create a message. Run it, bin
console, make message. Started notification. Let's put this into the async transport,
and done. So, I created a message class, a handler class, and it also updated our
messenger.yaml config so that this class is handled by our async transport. In the
test environment, we'll use our test transport. All right, so next up in lockdown
helper, let's dispatch that. So, on top, we're going to need a new private message
bus interface. Message bus, and then down here at the bottom, we'll say this arrow,
message bus arrow dispatch, new lockdown started notification. Now, the handler for
this class, if we look down in source message handler, isn't doing anything yet, but
this should be enough to get our test passing, because our test is just making sure
that a message actually was sent to messenger. And when we run the test, it still
fails. And that is because there is a bug in my code. I put this message bus, look,
inside of end current lockdown, not dyno escape. That's why we run tests. Check that
out. Now it passes. All right, so let's move all of our mailing logic out of this
class. So, I'll delete that. We'll delete send email alert. And we will delete the
mailer interface. And I'll even clean up some use statements. Now, let's open our
handler. I'll paste the private method there. Hit OK to add some use statements. And
then very simply, this error, send email alert. Cool. So, we just moved the logic
over here. Everything should still work just fine. Except that it doesn't. Now it
dies on expected one email to be sent, but zero were sent. So, if we think about
this, inside of, if this were production, when we dispatch this message, if this is
an async message, then it's not going to send our email immediately. This is going to
be sent to a queue, and then it's going to be processed later. And when we use the
test transport, the way that works is it works like a true transport. It receives the
message, but it doesn't automatically handle it, which is cool. It's actually acting
like a real transport. So, that means over in our test, we are actually dispatching
this message, but the email is never sent. It's still waiting to be processed on the
queue. So, what you do here is up to you. It might just be enough that you assert
that it was sent to the transport, and maybe you have a different test somewhere else
to make sure that your handler has the correct logic in it. Or you might want to be a
little more hands-on here and say, no, I really want to make sure that this sent a
message, and that if that message was handled, the result is that it would send this
email. The way to do that, we can do that by telling the test transport to process
this message. So, copy those two mailer lines and delete them. And down here, we're
going to say this arrow transport, arrow process. And that will actually execute the
handlers. And below that, the email should be sent. And it's not. And once again,
this is so great because we have a bug in our code. Why wasn't it sent? Because I was
a little too fast with my handler here. Check this out. This arrow mailer, there is
no this arrow mailer. I'm actually a little surprised that we didn't get a bigger
blowing up error inside of our test. So, let's add public function, underscore,
underscore construct, private mailer interface, mailer. Now, that looks better. And
let's try the test again. And it passes. And we can actually even shorten our test a
little bit. Instead of asserting count one and processing, you can say process or
fail. What that method does is it actually makes sure that there are at least one
message to process. And then it processes them. So, it kind of combines those two
lines into each other. And you can see now the test still passes. All right, team. We
did it. Woo! Our Dynatopia application is dangerous. It's well tested thanks to unit
tests and integration tests. And in the next tutorial in this series, we'll turn to
the final type of testing, functional testing, where you actually, in effect, control
a browser and navigate to pages. Join us for that. See you later.

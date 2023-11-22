# Messenger

Let's complicate our `LockDownHelper` *even further*. When we create a lockdown, instead of sending the email message from here, we're going to dispatch a message to *Messenger* and have *it* send the email. *So*, let's start by installing Messenger. Run:

```terminal
composer require symfony/messenger
```

Awesome! This is going to create a `.env` with a `MESSENGER_TRANSPORT_DSN` which, by default, is going to use the Doctrine transport. It doesn't matter which transport you use - Doctrine, Redis, etc. And as you'll see in the test environment, we're not actually going to send to *any* transport, so it won't really matter. To make testing *easier*, let's also require *another* package called Zenstruck. That's familiar! Say:

```terminal
composer require zenstruck/messenger-test --dev
```

Cool! This `messenger-test` library gives us a special transport called `test`. We're using the Doctrine transport here, and the new `test` transport is going to make it really easy to control the *behavior* of our Messenger transports in the test environment.

Okay, let's open up `/config/packages/messenger.yaml`. You can see our transports here. Let's uncomment our `async` transport, which is using `MESSENGER_TRANSPORT_DSN`. Then, down here for `when@test`, we're *overriding* that `async` transport and setting it to `in-memory`. Oh, and I need to get rid of one extra space there. Perfect!

Now, `in-memory` is a transport that comes from Symfony. It's *nice*, but it's not *as nice* as the one that comes from the testing library, so change this to `test://`. We'll see what that does in a moment.

Before we actually *dispatch* a message inside of our helper, let's go into our test. Here, we want to *assert* that we sent a message to Messenger. And - surprise, surprise - we're going to use another trait called `InteractsWithMessenger`. Then, down here, right before we call the method, we'll say `$this->transport()->` and we'll just give it the name of the transport - `queue()->assertEmpty()`. Similar to the mailer library, there are *a lot* of different things about our messages that we can check here. We're going to keep things nice and simple, so we're asserting that we start *empty*, which isn't *required*, but it's nice. Down here at the end, we'll also `assertCount()` that there is `1` message that's been sent. Okay, let's run our test! We'll keep running all of our tests from `LockDownHelper`, and... it *fails* with the exact message we were hoping for:

`Expected 1 messages, but 0 messages found.`

*Sweet*!

Okay, now we can create a message. Run

```terminal
./bin/console make:message
```
and we'll call it

```
LockDownStartedNotification
```

Let's put this into the `async` transport and... *done*! We created a message class, a *handler* class, and it also updated our `messenger.yaml` config so that this class is handled by our `async` transport. In the test environment, we'll use our `test` transport.

Next up, in `LockDownHelper.php`, let's *dispatch* that. On top, we're going to need a new `private MessageBusInterface $messageBus`, and then, down here at the bottom, we'll say `$this->messageBus->dispatch(new LockDownStartedNotification)`. The *handler* for this class, if we look down in `/src/MessageHandler/LockDownStartedNotification.php`, isn't doing anything *yet*. But this *should* be enough to get our test to pass, because our test is just making sure that a message actually *was* sent to Messenger. And when we run it... it *still* fails. *That's* because there is a bug in my code. *Whoops*! I put this `MessageBusInterface` inside of `endCurrentLockDown()` instead of `dinoEscaped()`. And *that* is why we run tests. If we try it again... it *passes*.

All right, now let's move all of our mailing logic *out* of this class. We can delete that... `sendEmailAlert()`... the `MailerInterface`... and we'll even clean up some `use` statements. Now, let's open our handler. We can paste the private method there, hit "OK" to add some `use` statements, and then, very simply, say `$this->sendEmailAlert()`. *Cool*. We just moved the logic over here, and everything should still work just fine... except that it *doesn't*. This time, it dies on

`Expected 1 emails to be sent, but 0 emails were sent.`

If we think about it, if this were *production*, when we dispatch this message, if this is an `async` message, then it won't send our email immediately. It will be sent to a *queue*, and it will be processed *later*. When we use the `test` transport, it works like a *true* transport. It *receives* the message, but it *doesn't* automatically handle it, which is cool. That means, over in our test, we're actually *dispatching* this message, but the email is *never* sent. It's still waiting to be processed in the queue.

What you do here is up to you. It might just be enough that you have *asserted* that it was sent to the transport, and you, perhaps, have a *different* test somewhere else to make sure that your handler has the correct logic in it. *Or* you might want to be a little more hands-on here and say 

`No, I really want to make sure that this actually sent a message, and that if that message was handled, it sent this email as a result.`

We can do that by telling the `test` transport to process its messages. Copy those two `mailer()` lines and delete them. And, down here, let's say `$this->transport()->process()`. That will execute the handlers. Below that, the email *should* be sent, and... it's *not*. Once again, we have a *bug* in our code! *Why* wasn't it sent? Because I was a little too quick with my handler here. Check this out! There *isn't* actually a `$this->mailer`. I'm honestly pretty surprised that we didn't get a *bigger* error inside of our test. *So*, to fix this, let's add `public function __construct(private MailerInterface $mailer)`. That looks better! And if we try that *again*... it *passes*. And we can even shorten our test a little bit. Instead of `assertCount(1)` and processing, we can say `processOrFail()`. This method makes sure that there's at least one message to process, and then it processes them. That basically *combines* those two lines into each other. And if we check... the test *still passes*. Sweet!

All right, team! We did it! Woo! Our Dinotopia application is *dangerous* and well-tested, thanks to unit and integration tests. In the next tutorial in this series, we'll turn to the *final* type of testing - *functional* testing - where you effectively control a browser and navigate to pages. It's going to be *exciting*, so make sure to join us for that. Until next time!

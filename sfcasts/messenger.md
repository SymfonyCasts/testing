# Testing Messenger

Let's spice up our `LockDownHelper` a bit more, shall we?! When we create a lockdown,
instead of sending the email directly, we're going to dispatch a message to
*Messenger* and have *it* send the email. Start by installing Messenger:

```terminal
composer require symfony/messenger
```

Lovely! In `.env`, this added a `MESSENGER_TRANSPORT_DSN` which, by default,
uses the Doctrine transport type. Though, it won't matter which transport type you
use - Doctrine, Redis, whatever. As you'll see, in the `test` environment, we'll
override this completely.

## Setting up the Test Environment Transport

To make testing *easier*, let's also require *another* package from, you guessed
it, Zenstruck! 

```terminal
composer require zenstruck/messenger-test --dev
```

Cool! This `messenger-test` library adds a special Messenger transport called `test`.
We'll still use Doctrine by default, but now open up
`config/packages/messenger.yaml`. Uncomment the `async` transport, which uses
`MESSENGER_TRANSPORT_DSN`. Below, under `when@test`, we *override* the `async`
transport and set it to the `in-memory` type. Oh, and I need to get rid of one
extra space. Perfect!

The `in-memory` comes from Symfony and it *is* nice for testing. When it's used,
messages are not *really* sent to a transport, but are stored - in memory - on
an object during the test... which you can then use to assert that the message
is there.

I like that! But the `messenger-test` packages gives us something even better.
Change this to `test://`. We'll see what that does in a moment.

## Testing that Messages were Dispatched

Before we *dispatch* the message inside our code, head into the test. Here, we want
to assert that we sent a message to Messenger. And - surprise, surprise - we're
going to use another trait. It's called `InteractsWithMessenger`. Down here, right
before we call the method, say `$this->transport()->queue()->assertEmpty()`.

Similar to the mailer library, there are *a lot* of different things about messages
that we can check. We're asserting that the queue starts *empty*, which isn't
*really* necessary - but it's a nice way for us to start. At the end, also
`assertCount()` that `1` message was sent.

Let's try this! Keep running all of the tests from `LockDownHelper`:

```terminal-silent
symfony php vendor/bin/phpunit tests/Integration/Service/LockDownHelperTest.php
```

And... it fails with the exact message we wanted!

> Expected 1 messages, but 0 messages found.

## Creating & Dispatching the Message

*Sweet*! Generate a Messenger message with:

```terminal
./bin/console make:message
```

Call it `LockDownStartedNotification` and put this into the `async` transport. 
Done! This created a message class, a *handler* class, and also updated
`messenger.yaml` so that this class is sent to the `async` transport.

Next, waltz into `LockDownHelper` to *dispatch* that. On top, add
a `private MessageBusInterface $messageBus`. Then, at the bottom, say
`$this->messageBus->dispatch(new LockDownStartedNotification())`.

The *handler* for this class, if we look in
`src/MessageHandler/LockDownStartedNotification.php`, isn't doing anything *yet*.
But this *should* be enough to get our test to pass.

```terminal-silent
symfony php vendor/bin/phpunit tests/Integration/Service/LockDownHelperTest.php
```

And... whoops! A gremlin sneaked into my code! I added the code inside `endCurrentLockDown()`
instead of `dinoEscaped()`. And *that's* why we have tests people. When we try again...
got it.

Let's move all the mailing logic *out* of this class. Copy the private method,
delete where we call it, the `MailerInterface`... and even the old `use` statements.

Open the handler, paste the private method there and hit "OK" to re-add those
`use` statements. Then say `$this->sendEmailAlert()`.

Cool! Everything should still work fine... except that the test fails:

> Expected 1 emails to be sent, but 0 emails were sent.

## Processing Messages in your Test

Hmm. If this were *production*, when we dispatch this message to the `async` transport,
it would *not* send the email immediately. It will be sent to a *queue* and processed
*later*. And, the `test` transport we're using *works* a lot like a *true* queue.
It *receives* the message, but *doesn't* automatically handle it, which is cool.
This means that, over in our test, we *are* dispatching this message... but the
email is *never* sent because it's still waiting to be processed.

What you do here is up to you. Maybe you're cool just knowing that the
message *was* sent.

*Or* you might want to be a bit more hands-on and say:

> No way! I want full proof that when this message is handled, it sends
> an email.

We can do that by telling the `test` transport to *process* its messages. Copy those
two `mailer()` lines and delete them. Down here, say `$this->transport()->process()`.

That's it! That will execute the handler for any messages in its queue. Below *that*,
the email *should* be sent.

Try it:

```terminal-silent
symfony php vendor/bin/phpunit tests/Integration/Service/LockDownHelperTest.php
```

And... it fails. Another bug! *Why* wasn't it sent? Because
I was too quick with my handler: there is no `$this->mailer` property. I'm
actually surprised that we didn't get a *bigger* error inside our test.

To fix this, add `public function __construct(private MailerInterface $mailer)`.
That looks better! And if we try that *again*... it *passes*.

And we can shorten things! Instead of `assertCount(1)` and `->process()`,
we can say `processOrFail()`. This method makes sure that there's at least one message
to process, and then processes it.

Double-check the test:

```terminal-silent
symfony php vendor/bin/phpunit tests/Integration/Service/LockDownHelperTest.php
```

Got it!

We did team! Our Dinotopia application is *dangerous* and well-tested,
thanks to unit *and* integration tests. In the next tutorial in this series, we'll
turn to the *final* type of testing - *functional* testing - where you effectively
control a browser, navigate to pages and check what's on them. It's fun and
can also be used to check JavaScript behavior.

Alright friends, see ya next time.

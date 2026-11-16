# End to End (E2E) App Testing

## The app: Shipshape

A starship repair dock. Ships come in, captains book repairs, engineers work the repair queue. Everything the course tests already exists, so we only write tests.

- Homepage, ships in dock, a ship's page with a JavaScript "Run full diagnostic" button
- Book a repair form, which emails the captain a confirmation
- Critical repairs sound a red alert in the background, which emails the engineers
- Engineer login and a repair queue only engineers can see
- A small JSON API that mirrors the website, protected by API tokens

Symfony 7.4, PHP 8.4, SQLite, Tailwind, and Foundry for fixtures. No testing tools installed yet.

## 1. Intro: the app & the testing pack

- Tour Shipshape
- Install the testing pack and run the empty suite

## 2. First feature test

- Unit vs integration vs end-to-end (feature) tests, and what each is good at
- Test the homepage: it loads and shows the right heading
- No database needed yet

## 3. Ship page: the database problem

- Test a ship's page
- It fails because the test database is empty
- Loading fixtures first works, but it's brittle

## 4. Foundry in tests

- Use the same factories inside tests
- Fresh database for each test, with only the ships the test needs
- Rewrite the ship page test

## 5. Ships in dock listing

- Create a few ships, check they're listed, newest first
- Departed ships don't show up

## 6. Book a repair with the built-in client

- Fill out and submit the booking form
- Show how clunky it is

## 7. zenstruck/browser

- Install it and rewrite the booking test so it reads like a user story
- Rewrite an earlier test to compare

## 8. Validation errors & redirects

- Submit a bad booking: see the errors, nothing saved
- Submit a good one: redirected to the ship, success message shown

## 9. Debugging failing tests

- Dump the page or save it to a file
- What gets saved automatically when a test fails
- Seeing the real exception

## 10. Emails

- Booking a repair emails the captain
- Install mailer-test and check the email's recipient, subject and contents
- A bad booking sends nothing

## 11. Messenger

- Critical bookings sound a red alert in the background
- In a test, nothing happens because the message just waits in the queue
- Install messenger-test, check the alert was queued, process it, then check the engineers got their email
- Normal bookings don't sound the alert

## 12. Logging in

- Log in as an engineer through the real login form
- Check we're logged in, then log out

## 13. Access control

- Skip the login form and log in directly in the test
- Visitors get sent to the login page, cadets are denied the repair queue

## 14. API: reading

- Fetch the ships from the API and check the JSON

## 15. API: tokens

- The ticket list needs a token: no token is rejected, a cadet is denied, an engineer gets in

## 16. API: booking a repair

- Book a repair through the API and check it was saved
- Bad data returns the same validation errors as the website

## 17. JavaScript testing: Playwright

- The diagnostic button needs JavaScript, so our current tests can't click it
- Install Playwright and run the same test in a real browser

## 18. JavaScript testing: waiting & debugging

- Wait for the diagnostic to finish before checking the result
- Watch the browser, pause it, take screenshots on failure

## 19. Faster tests: DAMA

- Time the suite
- Roll back each test instead of rebuilding the database
- Time it again

## 20. Faster tests: ParaTest

- Run tests in parallel
- Give each worker its own SQLite database
- Time it again

## Bonus ideas

- Running the suite, Playwright included, in CI
- Counting database queries with the profiler (the ships listing has an easy one to find)
- Wrap-up: which kind of test to write for what

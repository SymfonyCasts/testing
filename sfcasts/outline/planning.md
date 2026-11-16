# End to End (E2E) App Testing

## Preconditions

- Fresh Symfony 7.4 webapp (PHP 8.4), SQLite, Tailwind, Foundry (fixtures only)
- No testing pack installed - chapter 1 does that
- Assumes the PHPUnit unit + integration testing courses

## 1. Intro: the app & installing the testing pack

- Unit vs integration vs E2E: what each is good at (promised on the course page)
- Tour the app, load fixtures (`foundry:load-fixtures`) - Foundry is already here, but only for fixtures
- `composer require --dev symfony/test-pack`: what it adds (phpunit, browser-kit, css-selector, `.env.test`)
- Run the (empty) suite

## 2. First functional test: `WebTestCase`

- `make:test` -> `WebTestCase`, homepage, no database
- `createClient()`, `request()`, `assertResponseIsSuccessful()`, `assertSelectorTextContains()`

## 3. Show page: the database problem

- Test an entity's show page
- Fails: test DB (`var/data_test.db`) is empty
- "Fix" by loading fixtures into the test env first - brittle: hardcoded ids/data, order-dependent, someone edits a fixture and tests break

## 4. Foundry in tests

- Same factories we already use for fixtures, now inside a test
- `ResetDatabase` + `Factories` traits: fresh schema, create exactly the state the test needs
- Rewrite chapter 3's test with `SomeFactory::createOne([...])`

## 5. Listing page

- `createMany()`, assert count of rows/items, ordering
- Maybe: only "published" items show up (a state the factory controls)

## 6. A form with the traditional client

- Submit a form with `selectButton()->form()`, `submit()`, `followRedirect()`
- Point out the pain: crawler juggling, field names like `thing[name]`, verbose assertions

## 7. `zenstruck/browser`

- Install, `HasBrowser` trait, rewrite chapter 6's form test fluently
- `visit()`, `fillField()`, `click()`, `assertOn()`, `assertSee()`
- Rewrite an earlier test too to show the contrast

## 8. Validation errors & redirects (new)

- Submit invalid data: assert error messages, status 422, nothing persisted (factory `::repository()->assert()->count()`)
- Happy path: `assertRedirectedTo()`, flash message

## 9. Debugging failing tests (new)

- `->dump()`, `->dd()`, `->saveSource()`
- Artifacts saved automatically on failure (`var/browser/...`)
- Exceptions: `expectException()` vs `throwExceptions()`

## 10. Emails (new)

- Booking a repair emails the reporter a confirmation ("Repair ticket SD-001 filed for USS Espresso")
- Emails go out through the `async` Messenger transport, but mailer-test collects them when they're queued: no worker needed
- `composer require --dev zenstruck/mailer-test`, `InteractsWithMailer`
- `$this->mailer()->assertSentEmailCount(1)->assertEmailSentTo(...)`: subject, body contains the ticket number, link to the ship
- Invalid submission -> `assertNoEmailSent()`
- Browser integration: assert emails mid-chain with `->use(function (MailerComponent $mailer) { ... })`

## 11. Messenger (new)

- Booking a *Critical* repair dispatches `SoundRedAlert`, routed to the `async` transport; its handler emails `engineering@shipshape.space`
- The red alert email never shows up in the test: the message just sits in the queue
- `composer require --dev zenstruck/messenger-test`, `test://` transport in `when@test`
- `InteractsWithMessenger`: `$this->transport('async')->queue()->assertContains(SoundRedAlert::class, 1)`
- The queue also holds the confirmation `SendEmailMessage`, so assert on the message class, not the total count
- `->process()` the queue, then assert the red alert email (reuses chapter 10)
- Normal priority -> no `SoundRedAlert` queued (`assertNotContains()`); mention `intercept=false` to handle messages immediately

## 12. Authentication: login form

- Log in through the real login form: `fillField()` + `click()`
- `assertAuthenticated()` / `assertNotAuthenticated()`, logout

## 13. `actingAs()` & access control

- Skip the form: `->actingAs($user)` with a `UserFactory` user
- Test the denied path too: anonymous -> redirect to login, wrong role -> 403

## 14. API testing with browser

- `get()`, `assertJson()`, `assertJsonMatches()` (needs `mtdowling/jmespath.php`)
- `->json()` to dig into the response

## 15. API auth: API key

- Unauthenticated -> 401
- `HttpOptions::json()->withHeader(...)` / reusable options

## 16. API create (POST)

- `post()` with a JSON body, assert 201 + response, assert it persisted
- Validation error -> 422 (reuses chapter 8's ideas)

## 17. JS testing: `PlaywrightBrowser` setup (new split)

- Something on the page that needs JS (Stimulus/Turbo/Live Component)
- Show it can't be tested with `KernelBrowser`
- `playwright-php/playwright-symfony` + `vendor/bin/playwright-install --browsers`
- `$this->playwrightBrowser()`: same API, real browser, requests stay in-process

## 18. JS testing: auto-waiting & debugging (new split)

- Auto-waiting for async UI, `BROWSER_DEFAULT_TIMEOUT`
- `PLAYWRIGHT_HEADLESS=false` to watch, pause into the Inspector, screenshots on failure
- Foundry + `actingAs()` still work, since it's all one process

## 19. Speed: DAMA DoctrineTestBundle

- Time the suite first: `ResetDatabase` re-creates the schema for every test
- Install DAMA: each test wrapped in a transaction, rolled back
- Time it again

## 20. Speed: ParaTest

- `vendor/bin/paratest`
- SQLite: each worker needs its own DB file (`TEST_TOKEN` in `DATABASE_URL`)
- Time it again

## Advanced bonus topics

- Running the suite (Playwright included) in CI with `playwright-php/setup-playwright`
- Profiler assertions (`withProfiling()`, query counts)
- Wrap-up: which kind of test to write for what (if not covered enough in chapter 1)

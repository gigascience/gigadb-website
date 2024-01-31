# Playwright

## Run tests locally

- First, make sure project is running locally on [http://gigadb.gigasciencejournal.com:9170](http://gigadb.gigasciencejournal.com:9170)

```sh
# cd to this folder
cd playwright
# install deps
npm i
# Run all tests
npm test
```

- Alternative: for VSCODE users, the [official Playwright VSCODE extension](https://playwright.dev/docs/getting-started-vscode) is recommended

## Run tests using Docker

Run single test
```
$ docker-compose run --rm playwright npm run test sanity.spec.js
> e2e@1.0.0 test
> npx playwright test sanity.spec.js
Running 3 tests using 1 worker
  3 passed (23.6s)
```

Run all tests
```
$ docker-compose run --rm playwright npm run test
> e2e@1.0.0 test
> npx playwright test
Running 71 tests using 1 worker
  71 passed (3.3m)
```

## Tags

- Update the `./tags.js` file to run different sets of tests

## Troubleshooting

- The time it takes to run tests can vary, if some unsuspecting tests timeout consistently, try to increase the timeout value in `playwright/playwright.config.js`
- If tests suddenly fail for no apparent reason, one possible cause is the need to recompile styles. Try to run `docker-compose run --rm less` and then run the tests again
- Docs for local debugging of failing tests: [https://playwright.dev/docs/debug](https://playwright.dev/docs/debug)
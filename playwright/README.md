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

## Troubleshooting

- The time it takes to run tests can vary, if some unsuspecting tests timeout consistently, try to increase the timeout value in `playwright/playwright.config.js`
- Docs for local debugging of failing tests: [https://playwright.dev/docs/debug](https://playwright.dev/docs/debug)
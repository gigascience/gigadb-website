# FUW Vue widget

## Dev

This project runs in the `js` container defined in the `ops/deployment/docker-compose.yml` file

the `up.sh` script installs dependencies via `npm install` and then the widget is built (`npm run build`) and deployed `npm run deploy`

- If you're reviewing a feature which implements changes in the Vue wigdet, you can simply rebuild once: `docker-compose run --rm js sh -c 'npm run build ; npm run deploy'` (no need to watch for changes).
- If you're editing Vue files and want to see the frontend update (after page refresh), run `watch-vue.sh`. You'll need to install `chokidar` globally: `npm install -g chokidar-cli`. This script will rebuild the project every time there is a change in the files inside the `./src` folder. If there's a change in the dependencies during development, you can `npm install` locally and then trigger a rebuild and redeploy by saving any file inside `./src`


## Test

Run all tests locally:

```sh
npm run test-local
```

Run specific test based on glob pattern:

```sh
env KARMA_SPECS="your-glob-pattern" npm run test-local
# e.g.
env KARMA_SPECS="test/FileUploader.spec.js" npm run test-local
```
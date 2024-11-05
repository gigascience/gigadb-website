# Vue client for project image location

## Usage

### Local dev

If you need to do local development, simply run the following commands (you will need `node` and `npm` installed on your machine):

```bash
cd vite
npm install
npm run dev
```

The app should be served from http://localhost:5173

Because the vite config is not using a index.html as entry point, no app is visible directly on that URL though.

Then make sure the entry point in the php application is using the local dev server

```php
<script type="module" src="http://localhost:5173/@vite/client"></script>
<script type="module" src="http://localhost:5173/src/main.ts"></script>
```

### Production

Run `./up.sh` script from root. This should bundle the app and copy it to the js folder for the php application to use.

To build the app in isolation from the rest of the commands, you can run the following commands from root:

```bash
NODE_VERSION=20.11.0 APPLICATION=../.. docker-compose run --rm vite-project-image-location-prod bash -c "npm install"
NODE_VERSION=20.11.0 APPLICATION=../.. docker-compose run --rm vite-project-image-location-prod
```

Make sure the entry point of the php application is using the bundled files. You can see an example in `protected/views/adminProject/_form.php`

## Tests

### unit tests

```sh
npm run test:unit
```

### e2e tests

From the root folder:

```sh
cd playwright
npm install
npm run test:single admin-project-logo-upload.spec.js
```

might need to run `npx playwright install` the first time

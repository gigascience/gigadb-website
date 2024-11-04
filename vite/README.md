# Vue client for project image location

## Tasks

- [x] add sr-only text to browse files button: upload logo, please upload one file as logo. maximum height, etc
- [x] adapt style to match gigadb theme, in the style.css file or in the component itself, or even better use directly the gigadb styles
- [ ] try to use variables.less for colors
- [x] create adds a new image to project
- [x] update overrides image (delete old and create new)
- [x] delete deletes image
- [x] refactor and delete logs
- [x] run related test suites
- [-] write new tests to cover new functionality
- [x] should work in production (i.e. prod container)
- [ ] replace local values by production values

## Usage

## Local dev

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
NODE_VERSION=20.11.0 APPLICATION=../..docker-compose run --rm vite-project-image-location-prod
```

Make sure the entry point of the php application is using the bundled files. You can see an example in `protected/views/adminProject/_form.php`
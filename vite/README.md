# Vue client for project image location

## Tasks

- [x] add sr-only text to browse files button: upload logo, please upload one file as logo. maximum height, etc
- [x] adapt style to match gigadb theme, in the style.css file or in the component itself, or even better use directly the gigadb styles
- [ ] try to use variables.less for colors
- [x] create adds a new image to project
- [x] update overrides image (delete old and create new)
- [x] delete deletes image
- [x] refactor and delete logs
- [ ] run related test suites
- [ ] write new tests to cover new functionality
- [ ] should work in production (i.e. prod container)
- [ ] replace local values by production values

## Usage

### Dev

Run `./up.sh` script from root. This should build and run the docker container with the vite dev server and hot reloading

Alternatively, run and build isolated from command line:

Useful docker commands:

```bash
# (re)build and run container (e.g. if Dockerfile changed)
NODE_VERSION=20.11.0 APPLICATION=../.. docker-compose -f ops/deployment/docker-compose.yml up --build vite-project-image-location-dev -d
# run container
NODE_VERSION=20.11.0 APPLICATION=../.. docker-compose -f ops/deployment/docker-compose.yml up vite-project-image-location-dev -d
# stop container
docker-compose down vite-project-image-location-dev
# remove dangling images after building
docker image prune -f
# remove dangling volumes
docker volume prune -f
# Full cleanup
docker system prune -a -f --volumes
```

Dependencies are installed within the container. To install a new dependency:

```bash
# access the container (run from root folder)
docker-compose exec vite-project-image-location-dev sh
# install new dependency
npm install <package-name>
```

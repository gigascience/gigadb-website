# FUW Vue widget

## Dev

This project runs in the `js` container defined in the `ops/deployment/docker-compose.yml` file

the `up.sh` script installs dependencies via `npm install` and then the widget is built (`npm run build`) and deployed `npm run deploy`

Running the script `watch-vue.sh` will rebuild the project every time there is a change in the files inside the `./src` folder. If there's a change in the dependencies during development, you can `npm install` locally and then trigger a rebuild and redeploy by saving any file inside `./src`
# Get base image of Node version 16
FROM node:16

# Get Playwright
# It is recommended to always pin your Docker image to a specific version if possible.
# If Playwright version in your Docker image does not match the version in your
# project/tests, Playwright will be unable to locate browser executables.
FROM mcr.microsoft.com/playwright:v1.39.0-focal

# Set working directory
WORKDIR /app

# Set environment path to node_modules/.bin
ENV PATH /app/node_modules/.bin:$PATH

# COPY needed files to /app in Docker image
COPY package.json /app/
COPY playwright.config.js /app/

# Get required libraries to run Playwright
RUN apt-get update && apt-get -y install libnss3 libatk-bridge2.0-0 libdrm-dev libxkbcommon-dev libgbm-dev libasound-dev libatspi2.0-0 libxshmfence-dev

# Install dependencies in node environment
RUN npm install

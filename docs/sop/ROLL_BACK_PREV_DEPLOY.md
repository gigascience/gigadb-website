# SOP: Roll back to previous deployment

There might be a situation in which we want the beta.gigadb.org website to run
`gigadb-website` repository code from a previous deployment state.

## Prerequisites

It is assumed there is a running beta.gigadb.org website.


## Procedure

Go to the [Pipelines](https://gitlab.com/gigascience/upstream/gigadb-website/-/pipelines)
page for the Upstream gigadb-website project.

Identify a previous successfully deployment of the `live` environment for the
beta.gigadb.org website. This should contain a pipeline with 9 **passed** 
stages.

Click on the final stage of the pipeline and select `ld_teardown`. This will 
stop and remove containers defined in the `docker-compose` file.

Click on the final stage of the pipeline and select `ld_deploy` to re-deploy the
code that this pipeline is associated with onto the beta-gigadb.org server.

Go to https://beta.gigadb.org in your browser to check the site is running.

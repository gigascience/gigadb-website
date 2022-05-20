# SOP: Roll back to previous deployment

There might be a situation in which we want the beta.gigadb.org website to run
`gigadb-website` repository code from a previous deployment state.

## Prerequisites

It is assumed there is a running beta.gigadb.org website.


## Procedure

1. Go to the [Pipelines](https://gitlab.com/gigascience/upstream/gigadb-website/-/pipelines)
page for the Upstream gigadb-website project.
2. Identify a previous successfully deployment of the `live` environment for the
beta.gigadb.org website. This should contain a pipeline with 9 **passed** 
stages.
3. We now need to decide if the docker volumes need to be removed as part of
this roll back procedure. If so, then trigger the `ld_teardown` job in the 
pipeline. N.B. triggering the `ld_teardown` job will delete all Docker volumes
including the Let’s Encrypt one, which will mean a new deployment will create a 
new certificate. More often than not, `ld_teardown` is not required but there
might be situations that it is desirable to execute this step.
4. Click on the final stage of the pipeline and select `ld_deploy` to re-deploy 
the code that this pipeline is associated with onto the beta-gigadb.org server.
5. Go to https://beta.gigadb.org in your browser to check the live website is 
running.

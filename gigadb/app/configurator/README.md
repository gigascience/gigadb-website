# About configurator

This project contains the bash script to help with configuring Gigascience webapps and tools.
It helps with creating `.env`, `.secrets` and interpolate configuration template (`*.dist`).
It was created to avoid duplicating the same logic in every projects.

# How to test

Ensure you have `bats` installed (e.g: on macOS, you could do `brew install bats-core`)
Then run:
```
$ bats tests
```

# How to use

In a new project, create an executable file called `configure`
Then source the `dotfiles.sh` script inside it.
Then you can use the methods to generate dot files and substitute variable in configuration templates.
An example can be found at `gigadb/app/tools/files-metadata-console/configure`
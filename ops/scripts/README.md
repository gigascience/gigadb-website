# Scripts

Configuration scripts go here.

The main script, `generate_config.sh`, will apply environment variables to the 
legacy Chef templates.

The other one, `restart_php.sh` is used by the acceptance tests to restart the 
PHP container after the database as been reset.
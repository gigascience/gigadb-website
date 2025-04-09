The files within this folder are related to the "Accession links", see also https://github.com/gigascience/gigadb-website/issues/17

---

The [bioregistry.io](https://bioregistry.io/) is a resource resolver, so the links should take this form:

https://bioregistry.io/{resource_prefix}:{local_identifier}

Example:

https://bioregistry.io/chebi:138488

There are two ways to resolve the links:

- A. relying gon the bioregistry for resource resolution (trivial)
- B. retrieving prefixes and uri_formats from the registry API and integrate them in our db (more steps, requires updating production db)

# Option A

This option is the one I enabled for now. In `protected/models/Link.php` the links are all returned using the format `https://bioregistry.io/{resource_prefix}:{local_identifier}`

Option A makes no use of the table prefix to generate accession links

If one resource `{resource_prefix}:{local_identifier}` is incorrect, the user will see a 404, e.g. https://bioregistry.io/lorem:ipsum

To "disable" this option simply comment line `return "https://bioregistry.io/$trimmedLink";` in `protected/models/Link.php`

# Option B

This involves retrieving uri_formats from the bioregistry API to build the links ourselves. The `protected/models/Link.php` is refactored to adapt to the format from the API, but existing links / prefixes will work so it is backwards compatible

## Setup

Purpose: fetch and insert bioregistry prefixes into the database *once*. Emphasis: It is expected that this needs to be done only once. In particular running the SQL script muliple times will lead to duplicate data (e.g. there is no condition in the table for unique prefix+source), so run it only once

Note: `cd` into this directory to run the scripts

1. Fetch bioregistry urls from bioregistry.io API and paste them into a file
  - bioregistries are already fetched and this script takes a while to complete because it involves almost 2,000 API calls, only do this if you need to refretch them for some reason
  - `python fetch-bioregistries.py > bioregistries.txt`
  - Unclear what is the optimal way to resolve the uri_formats as each resource has multiple providers. The script assumes it is just the `uri_format` property from the response
2. Generate SQL from bioregistries `python process-bioregistries.py`
3. Retrieve character count of longest prefix found in the bioregistry (`awk -F: '{ if (length($1) > max) max = length($1) } END { print max }' bioregistries.txt`)
4. Alter prefix table to have enough room for longest prefix (run `alter_prefix.sql`, make sure the max column length is above the longest prefix --24)
5. Run generated SQL script (`insert_bioregistry_prefixes.sql`) to insert bioregistry urls to the prefix table

This can be done in the local dev db for testing, and once the behavior is cofirmed, the SQL script should be run once on the production db


## What are prefixes?

Two relevant tables in the db: prefix and link. The link table has a link value looking like this: `ENA:PRJEB225`. These are really two values separated by a colon: `prefix:value`. The prefix is used to connect the value to a URL in the prefix table

So the `url` from the prefix table and the value from the link table are used to create a full url

The `Link` model (`protected/models/Link.php`) handles the building of the full url with the `getFullUrl` function

Some urls have a placeholder `$1`, this gets replaced by the value in `getFullUrl`. If `$1` is not present, the value is simply appended. The `$1` comes from the bioregistry.io API


## Manual testing

- Update the DB as described above
- Go to http://gigadb.gigasciencejournal.com/adminLink/create (or the url for the environment you are testing)
- in the `link` field, input this format: `prefix:value`
  - prefix: any prefix from the prefix table
  - value: any arbitrary value (obviosuly a random value will most likely result in a broken link)
- select a dataset from the dropdown, keep note of it, and click "create"
- In the dataset page for the dataset you updated (e.g. http://gigadb.gigasciencejournal.com/dataset/100020) you should see a new accession link with a url correctly derived from the prefix url and the value
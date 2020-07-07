PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb postgres

\c gigadbv3_20200210;

\copy (select * from dataset where id = 41 or id = 27 or id = 29) To '/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/data/dev/dataset.csv' With (FORMAT CSV, HEADER)

\copy (select * from publisher where id in (select publisher_id from dataset where id = 41 or id = 27)) To '/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/data/dev/publisher.csv' With (FORMAT CSV, HEADER)

\copy (select * from manuscript where dataset_id = 41) To '/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/data/dev/manuscript.csv' With (FORMAT CSV, HEADER)

\copy (select * from dataset_type where dataset_id = 41) To '/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/data/dev/dataset_type.csv' With (FORMAT CSV, HEADER)

\copy (select * from "type" where id in (select type_id from dataset_type where dataset_id = 41)) To '/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/data/dev/type.csv' With (FORMAT CSV, HEADER)

\copy (select * from external_link where dataset_id = 41) To '/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/data/dev/external_link.csv' With (FORMAT CSV, HEADER)

\copy (select * from "external_link_type" where id in (select external_link_type_id from external_link where dataset_id = 41)) To '/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/data/dev/external_link_type.csv' With (FORMAT CSV, HEADER)

\copy (select * from dataset_funder where dataset_id = 41) To '/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/data/dev/dataset_funder.csv' With (FORMAT CSV, HEADER)

\copy (select * from funder_name where id in (select funder_id from dataset_funder where dataset_id = 41)) To '/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/data/dev/funder_name.csv' With (FORMAT CSV, HEADER)

\copy (select * from link where dataset_id = 41) To '/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/data/dev/link.csv' With (FORMAT CSV, HEADER)

\copy (select * from dataset_log where dataset_id = 41) To '/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/data/dev/dataset_log.csv' With (FORMAT CSV, HEADER)

\copy (select * from curation_log where dataset_id = 41) To '/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/data/dev/curation_log.csv' With (FORMAT CSV, HEADER)

\copy (select * from dataset_project where dataset_id = 41) To '/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/data/dev/dataset_project.csv' With (FORMAT CSV, HEADER)

\copy (select * from project where id in (select project_id from dataset_project where dataset_id = 41)) To '/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/data/dev/project.csv' With (FORMAT CSV, HEADER)

\copy (select * from image where id in (select image_id from dataset where id = 41)) To '/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/data/dev/image.csv' With (FORMAT CSV, HEADER)

\copy (select * from dataset_attributes where dataset_id = 259) To '/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/data/dev/dataset_attributes.csv' With (FORMAT CSV, HEADER)

\copy (select * from relation where dataset_id = 41) To '/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/data/dev/relation.csv' With (FORMAT CSV, HEADER)

\copy (select * from relationship where id in (select relationship_id from relation where dataset_id = 41)) To '/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/data/dev/relationship.csv' With (FORMAT CSV, HEADER)

\copy (select * from sample_rel where relationship_id in (select relationship_id from relation where dataset_id = 41)) To '/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/data/dev/sample_rel.csv' With (FORMAT CSV, HEADER)

\copy (select * from dataset_author where dataset_id = 41) To '/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/data/dev/dataset_author.csv' With (FORMAT CSV, HEADER)

\copy (select * from gigadb_user where id in (select submitter_id from dataset where id = 41) or id in (select curator_id from dataset where id = 41)) To '/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/data/dev/gigadb_user.csv' With (FORMAT CSV, HEADER)

\copy (select * from author where id in (select author_id from dataset_author where dataset_id = 41)) To '/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/data/dev/author.csv' With (FORMAT CSV, HEADER)

\copy (select * from "search" where user_id in (select submitter_id from dataset where id = 41) or id in (select curator_id from dataset where id = 41)) To '/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/data/dev/search.csv' With (FORMAT CSV, HEADER)

\copy (select * from dataset_sample where dataset_id = 41) To '/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/data/dev/dataset_sample.csv' With (FORMAT CSV, HEADER)

\copy (select * from sample where id in (select sample_id from dataset_sample where dataset_id = 41)) To '/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/data/dev/sample.csv' With (FORMAT CSV, HEADER)

\copy (select * from species where id in (select species_id from sample where id in (select sample_id from dataset_sample where dataset_id = 41))) To '/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/data/dev/species.csv' With (FORMAT CSV, HEADER)

\copy (select * from alternative_identifiers where sample_id in (select sample_id from dataset_sample where dataset_id = 41)) To '/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/data/dev/alternative_identifiers.csv' With (FORMAT CSV, HEADER)

\copy (select * from extdb where id in (select extdb_id from alternative_identifiers where sample_id in (select sample_id from dataset_sample where dataset_id = 41))) To '/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/data/dev/extdb.csv' With (FORMAT CSV, HEADER)

\copy (select * from experiment where dataset_id = 40) To '/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/data/dev/experiment.csv' With (FORMAT CSV, HEADER)

\copy (select * from sample_experiment where experiment_id in (select id from experiment where dataset_id = 40)) To '/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/data/dev/sample_experiment.csv' With (FORMAT CSV, HEADER)

\copy (select * from sample_attribute where sample_id in (select sample_id from dataset_sample where dataset_id = 196)) To '/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/data/dev/sample_attribute.csv' With (FORMAT CSV, HEADER)

\copy (select * from attribute where id in (select attribute_id from sample_attribute where sample_id in (select sample_id from dataset_sample where dataset_id = 196))) To '/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/data/dev/attribute.csv' With (FORMAT CSV, HEADER)

\copy (select * from file where dataset_id = 41) To '/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/data/dev/file.csv' With (FORMAT CSV, HEADER)

\copy (select * from file_format where id in (select format_id from file where dataset_id = 40)) To '/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/data/dev/file_format.csv' With (FORMAT CSV, HEADER)

\copy (select * from file_type where id in (select type_id from file where dataset_id = 40)) To '/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/data/dev/file_type.csv' With (FORMAT CSV, HEADER)

\copy (select * from file_attributes where file_id in (select id from file where dataset_id = 40)) To '/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/data/dev/file_attributes.csv' With (FORMAT CSV, HEADER)

\copy (select * from file_relationship where file_id in (select id from file where dataset_id = 40)) To '/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/data/dev/file_relationship.csv' With (FORMAT CSV, HEADER)

\copy (select * from file_sample where file_id in (select id from file where dataset_id = 40)) To '/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/data/dev/file_sample.csv' With (FORMAT CSV, HEADER)

\copy (select * from file_experiment where file_id in (select id from file where dataset_id = 40)) To '/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/data/dev/file_experiment.csv' With (FORMAT CSV, HEADER)

\copy (select * from exp_attributes where exp_id in (select id from experiment where dataset_id = 40)) To '/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/data/dev/exp_attributes.csv' With (FORMAT CSV, HEADER)

\copy (select * from unit where id in (select units_id from exp_attributes where exp_id in (select id from experiment where dataset_id = 40))) To '/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/data/dev/unit.csv' With (FORMAT CSV, HEADER)

\copy (select * from news) To '/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/data/dev/news.csv' With (FORMAT CSV, HEADER)

\copy (select * from prefix) To '/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/data/dev/prefix.csv' With (FORMAT CSV, HEADER)

\copy (select * from rss_message) To '/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/data/dev/rss_message.csv' With (FORMAT CSV, HEADER)

\copy (select * from search) To '/Volumes/PLEXTOR/PhpstormProjects/gigadb-website/data/dev/search.csv' With (FORMAT CSV, HEADER)


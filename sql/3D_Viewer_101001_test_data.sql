-- to be loaded in production_like.pgdmp
insert into external_link_type(id, name) values(5, '3D Models');
insert into external_link(dataset_id, url, external_link_type_id) values(80,'https://sketchfab.com/models/ea49d0dd500647cbb4b61ad5ca9e659a',5);

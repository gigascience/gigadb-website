-- to be loaded in production_like.pgdmp
insert into external_link_type(id, name) values(7, 'USCS Tumour Map Viewer');
insert into external_link(dataset_id, url, external_link_type_id) values(80,'https://tumormap.ucsc.edu/?bookmark=7bec77b55a01ef217a7f62d505cb0470fb1345c248324a213d92055be8ee4ee1',7);

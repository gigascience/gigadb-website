-- to be loaded in production_like.pgdmp
insert into external_link_type(id, name)
select 5, '3D Models'
where not exists (
    select 1 from external_link_type
    where id = 5 and name = '3D Models'
);
insert into external_link(dataset_id, url, external_link_type_id) values(80,'https://s3.ap-northeast-1.wasabisys.com/test-gigadb-datasets/3d-models/100006/GeoB8502_865cm_Shell-4.obj',5);

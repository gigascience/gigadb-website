-- to be loaded in production_like.pgdmp
insert into external_link_type(id, name) values(4, 'JBrowse');
insert into external_link(dataset_id, url, external_link_type_id) values(80,'http://penguin.genomics.cn/jbrowse/index.html?data=100240&loc=PIN_chr1%3A10455684..94134086&tracks=DNA',4);

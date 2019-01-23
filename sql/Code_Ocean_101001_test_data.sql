-- to be loaded in production_like.pgdmp
insert into external_link_type(id, name) values(6, 'Code Ocean');
insert into external_link(dataset_id, url, external_link_type_id) values(80,'<script src="https://codeocean.com/widget.js?id=fceb0521-a26d-441f-9fe0-bccc6a250fc9" async></script>',6);

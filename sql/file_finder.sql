create materialized view if not exists file_finder as
SELECT f.*, fs.sample_id as sample_id, ff.name as file_format, ft.name as file_type, d.upload_status as upload_status, coalesce(replace(f.name,'.',' '),'') || coalesce(f.description,'') || coalesce(a.attribute_name,'') || coalesce(fa.value,'') || coalesce(d.title,'') as document
FROM file f
         left join file_sample fs on f.id = fs.file_id
         left join dataset d on d.id = f.dataset_id
         left join file_attributes fa on f.id = fa.file_id
         left join attribute a on a.id = fa.attribute_id
         left join file_type ft on f.type_id = ft.id
         left join file_format ff on f.format_id = ff.id ;

create index if not exists file_finder_search_idx on file_finder using GIN (to_tsvector('english',document));
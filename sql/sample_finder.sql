create materialized view if not exists sample_finder as
select s.id as id, ds.dataset_id as dataset_id, sp.scientific_name as scientific_name, d.upload_status as upload_status, coalesce(s.name,'') || coalesce(s.consent_document, '') || coalesce(s.contact_author_name, '') || coalesce(sp.common_name, '') || coalesce(sp.genbank_name,'') || coalesce(sp.scientific_name, '') || coalesce(a.attribute_name,'') || coalesce(sa.value,'') as document
from sample s
         left join dataset_sample ds on ds.sample_id = s.id
         left join species sp on sp.id = s.species_id
         left join dataset d on d.id = ds.dataset_id
         left join sample_attribute sa on sa.sample_id = s.id
         left join attribute a on sa.attribute_id = a.id;

create index sample_finder_search_idx on sample_finder using GIN (to_tsvector('english',document));
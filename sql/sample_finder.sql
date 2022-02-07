create materialized view if not exists sample_finder as
select s.id as id, s.name as name, sp.common_name as species_common_name, sp.tax_id as species_tax_id , ds.dataset_id as dataset_id, d.identifier as dataset_identifer, sp.scientific_name as species_scientific_name, d.upload_status as upload_status, coalesce(s.name,'') || coalesce(s.consent_document, '') || coalesce(s.contact_author_name, '') || coalesce(sp.common_name, '') || coalesce(sp.genbank_name,'') || coalesce(sp.scientific_name, '') || coalesce(a.attribute_name,'') || coalesce(sa.value,'') || coalesce(d.title,'') as document
from sample s
         left join dataset_sample ds on ds.sample_id = s.id
         left join species sp on sp.id = s.species_id
         left join dataset d on d.id = ds.dataset_id
         left join sample_attribute sa on sa.sample_id = s.id
         left join attribute a on sa.attribute_id = a.id;

create index sample_finder_search_idx on sample_finder using GIN (to_tsvector('english',document));
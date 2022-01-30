create materialized view if not exists dataset_finder as
	with dataset_author_fullnames as (
		select dataset_id, string_agg(a.surname || ' '||a.first_name||' ' || a.middle_name, ';') as names
		from dataset_author da, author a
		where da.author_id = a.id
		group by dataset_id
	)

	, dataset_author_initialednames as (
		select dataset_id, string_agg(a.surname||', '||substring(a.first_name,1,1)||', '||substring(a.middle_name,1,1), ';') as names
		from dataset_author da, author a
		where da.author_id = a.id
		group by dataset_id
	)

	, dataset_keywords as (
		select dataset_id, string_agg(value, ';') as keywords
		from dataset_attributes
		where attribute_id=455
		group by dataset_id
	)

	, dataset_projects as (
		select dataset_id, string_agg(p.name, ',') as names
		from dataset_project dp, project p
		where dp.project_id = p.id
		group by dataset_id
	)

	, dataset_types as (
		select dt.dataset_id, string_agg(t.name,';') as types
		from type t, dataset_type dt
		where dt.type_id=t.id
		group by dt.dataset_id
	)

	, external_links as (
		select el.dataset_id, string_agg(el.url || ' (' || elt.name || ')',';') as external_links
		from external_link_type  elt, external_link el
		where el.external_link_type_id=elt.id
		group by el.dataset_id
	)

select d.id as id,
       d.upload_status as upload_status,
       d.publication_date as publication_date,
       coalesce(d.identifier,'') || coalesce(d.title,'') || coalesce(daf.names,'') || coalesce(dai.names,'') || coalesce(d.description,'') || coalesce(dt.types,'') || coalesce(dk.keywords,'') || coalesce(dp.names,'') || coalesce(m.identifier,'') || coalesce(m.pmid::varchar,'') || coalesce(df.grant_award, '') || coalesce(df.comments,'') || coalesce(fn.primary_name_display,'') || coalesce(el.external_links,'') as document
from dataset d
         left join manuscript m on d.id = m.dataset_id
         left join dataset_funder df on df.dataset_id = d.id
         left join funder_name fn on fn.id = df.funder_id
         left join dataset_author_fullnames daf on daf.dataset_id = d.id
         left join dataset_author_initialednames dai on dai.dataset_id = d.id
         left join dataset_keywords dk on dk.dataset_id = d.id
         left join dataset_projects dp on dp.dataset_id = d.id
         left join dataset_types dt on dt.dataset_id = d.id
         left join external_links el on el.dataset_id = d.id;

create index if not exists dataset_finder_search_idx on dataset_finder using GIN (to_tsvector('english',document));
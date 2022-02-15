<?php

/**
 * Class m220127_150844_search_materialized_views_gin_indexes
 *
 * migrations to enable fast full text search
 */
class m220127_150844_search_materialized_views_gin_indexes extends CDbMigration
{



	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
        //Ensure full-text search uses english dictionary on all environments
	    Yii::app()->db->createCommand("alter role gigadb in database gigadb SET default_text_search_config TO 'pg_catalog.english'")->execute();

	    //First query
        Yii::app()->db->createCommand("drop materialized view if exists file_finder")->execute();
	    Yii::app()->db->createCommand("create materialized view file_finder as
SELECT f.*, fs.sample_id as sample_id, ff.name as file_format, ft.name as file_type, d.upload_status as upload_status, coalesce(replace(f.name,'.',' '),'') || coalesce(f.description,'') || coalesce(a.attribute_name,'') || coalesce(fa.value,'') || coalesce(d.title,'') as document
	FROM file f
	left join file_sample fs on f.id = fs.file_id
	left join dataset d on d.id = f.dataset_id
	left join file_attributes fa on f.id = fa.file_id
	left join attribute a on a.id = fa.attribute_id
	left join file_type ft on f.type_id = ft.id
	left join file_format ff on f.format_id = ff.id")->execute();

        Yii::app()->db->createCommand("drop index if exists file_finder_search_idx")->execute();
	    Yii::app()->db->createCommand("create index file_finder_search_idx on file_finder using GIN (to_tsvector('english',document))")->execute();

	    //Second query
        Yii::app()->db->createCommand("drop materialized view if exists sample_finder")->execute();
        Yii::app()->db->createCommand("create materialized view sample_finder as
	select s.id as id, s.name as name, sp.common_name as species_common_name, sp.tax_id as species_tax_id , ds.dataset_id as dataset_id, d.identifier as dataset_identifer, sp.scientific_name as species_scientific_name, d.upload_status as upload_status, coalesce(s.name,'') || coalesce(s.consent_document, '') || coalesce(s.contact_author_name, '') || coalesce(sp.common_name, '') || coalesce(sp.genbank_name,'') || coalesce(sp.scientific_name, '') || coalesce(a.attribute_name,'') || coalesce(sa.value,'') || coalesce(d.title,'') as document 
	from sample s
		left join dataset_sample ds on ds.sample_id = s.id
		left join species sp on sp.id = s.species_id
		left join dataset d on d.id = ds.dataset_id
		left join sample_attribute sa on sa.sample_id = s.id
		left join attribute a on sa.attribute_id = a.id")->execute();

        Yii::app()->db->createCommand("drop index if exists sample_finder_search_idx")->execute();
        Yii::app()->db->createCommand("create index sample_finder_search_idx on sample_finder using GIN (to_tsvector('english',document))")->execute();

        //Third query
        Yii::app()->db->createCommand("drop materialized view if exists dataset_finder")->execute();
        Yii::app()->db->createCommand("create materialized view dataset_finder as
	with dataset_author_fullnames as (
		select dataset_id, string_agg(coalesce(a.surname,'') || ' '||coalesce(a.first_name,'')||' ' || coalesce(a.middle_name,''), ';') as names 
		from dataset_author da, author a 
		where da.author_id = a.id 
		group by dataset_id
	)

	, dataset_author_initialednames as (
		select dataset_id, string_agg(coalesce(a.surname,'') ||' '||substring(coalesce(a.first_name,''),1,1) || substring(coalesce(a.middle_name,''),1,1), '; ') as names 
		from dataset_author da, author a 
		where da.author_id = a.id 
		group by dataset_id
	)

	, dataset_author_linkednames as (
		select dataset_id, string_agg('<a class=result-sub-links href=/search/new?keyword=' || coalesce(a.surname,'') ||','||substring(coalesce(a.first_name,''),1,1) || substring(coalesce(a.middle_name,''),1,1) || '&author_id=' || a.id || '>'|| coalesce(a.surname,'') ||' '|| substring(coalesce(a.first_name,''),1,1) || substring(coalesce(a.middle_name,''),1,1) || '</a>' , '; ') as authorlinks 
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
		d.identifier as identifier,
		d.upload_status as upload_status, 
		d.publication_date as publication_date,
	    'published:' || to_char(d.publication_date,'YYYY-MM-DD') as dayofpublication, 
	    'published:' || to_char(d.publication_date,'YYYY-MM') as monthofpublication, 
	    'published:' || to_char(d.publication_date,'YYYY') as yearofpublication,		 
		'/dataset/' || d.identifier as shorturl,
		dal.authorlinks as authornames,
		d.title as title,
		d.description as description,
		coalesce(d.identifier,'') || ' ' || coalesce(d.title,'') || ' ' || coalesce(dt.types,'') || ' ' || coalesce(daf.names,'') || ' ' || coalesce(dai.names,'') || ' ' || coalesce(d.description,'') || ' ' || coalesce(dt.types,'') || ' ' || coalesce(dk.keywords,'') || ' ' || coalesce(dp.names,'') || ' ' || coalesce(m.identifier,'') || ' '  || coalesce(m.pmid::varchar,'') || ' ' || coalesce(df.grant_award, '') || ' ' || coalesce(df.comments,'') || ' ' || coalesce(fn.primary_name_display,'') || ' ' || coalesce(el.external_links,'') || ' ' || 'published:' || to_char(d.publication_date,'YYYY-MM-DD') || ',' || to_char(d.publication_date,'YYYY-MM') || ',' || to_char(d.publication_date,'YYYY') as document
	from dataset d
	left join manuscript m on d.id = m.dataset_id
	left join dataset_funder df on df.dataset_id = d.id
	left join funder_name fn on fn.id = df.funder_id
	left join dataset_author_fullnames daf on daf.dataset_id = d.id
	left join dataset_author_initialednames dai on dai.dataset_id = d.id
	left join dataset_author_linkednames dal on dal.dataset_id = d.id
	left join dataset_keywords dk on dk.dataset_id = d.id
	left join dataset_projects dp on dp.dataset_id = d.id
	left join dataset_types dt on dt.dataset_id = d.id
	left join external_links el on el.dataset_id = d.id")->execute();


        Yii::app()->db->createCommand("drop index if exists dataset_finder_search_idx")->execute();
        Yii::app()->db->createCommand("create index dataset_finder_search_idx on dataset_finder using GIN (to_tsvector('english',document))")->execute();

    }

	public function safeDown()
	{
	    Yii::app()->db->createCommand("drop materialized view if exists file_finder;")->execute();
	    Yii::app()->db->createCommand("drop index if exists file_finder_search_idx")->execute();
        Yii::app()->db->createCommand("drop materialized view if exists sample_finder;")->execute();
        Yii::app()->db->createCommand("drop index if exists sample_finder_search_idx")->execute();
        Yii::app()->db->createCommand("drop materialized view if exists dataset_finder;")->execute();
        Yii::app()->db->createCommand("drop index if exists dataset_finder_search_idx")->execute();
	}

}
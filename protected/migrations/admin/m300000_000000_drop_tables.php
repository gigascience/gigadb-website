<?php

 /**
 * Remove objects in Postgresql database in preparation
 * for running Yii schema and data migrations
 *
 * @author  peter+github@gigasciencejournal.com
 */
class m300000_000000_drop_tables extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS show_accession;");
        $this->execute("DROP VIEW IF EXISTS show_manuscript;");
        $this->execute("DROP VIEW IF EXISTS show_project;");
        $this->execute("DROP VIEW IF EXISTS show_externallink;");
        $this->execute("DROP VIEW IF EXISTS show_file;");
        $this->execute("DROP VIEW IF EXISTS homepage_dataset_type;");
        $this->execute("DROP VIEW IF EXISTS file_number;");
        $this->execute("DROP VIEW IF EXISTS sample_number;");

        $this->execute("DROP SEQUENCE IF EXISTS relationship_id_seq CASCADE;");
        $this->execute("DROP SEQUENCE IF EXISTS link_prefix_id_seq CASCADE;");
        $this->execute("DROP SEQUENCE IF EXISTS extdb_id_seq CASCADE;");
        $this->execute("DROP SEQUENCE IF EXISTS species_id_seq CASCADE;");
        $this->execute("DROP SEQUENCE IF EXISTS gigadb_user_id_seq CASCADE;");
        $this->execute("DROP SEQUENCE IF EXISTS sample_id_seq CASCADE;");
        $this->execute("DROP SEQUENCE IF EXISTS alternative_identifiers_id_seq CASCADE;");
        $this->execute("DROP SEQUENCE IF EXISTS attribute_id_seq CASCADE;");
        $this->execute("DROP SEQUENCE IF EXISTS author_id_seq CASCADE;");
        $this->execute("DROP SEQUENCE IF EXISTS author_rel_id_seq CASCADE;");
        $this->execute("DROP SEQUENCE IF EXISTS image_id_seq CASCADE;");
        $this->execute("DROP SEQUENCE IF EXISTS publisher_id_seq CASCADE;");
        $this->execute("DROP SEQUENCE IF EXISTS dataset_id_seq CASCADE;");
        $this->execute("DROP SEQUENCE IF EXISTS curation_log_id_seq CASCADE;");
        $this->execute("DROP SEQUENCE IF EXISTS dataset_attributes_id_seq CASCADE;");
        $this->execute("DROP SEQUENCE IF EXISTS dataset_author_id_seq CASCADE;");
        $this->execute("DROP SEQUENCE IF EXISTS funder_name_id_seq CASCADE;");
        $this->execute("DROP SEQUENCE IF EXISTS dataset_funder_id_seq CASCADE;");
        $this->execute("DROP SEQUENCE IF EXISTS dataset_log_id_seq CASCADE;");
        $this->execute("DROP SEQUENCE IF EXISTS project_id_seq CASCADE;");
        $this->execute("DROP SEQUENCE IF EXISTS dataset_project_id_seq CASCADE;");
        $this->execute("DROP SEQUENCE IF EXISTS dataset_sample_id_seq CASCADE;");
        $this->execute("DROP SEQUENCE IF EXISTS dataset_session_id_seq CASCADE;");
        $this->execute("DROP SEQUENCE IF EXISTS type_id_seq CASCADE;");
        $this->execute("DROP SEQUENCE IF EXISTS dataset_type_id_seq CASCADE;");
        $this->execute("DROP SEQUENCE IF EXISTS experiment_id_seq CASCADE;");
        $this->execute("DROP SEQUENCE IF EXISTS exp_attributes_id_seq CASCADE;");
        $this->execute("DROP SEQUENCE IF EXISTS external_link_type_id_seq CASCADE;");
        $this->execute("DROP SEQUENCE IF EXISTS external_link_id_seq CASCADE;");
        $this->execute("DROP SEQUENCE IF EXISTS file_format_id_seq CASCADE;");
        $this->execute("DROP SEQUENCE IF EXISTS file_type_id_seq CASCADE;");
        $this->execute("DROP SEQUENCE IF EXISTS file_id_seq CASCADE;");
        $this->execute("DROP SEQUENCE IF EXISTS file_attributes_id_seq CASCADE;");
        $this->execute("DROP SEQUENCE IF EXISTS file_experiment_id_seq CASCADE;");
        $this->execute("DROP SEQUENCE IF EXISTS relationship_id_seq CASCADE;");
        $this->execute("DROP SEQUENCE IF EXISTS file_relationship_id_seq CASCADE;");
        $this->execute("DROP SEQUENCE IF EXISTS file_sample_id_seq CASCADE;");
        $this->execute("DROP SEQUENCE IF EXISTS link_id_seq CASCADE;");
        $this->execute("DROP SEQUENCE IF EXISTS manuscript_id_seq CASCADE;");
        $this->execute("DROP SEQUENCE IF EXISTS news_id_seq CASCADE;");
        $this->execute("DROP SEQUENCE IF EXISTS relation_id_seq CASCADE;");
        $this->execute("DROP SEQUENCE IF EXISTS rss_message_id_seq CASCADE;");
        $this->execute("DROP SEQUENCE IF EXISTS sample_attribute_id_seq CASCADE;");
        $this->execute("DROP SEQUENCE IF EXISTS sample_experiment_id_seq CASCADE;");
        $this->execute("DROP SEQUENCE IF EXISTS sample_rel_id_seq CASCADE;");
        $this->execute("DROP SEQUENCE IF EXISTS search_id_seq CASCADE;");
        $this->execute("DROP SEQUENCE IF EXISTS user_command_id_seq CASCADE;");

        $this->execute("DROP TABLE IF EXISTS user_command CASCADE;");
        $this->execute("DROP TABLE IF EXISTS search CASCADE;");
        $this->execute("DROP TABLE IF EXISTS sample_rel CASCADE;");
        $this->execute("DROP TABLE IF EXISTS sample_experiment CASCADE;");
        $this->execute("DROP TABLE IF EXISTS sample_attribute CASCADE;");
        $this->execute("DROP TABLE IF EXISTS rss_message CASCADE;");
        $this->execute("DROP TABLE IF EXISTS relation CASCADE;");
        $this->execute("DROP TABLE IF EXISTS prefix CASCADE;");
        $this->execute("DROP TABLE IF EXISTS news CASCADE;");
        $this->execute("DROP TABLE IF EXISTS manuscript CASCADE;");
        $this->execute("DROP TABLE IF EXISTS link CASCADE;");
        $this->execute("DROP TABLE IF EXISTS file_sample CASCADE;");
        $this->execute("DROP TABLE IF EXISTS file_relationship CASCADE;");
        $this->execute("DROP TABLE IF EXISTS relationship CASCADE;");
        $this->execute("DROP TABLE IF EXISTS file_experiment CASCADE;");
        $this->execute("DROP TABLE IF EXISTS file_attributes CASCADE;");
        $this->execute("DROP TABLE IF EXISTS file CASCADE;");
        $this->execute("DROP TABLE IF EXISTS file_type CASCADE;");
        $this->execute("DROP TABLE IF EXISTS file_format CASCADE;");
        $this->execute("DROP TABLE IF EXISTS external_link CASCADE;");
        $this->execute("DROP TABLE IF EXISTS external_link_type CASCADE;");
        $this->execute("DROP TABLE IF EXISTS exp_attributes CASCADE;");
        $this->execute("DROP TABLE IF EXISTS experiment CASCADE;");
        $this->execute("DROP TABLE IF EXISTS dataset_type CASCADE;");
        $this->execute("DROP TABLE IF EXISTS type CASCADE;");
        $this->execute("DROP TABLE IF EXISTS dataset_session CASCADE;");
        $this->execute("DROP TABLE IF EXISTS dataset_sample CASCADE;");
        $this->execute("DROP TABLE IF EXISTS dataset_project CASCADE;");
        $this->execute("DROP TABLE IF EXISTS project CASCADE;");
        $this->execute("DROP TABLE IF EXISTS dataset_log CASCADE;");
        $this->execute("DROP TABLE IF EXISTS dataset_funder CASCADE;");
        $this->execute("DROP TABLE IF EXISTS funder_name CASCADE;");
        $this->execute("DROP TABLE IF EXISTS dataset_author CASCADE;");
        $this->execute("DROP TABLE IF EXISTS dataset_attributes CASCADE;");
        $this->execute("DROP TABLE IF EXISTS unit CASCADE;");
        $this->execute("DROP TABLE IF EXISTS curation_log CASCADE;");
        $this->execute("DROP TABLE IF EXISTS dataset CASCADE;");
        $this->execute("DROP TABLE IF EXISTS publisher CASCADE;");
        $this->execute("DROP TABLE IF EXISTS image CASCADE;");
        $this->execute("DROP TABLE IF EXISTS author_rel CASCADE;");
        $this->execute("DROP TABLE IF EXISTS author CASCADE;");
        $this->execute("DROP TABLE IF EXISTS attribute CASCADE;");
        $this->execute("DROP TABLE IF EXISTS alternative_identifiers CASCADE;");
        $this->execute("DROP TABLE IF EXISTS sample CASCADE;");
        $this->execute("DROP TABLE IF EXISTS gigadb_user CASCADE;");
        $this->execute("DROP TABLE IF EXISTS species CASCADE;");
        $this->execute("DROP TABLE IF EXISTS extdb CASCADE;");
        $this->execute("DROP TABLE IF EXISTS YiiSession CASCADE;");
        $this->execute("DROP TABLE IF EXISTS AuthAssignment CASCADE;");
        $this->execute("DROP TABLE IF EXISTS AuthItem CASCADE;");
    }
}

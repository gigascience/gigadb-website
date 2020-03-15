<?php

class m200305_163250_create_attribute_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE attribute (
                id integer NOT NULL,
                attribute_name character varying(100),
                definition character varying(1000),
                model character varying(100),
                structured_comment_name character varying(100),
                value_syntax character varying(500),
                allowed_units character varying(100),
                occurance character varying(5),
                ontology_link character varying(1000),
                note character varying(100));'
        );

        $sql_createseq = sprintf(
            'CREATE SEQUENCE attribute_id_seq
                START WITH 1
                INCREMENT BY 1
                NO MINVALUE
                NO MAXVALUE
                CACHE 1;'
        );

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE attribute_id_seq 
                OWNED BY attribute.id;'
        );

        $sql_altertab1 = sprintf(
            'ALTER TABLE ONLY attribute 
                ALTER COLUMN id SET DEFAULT nextval(\'attribute_id_seq\'::regclass);'
        );

        $sql_altertab2 = sprintf(
            'ALTER TABLE ONLY attribute
                ADD CONSTRAINT attribute_pkey PRIMARY KEY (id);'
        );

        $sql_cmds = array($sql_createtab, $sql_createseq, $sql_alterseq, $sql_altertab1, $sql_altertab2);
        foreach ($sql_cmds as $sql_cmd)
            Yii::app()->db->createCommand($sql_cmd)->execute();

        // Add data to table. Using insert() method from
        // CDbMigration because the code looks cleaner,
        // logging is provided and will be easier to update
        // if required.
        $this->insert('attribute', array(
            'id' => '497',
            'attribute_name' => 'urltoredirect',
            'structured_comment_name' => 'urltoredirect'
        ));
        $this->insert('attribute', array(
            'id' => '605',
            'attribute_name' =>'MD5 checksum',
            'definition' => 'The MD5 hash functions as a compact digital fingerprint of a file and is calculated by using the MD5 hashing algorithm md5sum, a program that is included in most Unix-like systems, Windows and Mac OS system alternatives are also readily available. md5sum is used to verify the integrity of files, as virtually any change to a file will cause its MD5 hash to change',
            'structured_comment_name' => 'MD5sum'
        ));
        $this->insert('attribute', array(
            'id' => '336',
            'attribute_name' => 'isolate',
            'definition' => 'individual isolate from which the sequence was obtained',
            'model' => 'INSDC',
            'structured_comment_name' => 'isolate',
            'occurance' => '1'
        ));
        $this->insert('attribute', array(
            'id' => '337',
            'attribute_name' => 'isolation source',
            'definition' => 'describes the physical, environmental and/or local geographical source of the biological sample from which the sequence was derived',
            'model' => 'INSDC',
            'structured_comment_name' => 'isolation_source',
            'occurance' => '1'
        ));
        $this->insert('attribute', array(
            'id' => '383',
            'attribute_name' => 'serovar',
            'definition' => 'The name used to determine the serotype/serovar being examined. Serotype or serovar are distinct variations within a species of bacteria or viruses or among immune cells of different individuals.',
            'structured_comment_name' => 'serovar',
            'occurance' => '1'
        ));
        $this->insert('attribute', array(
            'id' => '327',
            'attribute_name' => 'alternative accession-SRA Sample',
            'definition' => 'accession number given to the same sample in the SRA database at either NCBI (http://www.ncbi.nlm.nih.gov/ ) or EBI (http://www.ebi.ac.uk/ena/data/view/[E,S,D]RSnnnnnnn ) or DDBJ',
            'structured_comment_name' => 'alt_acc_SRA_sample',
            'value_syntax' => '[E|D|S]RSnnnnnn',
            'occurance' => '1'
        ));
        $this->insert('attribute', array(
            'id' => '282',
            'attribute_name' => 'source material identifiers',
            'definition' => 'The name of the culture collection, holder of the voucher or an institution. Could enumerate a list of common resources, just as the American Type Culture Collection (ATCC), German Collection of Microorganisms and Cell Cultures (DSMZ) etc. Can select not deposited',
            'model' => 'GSC_MIxS_v3',
            'structured_comment_name' => 'source_mat_id',
            'value_syntax' => 'for cultures of microorganisms: identifiers for two culture collections| for specimens (e.g., organelles and Eukarya): voucher condition and location',
            'occurance' => 'm'
        ));
        $this->insert('attribute', array(
            'id' => '376',
            'attribute_name' => 'estimated genome size',
            'definition' => 'An estimate of the size of the genome of the species being studied, in basepairs (Gb, Mb or Kb)',
            'structured_comment_name' => 'est_genome_size',
            'value_syntax' => 'text',
            'occurance' => '1'
        ));
        $this->insert('attribute', array(
            'id' => '314',
            'attribute_name' => 'funding source',
            'structured_comment_name' => 'fundref',
            'value_syntax' => 'from FundRef registry http://www.crossref.org/fundref/',
            'occurance' => 'm'
        ));
        $this->insert('attribute', array(
            'id' => '270',
            'attribute_name' => 'geographic location (country and/or sea,region)',
            'definition' => 'The geographical origin of the sample as defined by the country or sea name followed by specific region name. Country or sea names should be chosen from the INSDC country list (http://insdc.org/country.html), or the GAZ ontology (v1.446) (http://bioportal.bioontology.org/visualize/40651)',
            'model' => 'GSC_MIxS_v3',
            'structured_comment_name' => 'geo_loc_name',
            'value_syntax' => 'country or sea name (INSDC or GAZ):region(GAZ):specific location name',
            'occurance' => '1',
            'ontology_link' => ' http://www.ebi.ac.uk/ontology-lookup/browse.do?ontName=GAZhttp://www.ebi.ac.uk/ontology-lookup/browse.do?ontName=EFOhttp://www.ebi.ac.uk/ontology-lookup/browse.do?ontName=POhttp://www.ebi.ac.uk/ontology-lookup/browse.do?ontName=PATOhttp://www.ebi.ac.uk/ontology-lookup/browse.do?ontName=DOID'
        ));
        $this->insert('attribute', array(
            'id' => '269',
            'attribute_name' => 'geographic location (latitude and longitude)',
            'definition' => 'The geographical origin of the sample as defined by latitude and longitude. The values should be reported in decimal degrees and in WGS84 system',
            'model' => 'GSC_MIxS_v3',
            'structured_comment_name' => 'lat_lon',
            'value_syntax' => 'decimal degrees',
            'occurance' => '1'
        ));
        $this->insert('attribute', array(
            'id' => '320',
            'attribute_name' => 'IUCN Red List',
            'definition' => 'The IUCN Red List of Threatened Species, is the world\'s most comprehensive inventory of the global conservation status of biological species. See wikipedia http://en.wikipedia.org/wiki/IUCN_Red_List.  (Extinct (EX) �\u0093 No known individuals remaining. Extinct in the Wild (EW) �\u0093 Known only to survive in captivity, or as a naturalized population outside its historic range. Critically Endangered (CR) �\u0093 Extremely high risk of extinction in the wild. Endangered (EN) �\u0093 High risk of extinction in the wild. Vulnerable (VU) �\u0093 High risk of endangerment in the wild. Near Threatened (NT) �\u0093 Likely to become endangered in the near future. Least Concern (LC) �\u0093 Lowest risk. Does not qualify for a more at risk category. Widespread and abundant taxa are included in this category. Data Deficient (DD) �\u0093 Not enough data to make an assessment of its risk of extinction. Not Evaluated (NE) �\u0093 Has not yet been evaluated against the criteria.)',
            'structured_comment_name' => 'red_list',
            'value_syntax' => '[EX|EW|CR|EN|VU|NT|LC|DD|NE]',
            'occurance' => '1'
        ));
        $this->insert('attribute', array(
            'id' => '277',
            'attribute_name' => 'ploidy',
            'definition' => 'The ploidy level of the genome (e.g. allopolyploid, haploid, diploid, triploid, tetraploid). It has implications for the downstream study of duplicated gene and regions of the genomes (and perhaps for difficulties in assembly). For terms, please select terms listed under class ploidy (PATO:001374) of Phenotypic Quality Ontology (PATO), and for a browser of PATO (v1.269) please refer to https://www.ebi.ac.uk/ols/ontologies/pato/terms?iri=http%3A%2F%2Fpurl.obolibrary.org%2Fobo%2FPATO_0001374',
            'model' => 'GSC_MIxS_v3',
            'structured_comment_name' => 'ploidy',
            'value_syntax' => 'PATO',
            'occurance' => '1',
            'ontology_link' => 'https://www.ebi.ac.uk/ols/ontologies/pato/terms?iri=http%3A%2F%2Fpurl.obolibrary.org%2Fobo%2FPATO_0001374'
        ));
        $this->insert('attribute', array(
            'id' => '319',
            'attribute_name' => 'sample contact',
            'definition' => 'the person or institute that can be contacted regarding the sample acquisition',
            'structured_comment_name' => 'sample_contact',
            'occurance' => 'm'
        ));
        $this->insert('attribute', array(
            'id' => '318',
            'attribute_name' => 'sample source',
            'definition' => 'additional information about where the sample came from, e.g. the particular zoo/avery/lab name',
            'structured_comment_name' => 'sample_source',
            'occurance' => 'm'
        ));
        $this->insert('attribute', array(
            'id' => '200',
            'attribute_name' => 'sex',
            'definition' => 'physical sex of the host',
            'model' => 'GSC_MIxS_v3',
            'value_syntax' => '[male|female|neuter|hermaphrodite|not determined]',
            'structured_comment_name' => 'sex'
        ));
        $this->insert('attribute', array(
            'id' => '315',
            'attribute_name' => 'tissue',
            'definition' => 'UBERON http://www.ebi.ac.uk/ontology-lookup/browse.do?ontName=UBERON',
            'value_syntax' => 'UBERON http://www.ebi.ac.uk/ontology-lookup/browse.do?ontName=UBERON',
            'structured_comment_name' => 'tissue_type',
            'occurance' => 'm'
        ));
        $this->insert('attribute', array(
            'id' => '316',
            'attribute_name' => 'alternative names',
            'definition' => 'the list of alternative identifiers used for this sample',
            'structured_comment_name' => 'alternative_names',
            'occurance' => 'm'
        ));
    }

    public function safeDown()
    {
        $this->dropTable('attribute');
        // Don't think you can drop SEQUENCE with a
        // function in CDbMigration
        Yii::app()->db->createCommand('DROP SEQUENCE attribute_id_seq;')->execute();
    }
}

<?php

class m200305_183452_create_file_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE file (
                id integer NOT NULL,
                dataset_id integer NOT NULL,
                name character varying(200) NOT NULL,
                location character varying(500) NOT NULL,
                extension character varying(100) NOT NULL,
                size bigint NOT NULL,
                description text DEFAULT \'\'::text NOT NULL,
                date_stamp date,
                format_id integer,
                type_id integer,
                code character varying(200) DEFAULT \'FILE_CODE\'::character varying,
                index4blast character varying(50),
                download_count integer DEFAULT 0 NOT NULL,
                alternative_location character varying(200));'
        );

        $sql_createseq = sprintf(
            'CREATE SEQUENCE file_id_seq
                START WITH 6300
                INCREMENT BY 1
                NO MINVALUE
                NO MAXVALUE
                CACHE 1;'
        );

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE file_id_seq 
                OWNED BY file.id;'
        );

        $sql_createview = sprintf(
            'CREATE VIEW file_number AS
                SELECT count(file.id) AS count 
                FROM file;'
        );

        $sql_altertab1 = sprintf(
            'ALTER TABLE ONLY file 
                ALTER COLUMN id SET DEFAULT nextval(\'file_id_seq\'::regclass);'
        );

        $sql_altertab2 = sprintf(
            'ALTER TABLE ONLY file
                ADD CONSTRAINT file_pkey PRIMARY KEY (id);'
        );

        $sql_altertab3 = sprintf(
            'ALTER TABLE ONLY file
                ADD CONSTRAINT file_dataset_id_fkey FOREIGN KEY (dataset_id) 
                REFERENCES dataset(id) ON DELETE CASCADE;'
        );

        $sql_altertab4 = sprintf(
            'ALTER TABLE ONLY file
                ADD CONSTRAINT file_format_id_fkey FOREIGN KEY (format_id) 
                REFERENCES file_format(id) ON DELETE CASCADE;'
        );

        $sql_altertab5 = sprintf(
            'ALTER TABLE ONLY file
                ADD CONSTRAINT file_type_id_fkey FOREIGN KEY (type_id) 
                REFERENCES file_type(id) ON DELETE CASCADE;'
        );

        $sql_cmds = array( $sql_createtab, $sql_createseq, $sql_alterseq, $sql_createview, $sql_altertab1, $sql_altertab2, $sql_altertab3, $sql_altertab4, $sql_altertab5);
        foreach ($sql_cmds as $sql_cmd)
            Yii::app()->db->createCommand($sql_cmd)->execute();

        // Add data to table. Using insert() method from
        // CDbMigration because the code looks cleaner,
        // logging is provided and will be easier to update
        // if required.
        $this->insert('file', array(
            'id' => '4281',
            'dataset_id' =>'13',
            'name' => 'CE.pep.fa.gz',
            'location' => 'ftp://parrot.genomics.cn/gigadb/pub/10.5524/100001_101000/100003/CE.pep.fa.gz',
            'extension' => 'fa',
            'size' => '6788222',
            'date_stamp' => '2011-07-06',
            'format_id' => '2',
            'type_id' => '5',
            'code' => 'CE',
            'download_count' => '1'
        ));
        $this->insert('file', array(
            'id' => '4286',
            'dataset_id' =>'13',
            'name' => 'CE.name.gz',
            'location' => 'ftp://parrot.genomics.cn/gigadb/pub/10.5524/100001_101000/100003/CE.name.gz',
            'extension' => 'name',
            'size' => '323108',
            'date_stamp' => '2011-07-06',
            'format_id' => '6',
            'type_id' => '6',
            'code' => 'CE',
            'download_count' => '0'
        ));
        $this->insert('file', array(
            'id' => '4289',
            'dataset_id' =>'13',
            'name' => 'brain.depth.gz',
            'location' => 'ftp://parrot.genomics.cn/gigadb/pub/10.5524/100001_101000/100003/brain.depth.gz',
            'extension' => 'depth',
            'size' => '164594769',
            'description' => 'Brain tissue',
            'date_stamp' => '2011-07-06',
            'format_id' => '43',
            'type_id' => '127',
            'code' => 'CE',
            'download_count' => '1'
        ));
        $this->insert('file', array(
            'id' => '4298',
            'dataset_id' =>'13',
            'name' => 'liver.depth.gz',
            'location' => 'ftp://parrot.genomics.cn/gigadb/pub/10.5524/100001_101000/100003/transcriptome/liver/liver.depth.gz',
            'extension' => 'depth',
            'size' => '163689845',
            'description' => 'Liver tissue',
            'date_stamp' => '2011-07-06',
            'format_id' => '43',
            'type_id' => '127',
            'code' => 'CE',
            'download_count' => '0'
        ));
        $this->insert('file', array(
            'id' => '4324',
            'dataset_id' =>'15',
            'name' => 'Escherichia_coli_TY-2482.contig.fa.gz',
            'location' => 'ftp://parrot.genomics.cn/gigadb/pub/10.5524/100001_101000/100001/Escherichia_coli_TY-2482.contig.fa.gz',
            'extension' => 'fa',
            'size' => '1761875',
            'description' => '06/06/11 Ion Torrent+Illumina hybrid assembly',
            'date_stamp' => '2012-02-28',
            'format_id' => '2',
            'type_id' => '132',
            'code' => 'TY-2482',
            'download_count' => '33'
        ));
        $this->insert('file', array(
            'id' => '4328',
            'dataset_id' =>'15',
            'name' => 'Escherichia_coli_TY-2482.plasmid.20110616.fa.gz',
            'location' => 'ftp://parrot.genomics.cn/gigadb/pub/10.5524/100001_101000/100001/Escherichia_coli_TY-2482.plasmid.20110616.fa.gz',
            'extension' => 'fa',
            'size' => '51201',
            'description' => '16/06/11 Gapless Illumina de novo assembly (plasmid)',
            'date_stamp' => '2012-02-28',
            'format_id' => '2',
            'type_id' => '132',
            'code' => 'TY-2482',
            'download_count' => '26'
        ));
        $this->insert('file', array(
            'id' => '4329',
            'dataset_id' =>'15',
            'name' => '110601_I238_FCB067HABXX_L3_ESCqslRAADIAAPEI-2_1.fq.gz',
            'location' => 'ftp://parrot.genomics.cn/gigadb/pub/10.5524/100001_101000/100001/110601_I238_FCB067HABXX_L3_ESCqslRAADIAAPEI-2_1.fq.gz',
            'extension' => 'fq',
            'size' => '1618412446',
            'description' => '11/06/11 Illumina reads',
            'date_stamp' => '2012-02-28',
            'format_id' => '7',
            'type_id' => '9',
            'code' => 'TY-2482',
            'download_count' => '38'
        ));
        $this->insert('file', array(
            'id' => '4336',
            'dataset_id' =>'15',
            'name' => 'run7.fastq.gz',
            'location' => 'ftp://parrot.genomics.cn/gigadb/pub/10.5524/100001_101000/100001/run7.fastq.gz',
            'extension' => 'fastq',
            'size' => '6543116',
            'description' => '03/06/11 Ion Torrent run 7',
            'date_stamp' => '2012-02-28',
            'format_id' => '7',
            'type_id' => '9',
            'code' => 'TY-2482',
            'download_count' => '21'
        ));
        $this->insert('file', array(
            'id' => '5717',
            'dataset_id' =>'25',
            'name' => 'Evaluation_result_and_method_with_simulation_data.doc',
            'location' => 'ftp://parrot.genomics.cn/gigadb/pub/10.5524/100001_101000/100004/genome_sequence/Evaluation_result_and_method_with_simulation_data.doc',
            'extension' => 'doc',
            'size' => '45568',
            'date_stamp' => '2011-07-06',
            'format_id' => '54',
            'type_id' => '64',
            'code' => 'SAMPLE:SRS004381',
            'download_count' => '0'
        ));
        $this->insert('file', array(
            'id' => '5721',
            'dataset_id' =>'25',
            'name' => 'panda.scafSeq.gapFilled.noMito.fa.gz',
            'location' => 'ftp://parrot.genomics.cn/gigadb/pub/10.5524/100001_101000/100004/genome_sequence/panda.scafSeq.gapFilled.noMito.fa.gz',
            'extension' => 'fa',
            'size' => '699450409',
            'date_stamp' => '2011-07-06',
            'format_id' => '2',
            'type_id' => '132',
            'code' => 'SAMPLE:SRS004381',
            'download_count' => '0'
        ));
        $this->insert('file', array(
            'id' => '5937',
            'dataset_id' =>'25',
            'name' => '080715_I354_FC307BGAAXX_L7_PAfwDADBFAPE_1.fq.clean.gz',
            'location' => 'ftp://parrot.genomics.cn/gigadb/pub/10.5524/100001_101000/100004/Sequencing_and_library/080715_I354_FC307BGAAXX_L7_PAfwDADBFAPE_1.fq.clean.gz',
            'extension' => 'fq',
            'size' => '273808936',
            'date_stamp' => '2011-07-06',
            'format_id' => '7',
            'type_id' => '9',
            'code' => 'SAMPLE:SRS004381',
            'download_count' => '0'
        ));
        $this->insert('file', array(
            'id' => '5993',
            'dataset_id' =>'25',
            'name' => '080721_I360_FC30ALEAAXX_L2_PAfwDADLBAPE_1.fq.clean.gz',
            'location' => 'ftp://parrot.genomics.cn/gigadb/pub/10.5524/100001_101000/100004/Sequencing_and_library/080721_I360_FC30ALEAAXX_L2_PAfwDADLBAPE_1.fq.clean.gz',
            'extension' => 'fq',
            'size' => '226221193',
            'date_stamp' => '2011-07-06',
            'format_id' => '7',
            'type_id' => '9',
            'code' => 'SAMPLE:SRS004381',
            'download_count' => '2'
        ));
        $this->insert('file', array(
            'id' => '6201',
            'dataset_id' =>'29',
            'name' => 'CR.cds.fa.gz',
            'location' => 'ftp://parrot.genomics.cn/gigadb/pub/10.5524/100001_101000/100002/CR.cds.fa.gz',
            'extension' => 'fa',
            'size' => '10571176',
            'date_stamp' => '2011-07-06',
            'format_id' => '2',
            'type_id' => '4',
            'code' => 'CR',
            'download_count' => '6'
        ));
        $this->insert('file', array(
            'id' => '6203',
            'dataset_id' =>'29',
            'name' => 'CR.gff.gz',
            'location' => 'ftp://parrot.genomics.cn/gigadb/pub/10.5524/100001_101000/100002/CR.gff.gz',
            'extension' => 'gff',
            'size' => '2325538',
            'date_stamp' => '2011-07-06',
            'format_id' => '5',
            'type_id' => '6',
            'code' => 'CR',
            'download_count' => '4'
        ));
        $this->insert('file', array(
            'id' => '6204',
            'dataset_id' =>'29',
            'name' => 'CR.ipr.gz',
            'location' => 'ftp://parrot.genomics.cn/gigadb/pub/10.5524/100001_101000/100002/CR.ipr.gz',
            'extension' => 'ipr',
            'size' => '189037',
            'date_stamp' => '2011-07-06',
            'format_id' => '11',
            'type_id' => '6',
            'code' => 'CR',
            'download_count' => '4'
        ));
        $this->insert('file', array(
            'id' => '6205',
            'dataset_id' =>'29',
            'name' => 'CR.kegg.gz',
            'location' => 'ftp://parrot.genomics.cn/gigadb/pub/10.5524/100001_101000/100002/CR.kegg.gz',
            'extension' => 'kegg',
            'size' => '39538',
            'date_stamp' => '2011-07-06',
            'format_id' => '12',
            'type_id' => '6',
            'code' => 'CR',
            'download_count' => '4'
        ));
    }

    public function safeDown()
    {
        // Don't think you can drop SEQUENCE with a
        // function in CDbMigration
        Yii::app()->db->createCommand('DROP SEQUENCE file_id_seq CASCADE;')->execute();
        Yii::app()->db->createCommand('DROP VIEW file_number;')->execute();
        $this->dropTable('file');
    }
}

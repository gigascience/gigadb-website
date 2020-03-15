<?php

class m200304_192607_create_dataset_table extends CDbMigration
{
// Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE dataset (
                id integer NOT NULL,
                submitter_id integer NOT NULL,
                image_id integer,
                identifier character varying(32) NOT NULL,
                title character varying(300) NOT NULL,
                description text DEFAULT \'\'::text NOT NULL,
                dataset_size bigint NOT NULL,
                ftp_site character varying(100) NOT NULL,
                upload_status character varying(45) DEFAULT \'Pending\'::character varying NOT NULL,
                excelfile character varying(50),
                excelfile_md5 character varying(32),
                publication_date date,
                modification_date date,
                publisher_id integer,
                token character varying(16) DEFAULT NULL::character varying,
                fairnuse date,
                curator_id integer,
                manuscript_id character varying(50),
                handing_editor character varying(50));'
        );

        $sql_createseq = sprintf(
            'CREATE SEQUENCE dataset_id_seq 
            START WITH 33 
            INCREMENT BY 1 
            NO MINVALUE 
            NO MAXVALUE 
            CACHE 1 
            OWNED BY dataset.id;'
        );

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE dataset_id_seq 
                OWNED BY dataset.id;'
        );

        $sql_altertab1 = sprintf(
            'ALTER TABLE ONLY dataset 
                ALTER COLUMN id SET DEFAULT nextval(\'dataset_id_seq\'::regclass);'
        );

        $sql_altertab2 = sprintf(
            'ALTER TABLE ONLY dataset
                ADD CONSTRAINT dataset_pkey PRIMARY KEY (id);'
        );

        $sql_altertab3 = sprintf(
            'ALTER TABLE ONLY dataset
                ADD CONSTRAINT dataset_curator_id FOREIGN KEY (curator_id) 
                REFERENCES gigadb_user(id);'
        );

        $sql_altertab4 = sprintf(
            'ALTER TABLE ONLY dataset
                ADD CONSTRAINT dataset_image_id_fkey FOREIGN KEY (image_id) 
                REFERENCES image(id) ON DELETE SET NULL;'
        );

        $sql_altertab5 = sprintf(
            'ALTER TABLE ONLY dataset
                ADD CONSTRAINT dataset_publisher_id_fkey FOREIGN KEY (publisher_id) 
                REFERENCES publisher(id) ON DELETE SET NULL;'
        );

        $sql_altertab6 = sprintf(
            'ALTER TABLE ONLY dataset
                ADD CONSTRAINT dataset_submitter_id_fkey FOREIGN KEY (submitter_id) 
                REFERENCES gigadb_user(id) ON DELETE RESTRICT;'
        );

        $sql_createindex = sprintf(
            'CREATE UNIQUE INDEX identifier_idx ON dataset 
                USING btree (identifier);'
        );

        $sql_cmds = array( $sql_createtab, $sql_createseq, $sql_alterseq, $sql_altertab1, $sql_altertab2, $sql_altertab3, $sql_altertab4, $sql_altertab5, $sql_altertab6, $sql_createindex);
        foreach ($sql_cmds as $sql_cmd)
            Yii::app()->db->createCommand($sql_cmd)->execute();

        // Add data to table
        $this->insert('dataset', array(
            'id' => '15',
            'submitter_id' => '3',
            'image_id' => '15',
            'identifier' => '100001',
            'title' => 'Genomic data from <em>Escherichia coli</em> O104:H4 isolate TY-2482',
            'description' => 'The May 2011 outbreak of an <em>E. coli</em> infection in Europe resulted in serious concerns about the potential appearance of a new deadly strain of bacteria, <em>Escherichia coli</em> O104:H4 TY-2482.  In response to this situation, and immediately after the reports of deaths, the University Medical Centre Hamburg-Eppendorf and BGI-Shenzhen worked together to sequence the bacterium and assess its human health risk.\r\n\r\nThe bacterium’s genome was first sequenced using Life Technologies; Ion Torrent sequencing platform. According to the results of the draft assembly, the estimated genome size of this new <em>E. coli</em> strain is about 5.2 Mb.  Sequence analysis indicated this bacterium is an EHEC serotype O104 <em>E. coli</em> strain.  Comparative analysis showed that this bacterium has 93% sequence similarity with the EAEC 55989 <em>E. coli</em> strain, which was isolated in the Central African Republic and known to cause serious diarrhea.  This strain of <em>E. coli</em>, however, has also acquired specific sequences that appear to be similar to those involved in the pathogenicity of hemorrhagic colitis and hemolytic-uremic syndrome.  The acquisition of these genes may have occurred through horizontal gene transfer.\r\n\r\nTo maximize its utility to the research community and aid those fighting the epidemic, this genomic data was released into the public domain under a <a href="http://creativecommons.org/about/cc0">CC0 license</a>.\r\n\r\nTo the extent possible under law, <a href ="http://www.genomics.cn/">BGI Shenzhen</a>  has waived all copyright and related or neighboring rights to genomic data from the 2011 <em>E. coli</em> outbreak. This work is published from: China.',
            'dataset_size' => '1696392192',
            'ftp_site' => 'ftp://parrot.genomics.cn/gigadb/pub/10.5524/100001_101000/100001',
            'upload_status' => 'Published',
            'excelfile' => 'GigaDBUploadForm_Ecoli.xls',
            'excelfile_md5' => '8256701ec49424484a7d93b86267f47a',
            'publication_date' => '2011-06-03',
            'publisher_id' => '2',
        ));
        $this->insert('dataset', array(
            'id' => '29',
            'submitter_id' => '8',
            'image_id' => '29',
            'identifier' => '100002',
            'title' => 'Genomic data from the Chinese Rhesus macaque (<em>Macaca mulatta lasiota</em>).',
            'description' => 'The Chinese rhesus macaque (<em>Macaca mulatta lasiota</em>) is a subspecies of rhesus macaques that mainly resides in western and central China.  Due to their anatomical and physiological similarity with human beings, macaques are a common laboratory model.  Also, as several macaques species have been sequenced, such as the Indian rhesus macaque and the crab-eating macaque, examination of the Chinese rhesus macaque (CR) genome offers interesting insights into the entire <em>Macaca</em> genus.\r\n\r\nThe DNA sample for data sequencing and analyses was obtained from a five-year old female CR from southwestern China.  The genome was sequenced on the IlluminaGAIIx platform, from which 142-Gb of high-quality sequence, representing 47-fold genome coverage for CR.  The total size of the assembled CR genome was about 2.84 Gb, providing 47-fold on average.  Scaffolds were assigned to the chromosomes according to the synteny displayed with the Indian rhesus macaque and human genome sequences.  About 97% of the CR scaffolds could be placed onto chromosomes.',
            'dataset_size' => '1054888960',
            'ftp_site' => 'ftp://parrot.genomics.cn/gigadb/pub/10.5524/100001_101000/100002/',
            'upload_status' => 'Published',
            'excelfile' => 'GigaDBUploadForm_RhesusMacaque.xls',
            'excelfile_md5' => 'bd444e7c591efb00ad3e125c52b337aa',
            'publication_date' => '2011-07-06',
            'modification_date' => '2012-04-27',
            'publisher_id' => '1',
        ));
        $this->insert('dataset', array(
            'id' => '13',
            'submitter_id' => '8',
            'image_id' => '13',
            'identifier' => '100003',
            'title' => 'Genomic data from the crab-eating macaque/cynomolgus monkey (<em>Macaca fascicularis</em>).',
            'description' => 'The crab-eating macaque (<em>Macaca fascicularis</em>), also known as the Java macaque or long-tailed macaque, is a species of primate located throughout Southeast Asia.  Due to the frequent usage of the genus <em>Macaca</em> in scientific research, the sequence the crab-eating macaque furthers our understanding on how it differs from other macaque species, like the Chinese rhesus macaque and the Indian rhesus macaque.  This is especially relevant considering the recent trend of using crab-eating macaque (CE) and Chinese rhesus macaques rather than the Indian rhesus macaque as laboratory models.\n\nThe DNA sample for genome sequencing and analyses was from a female CE that was a captive-bred descendent of a CE from Vietnam.  The genome was sequenced on the IlluminaGAIIx platform, and we obtained 162-Gb of high-quality sequence, representing 54-fold coverage.  The sequencing data were processed with Illumina custom computational pipelines. The genome was <em>de novo</em> assembled using SOAPdenovo program based on the de Bruijn graph algorithm methods.  The total size of the assembled genome was about 2.85 Gb, providing 54-fold coverage on average.  The scaffolds were assigned to the chromosomes according to the synteny displayed with the Indian rhesus macaque and human genome sequences.  About 92% of the CE scaffolds could be placed onto chromosomes.',
            'dataset_size' => '2582601728',
            'ftp_site' => 'ftp://parrot.genomics.cn/gigadb/pub/10.5524/100001_101000/100003/',
            'upload_status' => 'Published',
            'excelfile' => 'GigaDBUploadForm_CrabEatingMacaque.xls',
            'excelfile_md5' => '33f5c85e12c0d24ccf6c48dc6bd507fb',
            'publication_date' => '2011-07-06',
            'modification_date' => '2012-04-27',
            'publisher_id' => '1',
        ));
        $this->insert('dataset', array(
            'id' => '25',
            'submitter_id' => '8',
            'image_id' => '25',
            'identifier' => '100004',
            'title' => 'Genomic data from the giant panda (<em>Ailuropoda melanoleuca</em>).',
            'description' => 'The giant panda (<em>Ailuropoda melanoleuca</em>) is considered a symbol of China and is a much loved animal all around the world.  It is also one of the world’s most endangered species, making it a flagship species for conservation efforts.  As the first fully sequenced Ursidae and the second fully sequenced carnivore after the dog, the whole genome sequence and annotation data provide an unparalleled amount of information to aid in understanding the genetic and biological underpinnings of this unique species, and will help contribute to disease control and conservation efforts.\n\nIn 2008, BGI completed a first draft of the genome sequence of a three-year old female giant panda named Jingjing, who was used as a model for the 2008 Olympics in Beijing, China (<a href="http://dx.doi.org/10.1038/nature08696">doi: 10.1038/nature08696</a>). Using second-generation Illumina GA sequencing data, the first de novo genome assembly was created using short-read sequencing technology.  Here you will find the giant panda genome sequence assembly as well as annotation information, such as gene structure and function, non-coding RNAs, and repeat elements.  Also presented are polymorphism information detected in the diploid genome, including SNPs, indels, and structural variations (SVs).  The assembly was done using SOAPdenovo software and the panda genome data is visualized via MapView, which is powered by the Google Web Toolkit.',
            'dataset_size' => '165263566848',
            'ftp_site' => 'ftp://parrot.genomics.cn/gigadb/pub/10.5524/100001_101000/100004/',
            'upload_status' => 'Published',
            'excelfile' => 'GigaDBUploadForm_Panda.xls',
            'excelfile_md5' => '1cfb49f6a622ae1c2599e6dd2eed9785',
            'publication_date' => '2011-07-06',
            'publisher_id' => '1',
        ));
    }

    public function safeDown()
    {
        $this->dropTable('dataset');
        Yii::app()->db->createCommand('DROP SEQUENCE dataset_id_seq;')->execute();
    }
}

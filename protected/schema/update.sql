-- funder_name - nht_2
ALTER TABLE funder_name ADD COLUMN country VARCHAR(128) DEFAULT '';

-- dataset_funder - nht_1
CREATE TABLE dataset_funder (
  id SERIAL NOT NULL PRIMARY KEY,
  dataset_id INT NOT NULL REFERENCES dataset(id) ON DELETE CASCADE,
  funder_id INT NOT NULL REFERENCES funder_name(id) ON DELETE CASCADE,
  grant_award TEXT DEFAULT '',
  comments TEXT DEFAULT ''
);

-- dataset_log
CREATE TABLE dataset_log (
  id SERIAL NOT NULL PRIMARY KEY,
  dataset_id INTEGER NOT NULL REFERENCES dataset(id) ON DELETE RESTRICT,
  message TEXT DEFAULT '',
  created_at timestamp without time zone default now(),
  model TEXT,
  model_id INTEGER
);

-- attribute
SELECT SETVAL('attribute_id_seq', (select max(id) from attribute));

INSERT INTO attribute (attribute_name, definition, model, structured_comment_name, value_syntax, occurance) VALUES ('PX_experiment_type',
'Specify the experimental method used, select from predefined list, if yours is not shown please include it in the others text box.', 'PX', 'PX_experiment_type', 'text', '1'); 

INSERT INTO attribute (attribute_name, definition, model, structured_comment_name, value_syntax, occurance) VALUES ('PX_data_processing_protocol','Specify a short description of the data processing protocol being followed. Please provide a couple of sentences on the bioinformatics pipeline used, main search parameters, quantitative analysis, softwarre tools and versions included. Something similar to the Data analysis section of your manuscript, only shorter.', 'PX', 'PX_Data_processing', 'text', '1');

INSERT INTO attribute (attribute_name, definition, model, structured_comment_name, value_syntax, occurance) VALUES ('PX_sample_processing_protocol','A short description of the sample processing protocol being followed, including preparation, separation, enrichment strategies and mass spectrometry protocols.', 'PX', 'PX_samp_process_prot', 'text', '1');

INSERT INTO attribute (attribute_name, definition, model, structured_comment_name, value_syntax, occurance) VALUES ('PX_keywords','keywords that describe the nature of the experiment.', 'PX', 'PX_keywords', 'text', 'm');

-- sample attribute
SELECT SETVAL('sample_attribute_id_seq', (select max(id) from sample_attribute));

-- data log
ALTER TABLE dataset_log ADD COLUMN url TEXT DEFAULT '';

-- user table
alter table gigadb_user add column username text unique;
update gigadb_user set username = email;
alter table gigadb_user alter column username set not null;
alter table gigadb_user add column facebook_id text unique;
alter table gigadb_user add column google_id text unique;
alter table gigadb_user add column twitter_id text unique;
alter table gigadb_user add column linkedin_id text unique;
alter table gigadb_user add column orcid_id text unique;

-- dataset_attribute
ALTER TABLE dataset_attributes ADD COLUMN image_id INT REFERENCES image(id) ON DELETE SET NULL;
ALTER TABLE dataset_attributes ADD COLUMN until_date DATE;

-- dataset
ALTER TABLE dataset ADD COLUMN fairnuse DATE DEFAULT NULL;

-- type
SELECT setval('type_id_seq', (select max(id) from type));

-- gigadb_user
ALTER TABLE gigadb_user ADD COLUMN preferred_link VARCHAR(128) DEFAULT 'EBI';

-- linkout 
CREATE TABLE linkout (
  id SERIAL NOT NULL PRIMARY KEY,
  source VARCHAR(128) NOT NULL,
  prefix TEXT NOT NULL,
  link TEXT NOT NULL,
  type TEXT DEFAULT ''
);

-- file
ALTER TABLE file ADD COLUMN download_count INT NOT NULL DEFAULT 0;

-- new attribute 
INSERT INTO attribute(attribute_name, definition, structured_comment_name) VALUES 
('number of words', 'number of words in file', 'num_words'),
('number of lines', 'number of lines in file', 'num_lines'),
('number of nucleotides', 'number of nucleotides in file', 'num_nucleotides'),
('number amino acids', 'number amino acids in file', 'num_amino_acids'),
('number of rows', 'number of rows in file', 'num_rows'),
('number of columns', 'number of columns in file', 'num_columns'),
('number of files', 'number of files in folder', 'num_files'),
('file size', 'file size', 'file_size');

-- linkout
DROP TABLE linkout;

-- prefix
ALTER TABLE prefix ADD COLUMN source VARCHAR(128) DEFAULT '';

-- dataset
ALTER TABLE dataset ADD COLUMN citation INT DEFAULT 0;
-- dataset_funder
ALTER TABLE "dataset_funder" ADD CONSTRAINT "un_dataset_funder" UNIQUE (dataset_id, funder_id);

-- funder
SELECT SETVAL('funder_name_id_seq', (select max(id) from funder_name));

-- update dataset_attributes constraints
ALTER TABLE dataset_attributes
drop constraint dataset_attributes_dataset_id_fkey,
add constraint dataset_attributes_dataset_id_fkey
  FOREIGN KEY (dataset_id)
  REFERENCES dataset(id)
  ON DELETE CASCADE;

-- update experiment constraints
ALTER TABLE experiment
drop constraint experiment_dataset_id_fkey,
add constraint experiment_dataset_id_fkey
  FOREIGN KEY (dataset_id)
  REFERENCES dataset(id)
  ON DELETE set null;

-- udpate file_attributes constraints
ALTER TABLE file_attributes
drop constraint file_attributes_file_id_fkey,
add constraint file_attributes_file_id_fkey
  FOREIGN KEY (file_id) 
  REFERENCES file(id)
  ON DELETE CASCADE;

-- update sample_attributes constraints
ALTER TABLE sample_attribute
drop constraint sample_attribute_id_fkey,
add constraint sample_attribute_id_fkey
  FOREIGN KEY (sample_id) 
  REFERENCES sample(id)
  ON DELETE CASCADE;

-- file
ALTER TABLE file ALTER column code SET DEFAULT '';



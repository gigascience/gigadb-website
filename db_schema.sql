CREATE TABLE file (
    id serial PRIMARY KEY,
    doi_suffix integer NOT NULL,
    name character varying(100) NOT NULL,
    size bigint NOT NULL,
    status character varying(100) DEFAULT 'uploading'::text, -- 1: uploading, 2: private, 3: public
    location character varying(200),
    description text DEFAULT ''::text,
    initial_md5 text DEFAULT ''::text,
    format text DEFAULT 'Unknown'::text,
    data_type text DEFAULT 'Unknown'::text,
    created_at timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
    updated_at timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);

CREATE OR REPLACE FUNCTION update_modified_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = now();
    RETURN NEW;
END;
$$ language 'plpgsql';

CREATE TRIGGER update_file_modtime BEFORE UPDATE ON file
FOR EACH ROW EXECUTE PROCEDURE  update_modified_column();

CREATE TABLE account (
    id serial PRIMARY KEY,
    doi_suffix integer NOT NULL,
    ulogin character varying(100),
    utoken character varying(128),
    dlogin character varying(100),
    dtoken character varying(128),
    space_used bigint,
    status character varying(100),
    created_at timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
    updated_at timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
    retired_at timestamp
);

CREATE TRIGGER update_account_modtime BEFORE UPDATE ON account
FOR EACH ROW EXECUTE PROCEDURE  update_modified_column();


CREATE OR REPLACE FUNCTION update_retired_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.retired_at = now();
    RETURN NEW;
END;
$$ language 'plpgsql';

CREATE TRIGGER update_account_retiredtime BEFORE UPDATE ON account
FOR EACH ROW
WHEN (OLD.status <> NEW.status AND NEW.status = 'retired')
EXECUTE PROCEDURE  update_retired_column();


--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: AuthAssignment; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE "AuthAssignment" (
    itemname character varying(64) NOT NULL,
    userid character varying(64) NOT NULL,
    bizrule text,
    data text
);


ALTER TABLE public."AuthAssignment" OWNER TO gigadb;

--
-- Name: AuthItem; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE "AuthItem" (
    name character varying(64) NOT NULL,
    type integer NOT NULL,
    description text,
    bizrule text,
    data text
);


ALTER TABLE public."AuthItem" OWNER TO gigadb;

--
-- Name: YiiSession; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE "YiiSession" (
    id character(32) NOT NULL,
    expire integer,
    data bytea
);


ALTER TABLE public."YiiSession" OWNER TO gigadb;


--
-- Name: user_command; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE user_command (
    id integer NOT NULL,
    action_label character varying(32) NOT NULL,
    requester_id integer NOT NULL,
    actioner_id integer,
    actionable_id integer NOT NULL,
    request_date timestamp,
    action_date timestamp,
    status character varying(32) NOT NULL
);


ALTER TABLE public.user_command OWNER TO gigadb;

--
-- Name: user_command_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE user_command_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.user_command_id_seq OWNER TO gigadb;

--
-- Name: user_command_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE user_command_id_seq OWNED BY user_command.id;

--
-- Name: alternative_identifiers; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE alternative_identifiers (
    id integer NOT NULL,
    sample_id integer NOT NULL,
    extdb_id integer NOT NULL,
    extdb_accession character varying(100)
);


ALTER TABLE public.alternative_identifiers OWNER TO gigadb;

--
-- Name: COLUMN alternative_identifiers.id; Type: COMMENT; Schema: public; Owner: gigadb
--

COMMENT ON COLUMN alternative_identifiers.id IS '

';


--
-- Name: alternative_identifiers_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE alternative_identifiers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.alternative_identifiers_id_seq OWNER TO gigadb;

--
-- Name: alternative_identifiers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE alternative_identifiers_id_seq OWNED BY alternative_identifiers.id;


--
-- Name: alternative_identifiers_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('alternative_identifiers_id_seq', 1, false);


--
-- Name: attribute; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE attribute (
    id integer NOT NULL,
    attribute_name character varying(100),
    definition character varying(1000),
    model character varying(100),
    structured_comment_name character varying(100),
    value_syntax character varying(500),
    allowed_units character varying(100),
    occurance character varying(5),
    ontology_link character varying(1000),
    note character varying(100)
);


ALTER TABLE public.attribute OWNER TO gigadb;

--
-- Name: attribute_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE attribute_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.attribute_id_seq OWNER TO gigadb;

--
-- Name: attribute_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE attribute_id_seq OWNED BY attribute.id;


--
-- Name: attribute_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('attribute_id_seq', 422, true);


--
-- Name: author; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE author (
    id integer NOT NULL,
    surname character varying(255) NOT NULL,
    middle_name character varying(255),
    first_name character varying(255),
    custom_name character varying(255),
    orcid character varying(255),
    gigadb_user_id integer UNIQUE
);


ALTER TABLE public.author OWNER TO gigadb;

--
-- Name: author_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE author_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.author_id_seq OWNER TO gigadb;

--
-- Name: author_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE author_id_seq OWNED BY author.id;


--
-- Name: author_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('author_id_seq', 3809, true);

--
-- TOC entry 252 (class 1259 OID 18403)
-- Name: author_rel; Type: TABLE; Schema: public; Owner: gigadb
--

CREATE TABLE author_rel (
    id integer NOT NULL,
    author_id integer NOT NULL,
    related_author_id integer NOT NULL,
    relationship_id integer
);


ALTER TABLE author_rel OWNER TO gigadb;

--
-- TOC entry 253 (class 1259 OID 18406)
-- Name: author_rel_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE author_rel_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE author_rel_id_seq OWNER TO gigadb;

--
-- TOC entry 2903 (class 0 OID 0)
-- Dependencies: 253
-- Name: author_rel_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE author_rel_id_seq OWNED BY author_rel.id;


--
-- Name: dataset; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE dataset (
    id integer NOT NULL,
    submitter_id integer NOT NULL,
    image_id integer,
    curator_id integer,
    manuscript_id character varying(50),
    identifier character varying(32) NOT NULL,
    title character varying(300) NOT NULL,
    description text DEFAULT ''::text NOT NULL,
    dataset_size bigint NOT NULL,
    ftp_site character varying(100) NOT NULL,
    upload_status character varying(45) DEFAULT 'Pending'::character varying NOT NULL,
    excelfile character varying(50),
    excelfile_md5 character varying(32),
    publication_date date,
    modification_date date,
    publisher_id integer,
    token character varying(16) DEFAULT NULL::character varying,
    fairnuse date
);


ALTER TABLE public.dataset OWNER TO gigadb;

--
-- Name: dataset_attributes; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE dataset_attributes (
    id integer NOT NULL,
    dataset_id integer,
    attribute_id integer,
    value character varying(200),
    units_id character varying(30),
    image_id integer,
    until_date date
);


ALTER TABLE public.dataset_attributes OWNER TO gigadb;

--
-- Name: dataset_attributes_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE dataset_attributes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.dataset_attributes_id_seq OWNER TO gigadb;

--
-- Name: dataset_attributes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE dataset_attributes_id_seq OWNED BY dataset_attributes.id;


--
-- Name: dataset_attributes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('dataset_attributes_id_seq', 35, true);


--
-- Name: dataset_author; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE dataset_author (
    id integer NOT NULL,
    dataset_id integer NOT NULL,
    author_id integer NOT NULL,
    rank integer DEFAULT 0,
    role character varying(30),
    awardee character varying(50)
);


ALTER TABLE public.dataset_author OWNER TO gigadb;

--
-- Name: dataset_author_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE dataset_author_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.dataset_author_id_seq OWNER TO gigadb;

--
-- Name: dataset_author_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE dataset_author_id_seq OWNED BY dataset_author.id;


--
-- Name: dataset_author_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('dataset_author_id_seq', 3492, true);


--
-- Name: dataset_funder; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE dataset_funder (
    id integer NOT NULL,
    dataset_id integer NOT NULL,
    funder_id integer NOT NULL,
    grant_award text DEFAULT ''::text,
    comments text DEFAULT ''::text,
    awardee character varying(50)
);


ALTER TABLE public.dataset_funder OWNER TO gigadb;

--
-- Name: dataset_funder_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE dataset_funder_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.dataset_funder_id_seq OWNER TO gigadb;

--
-- Name: dataset_funder_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE dataset_funder_id_seq OWNED BY dataset_funder.id;


--
-- Name: dataset_funder_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('dataset_funder_id_seq', 31, true);


--
-- Name: dataset_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE dataset_id_seq
    START WITH 33
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.dataset_id_seq OWNER TO gigadb;

--
-- Name: dataset_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE dataset_id_seq OWNED BY dataset.id;


--
-- Name: dataset_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('dataset_id_seq', 213, true);


--
-- Name: dataset_log; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE dataset_log (
    id integer NOT NULL,
    dataset_id integer NOT NULL,
    message text DEFAULT ''::text,
    created_at timestamp without time zone DEFAULT now(),
    model text,
    model_id integer,
    url text DEFAULT ''::text
);


ALTER TABLE public.dataset_log OWNER TO gigadb;

--
-- Name: dataset_log_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE dataset_log_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.dataset_log_id_seq OWNER TO gigadb;

--
-- Name: dataset_log_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE dataset_log_id_seq OWNED BY dataset_log.id;


--
-- Name: dataset_log_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('dataset_log_id_seq', 119, true);


--
-- Name: dataset_project; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE dataset_project (
    id integer NOT NULL,
    dataset_id integer,
    project_id integer
);


ALTER TABLE public.dataset_project OWNER TO gigadb;

--
-- Name: dataset_project_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE dataset_project_id_seq
    START WITH 7
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.dataset_project_id_seq OWNER TO gigadb;

--
-- Name: dataset_project_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE dataset_project_id_seq OWNED BY dataset_project.id;


--
-- Name: dataset_project_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('dataset_project_id_seq', 127, true);


--
-- Name: dataset_sample; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE dataset_sample (
    id integer NOT NULL,
    dataset_id integer NOT NULL,
    sample_id integer NOT NULL
);


ALTER TABLE public.dataset_sample OWNER TO gigadb;

--
-- Name: dataset_sample_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE dataset_sample_id_seq
    START WITH 211
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.dataset_sample_id_seq OWNER TO gigadb;

--
-- Name: dataset_sample_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE dataset_sample_id_seq OWNED BY dataset_sample.id;


--
-- Name: dataset_sample_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('dataset_sample_id_seq', 4355, true);


--
-- Name: dataset_session; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE dataset_session (
    id integer NOT NULL,
    identifier text NOT NULL,
    dataset text,
    dataset_id text,
    datasettypes text,
    images text,
    authors text,
    projects text,
    links text,
    "externalLinks" text,
    relations text,
    samples text
);


ALTER TABLE public.dataset_session OWNER TO gigadb;

--
-- Name: dataset_session_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE dataset_session_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.dataset_session_id_seq OWNER TO gigadb;

--
-- Name: dataset_session_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE dataset_session_id_seq OWNED BY dataset_session.id;


--
-- Name: dataset_session_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('dataset_session_id_seq', 26, true);


--
-- Name: dataset_type; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE dataset_type (
    id integer NOT NULL,
    dataset_id integer NOT NULL,
    type_id integer
);


ALTER TABLE public.dataset_type OWNER TO gigadb;

--
-- Name: dataset_type_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE dataset_type_id_seq
    START WITH 37
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.dataset_type_id_seq OWNER TO gigadb;

--
-- Name: dataset_type_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE dataset_type_id_seq OWNED BY dataset_type.id;


--
-- Name: dataset_type_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('dataset_type_id_seq', 258, true);


--
-- Name: exp_attributes; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE exp_attributes (
    id integer NOT NULL,
    exp_id integer,
    attribute_id integer,
    value character varying(1000),
    units_id character varying(50)
);


ALTER TABLE public.exp_attributes OWNER TO gigadb;

--
-- Name: exp_attributes_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE exp_attributes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.exp_attributes_id_seq OWNER TO gigadb;

--
-- Name: exp_attributes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE exp_attributes_id_seq OWNED BY exp_attributes.id;


--
-- Name: exp_attributes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('exp_attributes_id_seq', 5, true);


--
-- Name: experiment; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE experiment (
    id integer NOT NULL,
    experiment_type character varying(100),
    experiment_name character varying(100),
    exp_description character varying(1000),
    dataset_id integer,
    "protocols.io" character varying(200)
);


ALTER TABLE public.experiment OWNER TO gigadb;

--
-- Name: experiment_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE experiment_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.experiment_id_seq OWNER TO gigadb;

--
-- Name: experiment_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE experiment_id_seq OWNED BY experiment.id;


--
-- Name: experiment_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('experiment_id_seq', 3, true);


--
-- Name: extdb; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE extdb (
    id integer NOT NULL,
    database_name character varying(100),
    definition character varying(1000),
    database_homepage character varying(100),
    database_search_url character varying(100)
);


ALTER TABLE public.extdb OWNER TO gigadb;

--
-- Name: extdb_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE extdb_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.extdb_id_seq OWNER TO gigadb;

--
-- Name: extdb_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE extdb_id_seq OWNED BY extdb.id;


--
-- Name: extdb_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('extdb_id_seq', 2, true);


--
-- Name: external_link; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE external_link (
    id integer NOT NULL,
    dataset_id integer NOT NULL,
    url character varying(128) NOT NULL,
    external_link_type_id integer NOT NULL
);


ALTER TABLE public.external_link OWNER TO gigadb;

--
-- Name: external_link_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE external_link_id_seq
    START WITH 17
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.external_link_id_seq OWNER TO gigadb;

--
-- Name: external_link_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE external_link_id_seq OWNED BY external_link.id;


--
-- Name: external_link_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('external_link_id_seq', 61, true);


--
-- Name: external_link_type; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE external_link_type (
    id integer NOT NULL,
    name character varying(45) NOT NULL
);


ALTER TABLE public.external_link_type OWNER TO gigadb;

--
-- Name: external_link_type_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE external_link_type_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.external_link_type_id_seq OWNER TO gigadb;

--
-- Name: external_link_type_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE external_link_type_id_seq OWNED BY external_link_type.id;


--
-- Name: external_link_type_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('external_link_type_id_seq', 4, true);


--
-- Name: file; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE file (
    id integer NOT NULL,
    dataset_id integer NOT NULL,
    name character varying(100) NOT NULL,
    location character varying(200) NOT NULL,
    extension character varying(100) NOT NULL,
    size bigint NOT NULL,
    description text DEFAULT ''::text NOT NULL,
    date_stamp date,
    format_id integer,
    type_id integer,
    code character varying(200) DEFAULT 'FILE_CODE'::character varying,
    index4blast character varying(50),
    download_count integer DEFAULT 0 NOT NULL,
    alternative_location character varying(200)
);


ALTER TABLE public.file OWNER TO gigadb;

--
-- Name: file_attributes; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE file_attributes (
    id integer NOT NULL,
    file_id integer NOT NULL,
    attribute_id integer NOT NULL,
    value character varying(200),
    unit_id character varying(30)
);


ALTER TABLE public.file_attributes OWNER TO gigadb;

--
-- Name: file_attributes_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE file_attributes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.file_attributes_id_seq OWNER TO gigadb;

--
-- Name: file_attributes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE file_attributes_id_seq OWNED BY file_attributes.id;


--
-- Name: file_attributes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('file_attributes_id_seq', 2, true);


--
-- Name: file_experiment; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE file_experiment (
    id integer NOT NULL,
    file_id integer,
    experiment_id integer
);


ALTER TABLE public.file_experiment OWNER TO gigadb;

--
-- Name: file_experiment_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE file_experiment_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.file_experiment_id_seq OWNER TO gigadb;

--
-- Name: file_experiment_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE file_experiment_id_seq OWNED BY file_experiment.id;


--
-- Name: file_experiment_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('file_experiment_id_seq', 1, true);


--
-- Name: file_format; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE file_format (
    id integer NOT NULL,
    name character varying(20) NOT NULL,
    description text DEFAULT ''::text NOT NULL,
    edam_ontology_id character varying(100)
);


ALTER TABLE public.file_format OWNER TO gigadb;

--
-- Name: file_format_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE file_format_id_seq
    START WITH 26
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.file_format_id_seq OWNER TO gigadb;

--
-- Name: file_format_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE file_format_id_seq OWNED BY file_format.id;


--
-- Name: file_format_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('file_format_id_seq', 45, true);


--
-- Name: file_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE file_id_seq
    START WITH 6716
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.file_id_seq OWNER TO gigadb;

--
-- Name: file_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE file_id_seq OWNED BY file.id;


--
-- Name: file_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('file_id_seq', 88270, true);


--
-- Name: file_relationship; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE file_relationship (
    id integer NOT NULL,
    file_id integer NOT NULL,
    related_file_id integer NOT NULL,
    relationship_id integer
);


ALTER TABLE public.file_relationship OWNER TO gigadb;

--
-- Name: file_relationship_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE file_relationship_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.file_relationship_id_seq OWNER TO gigadb;

--
-- Name: file_relationship_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE file_relationship_id_seq OWNED BY file_relationship.id;


--
-- Name: file_relationship_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('file_relationship_id_seq', 4, true);


--
-- Name: file_sample; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE file_sample (
    id integer NOT NULL,
    sample_id integer NOT NULL,
    file_id integer NOT NULL
);


ALTER TABLE public.file_sample OWNER TO gigadb;

--
-- Name: file_sample_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE file_sample_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.file_sample_id_seq OWNER TO gigadb;

--
-- Name: file_sample_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE file_sample_id_seq OWNED BY file_sample.id;


--
-- Name: file_sample_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('file_sample_id_seq', 18935, true);


--
-- Name: file_type; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE file_type (
    id integer NOT NULL,
    name character varying(100) NOT NULL,
    description text DEFAULT ''::text NOT NULL,
    edam_ontology_id character varying(100)
);


ALTER TABLE public.file_type OWNER TO gigadb;

--
-- Name: file_type_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE file_type_id_seq
    START WITH 15
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.file_type_id_seq OWNER TO gigadb;

--
-- Name: file_type_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE file_type_id_seq OWNED BY file_type.id;


--
-- Name: file_type_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('file_type_id_seq', 119, true);


--
-- Name: funder_name; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE funder_name (
    id integer NOT NULL,
    uri character varying(100) NOT NULL,
    primary_name_display character varying(1000),
    country character varying(128) DEFAULT ''::character varying
);


ALTER TABLE public.funder_name OWNER TO gigadb;

--
-- Name: funder_name_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE funder_name_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.funder_name_id_seq OWNER TO gigadb;

--
-- Name: funder_name_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE funder_name_id_seq OWNED BY funder_name.id;


--
-- Name: funder_name_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('funder_name_id_seq', 6171, true);


--
-- Name: gigadb_user; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE gigadb_user (
    id integer NOT NULL,
    email character varying(64) NOT NULL,
    password character varying(128) NOT NULL,
    first_name character varying(100) NOT NULL,
    last_name character varying(100) NOT NULL,
    affiliation character varying(200),
    role character varying(30) DEFAULT 'user'::character varying NOT NULL,
    is_activated boolean DEFAULT false NOT NULL,
    newsletter boolean DEFAULT true NOT NULL,
    previous_newsletter_state boolean DEFAULT false NOT NULL,
    facebook_id text,
    twitter_id text,
    linkedin_id text,
    google_id text,
    username text NOT NULL,
    orcid_id text,
    preferred_link character varying(128) DEFAULT 'EBI'::character varying
);


ALTER TABLE public.gigadb_user OWNER TO gigadb;

--
-- Name: gigadb_user_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE gigadb_user_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.gigadb_user_id_seq OWNER TO gigadb;

--
-- Name: gigadb_user_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE gigadb_user_id_seq OWNED BY gigadb_user.id;


--
-- Name: gigadb_user_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('gigadb_user_id_seq', 345, true);


--
-- Name: image; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE image (
    id integer NOT NULL,
    location character varying(200) DEFAULT ''::character varying NOT NULL,
    tag character varying(300),
    url character varying(256),
    license text NOT NULL,
    photographer character varying(128) NOT NULL,
    source character varying(256) NOT NULL
);


ALTER TABLE public.image OWNER TO gigadb;

--
-- Name: image_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE image_id_seq
    START WITH 31
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.image_id_seq OWNER TO gigadb;

--
-- Name: image_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE image_id_seq OWNED BY image.id;


--
-- Name: image_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('image_id_seq', 225, true);


--
-- Name: link; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE link (
    id integer NOT NULL,
    dataset_id integer NOT NULL,
    is_primary boolean DEFAULT false NOT NULL,
    link character varying(100) NOT NULL,
    description character varying(200)
);


ALTER TABLE public.link OWNER TO gigadb;

--
-- Name: link_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE link_id_seq
    START WITH 66
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.link_id_seq OWNER TO gigadb;

--
-- Name: link_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE link_id_seq OWNED BY link.id;


--
-- Name: link_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('link_id_seq', 294, true);


--
-- Name: link_prefix_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE link_prefix_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.link_prefix_id_seq OWNER TO gigadb;

--
-- Name: link_prefix_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('link_prefix_id_seq', 46, true);


--
-- Name: manuscript; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE manuscript (
    id integer NOT NULL,
    identifier character varying(32) NOT NULL,
    pmid integer,
    dataset_id integer NOT NULL
);


ALTER TABLE public.manuscript OWNER TO gigadb;

--
-- Name: manuscript_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE manuscript_id_seq
    START WITH 27
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.manuscript_id_seq OWNER TO gigadb;

--
-- Name: manuscript_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE manuscript_id_seq OWNED BY manuscript.id;


--
-- Name: manuscript_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('manuscript_id_seq', 284, true);


--
-- Name: news; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE news (
    id integer NOT NULL,
    title character varying(200) NOT NULL,
    body text DEFAULT ''::text NOT NULL,
    start_date date NOT NULL,
    end_date date NOT NULL
);


ALTER TABLE public.news OWNER TO gigadb;

--
-- Name: news_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE news_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.news_id_seq OWNER TO gigadb;

--
-- Name: news_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE news_id_seq OWNED BY news.id;


--
-- Name: news_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('news_id_seq', 3, true);


--
-- Name: prefix; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE prefix (
    id integer DEFAULT nextval('link_prefix_id_seq'::regclass) NOT NULL,
    prefix character(20) NOT NULL,
    url text NOT NULL,
    source character varying(128) DEFAULT ''::character varying,
    icon character varying(100)
);


ALTER TABLE public.prefix OWNER TO gigadb;

--
-- Name: project; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE project (
    id integer NOT NULL,
    url character varying(128) NOT NULL,
    name character varying(255) DEFAULT ''::character varying NOT NULL,
    image_location character varying(100)
);


ALTER TABLE public.project OWNER TO gigadb;

--
-- Name: project_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE project_id_seq
    START WITH 7
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.project_id_seq OWNER TO gigadb;

--
-- Name: project_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE project_id_seq OWNED BY project.id;


--
-- Name: project_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('project_id_seq', 17, true);


--
-- Name: publisher; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE publisher (
    id integer NOT NULL,
    name character varying(45) NOT NULL,
    description text DEFAULT ''::text NOT NULL
);


ALTER TABLE public.publisher OWNER TO gigadb;

--
-- Name: publisher_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE publisher_id_seq
    START WITH 3
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.publisher_id_seq OWNER TO gigadb;

--
-- Name: publisher_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE publisher_id_seq OWNED BY publisher.id;


--
-- Name: publisher_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('publisher_id_seq', 4, true);


--
-- Name: relation; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE relation (
    id integer NOT NULL,
    dataset_id integer NOT NULL,
    related_doi character varying(15) NOT NULL,
    relationship_id integer
);


ALTER TABLE public.relation OWNER TO gigadb;

--
-- Name: relation_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE relation_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.relation_id_seq OWNER TO gigadb;

--
-- Name: relation_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE relation_id_seq OWNED BY relation.id;


--
-- Name: relation_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('relation_id_seq', 84, true);


--
-- Name: relationship; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE relationship (
    id integer NOT NULL,
    name character varying(100)
);


ALTER TABLE public.relationship OWNER TO gigadb;

--
-- Name: relationship_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE relationship_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.relationship_id_seq OWNER TO gigadb;

--
-- Name: relationship_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE relationship_id_seq OWNED BY relationship.id;


--
-- Name: relationship_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('relationship_id_seq', 22, true);


--
-- Name: rss_message; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE rss_message (
    id integer NOT NULL,
    message character varying(128) NOT NULL,
    publication_date date DEFAULT ('now'::text)::date NOT NULL
);


ALTER TABLE public.rss_message OWNER TO gigadb;

--
-- Name: rss_message_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE rss_message_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.rss_message_id_seq OWNER TO gigadb;

--
-- Name: rss_message_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE rss_message_id_seq OWNED BY rss_message.id;


--
-- Name: rss_message_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('rss_message_id_seq', 2, true);


--
-- Name: sample; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE sample (
    id integer NOT NULL,
    species_id integer NOT NULL,
    name character varying(100) DEFAULT 'SAMPLE:SRS188811'::character varying NOT NULL,
    consent_document character varying(45),
    submitted_id integer,
    submission_date date,
    contact_author_name character varying(45),
    contact_author_email character varying(100),
    sampling_protocol character varying(100)
);


ALTER TABLE public.sample OWNER TO gigadb;

--
-- Name: sample_attribute; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE sample_attribute (
    id integer NOT NULL,
    sample_id integer NOT NULL,
    attribute_id integer NOT NULL,
    value character varying(10000),
    unit_id character varying(30)
);


ALTER TABLE public.sample_attribute OWNER TO gigadb;

--
-- Name: sample_attribute_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE sample_attribute_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.sample_attribute_id_seq OWNER TO gigadb;

--
-- Name: sample_attribute_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE sample_attribute_id_seq OWNED BY sample_attribute.id;


--
-- Name: sample_attribute_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('sample_attribute_id_seq', 30060, true);


--
-- Name: sample_experiment; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE sample_experiment (
    id integer NOT NULL,
    sample_id integer,
    experiment_id integer
);


ALTER TABLE public.sample_experiment OWNER TO gigadb;

--
-- Name: sample_experiment_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE sample_experiment_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.sample_experiment_id_seq OWNER TO gigadb;

--
-- Name: sample_experiment_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE sample_experiment_id_seq OWNED BY sample_experiment.id;


--
-- Name: sample_experiment_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('sample_experiment_id_seq', 2, true);


--
-- Name: sample_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE sample_id_seq
    START WITH 210
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.sample_id_seq OWNER TO gigadb;

--
-- Name: sample_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE sample_id_seq OWNED BY sample.id;


--
-- Name: sample_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('sample_id_seq', 4347, true);


--
-- Name: sample_rel; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE sample_rel (
    id integer NOT NULL,
    sample_id integer NOT NULL,
    related_sample_id integer NOT NULL,
    relationship_id integer
);


ALTER TABLE public.sample_rel OWNER TO gigadb;

--
-- Name: sample_rel_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE sample_rel_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.sample_rel_id_seq OWNER TO gigadb;

--
-- Name: sample_rel_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE sample_rel_id_seq OWNED BY sample_rel.id;

--
-- Name: sample_rel_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('sample_rel_id_seq', 8, true);


--
-- Name: curation_log; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE curation_log (
    id integer NOT NULL,
    dataset_id integer NOT NULL,
    CREATION_DATE date, 
    CREATED_BY varchar(20),
    LAST_MODIFIED_DATE date, 
    LAST_MODIFIED_BY varchar(20),
    ACTION varchar(100), 
    COMMENTS varchar(200)
);


ALTER TABLE public.curation_log OWNER TO gigadb;


--
-- Name: curation_log_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE curation_log_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.curation_log_id_seq OWNER TO gigadb;

--
-- Name: sample_rel_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE curation_log_id_seq OWNED BY curation_log.id;


--
-- Name: schemup_tables; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE schemup_tables (
    table_name character varying NOT NULL,
    version character varying NOT NULL,
    is_current boolean DEFAULT false NOT NULL,
    schema text
);


ALTER TABLE public.schemup_tables OWNER TO gigadb;

--
-- Name: search; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE search (
    id integer NOT NULL,
    user_id integer NOT NULL,
    name character varying(128) NOT NULL,
    query text NOT NULL,
    result text
);


ALTER TABLE public.search OWNER TO gigadb;

--
-- Name: search_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE search_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.search_id_seq OWNER TO gigadb;

--
-- Name: search_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE search_id_seq OWNED BY search.id;


--
-- Name: search_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('search_id_seq', 27, true);


--
-- Name: show_accession; Type: VIEW; Schema: public; Owner: gigadb
--

CREATE VIEW show_accession AS
    SELECT ('DOI: '::text || (dataset.identifier)::text) AS doi_number, link.link AS related_accessions FROM (dataset JOIN link ON ((dataset.id = link.dataset_id)));


ALTER TABLE public.show_accession OWNER TO gigadb;

--
-- Name: show_externallink; Type: VIEW; Schema: public; Owner: gigadb
--

CREATE VIEW show_externallink AS
    SELECT ('DOI: '::text || (dataset.identifier)::text) AS doi_number, external_link.url AS additional_information FROM (dataset JOIN external_link ON ((dataset.id = external_link.dataset_id)));


ALTER TABLE public.show_externallink OWNER TO gigadb;

--
-- Name: show_file; Type: VIEW; Schema: public; Owner: gigadb
--

CREATE VIEW show_file AS
    SELECT ('DOI: '::text || (dataset.identifier)::text) AS doi_number, file.name AS file_name FROM (dataset JOIN file ON ((dataset.id = file.dataset_id)));


ALTER TABLE public.show_file OWNER TO gigadb;

--
-- Name: show_manuscript; Type: VIEW; Schema: public; Owner: gigadb
--

CREATE VIEW show_manuscript AS
    SELECT ('DOI: '::text || (dataset.identifier)::text) AS doi_number, manuscript.identifier AS related_manuscript FROM (dataset JOIN manuscript ON ((dataset.id = manuscript.dataset_id)));


ALTER TABLE public.show_manuscript OWNER TO gigadb;

--
-- Name: show_project; Type: VIEW; Schema: public; Owner: gigadb
--

CREATE VIEW show_project AS
    SELECT ('DOI: '::text || (dataset.identifier)::text) AS doi_number, project.name AS project FROM ((dataset JOIN dataset_project ON ((dataset.id = dataset_project.dataset_id))) JOIN project ON ((dataset_project.project_id = project.id)));


ALTER TABLE public.show_project OWNER TO gigadb;

--
-- Name: species; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE species (
    id integer NOT NULL,
    tax_id integer NOT NULL,
    common_name character varying(128),
    genbank_name character varying(128),
    scientific_name character varying(128) NOT NULL,
    eol_link character varying(100)
);


ALTER TABLE public.species OWNER TO gigadb;

--
-- Name: species_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE species_id_seq
    START WITH 28
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.species_id_seq OWNER TO gigadb;

--
-- Name: species_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE species_id_seq OWNED BY species.id;


--
-- Name: species_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('species_id_seq', 1128856, true);

--
-- Name: tbl_migration; Type: TABLE; Schema: public; Owner: gigadb; Tablespace:
--

CREATE TABLE tbl_migration (
    version character varying(180) NOT NULL,
    apply_time integer
);


ALTER TABLE tbl_migration OWNER TO gigadb;


--
-- Name: type; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE type (
    id integer NOT NULL,
    name character varying(32) NOT NULL,
    description text DEFAULT ''::text NOT NULL
);


ALTER TABLE public.type OWNER TO gigadb;

--
-- Name: type_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE type_id_seq
    START WITH 6
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.type_id_seq OWNER TO gigadb;

--
-- Name: type_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE type_id_seq OWNED BY type.id;


--
-- Name: type_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('type_id_seq', 26, true);


--
-- Name: unit; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE unit (
    id character varying(30) NOT NULL,
    name character varying(200),
    definition character varying(500)
);


ALTER TABLE public.unit OWNER TO gigadb;

--
-- Name: COLUMN unit.id; Type: COMMENT; Schema: public; Owner: gigadb
--

COMMENT ON COLUMN unit.id IS 'the ID from the unit ontology';


--
-- Name: COLUMN unit.name; Type: COMMENT; Schema: public; Owner: gigadb
--

COMMENT ON COLUMN unit.name IS 'the name of the unit (taken from the Unit Ontology)';


--
-- Name: COLUMN unit.definition; Type: COMMENT; Schema: public; Owner: gigadb
--

COMMENT ON COLUMN unit.definition IS 'the inition taken from the unit ontology';


--
-- Name: yiisession; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE yiisession (
    id character(32) NOT NULL,
    expire integer,
    data text
);


ALTER TABLE public.yiisession OWNER TO gigadb;


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY user_command ALTER COLUMN id SET DEFAULT nextval('user_command_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY alternative_identifiers ALTER COLUMN id SET DEFAULT nextval('alternative_identifiers_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY attribute ALTER COLUMN id SET DEFAULT nextval('attribute_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY author ALTER COLUMN id SET DEFAULT nextval('author_id_seq'::regclass);

--
-- TOC entry 2479 (class 2604 OID 18508)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY author_rel ALTER COLUMN id SET DEFAULT nextval('author_rel_id_seq'::regclass);

--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset ALTER COLUMN id SET DEFAULT nextval('dataset_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset_attributes ALTER COLUMN id SET DEFAULT nextval('dataset_attributes_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset_author ALTER COLUMN id SET DEFAULT nextval('dataset_author_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset_funder ALTER COLUMN id SET DEFAULT nextval('dataset_funder_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset_log ALTER COLUMN id SET DEFAULT nextval('dataset_log_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset_project ALTER COLUMN id SET DEFAULT nextval('dataset_project_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset_sample ALTER COLUMN id SET DEFAULT nextval('dataset_sample_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset_session ALTER COLUMN id SET DEFAULT nextval('dataset_session_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset_type ALTER COLUMN id SET DEFAULT nextval('dataset_type_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY exp_attributes ALTER COLUMN id SET DEFAULT nextval('exp_attributes_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY experiment ALTER COLUMN id SET DEFAULT nextval('experiment_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY extdb ALTER COLUMN id SET DEFAULT nextval('extdb_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY external_link ALTER COLUMN id SET DEFAULT nextval('external_link_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY external_link_type ALTER COLUMN id SET DEFAULT nextval('external_link_type_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY file ALTER COLUMN id SET DEFAULT nextval('file_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY file_attributes ALTER COLUMN id SET DEFAULT nextval('file_attributes_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY file_experiment ALTER COLUMN id SET DEFAULT nextval('file_experiment_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY file_format ALTER COLUMN id SET DEFAULT nextval('file_format_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY file_relationship ALTER COLUMN id SET DEFAULT nextval('file_relationship_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY file_sample ALTER COLUMN id SET DEFAULT nextval('file_sample_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY file_type ALTER COLUMN id SET DEFAULT nextval('file_type_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY funder_name ALTER COLUMN id SET DEFAULT nextval('funder_name_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY gigadb_user ALTER COLUMN id SET DEFAULT nextval('gigadb_user_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY image ALTER COLUMN id SET DEFAULT nextval('image_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY link ALTER COLUMN id SET DEFAULT nextval('link_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY manuscript ALTER COLUMN id SET DEFAULT nextval('manuscript_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY news ALTER COLUMN id SET DEFAULT nextval('news_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY project ALTER COLUMN id SET DEFAULT nextval('project_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY publisher ALTER COLUMN id SET DEFAULT nextval('publisher_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY relation ALTER COLUMN id SET DEFAULT nextval('relation_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY relationship ALTER COLUMN id SET DEFAULT nextval('relationship_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY rss_message ALTER COLUMN id SET DEFAULT nextval('rss_message_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY sample ALTER COLUMN id SET DEFAULT nextval('sample_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY sample_attribute ALTER COLUMN id SET DEFAULT nextval('sample_attribute_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY sample_experiment ALTER COLUMN id SET DEFAULT nextval('sample_experiment_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY sample_rel ALTER COLUMN id SET DEFAULT nextval('sample_rel_id_seq'::regclass);

--
-- TOC entry 2479 (class 2604 OID 18508)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY curation_log ALTER COLUMN id SET DEFAULT nextval('curation_log_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY search ALTER COLUMN id SET DEFAULT nextval('search_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY species ALTER COLUMN id SET DEFAULT nextval('species_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY type ALTER COLUMN id SET DEFAULT nextval('type_id_seq'::regclass);


--
-- Data for Name: AuthAssignment; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY "AuthAssignment" (itemname, userid, bizrule, data) FROM stdin;
\.


--
-- Data for Name: AuthItem; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY "AuthItem" (name, type, description, bizrule, data) FROM stdin;
\.


--
-- Data for Name: YiiSession; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY "YiiSession" (id, expire, data) FROM stdin;
6jsqqar06qp73kd5lsn5uaona5      	1510721051	
\.


--
-- Data for Name: alternative_identifiers; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY alternative_identifiers (id, sample_id, extdb_id, extdb_accession) FROM stdin;
\.


--
-- Data for Name: attribute; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY attribute (id, attribute_name, definition, model, structured_comment_name, value_syntax, allowed_units, occurance, ontology_link, note) FROM stdin;
422	Source material identifiers	\N	\N	\N	\N	\N	\N	\N	\N
497	urltoredirect	\N	\N	urltoredirect	\N	\N	\N	\N	\N
455	keyword	\N	\N	keywords	\N	\N	\N	\N	\N
269	Geographic location (latitude and longitude)	\N	\N	\N	\N	\N	\N	\N	\N
\.

--
-- Data for Name: tbl_migration; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY public.tbl_migration (version, apply_time) FROM stdin;
m000000_000000_base 1541674918
\.

--
-- Data for Name: author; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY author (id, surname, middle_name, first_name, orcid, gigadb_user_id) FROM stdin;
3789	Lambert	M	David		\N
3790	Wang		Jun		\N
3791	Zhang		Guojie		\N
3792	Cheng		S		\N
3793	Liu		X		\N
3794	Pan		S		\N
3795	Quan		Z		\N
3796	Xie		M		\N
3797	Xu		X		\N
3798	Yue		Z		\N
3799	Zeng		P		\N
3800	Zhan		D		\N
3801	Li		Y		\N
3802	Zhao		Z		\N
3803	Chang	C	C		\N
3804	Chow	C	C		\N
3805	Hsu	D	S		\N
3806	Lee	J	J		\N
3807	Vattikuti		S		\N
3808	Alnasir		J		\N
3809	Shanahan		H		\N
\.


--
-- Data for Name: dataset; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY dataset (id, submitter_id, image_id, identifier, title, description, dataset_size, ftp_site, upload_status, excelfile, excelfile_md5, publication_date, modification_date, publisher_id, token, fairnuse) FROM stdin;
210	345	222	100002	Genomic data from Adelie penguin (Pygoscelis adeliae).	The Adelie penguin (Pygoscelis adeliae) is an iconic penguin of moderate stature and a tuxedo of black and white feathers. The penguins are only found in the Antarctic region and surrounding islands. Being very sensitive to climate change, and due to changes in their behavior based on minor shifts in climate, they are often used as a barometer of the Antarctic. \r\nWith its status as one of the adorable and cuddly flightless birds of Antarctica, they serve as an example for conservation, and as a result they are now categorised at low risk for endangerment. The sequence of the penguin can be of use in understanding the genetic underpinnings of its evolutionary traits and adaptation to its extreme environment; its unique system of feathers; its prowess as a diver; and its sensitivity to climate change. We hope that this genome data will further our understanding of one of the most remarkable creatures to waddle the planet Earth.\r\nWe sequenced the genome of an adult male from Inexpressible Island, Ross Sea, Antartica (provided by David Lambert) to a depth of approximately 60X with short reads from a series of libraries with various insert sizes (200bp- 20kb). The assembled scaffolds of high quality sequences total 1.23 Gb, with the contig and scaffold N50 values of 19 kb and 5 mb respectively. We identified 15,270 protein-coding genes with a mean length of 21.3 kb.	1073741824	ftp://climb.genomics.cn/pub/10.5524/100001_101000/100002	Published	\N	\N	2016-05-09	\N	1	\N	\N
211	345	223	100003	Genome data from foxtail millet (Setaria italica).	Foxtail millet (Setaria italica) (2n=18), is an annual grass grown both as cereal crop (grain production) and as forage food. It is primarily grown in temperate, subtropical and tropical areas. With approximately 6,000 varieties, millet is one member of the Panicoideae (grasses subfamily), which includes maize (Zea mays), sorghum (Sorghum bicolor), and sugar cane (Saccharum officinarum). It is a nutritious dietary staple, containing starch, proteins, and a number of vitamins and minerals, such as calcium, iron, and sodium. It feeds nearly one-third of the world population with main daily-calories intake, and is especially prevalent in dry climates or soil-poor regions that are not suited for the cultivation of many other crops. Millet is self-pollinating, has a short lifecycle, is small in stature, and has a small genome size; all of these useful attributes make it an invaluable functional genomics model system, and an excellent reference genome to aid in the sequencing of other larger grasses genomes.	314572800	ftp://climb.genomics.cn/pub/10.5524/100001_101000/100003	Published	\N	\N	2011-11-12	\N	1	\N	\N
212	345	224	100004	Data and software to accompany the paper: Applying compressed sensing to genome-wide association studies.	The aim of a genome-wide association study (GWAS) is to isolate DNA markers for variants affecting phenotypes of interest. Linear regression is employed for this purpose, and in recent years a signal-processing paradigm known as compressed sensing (CS) has coalesced around a particular class of regression techniques. CS is not a method in its own right, but rather a body of theory regarding signal recovery when the number of predictor variables (i.e., genotyped markers) exceeds the sample size. The paper shows the applicability of compressed sensing (CS) theory to genome-wide association studies (GWAS), where the purpose is to ﬁnd trait-associated tagging markers (genetic variants). Analysis scripts are contained in the compressed CS file. Mock data and scripts are found in the compressed GD file. The example scripts found in the CS repository require the GD files to be unpacked in a separate folder. Please look at accompanying readme pdfs for both repositories and annotations in the example scripts before using.	209715200	ftp://climb.genomics.cn/pub/10.5524/100001_101000/100004	Published	\N	\N	2016-05-11	\N	1	\N	\N
213	345	225	100005	Supporting scripts and data for "Investigation into the annotation of protocol sequencing steps in the Sequence Read Archive".	The workflow for the production of high-throughput sequencing data from nucleic acid samples is complex. There are a series of protocol steps to be followed in the preparation of samples for next-generation sequencing. The quantification of bias in a number of protocol steps, namely DNA fractionation, blunting, phosphorylation, adapter ligation and library enrichment, remains to be determined. We examined the experimental metadata of the public repository Sequence Read Archive (SRA) in order to ascertain the level of annotation of important sequencing steps in submissions to the database.	524288000	ftp://climb.genomics.cn/pub/10.5524/100001_101000/100005	Published	\N	\N	2016-05-11	\N	1	\N	\N
\.


--
-- Data for Name: dataset_attributes; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY dataset_attributes (id, dataset_id, attribute_id, value, units_id, image_id, until_date) FROM stdin;
\.


--
-- Data for Name: dataset_author; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY dataset_author (id, dataset_id, author_id, rank, role) FROM stdin;
3478	210	3789	1	\N
3479	210	3790	2	\N
3480	210	3791	3	\N
3481	211	3792	1	\N
3482	211	3793	2	\N
3483	211	3794	3	\N
3484	211	3795	4	\N
3485	211	3796	5	\N
3486	212	3803	1	\N
3487	212	3804	2	\N
3488	212	3805	3	\N
3489	212	3806	4	\N
3490	212	3807	5	\N
3491	213	3808	1	\N
3492	213	3809	2	\N
\.


--
-- Data for Name: dataset_funder; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY dataset_funder (id, dataset_id, funder_id, grant_award, comments, awardee) FROM stdin;
\.


--
-- Data for Name: dataset_log; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY dataset_log (id, dataset_id, message, created_at, model, model_id, url) FROM stdin;
\.


--
-- Data for Name: dataset_project; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY dataset_project (id, dataset_id, project_id) FROM stdin;
126	210	16
127	210	17
\.


--
-- Data for Name: dataset_sample; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY dataset_sample (id, dataset_id, sample_id) FROM stdin;
4354	210	4346
4355	211	4347
\.


--
-- Data for Name: dataset_session; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY dataset_session (id, identifier, dataset, dataset_id, datasettypes, images, authors, projects, links, "externalLinks", relations, samples) FROM stdin;
\.


--
-- Data for Name: dataset_type; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY dataset_type (id, dataset_id, type_id) FROM stdin;
255	210	25
256	211	18
257	212	21
258	213	26
\.


--
-- Data for Name: exp_attributes; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY exp_attributes (id, exp_id, attribute_id, value, units_id) FROM stdin;
\.


--
-- Data for Name: experiment; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY experiment (id, experiment_type, experiment_name, exp_description, dataset_id, "protocols.io") FROM stdin;
\.


--
-- Data for Name: extdb; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY extdb (id, database_name, definition, database_homepage, database_search_url) FROM stdin;
\.


--
-- Data for Name: external_link; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY external_link (id, dataset_id, url, external_link_type_id) FROM stdin;
60	212	https://github.com/ShashaankV/CS	3
61	212	https://github.com/ShashaankV/GD	3
\.


--
-- Data for Name: external_link_type; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY external_link_type (id, name) FROM stdin;
3	Additional information
4	Genome browser
5	Protocols.io
6	JBrowse
7	3D Models
\.


--
-- Data for Name: file; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY file (id, dataset_id, name, location, extension, size, description, date_stamp, format_id, type_id, code, index4blast, download_count, alternative_location) FROM stdin;
88252	210	Pygoscelis_adeliae.cds.gz	ftp://climb.genomics.cn/pub/10.5524/100001_101000/100006/phylogeny_study_update/Pygoscelis_adeliae.cds.gz	cds	6	coding sequence predictions on genome assembly	2014-05-12	41	117	FILE_CODE	\N	0	\N
88254	210	Pygoscelis_adeliae.fa.gz	ftp://climb.genomics.cn/pub/10.5524/100001_101000/100006/phylogeny_study_update/Pygoscelis_adeliae.fa.gz	gz	350	assembled scaffolds from sequence data	2014-05-12	41	113	FILE_CODE	\N	0	\N
88255	210	Pygoscelis_adeliae.gff.gz	ftp://climb.genomics.cn/pub/10.5524/100001_101000/100006/phylogeny_study_update/Pygoscelis_adeliae.gff.gz	gz	1590	coding sequence annotation of assembly	2014-05-12	42	114	FILE_CODE	\N	0	\N
88256	210	Pygoscelis_adeliae.pep.gz	ftp://climb.genomics.cn/pub/10.5524/100001_101000/100006/phylogeny_study_update/Pygoscelis_adeliae.pep.gz	gz	4170000	peptide translations of CDS predictions	2014-05-12	41	115	FILE_CODE	\N	0	\N
88257	210	Pygoscelis_adeliae.RepeatMasker.out.gz	ftp://climb.genomics.cn/pub/10.5524/100001_101000/100006/phylogeny_study_update/Pygoscelis_adeliae.RepeatMasker.out.gz	gz	7490000	repeat masker results	2014-05-12	43	116	FILE_CODE	\N	0	\N
88258	210	Pygoscelis_adeliae.scaf.fa.gz	ftp://climb.genomics.cn/pub/10.5524/100001_101000/100006/Pygoscelis_adeliae.scaf.fa.gz	gz	350		2014-05-12	41	113	FILE_CODE	\N	0	\N
88259	210	readme.txt	ftp://climb.genomics.cn/pub/10.5524/100001_101000/100006/readme.txt	txt	1		2014-05-12	43	112	FILE_CODE	\N	0	\N
88261	211	millet.chr.version2.3.fa.gz	ftp://climb.genomics.cn/pub/10.5524/100001_101000/100020/millet.chr.version2.3.fa.gz	gz	109		2011-11-12	41	113	FILE_CODE	\N	0	\N
88262	211	Millet.fa.glean.cds.v3.gz	ftp://climb.genomics.cn/pub/10.5524/100001_101000/100020/Millet.fa.glean.cds.v3.gz	gz	13000		2011-11-12	41	117	FILE_CODE	\N	0	\N
88263	211	Millet.fa.glean.pep.v3.gz	ftp://climb.genomics.cn/pub/10.5524/100001_101000/100020/Millet.fa.glean.pep.v3.gz	gz	85000000		2011-11-12	41	115	FILE_CODE	\N	0	\N
88264	211	Millet.fa.glean.v3.gff	ftp://climb.genomics.cn/pub/10.5524/100001_101000/100020/Millet.fa.glean.v3.gff	gz	14000000		2011-11-12	42	114	FILE_CODE	\N	0	\N
88265	211	Millet_scaffoldVersion2.3.fa.gz	ftp://climb.genomics.cn/pub/10.5524/100001_101000/100020/Millet_scaffoldVersion2.3.fa.gz	gz	109000		2011-11-12	41	113	FILE_CODE	\N	0	\N
88266	211	readme.txt	ftp://climb.genomics.cn/pub/10.5524/100001_101000/100020/readme.txt	txt	1		2011-11-12	43	112	FILE_CODE	\N	0	\N
88267	212	CS-master.tar.gz	ftp://climb.genomics.cn/pub/10.5524/100001_101000/100094/CS-master.tar.gz	gz	114	compressed archive of the Analysis scripts (CS) files	2014-06-06	44	118	FILE_CODE	\N	0	\N
88269	212	GD-master.tar.gz	ftp://climb.genomics.cn/pub/10.5524/100001_101000/100094/GD-master.tar.gz	gz	163	compressed archive of the mock data and scripts GD files	2014-06-06	44	119	FILE_CODE	\N	0	\N
88270	213	Diagram-ALL-FIELDS-Check-annotation.jpg	ftp://climb.genomics.cn/pub/10.5524/100001_101000/100142/Diagram-ALL-FIELDS-Check-annotation.jpg	jpg	54	image used in manuscript	2015-04-29	41	113	FILE_CODE	\N	0	\N
\.


--
-- Data for Name: file_attributes; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY file_attributes (id, file_id, attribute_id, value, unit_id) FROM stdin;
\.


--
-- Data for Name: file_experiment; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY file_experiment (id, file_id, experiment_id) FROM stdin;
\.


--
-- Data for Name: file_format; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY file_format (id, name, description, edam_ontology_id) FROM stdin;
41	FASTA	A text-based format which begins with a single-line description, followed by lines of sequence data	\N
42	GFF	The General Feature Format (GFF) is used for describing genes and other features of DNA, RNA and protein sequences	\N
43	TEXT	(.doc, .readme, .text, .txt) - a text file	\N
44	TAR	(.tar) - an archive containing other files	\N
45	PDF	(.pdf) - portable document format	\N
\.


--
-- Data for Name: file_relationship; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY file_relationship (id, file_id, related_file_id, relationship_id) FROM stdin;
\.


--
-- Data for Name: file_sample; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY file_sample (id, sample_id, file_id) FROM stdin;
18916	4346	88252
18918	4346	88254
18919	4346	88255
18920	4346	88256
18921	4346	88257
18922	4346	88258
18925	4347	88261
18926	4347	88262
18927	4347	88263
18928	4347	88264
18929	4347	88265
18930	4347	88266
\.


--
-- Data for Name: file_type; Type: TABLE DATA; Schema: public; Owner: gigadb
--


COPY file_type (id, name, description, edam_ontology_id) FROM stdin;
112	Readme		\N
113	Sequence assembly		\N
114	Annotation		\N
115	Protein sequence		\N
116	Repeat sequence		\N
117	Coding sequence		\N
118	Script		\N
119	Mixed archive		\N
\.


--
-- Data for Name: funder_name; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY funder_name (id, uri, primary_name_display, country) FROM stdin;
\.


--
-- Data for Name: gigadb_user; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY gigadb_user (id, email, password, first_name, last_name, affiliation, role, is_activated, newsletter, previous_newsletter_state, facebook_id, twitter_id, linkedin_id, google_id, username, orcid_id, preferred_link) FROM stdin;
344	admin@gigadb.org	5a4f75053077a32e681f81daa8792f95	Joe	Bloggs	BGI	admin	t	f	t	\N	\N	\N	\N	test@gigadb.org	\N	EBI
345	user@gigadb.org	5a4f75053077a32e681f81daa8792f95	John	Smith	BGI	user	t	f	t	\N	\N	\N	\N	user@gigadb.org	\N	EBI
\.


--
-- Data for Name: image; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY image (id, location, tag, url, license, photographer, source) FROM stdin;
221	ftp	\N	\N	cc0	jesse	gigadb
222	100006_Pygoscelis_adeliae.jpg	Adelie penguin\t	http://gigadb.org/images/data/cropped/100006_Pygoscelis_adeliae.jpg	Public Domain, US Government	Michael Van Woert, 1998	NOAA Photo Library
223	100020_Setaria_italica.jpg	Foxtail millet	http://gigadb.org/images/data/cropped/100020_Setaria_italica.jpg	GNU Free Documentation License, CC SA	Markus Hagenlocher	Wikimedia Commons
224	Images_147.png	CS icon 1	http://gigadb.org/images/uploads/image_upload/Images_147.png	public domain	Shashaank Vattikuti	Gigascience
225	100142.jpg	Overlap between different protocol search terms	http://gigadb.org/images/data/cropped/100142.jpg	CC0	Alnasir and Shanahan 2015	Alnasir and Shanahan 2015
\.


--
-- Data for Name: link; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY link (id, dataset_id, is_primary, link, description) FROM stdin;
\.


--
-- Data for Name: manuscript; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY manuscript (id, identifier, pmid, dataset_id) FROM stdin;
\.


--
-- Data for Name: news; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY news (id, title, body, start_date, end_date) FROM stdin;
\.


--
-- Data for Name: prefix; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY prefix (id, prefix, url, source, icon) FROM stdin;
45	ENA                 	http://www.ebi.ac.uk/ena/data/view/	EBI	\N
46	SRA                 	http://www.ncbi.nlm.nih.gov/sra?term=	NCBI	\N
\.


--
-- Data for Name: project; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY project (id, url, name, image_location) FROM stdin;
16	http://www.genome10k.org/	Genome 10K	http://gigadb.org/images/project/G10Klogo.jpg
17	http://avian.genomics.cn/en/index.html	The Avian Phylogenomic Project	http://gigadb.org/images/project/phylogenomiclogo.png
\.


--
-- Data for Name: publisher; Type: TABLE DATA; Schema: public; Owner: gigadb
--


COPY publisher (id, name, description) FROM stdin;
1	GigaScience	
\.


--
-- Data for Name: relation; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY relation (id, dataset_id, related_doi, relationship_id) FROM stdin;
\.


--
-- Data for Name: relationship; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY relationship (id, name) FROM stdin;
22	IsIdenticalTo
\.


--
-- Data for Name: rss_message; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY rss_message (id, message, publication_date) FROM stdin;
\.


--
-- Data for Name: sample; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY sample (id, species_id, name, consent_document, submitted_id, submission_date, contact_author_name, contact_author_email, sampling_protocol) FROM stdin;
4346	1128854	Pygoscelis_adeliae	\N	345	2016-05-09	John Smith	user@gigadb.org	\N
4347	1128855	Zhang gu	\N	345	2016-05-11	John Smith	user@gigadb.org	\N
\.


--
-- Data for Name: sample_attribute; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY sample_attribute (id, sample_id, attribute_id, value, unit_id) FROM stdin;
30060	4346	422	David Lambert & BGI	\N
30061	4346	269	35.904731, 127.162202	\N
30062	4347	269	16.606667,  49.259444	\N
\.


--
-- Data for Name: sample_experiment; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY sample_experiment (id, sample_id, experiment_id) FROM stdin;
\.


--
-- Data for Name: sample_rel; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY sample_rel (id, sample_id, related_sample_id, relationship_id) FROM stdin;
\.


--
-- Data for Name: schemup_tables; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY schemup_tables (table_name, version, is_current, schema) FROM stdin;
\.


--
-- Data for Name: search; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY search (id, user_id, name, query, result) FROM stdin;
\.


--
-- Data for Name: species; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY species (id, tax_id, common_name, genbank_name, scientific_name, eol_link) FROM stdin;
1128854	9238	Adelie penguin	Adelie penguin	Pygoscelis adeliae	\N
1128855	4555	Foxtail millet	foxtail millet	Setaria italica	\N
1128856	-1	None assigned	None assigned	None assigned	\N
\.


--
-- Data for Name: type; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY type (id, name, description) FROM stdin;
17	Workflow	data analysis pipelines
18	Epigenomic	methylation and histone modification data
19	Metagenomic	genetic and genomic data from environmental samples
20	Transcriptomic	data relating to mRNA
21	Software	computational tools for analysing and managing biological data
22	Imaging	data involving the visual depiction of biological samples
23	Metabolomic	
24	Proteomic	large scale protein analysis dataset
25	Genomic	genetic and genomic data e.g. sequence and assemblies
26	Metadata	
\.


--
-- Data for Name: unit; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY unit (id, name, definition) FROM stdin;
\.


--
-- Data for Name: yiisession; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY yiisession (id, expire, data) FROM stdin;
\.


--
-- Name: AuthAssignment_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY "AuthAssignment"
    ADD CONSTRAINT "AuthAssignment_pkey" PRIMARY KEY (itemname, userid);


--
-- Name: AuthItem_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY "AuthItem"
    ADD CONSTRAINT "AuthItem_pkey" PRIMARY KEY (name);

--
-- Name: user_command_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY user_command
    ADD CONSTRAINT user_command_pkey PRIMARY KEY (id);


--
-- Name: YiiSession_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY "YiiSession"
    ADD CONSTRAINT "YiiSession_pkey" PRIMARY KEY (id);


--
-- Name: alternative_identifiers_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY alternative_identifiers
    ADD CONSTRAINT alternative_identifiers_pkey PRIMARY KEY (id);


--
-- Name: attribute_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY attribute
    ADD CONSTRAINT attribute_pkey PRIMARY KEY (id);


--
-- Name: author_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY author
    ADD CONSTRAINT author_pkey PRIMARY KEY (id);

--
-- TOC entry 2496 (class 2606 OID 18532)
-- Name: author_rel_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY author_rel
    ADD CONSTRAINT author_rel_pkey PRIMARY KEY (id);

--
-- Name: dataset_attributes_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY dataset_attributes
    ADD CONSTRAINT dataset_attributes_pkey PRIMARY KEY (id);


--
-- Name: dataset_author_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY dataset_author
    ADD CONSTRAINT dataset_author_pkey PRIMARY KEY (id);


--
-- Name: dataset_funder_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY dataset_funder
    ADD CONSTRAINT dataset_funder_pkey PRIMARY KEY (id);


--
-- Name: dataset_log_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY dataset_log
    ADD CONSTRAINT dataset_log_pkey PRIMARY KEY (id);


--
-- Name: dataset_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY dataset
    ADD CONSTRAINT dataset_pkey PRIMARY KEY (id);


--
-- Name: dataset_project_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY dataset_project
    ADD CONSTRAINT dataset_project_pkey PRIMARY KEY (id);


--
-- Name: dataset_sample_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY dataset_sample
    ADD CONSTRAINT dataset_sample_pkey PRIMARY KEY (id);


--
-- Name: dataset_session_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY dataset_session
    ADD CONSTRAINT dataset_session_pkey PRIMARY KEY (id);


--
-- Name: dataset_type_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY dataset_type
    ADD CONSTRAINT dataset_type_pkey PRIMARY KEY (id);

--
-- Name: curation_log_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY curation_log
    ADD CONSTRAINT curation_log_pkey PRIMARY KEY (id);


--
-- Name: email_unique; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY gigadb_user
    ADD CONSTRAINT email_unique UNIQUE (email);


--
-- Name: exp_attributes_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY exp_attributes
    ADD CONSTRAINT exp_attributes_pkey PRIMARY KEY (id);


--
-- Name: experiment_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY experiment
    ADD CONSTRAINT experiment_pkey PRIMARY KEY (id);


--
-- Name: extdb_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY extdb
    ADD CONSTRAINT extdb_pkey PRIMARY KEY (id);


--
-- Name: external_link_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY external_link
    ADD CONSTRAINT external_link_pkey PRIMARY KEY (id);


--
-- Name: external_link_type_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY external_link_type
    ADD CONSTRAINT external_link_type_pkey PRIMARY KEY (id);


--
-- Name: file_attributes_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY file_attributes
    ADD CONSTRAINT file_attributes_pkey PRIMARY KEY (id);


--
-- Name: file_experiment_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY file_experiment
    ADD CONSTRAINT file_experiment_pkey PRIMARY KEY (id);


--
-- Name: file_format_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY file_format
    ADD CONSTRAINT file_format_pkey PRIMARY KEY (id);


--
-- Name: file_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY file
    ADD CONSTRAINT file_pkey PRIMARY KEY (id);


--
-- Name: file_relationship_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY file_relationship
    ADD CONSTRAINT file_relationship_pkey PRIMARY KEY (id);


--
-- Name: file_sample_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY file_sample
    ADD CONSTRAINT file_sample_pkey PRIMARY KEY (id);


--
-- Name: file_type_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY file_type
    ADD CONSTRAINT file_type_pkey PRIMARY KEY (id);


--
-- Name: funder_name_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY funder_name
    ADD CONSTRAINT funder_name_pkey PRIMARY KEY (id);


--
-- Name: gigadb_user_facebook_id_key; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY gigadb_user
    ADD CONSTRAINT gigadb_user_facebook_id_key UNIQUE (facebook_id);


--
-- Name: gigadb_user_google_id_key; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY gigadb_user
    ADD CONSTRAINT gigadb_user_google_id_key UNIQUE (google_id);


--
-- Name: gigadb_user_linked_id_key; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY gigadb_user
    ADD CONSTRAINT gigadb_user_linked_id_key UNIQUE (linkedin_id);


--
-- Name: gigadb_user_orcid_id_key; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY gigadb_user
    ADD CONSTRAINT gigadb_user_orcid_id_key UNIQUE (orcid_id);


--
-- Name: gigadb_user_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY gigadb_user
    ADD CONSTRAINT gigadb_user_pkey PRIMARY KEY (id);


--
-- Name: gigadb_user_twitter_id_key; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY gigadb_user
    ADD CONSTRAINT gigadb_user_twitter_id_key UNIQUE (twitter_id);


--
-- Name: gigadb_user_username_key; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY gigadb_user
    ADD CONSTRAINT gigadb_user_username_key UNIQUE (username);


--
-- Name: image_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY image
    ADD CONSTRAINT image_pkey PRIMARY KEY (id);


--
-- Name: link_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY link
    ADD CONSTRAINT link_pkey PRIMARY KEY (id);


--
-- Name: link_prefix_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY prefix
    ADD CONSTRAINT link_prefix_pkey PRIMARY KEY (id);


--
-- Name: manuscript_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY manuscript
    ADD CONSTRAINT manuscript_pkey PRIMARY KEY (id);


--
-- Name: news_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY news
    ADD CONSTRAINT news_pkey PRIMARY KEY (id);


--
-- Name: project_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY project
    ADD CONSTRAINT project_pkey PRIMARY KEY (id);


--
-- Name: publisher_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY publisher
    ADD CONSTRAINT publisher_pkey PRIMARY KEY (id);


--
-- Name: relation_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY relation
    ADD CONSTRAINT relation_pkey PRIMARY KEY (id);


--
-- Name: relationship_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY relationship
    ADD CONSTRAINT relationship_pkey PRIMARY KEY (id);


--
-- Name: rss_message_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY rss_message
    ADD CONSTRAINT rss_message_pkey PRIMARY KEY (id);


--
-- Name: sample_attribute_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY sample_attribute
    ADD CONSTRAINT sample_attribute_pkey PRIMARY KEY (id);


--
-- Name: sample_experiment_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY sample_experiment
    ADD CONSTRAINT sample_experiment_pkey PRIMARY KEY (id);


--
-- Name: sample_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY sample
    ADD CONSTRAINT sample_pkey PRIMARY KEY (id);


--
-- Name: sample_rel_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY sample_rel
    ADD CONSTRAINT sample_rel_pkey PRIMARY KEY (id);


--
-- Name: search_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY search
    ADD CONSTRAINT search_pkey PRIMARY KEY (id);


--
-- Name: species_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY species
    ADD CONSTRAINT species_pkey PRIMARY KEY (id);


--
-- Name: tbl_migration_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY tbl_migration
    ADD CONSTRAINT tbl_migration_pkey PRIMARY KEY (version);

--
-- Name: type_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY type
    ADD CONSTRAINT type_pkey PRIMARY KEY (id);


--
-- Name: un_dataset_funder; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY dataset_funder
    ADD CONSTRAINT un_dataset_funder UNIQUE (dataset_id, funder_id);


--
-- Name: unit_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY unit
    ADD CONSTRAINT unit_pkey PRIMARY KEY (id);


--
-- Name: yiisession_pkey1; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY yiisession
    ADD CONSTRAINT yiisession_pkey1 PRIMARY KEY (id);


--
-- Name: fki_sample_attribute_fkey; Type: INDEX; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE INDEX fki_sample_attribute_fkey ON sample_attribute USING btree (attribute_id);


--
-- Name: identifier_idx; Type: INDEX; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE UNIQUE INDEX identifier_idx ON dataset USING btree (identifier);


--
-- Name: AuthAssignment_itemname_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY "AuthAssignment"
    ADD CONSTRAINT "AuthAssignment_itemname_fkey" FOREIGN KEY (itemname) REFERENCES "AuthItem"(name) ON UPDATE CASCADE ON DELETE CASCADE;

--
-- Name: curation_log_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY "curation_log"
    ADD CONSTRAINT "curation_log_dataset_id_fkey" FOREIGN KEY (dataset_id) REFERENCES "dataset"(id) ON UPDATE NO ACTION ON DELETE CASCADE;

--
-- Name: dataset_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY "dataset"
    ADD CONSTRAINT "dataset_curator_id_fkey" FOREIGN KEY (curator_id) REFERENCES "gigadb_user"(id) ON UPDATE NO ACTION ON DELETE NO ACTION;

--
-- Name: alternative_identifiers_extdb_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY alternative_identifiers
    ADD CONSTRAINT alternative_identifiers_extdb_id_fkey FOREIGN KEY (extdb_id) REFERENCES extdb(id);


--
-- Name: alternative_identifiers_sample_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY alternative_identifiers
    ADD CONSTRAINT alternative_identifiers_sample_id_fkey FOREIGN KEY (sample_id) REFERENCES sample(id);


--
-- Name: dataset_attributes_attribute_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset_attributes
    ADD CONSTRAINT dataset_attributes_attribute_id_fkey FOREIGN KEY (attribute_id) REFERENCES attribute(id);


--
-- Name: dataset_attributes_dataset_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset_attributes
    ADD CONSTRAINT dataset_attributes_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES dataset(id);


--
-- Name: dataset_attributes_units_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset_attributes
    ADD CONSTRAINT dataset_attributes_units_id_fkey FOREIGN KEY (units_id) REFERENCES unit(id);


--
-- Name: dataset_author_author_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset_author
    ADD CONSTRAINT dataset_author_author_id_fkey FOREIGN KEY (author_id) REFERENCES author(id) ON DELETE CASCADE;


--
-- Name: dataset_author_dataset_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset_author
    ADD CONSTRAINT dataset_author_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES dataset(id) ON DELETE CASCADE;


--
-- Name: dataset_funder_dataset_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset_funder
    ADD CONSTRAINT dataset_funder_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES dataset(id) ON DELETE CASCADE;


--
-- Name: dataset_funder_funder_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset_funder
    ADD CONSTRAINT dataset_funder_funder_id_fkey FOREIGN KEY (funder_id) REFERENCES funder_name(id) ON DELETE CASCADE;


--
-- Name: dataset_image_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset
    ADD CONSTRAINT dataset_image_id_fkey FOREIGN KEY (image_id) REFERENCES image(id) ON DELETE SET NULL;


--
-- Name: dataset_log_dataset_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset_log
    ADD CONSTRAINT dataset_log_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES dataset(id) ON DELETE CASCADE;


--
-- Name: dataset_project_dataset_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset_project
    ADD CONSTRAINT dataset_project_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES dataset(id) ON DELETE CASCADE;


--
-- Name: dataset_project_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset_project
    ADD CONSTRAINT dataset_project_project_id_fkey FOREIGN KEY (project_id) REFERENCES project(id) ON DELETE CASCADE;


--
-- Name: dataset_sample_dataset_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset_sample
    ADD CONSTRAINT dataset_sample_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES dataset(id) ON DELETE CASCADE;


--
-- Name: dataset_sample_sample_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset_sample
    ADD CONSTRAINT dataset_sample_sample_id_fkey FOREIGN KEY (sample_id) REFERENCES sample(id) ON DELETE CASCADE;


--
-- Name: dataset_submitter_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset
    ADD CONSTRAINT dataset_submitter_id_fkey FOREIGN KEY (submitter_id) REFERENCES gigadb_user(id) ON DELETE RESTRICT;


--
-- Name: dataset_type_dataset_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset_type
    ADD CONSTRAINT dataset_type_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES dataset(id) ON DELETE CASCADE;


--
-- Name: exp_attributes_attribute_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY exp_attributes
    ADD CONSTRAINT exp_attributes_attribute_id_fkey FOREIGN KEY (attribute_id) REFERENCES attribute(id);


--
-- Name: exp_attributes_exp_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY exp_attributes
    ADD CONSTRAINT exp_attributes_exp_id_fkey FOREIGN KEY (exp_id) REFERENCES experiment(id);


--
-- Name: exp_attributes_units_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY exp_attributes
    ADD CONSTRAINT exp_attributes_units_id_fkey FOREIGN KEY (units_id) REFERENCES unit(id);


--
-- Name: experiment_dataset_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY experiment
    ADD CONSTRAINT experiment_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES dataset(id);


--
-- Name: external_link_dataset_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY external_link
    ADD CONSTRAINT external_link_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES dataset(id) ON DELETE CASCADE;


--
-- Name: external_link_external_link_type_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY external_link
    ADD CONSTRAINT external_link_external_link_type_id_fkey FOREIGN KEY (external_link_type_id) REFERENCES external_link_type(id) ON DELETE CASCADE;


--
-- Name: file_attributes_attribute_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY file_attributes
    ADD CONSTRAINT file_attributes_attribute_id_fkey FOREIGN KEY (attribute_id) REFERENCES attribute(id);


--
-- Name: file_attributes_file_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY file_attributes
    ADD CONSTRAINT file_attributes_file_id_fkey FOREIGN KEY (file_id) REFERENCES file(id);


--
-- Name: file_attributes_unit_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY file_attributes
    ADD CONSTRAINT file_attributes_unit_id_fkey FOREIGN KEY (unit_id) REFERENCES unit(id);


--
-- Name: file_dataset_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY file
    ADD CONSTRAINT file_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES dataset(id) ON DELETE CASCADE;


--
-- Name: file_experiment_experiment_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY file_experiment
    ADD CONSTRAINT file_experiment_experiment_id_fkey FOREIGN KEY (experiment_id) REFERENCES experiment(id);


--
-- Name: file_experiment_file_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY file_experiment
    ADD CONSTRAINT file_experiment_file_id_fkey FOREIGN KEY (file_id) REFERENCES file(id);


--
-- Name: file_format_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY file
    ADD CONSTRAINT file_format_id_fkey FOREIGN KEY (format_id) REFERENCES file_format(id) ON DELETE CASCADE;


--
-- Name: file_relationship_file_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY file_relationship
    ADD CONSTRAINT file_relationship_file_id_fkey FOREIGN KEY (file_id) REFERENCES file(id);


--
-- Name: file_relationship_relationship_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY file_relationship
    ADD CONSTRAINT file_relationship_relationship_id_fkey FOREIGN KEY (relationship_id) REFERENCES relationship(id);


--
-- Name: file_sample_file_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY file_sample
    ADD CONSTRAINT file_sample_file_id_fkey FOREIGN KEY (file_id) REFERENCES file(id);


--
-- Name: file_sample_sample_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY file_sample
    ADD CONSTRAINT file_sample_sample_id_fkey FOREIGN KEY (sample_id) REFERENCES sample(id);


--
-- Name: link_dataset_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY link
    ADD CONSTRAINT link_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES dataset(id) ON DELETE CASCADE;


--
-- Name: manuscript_dataset_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY manuscript
    ADD CONSTRAINT manuscript_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES dataset(id) ON DELETE CASCADE;


--
-- Name: relation_dataset_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY relation
    ADD CONSTRAINT relation_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES dataset(id) ON DELETE CASCADE;


--
-- Name: relation_relationship_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY relation
    ADD CONSTRAINT relation_relationship_fkey FOREIGN KEY (relationship_id) REFERENCES relationship(id);


--
-- Name: sample_attribute_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY sample_attribute
    ADD CONSTRAINT sample_attribute_fkey FOREIGN KEY (attribute_id) REFERENCES attribute(id);


--
-- Name: sample_attribute_unit_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY sample_attribute
    ADD CONSTRAINT sample_attribute_unit_id_fkey FOREIGN KEY (unit_id) REFERENCES unit(id);


--
-- Name: sample_experiment_experiment_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY sample_experiment
    ADD CONSTRAINT sample_experiment_experiment_id_fkey FOREIGN KEY (experiment_id) REFERENCES experiment(id);


--
-- Name: sample_experiment_sample_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY sample_experiment
    ADD CONSTRAINT sample_experiment_sample_id_fkey FOREIGN KEY (sample_id) REFERENCES sample(id);


--
-- Name: sample_rel_relationship_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY sample_rel
    ADD CONSTRAINT sample_rel_relationship_id_fkey FOREIGN KEY (relationship_id) REFERENCES relationship(id);


--
-- Name: sample_rel_sample_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY sample_rel
    ADD CONSTRAINT sample_rel_sample_id_fkey FOREIGN KEY (sample_id) REFERENCES sample(id);


--
-- Name: sample_species_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY sample
    ADD CONSTRAINT sample_species_id_fkey FOREIGN KEY (species_id) REFERENCES species(id) ON DELETE CASCADE;


--
-- Name: sample_submitted_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY sample
    ADD CONSTRAINT sample_submitted_id_fkey FOREIGN KEY (submitted_id) REFERENCES gigadb_user(id);


--
-- Name: search_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY search
    ADD CONSTRAINT search_user_id_fkey FOREIGN KEY (user_id) REFERENCES gigadb_user(id) ON DELETE RESTRICT;


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- Name: dataset_session; Type: ACL; Schema: public; Owner: gigadb
--

REVOKE ALL ON TABLE dataset_session FROM PUBLIC;
REVOKE ALL ON TABLE dataset_session FROM gigadb;
GRANT ALL ON TABLE dataset_session TO gigadb;


--
-- Name: prefix; Type: ACL; Schema: public; Owner: gigadb
--

REVOKE ALL ON TABLE prefix FROM PUBLIC;
REVOKE ALL ON TABLE prefix FROM gigadb;
GRANT ALL ON TABLE prefix TO gigadb;
GRANT ALL ON TABLE prefix TO PUBLIC;


CREATE VIEW file_number AS (SELECT count (id) AS count FROM file);

CREATE VIEW sample_number AS (SELECT count (id) AS count FROM sample);

CREATE VIEW homepage_dataset_type AS (SELECT type.name, count(dataset_type.id) FROM dataset_type, type, dataset where dataset_type.type_id=type.id and dataset_type.dataset_id=dataset.id and dataset.upload_status = 'Published' group by type.name);

GRANT SELECT ON TABLE file_number TO public;

GRANT SELECT ON TABLE homepage_dataset_type TO public;

GRANT SELECT ON TABLE sample_number TO public;



--
-- PostgreSQL database dump complete
--


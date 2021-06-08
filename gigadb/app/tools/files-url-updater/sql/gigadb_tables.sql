--
-- PostgreSQL database dump
--

-- Dumped from database version 9.3.25
-- Dumped by pg_dump version 9.3.25
-- Started on 2021-06-08 09:31:44 UTC

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- TOC entry 1 (class 3079 OID 11756)
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- TOC entry 2525 (class 0 OID 0)
-- Dependencies: 1
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET default_tablespace = '';

SET default_with_oids = false;

--
-- TOC entry 171 (class 1259 OID 16389)
-- Name: AuthAssignment; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public."AuthAssignment" (
    itemname character varying(64) NOT NULL,
    userid character varying(64) NOT NULL,
    bizrule text,
    data text
);


ALTER TABLE public."AuthAssignment" OWNER TO gigadb;

--
-- TOC entry 172 (class 1259 OID 16395)
-- Name: AuthItem; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public."AuthItem" (
    name character varying(64) NOT NULL,
    type integer NOT NULL,
    description text,
    bizrule text,
    data text
);


ALTER TABLE public."AuthItem" OWNER TO gigadb;

--
-- TOC entry 173 (class 1259 OID 16401)
-- Name: YiiSession; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public."YiiSession" (
    id character(32) NOT NULL,
    expire integer,
    data bytea
);


ALTER TABLE public."YiiSession" OWNER TO gigadb;

--
-- TOC entry 174 (class 1259 OID 16407)
-- Name: alternative_identifiers; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public.alternative_identifiers (
    id integer NOT NULL,
    sample_id integer NOT NULL,
    extdb_id integer NOT NULL,
    extdb_accession character varying(100)
);


ALTER TABLE public.alternative_identifiers OWNER TO gigadb;

--
-- TOC entry 2526 (class 0 OID 0)
-- Dependencies: 174
-- Name: COLUMN alternative_identifiers.id; Type: COMMENT; Schema: public; Owner: gigadb
--

COMMENT ON COLUMN public.alternative_identifiers.id IS '

';


--
-- TOC entry 175 (class 1259 OID 16410)
-- Name: alternative_identifiers_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE public.alternative_identifiers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.alternative_identifiers_id_seq OWNER TO gigadb;

--
-- TOC entry 2527 (class 0 OID 0)
-- Dependencies: 175
-- Name: alternative_identifiers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE public.alternative_identifiers_id_seq OWNED BY public.alternative_identifiers.id;


--
-- TOC entry 176 (class 1259 OID 16412)
-- Name: attribute; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public.attribute (
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
-- TOC entry 177 (class 1259 OID 16418)
-- Name: attribute_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE public.attribute_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.attribute_id_seq OWNER TO gigadb;

--
-- TOC entry 2528 (class 0 OID 0)
-- Dependencies: 177
-- Name: attribute_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE public.attribute_id_seq OWNED BY public.attribute.id;


--
-- TOC entry 178 (class 1259 OID 16420)
-- Name: author; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public.author (
    id integer NOT NULL,
    surname character varying(255) NOT NULL,
    middle_name character varying(255),
    first_name character varying(255),
    orcid character varying(255),
    gigadb_user_id integer,
    custom_name character varying(100)
);


ALTER TABLE public.author OWNER TO gigadb;

--
-- TOC entry 179 (class 1259 OID 16426)
-- Name: author_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE public.author_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.author_id_seq OWNER TO gigadb;

--
-- TOC entry 2529 (class 0 OID 0)
-- Dependencies: 179
-- Name: author_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE public.author_id_seq OWNED BY public.author.id;


--
-- TOC entry 180 (class 1259 OID 16428)
-- Name: curation_log; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public.curation_log (
    id integer NOT NULL,
    dataset_id integer NOT NULL,
    creation_date date,
    created_by character varying(100),
    last_modified_date date,
    last_modified_by character varying(100),
    action character varying(100),
    comments character varying(1000)
);


ALTER TABLE public.curation_log OWNER TO gigadb;

--
-- TOC entry 181 (class 1259 OID 16434)
-- Name: curation_log_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE public.curation_log_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.curation_log_id_seq OWNER TO gigadb;

--
-- TOC entry 2531 (class 0 OID 0)
-- Dependencies: 181
-- Name: curation_log_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE public.curation_log_id_seq OWNED BY public.curation_log.id;


--
-- TOC entry 182 (class 1259 OID 16436)
-- Name: dataset; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public.dataset (
    id integer NOT NULL,
    submitter_id integer NOT NULL,
    image_id integer,
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
    fairnuse date,
    curator_id integer,
    manuscript_id character varying(50),
    handing_editor character varying(50)
);


ALTER TABLE public.dataset OWNER TO gigadb;

--
-- TOC entry 183 (class 1259 OID 16445)
-- Name: dataset_attributes; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public.dataset_attributes (
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
-- TOC entry 184 (class 1259 OID 16448)
-- Name: dataset_attributes_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE public.dataset_attributes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dataset_attributes_id_seq OWNER TO gigadb;

--
-- TOC entry 2532 (class 0 OID 0)
-- Dependencies: 184
-- Name: dataset_attributes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE public.dataset_attributes_id_seq OWNED BY public.dataset_attributes.id;


--
-- TOC entry 185 (class 1259 OID 16450)
-- Name: dataset_author; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public.dataset_author (
    id integer NOT NULL,
    dataset_id integer NOT NULL,
    author_id integer NOT NULL,
    rank integer DEFAULT 0,
    role character varying(30)
);


ALTER TABLE public.dataset_author OWNER TO gigadb;

--
-- TOC entry 186 (class 1259 OID 16454)
-- Name: dataset_author_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE public.dataset_author_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dataset_author_id_seq OWNER TO gigadb;

--
-- TOC entry 2533 (class 0 OID 0)
-- Dependencies: 186
-- Name: dataset_author_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE public.dataset_author_id_seq OWNED BY public.dataset_author.id;


--
-- TOC entry 187 (class 1259 OID 16456)
-- Name: dataset_funder; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public.dataset_funder (
    id integer NOT NULL,
    dataset_id integer NOT NULL,
    funder_id integer NOT NULL,
    grant_award text DEFAULT ''::text,
    comments text DEFAULT ''::text,
    awardee character varying(500)
);


ALTER TABLE public.dataset_funder OWNER TO gigadb;

--
-- TOC entry 188 (class 1259 OID 16464)
-- Name: dataset_funder_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE public.dataset_funder_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dataset_funder_id_seq OWNER TO gigadb;

--
-- TOC entry 2534 (class 0 OID 0)
-- Dependencies: 188
-- Name: dataset_funder_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE public.dataset_funder_id_seq OWNED BY public.dataset_funder.id;


--
-- TOC entry 189 (class 1259 OID 16466)
-- Name: dataset_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE public.dataset_id_seq
    START WITH 33
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dataset_id_seq OWNER TO gigadb;

--
-- TOC entry 2535 (class 0 OID 0)
-- Dependencies: 189
-- Name: dataset_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE public.dataset_id_seq OWNED BY public.dataset.id;


--
-- TOC entry 190 (class 1259 OID 16468)
-- Name: dataset_log; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public.dataset_log (
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
-- TOC entry 191 (class 1259 OID 16477)
-- Name: dataset_log_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE public.dataset_log_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dataset_log_id_seq OWNER TO gigadb;

--
-- TOC entry 2536 (class 0 OID 0)
-- Dependencies: 191
-- Name: dataset_log_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE public.dataset_log_id_seq OWNED BY public.dataset_log.id;


--
-- TOC entry 192 (class 1259 OID 16479)
-- Name: dataset_project; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public.dataset_project (
    id integer NOT NULL,
    dataset_id integer,
    project_id integer
);


ALTER TABLE public.dataset_project OWNER TO gigadb;

--
-- TOC entry 193 (class 1259 OID 16482)
-- Name: dataset_project_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE public.dataset_project_id_seq
    START WITH 7
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dataset_project_id_seq OWNER TO gigadb;

--
-- TOC entry 2537 (class 0 OID 0)
-- Dependencies: 193
-- Name: dataset_project_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE public.dataset_project_id_seq OWNED BY public.dataset_project.id;


--
-- TOC entry 194 (class 1259 OID 16484)
-- Name: dataset_sample; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public.dataset_sample (
    id integer NOT NULL,
    dataset_id integer NOT NULL,
    sample_id integer NOT NULL
);


ALTER TABLE public.dataset_sample OWNER TO gigadb;

--
-- TOC entry 195 (class 1259 OID 16487)
-- Name: dataset_sample_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE public.dataset_sample_id_seq
    START WITH 211
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dataset_sample_id_seq OWNER TO gigadb;

--
-- TOC entry 2538 (class 0 OID 0)
-- Dependencies: 195
-- Name: dataset_sample_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE public.dataset_sample_id_seq OWNED BY public.dataset_sample.id;


--
-- TOC entry 196 (class 1259 OID 16489)
-- Name: dataset_session; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public.dataset_session (
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
-- TOC entry 197 (class 1259 OID 16495)
-- Name: dataset_session_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE public.dataset_session_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dataset_session_id_seq OWNER TO gigadb;

--
-- TOC entry 2540 (class 0 OID 0)
-- Dependencies: 197
-- Name: dataset_session_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE public.dataset_session_id_seq OWNED BY public.dataset_session.id;


--
-- TOC entry 198 (class 1259 OID 16497)
-- Name: dataset_type; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public.dataset_type (
    id integer NOT NULL,
    dataset_id integer NOT NULL,
    type_id integer
);


ALTER TABLE public.dataset_type OWNER TO gigadb;

--
-- TOC entry 199 (class 1259 OID 16500)
-- Name: dataset_type_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE public.dataset_type_id_seq
    START WITH 37
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dataset_type_id_seq OWNER TO gigadb;

--
-- TOC entry 2541 (class 0 OID 0)
-- Dependencies: 199
-- Name: dataset_type_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE public.dataset_type_id_seq OWNED BY public.dataset_type.id;


--
-- TOC entry 200 (class 1259 OID 16502)
-- Name: exp_attributes; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public.exp_attributes (
    id integer NOT NULL,
    exp_id integer,
    attribute_id integer,
    value character varying(1000),
    units_id character varying(50)
);


ALTER TABLE public.exp_attributes OWNER TO gigadb;

--
-- TOC entry 201 (class 1259 OID 16508)
-- Name: exp_attributes_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE public.exp_attributes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.exp_attributes_id_seq OWNER TO gigadb;

--
-- TOC entry 2542 (class 0 OID 0)
-- Dependencies: 201
-- Name: exp_attributes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE public.exp_attributes_id_seq OWNED BY public.exp_attributes.id;


--
-- TOC entry 202 (class 1259 OID 16510)
-- Name: experiment; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public.experiment (
    id integer NOT NULL,
    experiment_type character varying(100),
    experiment_name character varying(100),
    exp_description character varying(1000),
    dataset_id integer,
    "protocols.io" character varying(200)
);


ALTER TABLE public.experiment OWNER TO gigadb;

--
-- TOC entry 203 (class 1259 OID 16516)
-- Name: experiment_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE public.experiment_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.experiment_id_seq OWNER TO gigadb;

--
-- TOC entry 2543 (class 0 OID 0)
-- Dependencies: 203
-- Name: experiment_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE public.experiment_id_seq OWNED BY public.experiment.id;


--
-- TOC entry 204 (class 1259 OID 16518)
-- Name: extdb; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public.extdb (
    id integer NOT NULL,
    database_name character varying(100),
    definition character varying(1000),
    database_homepage character varying(100),
    database_search_url character varying(100)
);


ALTER TABLE public.extdb OWNER TO gigadb;

--
-- TOC entry 205 (class 1259 OID 16524)
-- Name: extdb_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE public.extdb_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.extdb_id_seq OWNER TO gigadb;

--
-- TOC entry 2544 (class 0 OID 0)
-- Dependencies: 205
-- Name: extdb_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE public.extdb_id_seq OWNED BY public.extdb.id;


--
-- TOC entry 206 (class 1259 OID 16526)
-- Name: external_link; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public.external_link (
    id integer NOT NULL,
    dataset_id integer NOT NULL,
    url character varying(300) NOT NULL,
    external_link_type_id integer NOT NULL
);


ALTER TABLE public.external_link OWNER TO gigadb;

--
-- TOC entry 207 (class 1259 OID 16529)
-- Name: external_link_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE public.external_link_id_seq
    START WITH 17
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.external_link_id_seq OWNER TO gigadb;

--
-- TOC entry 2545 (class 0 OID 0)
-- Dependencies: 207
-- Name: external_link_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE public.external_link_id_seq OWNED BY public.external_link.id;


--
-- TOC entry 208 (class 1259 OID 16531)
-- Name: external_link_type; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public.external_link_type (
    id integer NOT NULL,
    name character varying(45) NOT NULL
);


ALTER TABLE public.external_link_type OWNER TO gigadb;

--
-- TOC entry 209 (class 1259 OID 16534)
-- Name: external_link_type_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE public.external_link_type_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.external_link_type_id_seq OWNER TO gigadb;

--
-- TOC entry 2546 (class 0 OID 0)
-- Dependencies: 209
-- Name: external_link_type_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE public.external_link_type_id_seq OWNED BY public.external_link_type.id;


--
-- TOC entry 210 (class 1259 OID 16536)
-- Name: file; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public.file (
    id integer NOT NULL,
    dataset_id integer NOT NULL,
    name character varying(500) NOT NULL,
    location character varying(1000) NOT NULL,
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
-- TOC entry 211 (class 1259 OID 16545)
-- Name: file_attributes; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public.file_attributes (
    id integer NOT NULL,
    file_id integer NOT NULL,
    attribute_id integer NOT NULL,
    value character varying(1000),
    unit_id character varying(30)
);


ALTER TABLE public.file_attributes OWNER TO gigadb;

--
-- TOC entry 212 (class 1259 OID 16551)
-- Name: file_attributes_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE public.file_attributes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.file_attributes_id_seq OWNER TO gigadb;

--
-- TOC entry 2547 (class 0 OID 0)
-- Dependencies: 212
-- Name: file_attributes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE public.file_attributes_id_seq OWNED BY public.file_attributes.id;


--
-- TOC entry 213 (class 1259 OID 16553)
-- Name: file_experiment; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public.file_experiment (
    id integer NOT NULL,
    file_id integer,
    experiment_id integer
);


ALTER TABLE public.file_experiment OWNER TO gigadb;

--
-- TOC entry 214 (class 1259 OID 16556)
-- Name: file_experiment_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE public.file_experiment_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.file_experiment_id_seq OWNER TO gigadb;

--
-- TOC entry 2548 (class 0 OID 0)
-- Dependencies: 214
-- Name: file_experiment_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE public.file_experiment_id_seq OWNED BY public.file_experiment.id;


--
-- TOC entry 215 (class 1259 OID 16558)
-- Name: file_format; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public.file_format (
    id integer NOT NULL,
    name character varying(20) NOT NULL,
    description text DEFAULT ''::text NOT NULL,
    edam_ontology_id character varying(100)
);


ALTER TABLE public.file_format OWNER TO gigadb;

--
-- TOC entry 216 (class 1259 OID 16565)
-- Name: file_format_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE public.file_format_id_seq
    START WITH 26
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.file_format_id_seq OWNER TO gigadb;

--
-- TOC entry 2549 (class 0 OID 0)
-- Dependencies: 216
-- Name: file_format_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE public.file_format_id_seq OWNED BY public.file_format.id;


--
-- TOC entry 217 (class 1259 OID 16567)
-- Name: file_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE public.file_id_seq
    START WITH 6716
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.file_id_seq OWNER TO gigadb;

--
-- TOC entry 2550 (class 0 OID 0)
-- Dependencies: 217
-- Name: file_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE public.file_id_seq OWNED BY public.file.id;


--
-- TOC entry 218 (class 1259 OID 16569)
-- Name: file_number; Type: VIEW; Schema: public; Owner: gigadb
--

CREATE VIEW public.file_number AS
 SELECT count(file.id) AS count
   FROM public.file;


ALTER TABLE public.file_number OWNER TO gigadb;

--
-- TOC entry 219 (class 1259 OID 16573)
-- Name: file_relationship; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public.file_relationship (
    id integer NOT NULL,
    file_id integer NOT NULL,
    related_file_id integer NOT NULL,
    relationship_id integer
);


ALTER TABLE public.file_relationship OWNER TO gigadb;

--
-- TOC entry 220 (class 1259 OID 16576)
-- Name: file_relationship_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE public.file_relationship_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.file_relationship_id_seq OWNER TO gigadb;

--
-- TOC entry 2552 (class 0 OID 0)
-- Dependencies: 220
-- Name: file_relationship_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE public.file_relationship_id_seq OWNED BY public.file_relationship.id;


--
-- TOC entry 221 (class 1259 OID 16578)
-- Name: file_sample; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public.file_sample (
    id integer NOT NULL,
    sample_id integer NOT NULL,
    file_id integer NOT NULL
);


ALTER TABLE public.file_sample OWNER TO gigadb;

--
-- TOC entry 222 (class 1259 OID 16581)
-- Name: file_sample_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE public.file_sample_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.file_sample_id_seq OWNER TO gigadb;

--
-- TOC entry 2553 (class 0 OID 0)
-- Dependencies: 222
-- Name: file_sample_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE public.file_sample_id_seq OWNED BY public.file_sample.id;


--
-- TOC entry 223 (class 1259 OID 16583)
-- Name: file_type; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public.file_type (
    id integer NOT NULL,
    name character varying(100) NOT NULL,
    description text DEFAULT ''::text NOT NULL,
    edam_ontology_id character varying(100)
);


ALTER TABLE public.file_type OWNER TO gigadb;

--
-- TOC entry 224 (class 1259 OID 16590)
-- Name: file_type_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE public.file_type_id_seq
    START WITH 15
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.file_type_id_seq OWNER TO gigadb;

--
-- TOC entry 2554 (class 0 OID 0)
-- Dependencies: 224
-- Name: file_type_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE public.file_type_id_seq OWNED BY public.file_type.id;


--
-- TOC entry 225 (class 1259 OID 16592)
-- Name: funder_name; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public.funder_name (
    id integer NOT NULL,
    uri character varying(100) NOT NULL,
    primary_name_display character varying(1000),
    country character varying(128) DEFAULT ''::character varying
);


ALTER TABLE public.funder_name OWNER TO gigadb;

--
-- TOC entry 226 (class 1259 OID 16599)
-- Name: funder_name_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE public.funder_name_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.funder_name_id_seq OWNER TO gigadb;

--
-- TOC entry 2555 (class 0 OID 0)
-- Dependencies: 226
-- Name: funder_name_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE public.funder_name_id_seq OWNED BY public.funder_name.id;


--
-- TOC entry 227 (class 1259 OID 16601)
-- Name: gigadb_user; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public.gigadb_user (
    id integer NOT NULL,
    email character varying(64) NOT NULL,
    password character varying(64) NOT NULL,
    first_name character varying(100) NOT NULL,
    last_name character varying(100) NOT NULL,
    affiliation character varying(500),
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
-- TOC entry 228 (class 1259 OID 16612)
-- Name: gigadb_user_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE public.gigadb_user_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.gigadb_user_id_seq OWNER TO gigadb;

--
-- TOC entry 2556 (class 0 OID 0)
-- Dependencies: 228
-- Name: gigadb_user_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE public.gigadb_user_id_seq OWNED BY public.gigadb_user.id;


--
-- TOC entry 229 (class 1259 OID 16614)
-- Name: type; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public.type (
    id integer NOT NULL,
    name character varying(32) NOT NULL,
    description text DEFAULT ''::text NOT NULL
);


ALTER TABLE public.type OWNER TO gigadb;

--
-- TOC entry 230 (class 1259 OID 16621)
-- Name: homepage_dataset_type; Type: VIEW; Schema: public; Owner: gigadb
--

CREATE VIEW public.homepage_dataset_type AS
 SELECT type.name,
    count(dataset_type.id) AS count
   FROM public.dataset_type,
    public.type,
    public.dataset
  WHERE (((dataset_type.type_id = type.id) AND (dataset_type.dataset_id = dataset.id)) AND ((dataset.upload_status)::text = 'Published'::text))
  GROUP BY type.name;


ALTER TABLE public.homepage_dataset_type OWNER TO gigadb;

--
-- TOC entry 231 (class 1259 OID 16625)
-- Name: image; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public.image (
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
-- TOC entry 232 (class 1259 OID 16632)
-- Name: image_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE public.image_id_seq
    START WITH 31
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.image_id_seq OWNER TO gigadb;

--
-- TOC entry 2558 (class 0 OID 0)
-- Dependencies: 232
-- Name: image_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE public.image_id_seq OWNED BY public.image.id;


--
-- TOC entry 233 (class 1259 OID 16634)
-- Name: link; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public.link (
    id integer NOT NULL,
    dataset_id integer NOT NULL,
    is_primary boolean DEFAULT false NOT NULL,
    link character varying(100) NOT NULL,
    description character varying(200)
);


ALTER TABLE public.link OWNER TO gigadb;

--
-- TOC entry 234 (class 1259 OID 16638)
-- Name: link_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE public.link_id_seq
    START WITH 66
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.link_id_seq OWNER TO gigadb;

--
-- TOC entry 2559 (class 0 OID 0)
-- Dependencies: 234
-- Name: link_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE public.link_id_seq OWNED BY public.link.id;


--
-- TOC entry 235 (class 1259 OID 16640)
-- Name: link_prefix_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE public.link_prefix_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.link_prefix_id_seq OWNER TO gigadb;

--
-- TOC entry 236 (class 1259 OID 16642)
-- Name: manuscript; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public.manuscript (
    id integer NOT NULL,
    identifier character varying(32) NOT NULL,
    pmid integer,
    dataset_id integer NOT NULL
);


ALTER TABLE public.manuscript OWNER TO gigadb;

--
-- TOC entry 237 (class 1259 OID 16645)
-- Name: manuscript_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE public.manuscript_id_seq
    START WITH 27
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.manuscript_id_seq OWNER TO gigadb;

--
-- TOC entry 2560 (class 0 OID 0)
-- Dependencies: 237
-- Name: manuscript_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE public.manuscript_id_seq OWNED BY public.manuscript.id;


--
-- TOC entry 238 (class 1259 OID 16647)
-- Name: news; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public.news (
    id integer NOT NULL,
    title character varying(200) NOT NULL,
    body text DEFAULT ''::text NOT NULL,
    start_date date NOT NULL,
    end_date date NOT NULL
);


ALTER TABLE public.news OWNER TO gigadb;

--
-- TOC entry 239 (class 1259 OID 16654)
-- Name: news_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE public.news_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.news_id_seq OWNER TO gigadb;

--
-- TOC entry 2561 (class 0 OID 0)
-- Dependencies: 239
-- Name: news_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE public.news_id_seq OWNED BY public.news.id;


--
-- TOC entry 240 (class 1259 OID 16656)
-- Name: prefix; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public.prefix (
    id integer DEFAULT nextval('public.link_prefix_id_seq'::regclass) NOT NULL,
    prefix character(20) NOT NULL,
    url text NOT NULL,
    source character varying(128) DEFAULT ''::character varying,
    icon character varying(100)
);


ALTER TABLE public.prefix OWNER TO gigadb;

--
-- TOC entry 241 (class 1259 OID 16664)
-- Name: project; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public.project (
    id integer NOT NULL,
    url character varying(128) NOT NULL,
    name character varying(255) DEFAULT ''::character varying NOT NULL,
    image_location character varying(100)
);


ALTER TABLE public.project OWNER TO gigadb;

--
-- TOC entry 242 (class 1259 OID 16668)
-- Name: project_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE public.project_id_seq
    START WITH 7
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.project_id_seq OWNER TO gigadb;

--
-- TOC entry 2563 (class 0 OID 0)
-- Dependencies: 242
-- Name: project_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE public.project_id_seq OWNED BY public.project.id;


--
-- TOC entry 243 (class 1259 OID 16670)
-- Name: publisher; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public.publisher (
    id integer NOT NULL,
    name character varying(45) NOT NULL,
    description text DEFAULT ''::text NOT NULL
);


ALTER TABLE public.publisher OWNER TO gigadb;

--
-- TOC entry 244 (class 1259 OID 16677)
-- Name: publisher_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE public.publisher_id_seq
    START WITH 3
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.publisher_id_seq OWNER TO gigadb;

--
-- TOC entry 2564 (class 0 OID 0)
-- Dependencies: 244
-- Name: publisher_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE public.publisher_id_seq OWNED BY public.publisher.id;


--
-- TOC entry 245 (class 1259 OID 16679)
-- Name: relation; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public.relation (
    id integer NOT NULL,
    dataset_id integer NOT NULL,
    related_doi character varying(15) NOT NULL,
    relationship_id integer
);


ALTER TABLE public.relation OWNER TO gigadb;

--
-- TOC entry 246 (class 1259 OID 16682)
-- Name: relation_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE public.relation_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.relation_id_seq OWNER TO gigadb;

--
-- TOC entry 2565 (class 0 OID 0)
-- Dependencies: 246
-- Name: relation_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE public.relation_id_seq OWNED BY public.relation.id;


--
-- TOC entry 247 (class 1259 OID 16684)
-- Name: relationship; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public.relationship (
    id integer NOT NULL,
    name character varying(100)
);


ALTER TABLE public.relationship OWNER TO gigadb;

--
-- TOC entry 248 (class 1259 OID 16687)
-- Name: relationship_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE public.relationship_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.relationship_id_seq OWNER TO gigadb;

--
-- TOC entry 2566 (class 0 OID 0)
-- Dependencies: 248
-- Name: relationship_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE public.relationship_id_seq OWNED BY public.relationship.id;


--
-- TOC entry 249 (class 1259 OID 16689)
-- Name: rss_message; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public.rss_message (
    id integer NOT NULL,
    message character varying(128) NOT NULL,
    publication_date date DEFAULT ('now'::text)::date NOT NULL
);


ALTER TABLE public.rss_message OWNER TO gigadb;

--
-- TOC entry 250 (class 1259 OID 16693)
-- Name: rss_message_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE public.rss_message_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.rss_message_id_seq OWNER TO gigadb;

--
-- TOC entry 2567 (class 0 OID 0)
-- Dependencies: 250
-- Name: rss_message_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE public.rss_message_id_seq OWNED BY public.rss_message.id;


--
-- TOC entry 251 (class 1259 OID 16695)
-- Name: sample; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public.sample (
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
-- TOC entry 252 (class 1259 OID 16699)
-- Name: sample_attribute; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public.sample_attribute (
    id integer NOT NULL,
    sample_id integer NOT NULL,
    attribute_id integer NOT NULL,
    value character varying(10000),
    unit_id character varying(30)
);


ALTER TABLE public.sample_attribute OWNER TO gigadb;

--
-- TOC entry 253 (class 1259 OID 16705)
-- Name: sample_attribute_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE public.sample_attribute_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.sample_attribute_id_seq OWNER TO gigadb;

--
-- TOC entry 2568 (class 0 OID 0)
-- Dependencies: 253
-- Name: sample_attribute_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE public.sample_attribute_id_seq OWNED BY public.sample_attribute.id;


--
-- TOC entry 254 (class 1259 OID 16707)
-- Name: sample_experiment; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public.sample_experiment (
    id integer NOT NULL,
    sample_id integer,
    experiment_id integer
);


ALTER TABLE public.sample_experiment OWNER TO gigadb;

--
-- TOC entry 255 (class 1259 OID 16710)
-- Name: sample_experiment_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE public.sample_experiment_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.sample_experiment_id_seq OWNER TO gigadb;

--
-- TOC entry 2569 (class 0 OID 0)
-- Dependencies: 255
-- Name: sample_experiment_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE public.sample_experiment_id_seq OWNED BY public.sample_experiment.id;


--
-- TOC entry 256 (class 1259 OID 16712)
-- Name: sample_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE public.sample_id_seq
    START WITH 210
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.sample_id_seq OWNER TO gigadb;

--
-- TOC entry 2570 (class 0 OID 0)
-- Dependencies: 256
-- Name: sample_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE public.sample_id_seq OWNED BY public.sample.id;


--
-- TOC entry 257 (class 1259 OID 16714)
-- Name: sample_number; Type: VIEW; Schema: public; Owner: gigadb
--

CREATE VIEW public.sample_number AS
 SELECT count(sample.id) AS count
   FROM public.sample;


ALTER TABLE public.sample_number OWNER TO gigadb;

--
-- TOC entry 258 (class 1259 OID 16718)
-- Name: sample_rel; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public.sample_rel (
    id integer NOT NULL,
    sample_id integer NOT NULL,
    related_sample_id integer NOT NULL,
    relationship_id integer
);


ALTER TABLE public.sample_rel OWNER TO gigadb;

--
-- TOC entry 259 (class 1259 OID 16721)
-- Name: sample_rel_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE public.sample_rel_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.sample_rel_id_seq OWNER TO gigadb;

--
-- TOC entry 2572 (class 0 OID 0)
-- Dependencies: 259
-- Name: sample_rel_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE public.sample_rel_id_seq OWNED BY public.sample_rel.id;


--
-- TOC entry 260 (class 1259 OID 16723)
-- Name: schemup_tables; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public.schemup_tables (
    table_name character varying NOT NULL,
    version character varying NOT NULL,
    is_current boolean DEFAULT false NOT NULL,
    schema text
);


ALTER TABLE public.schemup_tables OWNER TO gigadb;

--
-- TOC entry 261 (class 1259 OID 16730)
-- Name: search; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public.search (
    id integer NOT NULL,
    user_id integer NOT NULL,
    name character varying(128) NOT NULL,
    query text NOT NULL,
    result text
);


ALTER TABLE public.search OWNER TO gigadb;

--
-- TOC entry 262 (class 1259 OID 16736)
-- Name: search_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE public.search_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.search_id_seq OWNER TO gigadb;

--
-- TOC entry 2573 (class 0 OID 0)
-- Dependencies: 262
-- Name: search_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE public.search_id_seq OWNED BY public.search.id;


--
-- TOC entry 263 (class 1259 OID 16738)
-- Name: show_accession; Type: VIEW; Schema: public; Owner: gigadb
--

CREATE VIEW public.show_accession AS
 SELECT ('DOI: '::text || (dataset.identifier)::text) AS doi_number,
    link.link AS related_accessions
   FROM (public.dataset
     JOIN public.link ON ((dataset.id = link.dataset_id)));


ALTER TABLE public.show_accession OWNER TO gigadb;

--
-- TOC entry 264 (class 1259 OID 16742)
-- Name: show_manuscript; Type: VIEW; Schema: public; Owner: gigadb
--

CREATE VIEW public.show_manuscript AS
 SELECT ('DOI: '::text || (dataset.identifier)::text) AS doi_number,
    manuscript.identifier AS related_manuscript
   FROM (public.dataset
     JOIN public.manuscript ON ((dataset.id = manuscript.dataset_id)));


ALTER TABLE public.show_manuscript OWNER TO gigadb;

--
-- TOC entry 265 (class 1259 OID 16746)
-- Name: show_project; Type: VIEW; Schema: public; Owner: gigadb
--

CREATE VIEW public.show_project AS
 SELECT ('DOI: '::text || (dataset.identifier)::text) AS doi_number,
    project.name AS project
   FROM ((public.dataset
     JOIN public.dataset_project ON ((dataset.id = dataset_project.dataset_id)))
     JOIN public.project ON ((dataset_project.project_id = project.id)));


ALTER TABLE public.show_project OWNER TO gigadb;

--
-- TOC entry 266 (class 1259 OID 16751)
-- Name: species; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public.species (
    id integer NOT NULL,
    tax_id integer NOT NULL,
    common_name character varying(128),
    genbank_name character varying(128),
    scientific_name character varying(128) NOT NULL,
    eol_link character varying(100)
);


ALTER TABLE public.species OWNER TO gigadb;

--
-- TOC entry 267 (class 1259 OID 16754)
-- Name: species_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE public.species_id_seq
    START WITH 28
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.species_id_seq OWNER TO gigadb;

--
-- TOC entry 2574 (class 0 OID 0)
-- Dependencies: 267
-- Name: species_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE public.species_id_seq OWNED BY public.species.id;


--
-- TOC entry 268 (class 1259 OID 16756)
-- Name: type_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE public.type_id_seq
    START WITH 6
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.type_id_seq OWNER TO gigadb;

--
-- TOC entry 2575 (class 0 OID 0)
-- Dependencies: 268
-- Name: type_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE public.type_id_seq OWNED BY public.type.id;


--
-- TOC entry 269 (class 1259 OID 16758)
-- Name: unit; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public.unit (
    id character varying(30) NOT NULL,
    name character varying(200),
    definition character varying(500)
);


ALTER TABLE public.unit OWNER TO gigadb;

--
-- TOC entry 2576 (class 0 OID 0)
-- Dependencies: 269
-- Name: COLUMN unit.id; Type: COMMENT; Schema: public; Owner: gigadb
--

COMMENT ON COLUMN public.unit.id IS 'the ID from the unit ontology';


--
-- TOC entry 2577 (class 0 OID 0)
-- Dependencies: 269
-- Name: COLUMN unit.name; Type: COMMENT; Schema: public; Owner: gigadb
--

COMMENT ON COLUMN public.unit.name IS 'the name of the unit (taken from the Unit Ontology)';


--
-- TOC entry 2578 (class 0 OID 0)
-- Dependencies: 269
-- Name: COLUMN unit.definition; Type: COMMENT; Schema: public; Owner: gigadb
--

COMMENT ON COLUMN public.unit.definition IS 'the inition taken from the unit ontology';


--
-- TOC entry 270 (class 1259 OID 16764)
-- Name: yiisession; Type: TABLE; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE TABLE public.yiisession (
    id character(32) NOT NULL,
    expire integer,
    data text
);


ALTER TABLE public.yiisession OWNER TO gigadb;

--
-- TOC entry 2162 (class 2604 OID 16770)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.alternative_identifiers ALTER COLUMN id SET DEFAULT nextval('public.alternative_identifiers_id_seq'::regclass);


--
-- TOC entry 2163 (class 2604 OID 16771)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.attribute ALTER COLUMN id SET DEFAULT nextval('public.attribute_id_seq'::regclass);


--
-- TOC entry 2164 (class 2604 OID 16772)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.author ALTER COLUMN id SET DEFAULT nextval('public.author_id_seq'::regclass);


--
-- TOC entry 2165 (class 2604 OID 16773)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.curation_log ALTER COLUMN id SET DEFAULT nextval('public.curation_log_id_seq'::regclass);


--
-- TOC entry 2169 (class 2604 OID 16774)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.dataset ALTER COLUMN id SET DEFAULT nextval('public.dataset_id_seq'::regclass);


--
-- TOC entry 2170 (class 2604 OID 16775)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.dataset_attributes ALTER COLUMN id SET DEFAULT nextval('public.dataset_attributes_id_seq'::regclass);


--
-- TOC entry 2172 (class 2604 OID 16776)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.dataset_author ALTER COLUMN id SET DEFAULT nextval('public.dataset_author_id_seq'::regclass);


--
-- TOC entry 2175 (class 2604 OID 16777)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.dataset_funder ALTER COLUMN id SET DEFAULT nextval('public.dataset_funder_id_seq'::regclass);


--
-- TOC entry 2179 (class 2604 OID 16778)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.dataset_log ALTER COLUMN id SET DEFAULT nextval('public.dataset_log_id_seq'::regclass);


--
-- TOC entry 2180 (class 2604 OID 16779)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.dataset_project ALTER COLUMN id SET DEFAULT nextval('public.dataset_project_id_seq'::regclass);


--
-- TOC entry 2181 (class 2604 OID 16780)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.dataset_sample ALTER COLUMN id SET DEFAULT nextval('public.dataset_sample_id_seq'::regclass);


--
-- TOC entry 2182 (class 2604 OID 16781)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.dataset_session ALTER COLUMN id SET DEFAULT nextval('public.dataset_session_id_seq'::regclass);


--
-- TOC entry 2183 (class 2604 OID 16782)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.dataset_type ALTER COLUMN id SET DEFAULT nextval('public.dataset_type_id_seq'::regclass);


--
-- TOC entry 2184 (class 2604 OID 16783)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.exp_attributes ALTER COLUMN id SET DEFAULT nextval('public.exp_attributes_id_seq'::regclass);


--
-- TOC entry 2185 (class 2604 OID 16784)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.experiment ALTER COLUMN id SET DEFAULT nextval('public.experiment_id_seq'::regclass);


--
-- TOC entry 2186 (class 2604 OID 16785)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.extdb ALTER COLUMN id SET DEFAULT nextval('public.extdb_id_seq'::regclass);


--
-- TOC entry 2187 (class 2604 OID 16786)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.external_link ALTER COLUMN id SET DEFAULT nextval('public.external_link_id_seq'::regclass);


--
-- TOC entry 2188 (class 2604 OID 16787)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.external_link_type ALTER COLUMN id SET DEFAULT nextval('public.external_link_type_id_seq'::regclass);


--
-- TOC entry 2192 (class 2604 OID 16788)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.file ALTER COLUMN id SET DEFAULT nextval('public.file_id_seq'::regclass);


--
-- TOC entry 2193 (class 2604 OID 16789)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.file_attributes ALTER COLUMN id SET DEFAULT nextval('public.file_attributes_id_seq'::regclass);


--
-- TOC entry 2194 (class 2604 OID 16790)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.file_experiment ALTER COLUMN id SET DEFAULT nextval('public.file_experiment_id_seq'::regclass);


--
-- TOC entry 2196 (class 2604 OID 16791)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.file_format ALTER COLUMN id SET DEFAULT nextval('public.file_format_id_seq'::regclass);


--
-- TOC entry 2197 (class 2604 OID 16792)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.file_relationship ALTER COLUMN id SET DEFAULT nextval('public.file_relationship_id_seq'::regclass);


--
-- TOC entry 2198 (class 2604 OID 16793)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.file_sample ALTER COLUMN id SET DEFAULT nextval('public.file_sample_id_seq'::regclass);


--
-- TOC entry 2200 (class 2604 OID 16794)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.file_type ALTER COLUMN id SET DEFAULT nextval('public.file_type_id_seq'::regclass);


--
-- TOC entry 2202 (class 2604 OID 16795)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.funder_name ALTER COLUMN id SET DEFAULT nextval('public.funder_name_id_seq'::regclass);


--
-- TOC entry 2208 (class 2604 OID 16796)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.gigadb_user ALTER COLUMN id SET DEFAULT nextval('public.gigadb_user_id_seq'::regclass);


--
-- TOC entry 2212 (class 2604 OID 16797)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.image ALTER COLUMN id SET DEFAULT nextval('public.image_id_seq'::regclass);


--
-- TOC entry 2214 (class 2604 OID 16798)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.link ALTER COLUMN id SET DEFAULT nextval('public.link_id_seq'::regclass);


--
-- TOC entry 2215 (class 2604 OID 16799)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.manuscript ALTER COLUMN id SET DEFAULT nextval('public.manuscript_id_seq'::regclass);


--
-- TOC entry 2217 (class 2604 OID 16800)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.news ALTER COLUMN id SET DEFAULT nextval('public.news_id_seq'::regclass);


--
-- TOC entry 2221 (class 2604 OID 16801)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.project ALTER COLUMN id SET DEFAULT nextval('public.project_id_seq'::regclass);


--
-- TOC entry 2223 (class 2604 OID 16802)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.publisher ALTER COLUMN id SET DEFAULT nextval('public.publisher_id_seq'::regclass);


--
-- TOC entry 2224 (class 2604 OID 16803)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.relation ALTER COLUMN id SET DEFAULT nextval('public.relation_id_seq'::regclass);


--
-- TOC entry 2225 (class 2604 OID 16804)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.relationship ALTER COLUMN id SET DEFAULT nextval('public.relationship_id_seq'::regclass);


--
-- TOC entry 2227 (class 2604 OID 16805)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.rss_message ALTER COLUMN id SET DEFAULT nextval('public.rss_message_id_seq'::regclass);


--
-- TOC entry 2229 (class 2604 OID 16806)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.sample ALTER COLUMN id SET DEFAULT nextval('public.sample_id_seq'::regclass);


--
-- TOC entry 2230 (class 2604 OID 16807)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.sample_attribute ALTER COLUMN id SET DEFAULT nextval('public.sample_attribute_id_seq'::regclass);


--
-- TOC entry 2231 (class 2604 OID 16808)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.sample_experiment ALTER COLUMN id SET DEFAULT nextval('public.sample_experiment_id_seq'::regclass);


--
-- TOC entry 2232 (class 2604 OID 16809)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.sample_rel ALTER COLUMN id SET DEFAULT nextval('public.sample_rel_id_seq'::regclass);


--
-- TOC entry 2234 (class 2604 OID 16810)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.search ALTER COLUMN id SET DEFAULT nextval('public.search_id_seq'::regclass);


--
-- TOC entry 2235 (class 2604 OID 16811)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.species ALTER COLUMN id SET DEFAULT nextval('public.species_id_seq'::regclass);


--
-- TOC entry 2210 (class 2604 OID 16812)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.type ALTER COLUMN id SET DEFAULT nextval('public.type_id_seq'::regclass);


--
-- TOC entry 2237 (class 2606 OID 16863)
-- Name: AuthAssignment_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public."AuthAssignment"
    ADD CONSTRAINT "AuthAssignment_pkey" PRIMARY KEY (itemname, userid);


--
-- TOC entry 2239 (class 2606 OID 16865)
-- Name: AuthItem_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public."AuthItem"
    ADD CONSTRAINT "AuthItem_pkey" PRIMARY KEY (name);


--
-- TOC entry 2241 (class 2606 OID 16867)
-- Name: YiiSession_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public."YiiSession"
    ADD CONSTRAINT "YiiSession_pkey" PRIMARY KEY (id);


--
-- TOC entry 2243 (class 2606 OID 16869)
-- Name: alternative_identifiers_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.alternative_identifiers
    ADD CONSTRAINT alternative_identifiers_pkey PRIMARY KEY (id);


--
-- TOC entry 2245 (class 2606 OID 16871)
-- Name: attribute_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.attribute
    ADD CONSTRAINT attribute_pkey PRIMARY KEY (id);


--
-- TOC entry 2247 (class 2606 OID 16873)
-- Name: author_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.author
    ADD CONSTRAINT author_pkey PRIMARY KEY (id);


--
-- TOC entry 2249 (class 2606 OID 16875)
-- Name: curation_log_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.curation_log
    ADD CONSTRAINT curation_log_pkey PRIMARY KEY (id);


--
-- TOC entry 2254 (class 2606 OID 16877)
-- Name: dataset_attributes_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.dataset_attributes
    ADD CONSTRAINT dataset_attributes_pkey PRIMARY KEY (id);


--
-- TOC entry 2256 (class 2606 OID 16879)
-- Name: dataset_author_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.dataset_author
    ADD CONSTRAINT dataset_author_pkey PRIMARY KEY (id);


--
-- TOC entry 2258 (class 2606 OID 16881)
-- Name: dataset_funder_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.dataset_funder
    ADD CONSTRAINT dataset_funder_pkey PRIMARY KEY (id);


--
-- TOC entry 2260 (class 2606 OID 16883)
-- Name: dataset_log_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.dataset_log
    ADD CONSTRAINT dataset_log_pkey PRIMARY KEY (id);


--
-- TOC entry 2251 (class 2606 OID 16885)
-- Name: dataset_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.dataset
    ADD CONSTRAINT dataset_pkey PRIMARY KEY (id);


--
-- TOC entry 2262 (class 2606 OID 16887)
-- Name: dataset_project_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.dataset_project
    ADD CONSTRAINT dataset_project_pkey PRIMARY KEY (id);


--
-- TOC entry 2264 (class 2606 OID 16889)
-- Name: dataset_sample_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.dataset_sample
    ADD CONSTRAINT dataset_sample_pkey PRIMARY KEY (id);


--
-- TOC entry 2266 (class 2606 OID 16891)
-- Name: dataset_session_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.dataset_session
    ADD CONSTRAINT dataset_session_pkey PRIMARY KEY (id);


--
-- TOC entry 2268 (class 2606 OID 16893)
-- Name: dataset_type_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.dataset_type
    ADD CONSTRAINT dataset_type_pkey PRIMARY KEY (id);


--
-- TOC entry 2296 (class 2606 OID 16895)
-- Name: email_unique; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.gigadb_user
    ADD CONSTRAINT email_unique UNIQUE (email);


--
-- TOC entry 2270 (class 2606 OID 16897)
-- Name: exp_attributes_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.exp_attributes
    ADD CONSTRAINT exp_attributes_pkey PRIMARY KEY (id);


--
-- TOC entry 2272 (class 2606 OID 16899)
-- Name: experiment_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.experiment
    ADD CONSTRAINT experiment_pkey PRIMARY KEY (id);


--
-- TOC entry 2274 (class 2606 OID 16901)
-- Name: extdb_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.extdb
    ADD CONSTRAINT extdb_pkey PRIMARY KEY (id);


--
-- TOC entry 2276 (class 2606 OID 16903)
-- Name: external_link_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.external_link
    ADD CONSTRAINT external_link_pkey PRIMARY KEY (id);


--
-- TOC entry 2278 (class 2606 OID 16905)
-- Name: external_link_type_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.external_link_type
    ADD CONSTRAINT external_link_type_pkey PRIMARY KEY (id);


--
-- TOC entry 2282 (class 2606 OID 16907)
-- Name: file_attributes_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.file_attributes
    ADD CONSTRAINT file_attributes_pkey PRIMARY KEY (id);


--
-- TOC entry 2284 (class 2606 OID 16909)
-- Name: file_experiment_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.file_experiment
    ADD CONSTRAINT file_experiment_pkey PRIMARY KEY (id);


--
-- TOC entry 2286 (class 2606 OID 16911)
-- Name: file_format_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.file_format
    ADD CONSTRAINT file_format_pkey PRIMARY KEY (id);


--
-- TOC entry 2280 (class 2606 OID 16913)
-- Name: file_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.file
    ADD CONSTRAINT file_pkey PRIMARY KEY (id);


--
-- TOC entry 2288 (class 2606 OID 16915)
-- Name: file_relationship_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.file_relationship
    ADD CONSTRAINT file_relationship_pkey PRIMARY KEY (id);


--
-- TOC entry 2290 (class 2606 OID 16917)
-- Name: file_sample_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.file_sample
    ADD CONSTRAINT file_sample_pkey PRIMARY KEY (id);


--
-- TOC entry 2292 (class 2606 OID 16919)
-- Name: file_type_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.file_type
    ADD CONSTRAINT file_type_pkey PRIMARY KEY (id);


--
-- TOC entry 2294 (class 2606 OID 16921)
-- Name: funder_name_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.funder_name
    ADD CONSTRAINT funder_name_pkey PRIMARY KEY (id);


--
-- TOC entry 2298 (class 2606 OID 16923)
-- Name: gigadb_user_facebook_id_key; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.gigadb_user
    ADD CONSTRAINT gigadb_user_facebook_id_key UNIQUE (facebook_id);


--
-- TOC entry 2300 (class 2606 OID 16925)
-- Name: gigadb_user_google_id_key; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.gigadb_user
    ADD CONSTRAINT gigadb_user_google_id_key UNIQUE (google_id);


--
-- TOC entry 2302 (class 2606 OID 16927)
-- Name: gigadb_user_linked_id_key; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.gigadb_user
    ADD CONSTRAINT gigadb_user_linked_id_key UNIQUE (linkedin_id);


--
-- TOC entry 2304 (class 2606 OID 16929)
-- Name: gigadb_user_orcid_id_key; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.gigadb_user
    ADD CONSTRAINT gigadb_user_orcid_id_key UNIQUE (orcid_id);


--
-- TOC entry 2306 (class 2606 OID 16931)
-- Name: gigadb_user_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.gigadb_user
    ADD CONSTRAINT gigadb_user_pkey PRIMARY KEY (id);


--
-- TOC entry 2308 (class 2606 OID 16933)
-- Name: gigadb_user_twitter_id_key; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.gigadb_user
    ADD CONSTRAINT gigadb_user_twitter_id_key UNIQUE (twitter_id);


--
-- TOC entry 2310 (class 2606 OID 16935)
-- Name: gigadb_user_username_key; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.gigadb_user
    ADD CONSTRAINT gigadb_user_username_key UNIQUE (username);


--
-- TOC entry 2314 (class 2606 OID 16937)
-- Name: image_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.image
    ADD CONSTRAINT image_pkey PRIMARY KEY (id);


--
-- TOC entry 2316 (class 2606 OID 16939)
-- Name: link_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.link
    ADD CONSTRAINT link_pkey PRIMARY KEY (id);


--
-- TOC entry 2322 (class 2606 OID 16941)
-- Name: link_prefix_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.prefix
    ADD CONSTRAINT link_prefix_pkey PRIMARY KEY (id);


--
-- TOC entry 2318 (class 2606 OID 16943)
-- Name: manuscript_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.manuscript
    ADD CONSTRAINT manuscript_pkey PRIMARY KEY (id);


--
-- TOC entry 2320 (class 2606 OID 16945)
-- Name: news_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.news
    ADD CONSTRAINT news_pkey PRIMARY KEY (id);


--
-- TOC entry 2324 (class 2606 OID 16947)
-- Name: project_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.project
    ADD CONSTRAINT project_pkey PRIMARY KEY (id);


--
-- TOC entry 2326 (class 2606 OID 16949)
-- Name: publisher_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.publisher
    ADD CONSTRAINT publisher_pkey PRIMARY KEY (id);


--
-- TOC entry 2328 (class 2606 OID 16951)
-- Name: relation_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.relation
    ADD CONSTRAINT relation_pkey PRIMARY KEY (id);


--
-- TOC entry 2330 (class 2606 OID 16953)
-- Name: relationship_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.relationship
    ADD CONSTRAINT relationship_pkey PRIMARY KEY (id);


--
-- TOC entry 2332 (class 2606 OID 16955)
-- Name: rss_message_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.rss_message
    ADD CONSTRAINT rss_message_pkey PRIMARY KEY (id);


--
-- TOC entry 2337 (class 2606 OID 16957)
-- Name: sample_attribute_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.sample_attribute
    ADD CONSTRAINT sample_attribute_pkey PRIMARY KEY (id);


--
-- TOC entry 2339 (class 2606 OID 16959)
-- Name: sample_experiment_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.sample_experiment
    ADD CONSTRAINT sample_experiment_pkey PRIMARY KEY (id);


--
-- TOC entry 2334 (class 2606 OID 16961)
-- Name: sample_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.sample
    ADD CONSTRAINT sample_pkey PRIMARY KEY (id);


--
-- TOC entry 2341 (class 2606 OID 16963)
-- Name: sample_rel_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.sample_rel
    ADD CONSTRAINT sample_rel_pkey PRIMARY KEY (id);


--
-- TOC entry 2343 (class 2606 OID 16965)
-- Name: search_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.search
    ADD CONSTRAINT search_pkey PRIMARY KEY (id);


--
-- TOC entry 2345 (class 2606 OID 16967)
-- Name: species_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.species
    ADD CONSTRAINT species_pkey PRIMARY KEY (id);


--
-- TOC entry 2312 (class 2606 OID 16969)
-- Name: type_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.type
    ADD CONSTRAINT type_pkey PRIMARY KEY (id);


--
-- TOC entry 2347 (class 2606 OID 16971)
-- Name: unit_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.unit
    ADD CONSTRAINT unit_pkey PRIMARY KEY (id);


--
-- TOC entry 2349 (class 2606 OID 16973)
-- Name: yiisession_pkey1; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY public.yiisession
    ADD CONSTRAINT yiisession_pkey1 PRIMARY KEY (id);


--
-- TOC entry 2335 (class 1259 OID 16974)
-- Name: fki_sample_attribute_fkey; Type: INDEX; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE INDEX fki_sample_attribute_fkey ON public.sample_attribute USING btree (attribute_id);


--
-- TOC entry 2252 (class 1259 OID 16975)
-- Name: identifier_idx; Type: INDEX; Schema: public; Owner: gigadb; Tablespace: 
--

CREATE UNIQUE INDEX identifier_idx ON public.dataset USING btree (identifier);


--
-- TOC entry 2350 (class 2606 OID 16976)
-- Name: AuthAssignment_itemname_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public."AuthAssignment"
    ADD CONSTRAINT "AuthAssignment_itemname_fkey" FOREIGN KEY (itemname) REFERENCES public."AuthItem"(name) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2351 (class 2606 OID 16981)
-- Name: alternative_identifiers_extdb_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.alternative_identifiers
    ADD CONSTRAINT alternative_identifiers_extdb_id_fkey FOREIGN KEY (extdb_id) REFERENCES public.extdb(id);


--
-- TOC entry 2352 (class 2606 OID 16986)
-- Name: alternative_identifiers_sample_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.alternative_identifiers
    ADD CONSTRAINT alternative_identifiers_sample_id_fkey FOREIGN KEY (sample_id) REFERENCES public.sample(id);


--
-- TOC entry 2353 (class 2606 OID 16991)
-- Name: curation_log_dataset_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.curation_log
    ADD CONSTRAINT curation_log_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES public.dataset(id) ON DELETE CASCADE;


--
-- TOC entry 2358 (class 2606 OID 16996)
-- Name: dataset_attributes_attribute_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.dataset_attributes
    ADD CONSTRAINT dataset_attributes_attribute_id_fkey FOREIGN KEY (attribute_id) REFERENCES public.attribute(id);


--
-- TOC entry 2359 (class 2606 OID 17001)
-- Name: dataset_attributes_dataset_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.dataset_attributes
    ADD CONSTRAINT dataset_attributes_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES public.dataset(id);


--
-- TOC entry 2360 (class 2606 OID 17006)
-- Name: dataset_attributes_units_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.dataset_attributes
    ADD CONSTRAINT dataset_attributes_units_id_fkey FOREIGN KEY (units_id) REFERENCES public.unit(id);


--
-- TOC entry 2361 (class 2606 OID 17011)
-- Name: dataset_author_author_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.dataset_author
    ADD CONSTRAINT dataset_author_author_id_fkey FOREIGN KEY (author_id) REFERENCES public.author(id) ON DELETE CASCADE;


--
-- TOC entry 2362 (class 2606 OID 17016)
-- Name: dataset_author_dataset_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.dataset_author
    ADD CONSTRAINT dataset_author_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES public.dataset(id) ON DELETE CASCADE;


--
-- TOC entry 2354 (class 2606 OID 17021)
-- Name: dataset_curator_id; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.dataset
    ADD CONSTRAINT dataset_curator_id FOREIGN KEY (curator_id) REFERENCES public.gigadb_user(id);


--
-- TOC entry 2363 (class 2606 OID 17026)
-- Name: dataset_funder_dataset_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.dataset_funder
    ADD CONSTRAINT dataset_funder_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES public.dataset(id) ON DELETE CASCADE;


--
-- TOC entry 2364 (class 2606 OID 17031)
-- Name: dataset_funder_funder_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.dataset_funder
    ADD CONSTRAINT dataset_funder_funder_id_fkey FOREIGN KEY (funder_id) REFERENCES public.funder_name(id) ON DELETE CASCADE;


--
-- TOC entry 2355 (class 2606 OID 17036)
-- Name: dataset_image_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.dataset
    ADD CONSTRAINT dataset_image_id_fkey FOREIGN KEY (image_id) REFERENCES public.image(id) ON DELETE SET NULL;


--
-- TOC entry 2365 (class 2606 OID 17041)
-- Name: dataset_log_dataset_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.dataset_log
    ADD CONSTRAINT dataset_log_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES public.dataset(id) ON DELETE CASCADE;


--
-- TOC entry 2366 (class 2606 OID 17046)
-- Name: dataset_project_dataset_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.dataset_project
    ADD CONSTRAINT dataset_project_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES public.dataset(id) ON DELETE CASCADE;


--
-- TOC entry 2367 (class 2606 OID 17051)
-- Name: dataset_project_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.dataset_project
    ADD CONSTRAINT dataset_project_project_id_fkey FOREIGN KEY (project_id) REFERENCES public.project(id) ON DELETE CASCADE;


--
-- TOC entry 2356 (class 2606 OID 17056)
-- Name: dataset_publisher_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.dataset
    ADD CONSTRAINT dataset_publisher_id_fkey FOREIGN KEY (publisher_id) REFERENCES public.publisher(id) ON DELETE SET NULL;


--
-- TOC entry 2368 (class 2606 OID 17061)
-- Name: dataset_sample_dataset_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.dataset_sample
    ADD CONSTRAINT dataset_sample_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES public.dataset(id) ON DELETE CASCADE;


--
-- TOC entry 2369 (class 2606 OID 17066)
-- Name: dataset_sample_sample_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.dataset_sample
    ADD CONSTRAINT dataset_sample_sample_id_fkey FOREIGN KEY (sample_id) REFERENCES public.sample(id) ON DELETE CASCADE;


--
-- TOC entry 2357 (class 2606 OID 17071)
-- Name: dataset_submitter_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.dataset
    ADD CONSTRAINT dataset_submitter_id_fkey FOREIGN KEY (submitter_id) REFERENCES public.gigadb_user(id) ON DELETE RESTRICT;


--
-- TOC entry 2370 (class 2606 OID 17076)
-- Name: dataset_type_dataset_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.dataset_type
    ADD CONSTRAINT dataset_type_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES public.dataset(id) ON DELETE CASCADE;


--
-- TOC entry 2371 (class 2606 OID 17081)
-- Name: dataset_type_type_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.dataset_type
    ADD CONSTRAINT dataset_type_type_id_fkey FOREIGN KEY (type_id) REFERENCES public.type(id) ON DELETE CASCADE;


--
-- TOC entry 2372 (class 2606 OID 17086)
-- Name: exp_attributes_attribute_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.exp_attributes
    ADD CONSTRAINT exp_attributes_attribute_id_fkey FOREIGN KEY (attribute_id) REFERENCES public.attribute(id);


--
-- TOC entry 2373 (class 2606 OID 17091)
-- Name: exp_attributes_exp_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.exp_attributes
    ADD CONSTRAINT exp_attributes_exp_id_fkey FOREIGN KEY (exp_id) REFERENCES public.experiment(id);


--
-- TOC entry 2374 (class 2606 OID 17096)
-- Name: exp_attributes_units_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.exp_attributes
    ADD CONSTRAINT exp_attributes_units_id_fkey FOREIGN KEY (units_id) REFERENCES public.unit(id);


--
-- TOC entry 2375 (class 2606 OID 17101)
-- Name: experiment_dataset_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.experiment
    ADD CONSTRAINT experiment_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES public.dataset(id);


--
-- TOC entry 2376 (class 2606 OID 17106)
-- Name: external_link_dataset_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.external_link
    ADD CONSTRAINT external_link_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES public.dataset(id) ON DELETE CASCADE;


--
-- TOC entry 2377 (class 2606 OID 17111)
-- Name: external_link_external_link_type_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.external_link
    ADD CONSTRAINT external_link_external_link_type_id_fkey FOREIGN KEY (external_link_type_id) REFERENCES public.external_link_type(id) ON DELETE CASCADE;


--
-- TOC entry 2381 (class 2606 OID 17116)
-- Name: file_attributes_attribute_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.file_attributes
    ADD CONSTRAINT file_attributes_attribute_id_fkey FOREIGN KEY (attribute_id) REFERENCES public.attribute(id);


--
-- TOC entry 2382 (class 2606 OID 17121)
-- Name: file_attributes_file_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.file_attributes
    ADD CONSTRAINT file_attributes_file_id_fkey FOREIGN KEY (file_id) REFERENCES public.file(id) ON DELETE CASCADE;


--
-- TOC entry 2383 (class 2606 OID 17126)
-- Name: file_attributes_unit_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.file_attributes
    ADD CONSTRAINT file_attributes_unit_id_fkey FOREIGN KEY (unit_id) REFERENCES public.unit(id);


--
-- TOC entry 2378 (class 2606 OID 17131)
-- Name: file_dataset_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.file
    ADD CONSTRAINT file_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES public.dataset(id) ON DELETE CASCADE;


--
-- TOC entry 2384 (class 2606 OID 17136)
-- Name: file_experiment_experiment_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.file_experiment
    ADD CONSTRAINT file_experiment_experiment_id_fkey FOREIGN KEY (experiment_id) REFERENCES public.experiment(id);


--
-- TOC entry 2385 (class 2606 OID 17141)
-- Name: file_experiment_file_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.file_experiment
    ADD CONSTRAINT file_experiment_file_id_fkey FOREIGN KEY (file_id) REFERENCES public.file(id);


--
-- TOC entry 2379 (class 2606 OID 17146)
-- Name: file_format_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.file
    ADD CONSTRAINT file_format_id_fkey FOREIGN KEY (format_id) REFERENCES public.file_format(id) ON DELETE CASCADE;


--
-- TOC entry 2386 (class 2606 OID 17151)
-- Name: file_relationship_file_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.file_relationship
    ADD CONSTRAINT file_relationship_file_id_fkey FOREIGN KEY (file_id) REFERENCES public.file(id);


--
-- TOC entry 2387 (class 2606 OID 17156)
-- Name: file_relationship_relationship_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.file_relationship
    ADD CONSTRAINT file_relationship_relationship_id_fkey FOREIGN KEY (relationship_id) REFERENCES public.relationship(id);


--
-- TOC entry 2388 (class 2606 OID 17161)
-- Name: file_sample_file_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.file_sample
    ADD CONSTRAINT file_sample_file_id_fkey FOREIGN KEY (file_id) REFERENCES public.file(id);


--
-- TOC entry 2389 (class 2606 OID 17166)
-- Name: file_sample_sample_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.file_sample
    ADD CONSTRAINT file_sample_sample_id_fkey FOREIGN KEY (sample_id) REFERENCES public.sample(id);


--
-- TOC entry 2380 (class 2606 OID 17171)
-- Name: file_type_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.file
    ADD CONSTRAINT file_type_id_fkey FOREIGN KEY (type_id) REFERENCES public.file_type(id) ON DELETE CASCADE;


--
-- TOC entry 2390 (class 2606 OID 17176)
-- Name: link_dataset_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.link
    ADD CONSTRAINT link_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES public.dataset(id) ON DELETE CASCADE;


--
-- TOC entry 2391 (class 2606 OID 17181)
-- Name: manuscript_dataset_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.manuscript
    ADD CONSTRAINT manuscript_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES public.dataset(id) ON DELETE CASCADE;


--
-- TOC entry 2392 (class 2606 OID 17186)
-- Name: relation_dataset_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.relation
    ADD CONSTRAINT relation_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES public.dataset(id) ON DELETE CASCADE;


--
-- TOC entry 2393 (class 2606 OID 17191)
-- Name: relation_relationship_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.relation
    ADD CONSTRAINT relation_relationship_fkey FOREIGN KEY (relationship_id) REFERENCES public.relationship(id);


--
-- TOC entry 2396 (class 2606 OID 17196)
-- Name: sample_attribute_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.sample_attribute
    ADD CONSTRAINT sample_attribute_fkey FOREIGN KEY (attribute_id) REFERENCES public.attribute(id);


--
-- TOC entry 2397 (class 2606 OID 17201)
-- Name: sample_attribute_sample_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.sample_attribute
    ADD CONSTRAINT sample_attribute_sample_id_fkey FOREIGN KEY (sample_id) REFERENCES public.sample(id) ON DELETE CASCADE;


--
-- TOC entry 2398 (class 2606 OID 17206)
-- Name: sample_attribute_unit_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.sample_attribute
    ADD CONSTRAINT sample_attribute_unit_id_fkey FOREIGN KEY (unit_id) REFERENCES public.unit(id);


--
-- TOC entry 2399 (class 2606 OID 17211)
-- Name: sample_experiment_experiment_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.sample_experiment
    ADD CONSTRAINT sample_experiment_experiment_id_fkey FOREIGN KEY (experiment_id) REFERENCES public.experiment(id);


--
-- TOC entry 2400 (class 2606 OID 17216)
-- Name: sample_experiment_sample_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.sample_experiment
    ADD CONSTRAINT sample_experiment_sample_id_fkey FOREIGN KEY (sample_id) REFERENCES public.sample(id);


--
-- TOC entry 2401 (class 2606 OID 17221)
-- Name: sample_rel_relationship_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.sample_rel
    ADD CONSTRAINT sample_rel_relationship_id_fkey FOREIGN KEY (relationship_id) REFERENCES public.relationship(id);


--
-- TOC entry 2402 (class 2606 OID 17226)
-- Name: sample_rel_sample_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.sample_rel
    ADD CONSTRAINT sample_rel_sample_id_fkey FOREIGN KEY (sample_id) REFERENCES public.sample(id);


--
-- TOC entry 2394 (class 2606 OID 17231)
-- Name: sample_species_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.sample
    ADD CONSTRAINT sample_species_id_fkey FOREIGN KEY (species_id) REFERENCES public.species(id) ON DELETE CASCADE;


--
-- TOC entry 2395 (class 2606 OID 17236)
-- Name: sample_submitted_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.sample
    ADD CONSTRAINT sample_submitted_id_fkey FOREIGN KEY (submitted_id) REFERENCES public.gigadb_user(id);


--
-- TOC entry 2403 (class 2606 OID 17241)
-- Name: search_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY public.search
    ADD CONSTRAINT search_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.gigadb_user(id) ON DELETE RESTRICT;


--
-- TOC entry 2524 (class 0 OID 0)
-- Dependencies: 7
-- Name: SCHEMA public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- TOC entry 2530 (class 0 OID 0)
-- Dependencies: 180
-- Name: TABLE curation_log; Type: ACL; Schema: public; Owner: gigadb
--

REVOKE ALL ON TABLE public.curation_log FROM PUBLIC;
REVOKE ALL ON TABLE public.curation_log FROM gigadb;
GRANT ALL ON TABLE public.curation_log TO gigadb;
GRANT ALL ON TABLE public.curation_log TO PUBLIC;


--
-- TOC entry 2539 (class 0 OID 0)
-- Dependencies: 196
-- Name: TABLE dataset_session; Type: ACL; Schema: public; Owner: gigadb
--

REVOKE ALL ON TABLE public.dataset_session FROM PUBLIC;
REVOKE ALL ON TABLE public.dataset_session FROM gigadb;
GRANT ALL ON TABLE public.dataset_session TO gigadb;


--
-- TOC entry 2551 (class 0 OID 0)
-- Dependencies: 218
-- Name: TABLE file_number; Type: ACL; Schema: public; Owner: gigadb
--

REVOKE ALL ON TABLE public.file_number FROM PUBLIC;
REVOKE ALL ON TABLE public.file_number FROM gigadb;
GRANT ALL ON TABLE public.file_number TO gigadb;
GRANT SELECT ON TABLE public.file_number TO PUBLIC;


--
-- TOC entry 2557 (class 0 OID 0)
-- Dependencies: 230
-- Name: TABLE homepage_dataset_type; Type: ACL; Schema: public; Owner: gigadb
--

REVOKE ALL ON TABLE public.homepage_dataset_type FROM PUBLIC;
REVOKE ALL ON TABLE public.homepage_dataset_type FROM gigadb;
GRANT ALL ON TABLE public.homepage_dataset_type TO gigadb;
GRANT SELECT ON TABLE public.homepage_dataset_type TO PUBLIC;


--
-- TOC entry 2562 (class 0 OID 0)
-- Dependencies: 240
-- Name: TABLE prefix; Type: ACL; Schema: public; Owner: gigadb
--

REVOKE ALL ON TABLE public.prefix FROM PUBLIC;
REVOKE ALL ON TABLE public.prefix FROM gigadb;
GRANT ALL ON TABLE public.prefix TO gigadb;
GRANT ALL ON TABLE public.prefix TO PUBLIC;


--
-- TOC entry 2571 (class 0 OID 0)
-- Dependencies: 257
-- Name: TABLE sample_number; Type: ACL; Schema: public; Owner: gigadb
--

REVOKE ALL ON TABLE public.sample_number FROM PUBLIC;
REVOKE ALL ON TABLE public.sample_number FROM gigadb;
GRANT ALL ON TABLE public.sample_number TO gigadb;
GRANT SELECT ON TABLE public.sample_number TO PUBLIC;


-- Completed on 2021-06-08 09:31:45 UTC

--
-- PostgreSQL database dump complete
--


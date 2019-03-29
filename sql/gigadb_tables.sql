--
-- PostgreSQL database dump
--

-- Dumped from database version 9.3.11
-- Dumped by pg_dump version 9.5.1

-- Started on 2016-04-18 16:43:55 HKT

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 1 (class 3079 OID 12018)
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- TOC entry 2861 (class 0 OID 0)
-- Dependencies: 1
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- TOC entry 171 (class 1259 OID 18104)
-- Name: AuthAssignment; Type: TABLE; Schema: public; Owner: gigadb
--

CREATE TABLE "AuthAssignment" (
    itemname character varying(64) NOT NULL,
    userid character varying(64) NOT NULL,
    bizrule text,
    data text
);


ALTER TABLE "AuthAssignment" OWNER TO gigadb;

--
-- TOC entry 172 (class 1259 OID 18110)
-- Name: AuthItem; Type: TABLE; Schema: public; Owner: gigadb
--

CREATE TABLE "AuthItem" (
    name character varying(64) NOT NULL,
    type integer NOT NULL,
    description text,
    bizrule text,
    data text
);


ALTER TABLE "AuthItem" OWNER TO gigadb;

--
-- TOC entry 173 (class 1259 OID 18116)
-- Name: YiiSession; Type: TABLE; Schema: public; Owner: gigadb
--

CREATE TABLE "YiiSession" (
    id character(32) NOT NULL,
    expire integer,
    data bytea
);


ALTER TABLE "YiiSession" OWNER TO gigadb;


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
-- TOC entry 174 (class 1259 OID 18122)
-- Name: alternative_identifiers; Type: TABLE; Schema: public; Owner: gigadb
--

CREATE TABLE alternative_identifiers (
    id integer NOT NULL,
    sample_id integer NOT NULL,
    extdb_id integer NOT NULL,
    extdb_accession character varying(100)
);


ALTER TABLE alternative_identifiers OWNER TO gigadb;

--
-- TOC entry 2862 (class 0 OID 0)
-- Dependencies: 174
-- Name: COLUMN alternative_identifiers.id; Type: COMMENT; Schema: public; Owner: gigadb
--

COMMENT ON COLUMN alternative_identifiers.id IS '

';


--
-- TOC entry 175 (class 1259 OID 18125)
-- Name: alternative_identifiers_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE alternative_identifiers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE alternative_identifiers_id_seq OWNER TO gigadb;

--
-- TOC entry 2863 (class 0 OID 0)
-- Dependencies: 175
-- Name: alternative_identifiers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE alternative_identifiers_id_seq OWNED BY alternative_identifiers.id;


--
-- TOC entry 176 (class 1259 OID 18127)
-- Name: attribute; Type: TABLE; Schema: public; Owner: gigadb
--

CREATE TABLE attribute (
    id integer NOT NULL,
    attribute_name character varying(100),
    definition character varying(1000),
    model character varying(30),
    structured_comment_name character varying(50),
    value_syntax character varying(500),
    allowed_units character varying(100),
    occurance character varying(5),
    ontology_link character varying(1000),
    note character varying(50)
);


ALTER TABLE attribute OWNER TO gigadb;

--
-- TOC entry 177 (class 1259 OID 18133)
-- Name: attribute_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE attribute_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE attribute_id_seq OWNER TO gigadb;

--
-- TOC entry 2864 (class 0 OID 0)
-- Dependencies: 177
-- Name: attribute_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE attribute_id_seq OWNED BY attribute.id;


--
-- TOC entry 178 (class 1259 OID 18135)
-- Name: author; Type: TABLE; Schema: public; Owner: gigadb
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


ALTER TABLE author OWNER TO gigadb;

--
-- TOC entry 179 (class 1259 OID 18141)
-- Name: author_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE author_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE author_id_seq OWNER TO gigadb;

--
-- TOC entry 2865 (class 0 OID 0)
-- Dependencies: 179
-- Name: author_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE author_id_seq OWNED BY author.id;


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
-- TOC entry 180 (class 1259 OID 18143)
-- Name: dataset; Type: TABLE; Schema: public; Owner: gigadb
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
    upload_status character varying(45) DEFAULT 'AuthorReview'::character varying NOT NULL,
    excelfile character varying(50),
    excelfile_md5 character varying(32),
    publication_date date,
    modification_date date,
    publisher_id integer,
    token character varying(16) DEFAULT NULL::character varying,
    fairnuse date
);


ALTER TABLE dataset OWNER TO gigadb;

--
-- TOC entry 181 (class 1259 OID 18152)
-- Name: dataset_attributes; Type: TABLE; Schema: public; Owner: gigadb
--

CREATE TABLE dataset_attributes (
    id integer NOT NULL,
    dataset_id integer,
    attribute_id integer,
    value character varying(50),
    units_id character varying(30),
    image_id integer,
    until_date date
);


ALTER TABLE dataset_attributes OWNER TO gigadb;

--
-- TOC entry 182 (class 1259 OID 18155)
-- Name: dataset_attributes_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE dataset_attributes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE dataset_attributes_id_seq OWNER TO gigadb;

--
-- TOC entry 2866 (class 0 OID 0)
-- Dependencies: 182
-- Name: dataset_attributes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE dataset_attributes_id_seq OWNED BY dataset_attributes.id;


--
-- TOC entry 183 (class 1259 OID 18157)
-- Name: dataset_author; Type: TABLE; Schema: public; Owner: gigadb
--

CREATE TABLE dataset_author (
    id integer NOT NULL,
    dataset_id integer NOT NULL,
    author_id integer NOT NULL,
    rank integer DEFAULT 0,
    role character varying(30)
);


ALTER TABLE dataset_author OWNER TO gigadb;

--
-- TOC entry 184 (class 1259 OID 18161)
-- Name: dataset_author_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE dataset_author_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE dataset_author_id_seq OWNER TO gigadb;

--
-- TOC entry 2867 (class 0 OID 0)
-- Dependencies: 184
-- Name: dataset_author_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE dataset_author_id_seq OWNED BY dataset_author.id;


--
-- TOC entry 185 (class 1259 OID 18163)
-- Name: dataset_funder; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE dataset_funder (
    id integer NOT NULL,
    dataset_id integer NOT NULL,
    funder_id integer NOT NULL,
    grant_award text DEFAULT ''::text,
    comments text DEFAULT ''::text,
    awardee character varying(50)
);


ALTER TABLE dataset_funder OWNER TO postgres;

--
-- TOC entry 186 (class 1259 OID 18171)
-- Name: dataset_funder_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE dataset_funder_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE dataset_funder_id_seq OWNER TO postgres;

--
-- TOC entry 2868 (class 0 OID 0)
-- Dependencies: 186
-- Name: dataset_funder_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE dataset_funder_id_seq OWNED BY dataset_funder.id;


--
-- TOC entry 187 (class 1259 OID 18173)
-- Name: dataset_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE dataset_id_seq
    START WITH 33
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE dataset_id_seq OWNER TO gigadb;

--
-- TOC entry 2869 (class 0 OID 0)
-- Dependencies: 187
-- Name: dataset_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE dataset_id_seq OWNED BY dataset.id;


--
-- TOC entry 188 (class 1259 OID 18175)
-- Name: dataset_log; Type: TABLE; Schema: public; Owner: gigadb
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


ALTER TABLE dataset_log OWNER TO gigadb;

--
-- TOC entry 189 (class 1259 OID 18184)
-- Name: dataset_log_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE dataset_log_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE dataset_log_id_seq OWNER TO gigadb;

--
-- TOC entry 2870 (class 0 OID 0)
-- Dependencies: 189
-- Name: dataset_log_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE dataset_log_id_seq OWNED BY dataset_log.id;


--
-- TOC entry 190 (class 1259 OID 18186)
-- Name: dataset_project; Type: TABLE; Schema: public; Owner: gigadb
--

CREATE TABLE dataset_project (
    id integer NOT NULL,
    dataset_id integer,
    project_id integer
);


ALTER TABLE dataset_project OWNER TO gigadb;

--
-- TOC entry 191 (class 1259 OID 18189)
-- Name: dataset_project_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE dataset_project_id_seq
    START WITH 7
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE dataset_project_id_seq OWNER TO gigadb;

--
-- TOC entry 2871 (class 0 OID 0)
-- Dependencies: 191
-- Name: dataset_project_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE dataset_project_id_seq OWNED BY dataset_project.id;


--
-- TOC entry 192 (class 1259 OID 18191)
-- Name: dataset_sample; Type: TABLE; Schema: public; Owner: gigadb
--

CREATE TABLE dataset_sample (
    id integer NOT NULL,
    dataset_id integer NOT NULL,
    sample_id integer NOT NULL
);


ALTER TABLE dataset_sample OWNER TO gigadb;

--
-- TOC entry 193 (class 1259 OID 18194)
-- Name: dataset_sample_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE dataset_sample_id_seq
    START WITH 211
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE dataset_sample_id_seq OWNER TO gigadb;

--
-- TOC entry 2872 (class 0 OID 0)
-- Dependencies: 193
-- Name: dataset_sample_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE dataset_sample_id_seq OWNED BY dataset_sample.id;


--
-- TOC entry 194 (class 1259 OID 18196)
-- Name: dataset_session; Type: TABLE; Schema: public; Owner: gigadb
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


ALTER TABLE dataset_session OWNER TO gigadb;

--
-- TOC entry 195 (class 1259 OID 18202)
-- Name: dataset_session_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE dataset_session_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE dataset_session_id_seq OWNER TO gigadb;

--
-- TOC entry 2874 (class 0 OID 0)
-- Dependencies: 195
-- Name: dataset_session_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE dataset_session_id_seq OWNED BY dataset_session.id;


--
-- TOC entry 196 (class 1259 OID 18204)
-- Name: dataset_type; Type: TABLE; Schema: public; Owner: gigadb
--

CREATE TABLE dataset_type (
    id integer NOT NULL,
    dataset_id integer NOT NULL,
    type_id integer
);


ALTER TABLE dataset_type OWNER TO gigadb;

--
-- TOC entry 197 (class 1259 OID 18207)
-- Name: dataset_type_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE dataset_type_id_seq
    START WITH 37
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE dataset_type_id_seq OWNER TO gigadb;

--
-- TOC entry 2875 (class 0 OID 0)
-- Dependencies: 197
-- Name: dataset_type_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE dataset_type_id_seq OWNED BY dataset_type.id;


--
-- TOC entry 198 (class 1259 OID 18209)
-- Name: exp_attributes; Type: TABLE; Schema: public; Owner: gigadb
--

CREATE TABLE exp_attributes (
    id integer NOT NULL,
    exp_id integer,
    attribute_id integer,
    value character varying(1000),
    units_id character varying(50)
);


ALTER TABLE exp_attributes OWNER TO gigadb;

--
-- TOC entry 199 (class 1259 OID 18215)
-- Name: exp_attributes_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE exp_attributes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE exp_attributes_id_seq OWNER TO gigadb;

--
-- TOC entry 2876 (class 0 OID 0)
-- Dependencies: 199
-- Name: exp_attributes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE exp_attributes_id_seq OWNED BY exp_attributes.id;


--
-- TOC entry 200 (class 1259 OID 18217)
-- Name: experiment; Type: TABLE; Schema: public; Owner: gigadb
--

CREATE TABLE experiment (
    id integer NOT NULL,
    experiment_type character varying(100),
    experiment_name character varying(100),
    exp_description character varying(1000),
    dataset_id integer
);


ALTER TABLE experiment OWNER TO gigadb;

--
-- TOC entry 201 (class 1259 OID 18223)
-- Name: experiment_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE experiment_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE experiment_id_seq OWNER TO gigadb;

--
-- TOC entry 2877 (class 0 OID 0)
-- Dependencies: 201
-- Name: experiment_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE experiment_id_seq OWNED BY experiment.id;


--
-- TOC entry 202 (class 1259 OID 18225)
-- Name: extdb; Type: TABLE; Schema: public; Owner: gigadb
--

CREATE TABLE extdb (
    id integer NOT NULL,
    database_name character varying(100),
    definition character varying(1000),
    database_homepage character varying(100),
    database_search_url character varying(100)
);


ALTER TABLE extdb OWNER TO gigadb;

--
-- TOC entry 203 (class 1259 OID 18231)
-- Name: extdb_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE extdb_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE extdb_id_seq OWNER TO gigadb;

--
-- TOC entry 2878 (class 0 OID 0)
-- Dependencies: 203
-- Name: extdb_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE extdb_id_seq OWNED BY extdb.id;


--
-- TOC entry 204 (class 1259 OID 18233)
-- Name: external_link; Type: TABLE; Schema: public; Owner: gigadb
--

CREATE TABLE external_link (
    id integer NOT NULL,
    dataset_id integer NOT NULL,
    url character varying(128) NOT NULL,
    external_link_type_id integer NOT NULL
);


ALTER TABLE external_link OWNER TO gigadb;

--
-- TOC entry 205 (class 1259 OID 18236)
-- Name: external_link_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE external_link_id_seq
    START WITH 17
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE external_link_id_seq OWNER TO gigadb;

--
-- TOC entry 2879 (class 0 OID 0)
-- Dependencies: 205
-- Name: external_link_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE external_link_id_seq OWNED BY external_link.id;


--
-- TOC entry 206 (class 1259 OID 18238)
-- Name: external_link_type; Type: TABLE; Schema: public; Owner: gigadb
--

CREATE TABLE external_link_type (
    id integer NOT NULL,
    name character varying(45) NOT NULL
);


ALTER TABLE external_link_type OWNER TO gigadb;

--
-- TOC entry 207 (class 1259 OID 18241)
-- Name: external_link_type_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE external_link_type_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE external_link_type_id_seq OWNER TO gigadb;

--
-- TOC entry 2880 (class 0 OID 0)
-- Dependencies: 207
-- Name: external_link_type_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE external_link_type_id_seq OWNED BY external_link_type.id;


--
-- TOC entry 208 (class 1259 OID 18243)
-- Name: file; Type: TABLE; Schema: public; Owner: gigadb
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


ALTER TABLE file OWNER TO gigadb;

--
-- TOC entry 209 (class 1259 OID 18252)
-- Name: file_attributes; Type: TABLE; Schema: public; Owner: gigadb
--

CREATE TABLE file_attributes (
    id integer NOT NULL,
    file_id integer NOT NULL,
    attribute_id integer NOT NULL,
    value character varying(50),
    unit_id character varying(30)
);


ALTER TABLE file_attributes OWNER TO gigadb;

--
-- TOC entry 210 (class 1259 OID 18255)
-- Name: file_attributes_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE file_attributes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE file_attributes_id_seq OWNER TO gigadb;

--
-- TOC entry 2881 (class 0 OID 0)
-- Dependencies: 210
-- Name: file_attributes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE file_attributes_id_seq OWNED BY file_attributes.id;


--
-- TOC entry 211 (class 1259 OID 18257)
-- Name: file_experiment; Type: TABLE; Schema: public; Owner: gigadb
--

CREATE TABLE file_experiment (
    id integer NOT NULL,
    file_id integer,
    experiment_id integer
);


ALTER TABLE file_experiment OWNER TO gigadb;

--
-- TOC entry 212 (class 1259 OID 18260)
-- Name: file_experiment_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE file_experiment_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE file_experiment_id_seq OWNER TO gigadb;

--
-- TOC entry 2882 (class 0 OID 0)
-- Dependencies: 212
-- Name: file_experiment_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE file_experiment_id_seq OWNED BY file_experiment.id;


--
-- TOC entry 213 (class 1259 OID 18262)
-- Name: file_format; Type: TABLE; Schema: public; Owner: gigadb
--

CREATE TABLE file_format (
    id integer NOT NULL,
    name character varying(20) NOT NULL,
    description text DEFAULT ''::text NOT NULL,
    edam_ontology_id character varying(100)
);


ALTER TABLE file_format OWNER TO gigadb;

--
-- TOC entry 214 (class 1259 OID 18269)
-- Name: file_format_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE file_format_id_seq
    START WITH 26
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE file_format_id_seq OWNER TO gigadb;

--
-- TOC entry 2883 (class 0 OID 0)
-- Dependencies: 214
-- Name: file_format_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE file_format_id_seq OWNED BY file_format.id;


--
-- TOC entry 215 (class 1259 OID 18271)
-- Name: file_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE file_id_seq
    START WITH 6716
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE file_id_seq OWNER TO gigadb;

--
-- TOC entry 2884 (class 0 OID 0)
-- Dependencies: 215
-- Name: file_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE file_id_seq OWNED BY file.id;


--
-- TOC entry 216 (class 1259 OID 18273)
-- Name: file_relationship; Type: TABLE; Schema: public; Owner: gigadb
--

CREATE TABLE file_relationship (
    id integer NOT NULL,
    file_id integer NOT NULL,
    related_file_id integer NOT NULL,
    relationship_id integer
);


ALTER TABLE file_relationship OWNER TO gigadb;

--
-- TOC entry 217 (class 1259 OID 18276)
-- Name: file_relationship_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE file_relationship_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE file_relationship_id_seq OWNER TO gigadb;

--
-- TOC entry 2885 (class 0 OID 0)
-- Dependencies: 217
-- Name: file_relationship_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE file_relationship_id_seq OWNED BY file_relationship.id;


--
-- TOC entry 218 (class 1259 OID 18278)
-- Name: file_sample; Type: TABLE; Schema: public; Owner: gigadb
--

CREATE TABLE file_sample (
    id integer NOT NULL,
    sample_id integer NOT NULL,
    file_id integer NOT NULL
);


ALTER TABLE file_sample OWNER TO gigadb;

--
-- TOC entry 219 (class 1259 OID 18281)
-- Name: file_sample_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE file_sample_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE file_sample_id_seq OWNER TO gigadb;

--
-- TOC entry 2886 (class 0 OID 0)
-- Dependencies: 219
-- Name: file_sample_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE file_sample_id_seq OWNED BY file_sample.id;


--
-- TOC entry 220 (class 1259 OID 18283)
-- Name: file_type; Type: TABLE; Schema: public; Owner: gigadb
--

CREATE TABLE file_type (
    id integer NOT NULL,
    name character varying(100) NOT NULL,
    description text DEFAULT ''::text NOT NULL,
    edam_ontology_id character varying(100)
);


ALTER TABLE file_type OWNER TO gigadb;

--
-- TOC entry 221 (class 1259 OID 18290)
-- Name: file_type_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE file_type_id_seq
    START WITH 15
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE file_type_id_seq OWNER TO gigadb;

--
-- TOC entry 2887 (class 0 OID 0)
-- Dependencies: 221
-- Name: file_type_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE file_type_id_seq OWNED BY file_type.id;


--
-- TOC entry 222 (class 1259 OID 18292)
-- Name: funder_name; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE funder_name (
    id integer NOT NULL,
    uri character varying(100) NOT NULL,
    primary_name_display character varying(1000),
    country character varying(128) DEFAULT ''::character varying
);


ALTER TABLE funder_name OWNER TO postgres;

--
-- TOC entry 223 (class 1259 OID 18299)
-- Name: funder_name_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE funder_name_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE funder_name_id_seq OWNER TO postgres;

--
-- TOC entry 2888 (class 0 OID 0)
-- Dependencies: 223
-- Name: funder_name_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE funder_name_id_seq OWNED BY funder_name.id;


--
-- TOC entry 224 (class 1259 OID 18301)
-- Name: gigadb_user; Type: TABLE; Schema: public; Owner: gigadb
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


ALTER TABLE gigadb_user OWNER TO gigadb;

--
-- TOC entry 225 (class 1259 OID 18312)
-- Name: gigadb_user_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE gigadb_user_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE gigadb_user_id_seq OWNER TO gigadb;

--
-- TOC entry 2889 (class 0 OID 0)
-- Dependencies: 225
-- Name: gigadb_user_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE gigadb_user_id_seq OWNED BY gigadb_user.id;


--
-- TOC entry 226 (class 1259 OID 18314)
-- Name: image; Type: TABLE; Schema: public; Owner: gigadb
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


ALTER TABLE image OWNER TO gigadb;

--
-- TOC entry 227 (class 1259 OID 18321)
-- Name: image_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE image_id_seq
    START WITH 31
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE image_id_seq OWNER TO gigadb;

--
-- TOC entry 2890 (class 0 OID 0)
-- Dependencies: 227
-- Name: image_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE image_id_seq OWNED BY image.id;


--
-- TOC entry 228 (class 1259 OID 18323)
-- Name: link; Type: TABLE; Schema: public; Owner: gigadb
--

CREATE TABLE link (
    id integer NOT NULL,
    dataset_id integer NOT NULL,
    is_primary boolean DEFAULT false NOT NULL,
    link character varying(100) NOT NULL,
    description character varying(200)
);


ALTER TABLE link OWNER TO gigadb;

--
-- TOC entry 229 (class 1259 OID 18327)
-- Name: link_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE link_id_seq
    START WITH 66
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE link_id_seq OWNER TO gigadb;

--
-- TOC entry 2891 (class 0 OID 0)
-- Dependencies: 229
-- Name: link_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE link_id_seq OWNED BY link.id;


--
-- TOC entry 230 (class 1259 OID 18329)
-- Name: link_prefix_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE link_prefix_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE link_prefix_id_seq OWNER TO gigadb;

--
-- TOC entry 231 (class 1259 OID 18331)
-- Name: manuscript; Type: TABLE; Schema: public; Owner: gigadb
--

CREATE TABLE manuscript (
    id integer NOT NULL,
    identifier character varying(32) NOT NULL,
    pmid integer,
    dataset_id integer NOT NULL
);


ALTER TABLE manuscript OWNER TO gigadb;

--
-- TOC entry 232 (class 1259 OID 18334)
-- Name: manuscript_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE manuscript_id_seq
    START WITH 27
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE manuscript_id_seq OWNER TO gigadb;

--
-- TOC entry 2892 (class 0 OID 0)
-- Dependencies: 232
-- Name: manuscript_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE manuscript_id_seq OWNED BY manuscript.id;


--
-- TOC entry 233 (class 1259 OID 18336)
-- Name: news; Type: TABLE; Schema: public; Owner: gigadb
--

CREATE TABLE news (
    id integer NOT NULL,
    title character varying(200) NOT NULL,
    body text DEFAULT ''::text NOT NULL,
    start_date date NOT NULL,
    end_date date NOT NULL
);


ALTER TABLE news OWNER TO gigadb;

--
-- TOC entry 234 (class 1259 OID 18343)
-- Name: news_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE news_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE news_id_seq OWNER TO gigadb;

--
-- TOC entry 2893 (class 0 OID 0)
-- Dependencies: 234
-- Name: news_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE news_id_seq OWNED BY news.id;


--
-- TOC entry 235 (class 1259 OID 18345)
-- Name: prefix; Type: TABLE; Schema: public; Owner: gigadb
--

CREATE TABLE prefix (
    id integer DEFAULT nextval('link_prefix_id_seq'::regclass) NOT NULL,
    prefix character(20) NOT NULL,
    url text NOT NULL,
    source character varying(128) DEFAULT ''::character varying,
    icon character varying(100)
);


ALTER TABLE prefix OWNER TO gigadb;

--
-- TOC entry 236 (class 1259 OID 18353)
-- Name: project; Type: TABLE; Schema: public; Owner: gigadb
--

CREATE TABLE project (
    id integer NOT NULL,
    url character varying(128) NOT NULL,
    name character varying(255) DEFAULT ''::character varying NOT NULL,
    image_location character varying(100)
);


ALTER TABLE project OWNER TO gigadb;

--
-- TOC entry 237 (class 1259 OID 18357)
-- Name: project_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE project_id_seq
    START WITH 7
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE project_id_seq OWNER TO gigadb;

--
-- TOC entry 2895 (class 0 OID 0)
-- Dependencies: 237
-- Name: project_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE project_id_seq OWNED BY project.id;


--
-- TOC entry 238 (class 1259 OID 18359)
-- Name: publisher; Type: TABLE; Schema: public; Owner: gigadb
--

CREATE TABLE publisher (
    id integer NOT NULL,
    name character varying(45) NOT NULL,
    description text DEFAULT ''::text NOT NULL
);


ALTER TABLE publisher OWNER TO gigadb;

--
-- TOC entry 239 (class 1259 OID 18366)
-- Name: publisher_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE publisher_id_seq
    START WITH 3
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE publisher_id_seq OWNER TO gigadb;

--
-- TOC entry 2896 (class 0 OID 0)
-- Dependencies: 239
-- Name: publisher_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE publisher_id_seq OWNED BY publisher.id;


--
-- TOC entry 240 (class 1259 OID 18368)
-- Name: relation; Type: TABLE; Schema: public; Owner: gigadb
--

CREATE TABLE relation (
    id integer NOT NULL,
    dataset_id integer NOT NULL,
    related_doi character varying(15) NOT NULL,
    relationship_id integer
);


ALTER TABLE relation OWNER TO gigadb;

--
-- TOC entry 241 (class 1259 OID 18371)
-- Name: relation_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE relation_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE relation_id_seq OWNER TO gigadb;

--
-- TOC entry 2897 (class 0 OID 0)
-- Dependencies: 241
-- Name: relation_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE relation_id_seq OWNED BY relation.id;


--
-- TOC entry 242 (class 1259 OID 18373)
-- Name: relationship; Type: TABLE; Schema: public; Owner: gigadb
--

CREATE TABLE relationship (
    id integer NOT NULL,
    name character varying(100)
);


ALTER TABLE relationship OWNER TO gigadb;

--
-- TOC entry 243 (class 1259 OID 18376)
-- Name: relationship_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE relationship_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE relationship_id_seq OWNER TO gigadb;

--
-- TOC entry 2898 (class 0 OID 0)
-- Dependencies: 243
-- Name: relationship_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE relationship_id_seq OWNED BY relationship.id;


--
-- TOC entry 244 (class 1259 OID 18378)
-- Name: rss_message; Type: TABLE; Schema: public; Owner: gigadb
--

CREATE TABLE rss_message (
    id integer NOT NULL,
    message character varying(128) NOT NULL,
    publication_date date DEFAULT ('now'::text)::date NOT NULL
);


ALTER TABLE rss_message OWNER TO gigadb;

--
-- TOC entry 245 (class 1259 OID 18382)
-- Name: rss_message_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE rss_message_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE rss_message_id_seq OWNER TO gigadb;

--
-- TOC entry 2899 (class 0 OID 0)
-- Dependencies: 245
-- Name: rss_message_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE rss_message_id_seq OWNED BY rss_message.id;


--
-- TOC entry 246 (class 1259 OID 18384)
-- Name: sample; Type: TABLE; Schema: public; Owner: gigadb
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


ALTER TABLE sample OWNER TO gigadb;

--
-- TOC entry 247 (class 1259 OID 18388)
-- Name: sample_attribute; Type: TABLE; Schema: public; Owner: gigadb
--

CREATE TABLE sample_attribute (
    id integer NOT NULL,
    sample_id integer NOT NULL,
    attribute_id integer NOT NULL,
    value character varying(5000),
    unit_id character varying(30)
);


ALTER TABLE sample_attribute OWNER TO gigadb;

--
-- TOC entry 248 (class 1259 OID 18394)
-- Name: sample_attribute_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE sample_attribute_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE sample_attribute_id_seq OWNER TO gigadb;

--
-- TOC entry 2900 (class 0 OID 0)
-- Dependencies: 248
-- Name: sample_attribute_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE sample_attribute_id_seq OWNED BY sample_attribute.id;


--
-- TOC entry 249 (class 1259 OID 18396)
-- Name: sample_experiment; Type: TABLE; Schema: public; Owner: gigadb
--

CREATE TABLE sample_experiment (
    id integer NOT NULL,
    sample_id integer,
    experiment_id integer
);


ALTER TABLE sample_experiment OWNER TO gigadb;

--
-- TOC entry 250 (class 1259 OID 18399)
-- Name: sample_experiment_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE sample_experiment_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE sample_experiment_id_seq OWNER TO gigadb;

--
-- TOC entry 2901 (class 0 OID 0)
-- Dependencies: 250
-- Name: sample_experiment_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE sample_experiment_id_seq OWNED BY sample_experiment.id;


--
-- TOC entry 251 (class 1259 OID 18401)
-- Name: sample_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE sample_id_seq
    START WITH 210
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE sample_id_seq OWNER TO gigadb;

--
-- TOC entry 2902 (class 0 OID 0)
-- Dependencies: 251
-- Name: sample_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE sample_id_seq OWNED BY sample.id;


--
-- TOC entry 252 (class 1259 OID 18403)
-- Name: sample_rel; Type: TABLE; Schema: public; Owner: gigadb
--

CREATE TABLE sample_rel (
    id integer NOT NULL,
    sample_id integer NOT NULL,
    related_sample_id integer NOT NULL,
    relationship_id integer
);


ALTER TABLE sample_rel OWNER TO gigadb;

--
-- TOC entry 253 (class 1259 OID 18406)
-- Name: sample_rel_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE sample_rel_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE sample_rel_id_seq OWNER TO gigadb;

--
-- TOC entry 2903 (class 0 OID 0)
-- Dependencies: 253
-- Name: sample_rel_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE sample_rel_id_seq OWNED BY sample_rel.id;

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
-- Name: curation_log_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE curation_log_id_seq OWNED BY curation_log.id;

--
-- TOC entry 254 (class 1259 OID 18408)
-- Name: schemup_tables; Type: TABLE; Schema: public; Owner: gigadb
--

CREATE TABLE schemup_tables (
    table_name character varying NOT NULL,
    version character varying NOT NULL,
    is_current boolean DEFAULT false NOT NULL,
    schema text
);


ALTER TABLE schemup_tables OWNER TO gigadb;

--
-- TOC entry 255 (class 1259 OID 18415)
-- Name: search; Type: TABLE; Schema: public; Owner: gigadb
--

CREATE TABLE search (
    id integer NOT NULL,
    user_id integer NOT NULL,
    name character varying(128) NOT NULL,
    query text NOT NULL,
    result text
);


ALTER TABLE search OWNER TO gigadb;

--
-- TOC entry 256 (class 1259 OID 18421)
-- Name: search_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE search_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE search_id_seq OWNER TO gigadb;

--
-- TOC entry 2904 (class 0 OID 0)
-- Dependencies: 256
-- Name: search_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE search_id_seq OWNED BY search.id;


--
-- TOC entry 257 (class 1259 OID 18423)
-- Name: show_accession; Type: VIEW; Schema: public; Owner: gigadb
--

CREATE VIEW show_accession AS
 SELECT ('DOI: '::text || (dataset.identifier)::text) AS doi_number,
    link.link AS related_accessions
   FROM (dataset
     JOIN link ON ((dataset.id = link.dataset_id)));


ALTER TABLE show_accession OWNER TO gigadb;

--
-- TOC entry 258 (class 1259 OID 18427)
-- Name: show_externallink; Type: VIEW; Schema: public; Owner: gigadb
--

CREATE VIEW show_externallink AS
 SELECT ('DOI: '::text || (dataset.identifier)::text) AS doi_number,
    external_link.url AS additional_information
   FROM (dataset
     JOIN external_link ON ((dataset.id = external_link.dataset_id)));


ALTER TABLE show_externallink OWNER TO gigadb;

--
-- TOC entry 259 (class 1259 OID 18431)
-- Name: show_file; Type: VIEW; Schema: public; Owner: gigadb
--

CREATE VIEW show_file AS
 SELECT ('DOI: '::text || (dataset.identifier)::text) AS doi_number,
    file.name AS file_name
   FROM (dataset
     JOIN file ON ((dataset.id = file.dataset_id)));


ALTER TABLE show_file OWNER TO gigadb;

--
-- TOC entry 260 (class 1259 OID 18435)
-- Name: show_manuscript; Type: VIEW; Schema: public; Owner: gigadb
--

CREATE VIEW show_manuscript AS
 SELECT ('DOI: '::text || (dataset.identifier)::text) AS doi_number,
    manuscript.identifier AS related_manuscript
   FROM (dataset
     JOIN manuscript ON ((dataset.id = manuscript.dataset_id)));


ALTER TABLE show_manuscript OWNER TO gigadb;

--
-- TOC entry 261 (class 1259 OID 18439)
-- Name: show_project; Type: VIEW; Schema: public; Owner: gigadb
--

CREATE VIEW show_project AS
 SELECT ('DOI: '::text || (dataset.identifier)::text) AS doi_number,
    project.name AS project
   FROM ((dataset
     JOIN dataset_project ON ((dataset.id = dataset_project.dataset_id)))
     JOIN project ON ((dataset_project.project_id = project.id)));


ALTER TABLE show_project OWNER TO gigadb;

--
-- TOC entry 262 (class 1259 OID 18444)
-- Name: species; Type: TABLE; Schema: public; Owner: gigadb
--

CREATE TABLE species (
    id integer NOT NULL,
    tax_id integer NOT NULL,
    common_name character varying(128),
    genbank_name character varying(128),
    scientific_name character varying(128) NOT NULL,
    eol_link character varying(100)
);


ALTER TABLE species OWNER TO gigadb;

--
-- TOC entry 263 (class 1259 OID 18447)
-- Name: species_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE species_id_seq
    START WITH 28
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE species_id_seq OWNER TO gigadb;

--
-- TOC entry 2905 (class 0 OID 0)
-- Dependencies: 263
-- Name: species_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE species_id_seq OWNED BY species.id;

--
-- Name: tbl_migration; Type: TABLE; Schema: public; Owner: gigadb; Tablespace:
--

CREATE TABLE tbl_migration (
    version character varying(180) NOT NULL,
    apply_time integer
);


ALTER TABLE tbl_migration OWNER TO gigadb;


--
-- TOC entry 264 (class 1259 OID 18449)
-- Name: type; Type: TABLE; Schema: public; Owner: gigadb
--

CREATE TABLE type (
    id integer NOT NULL,
    name character varying(32) NOT NULL,
    description text DEFAULT ''::text NOT NULL
);


ALTER TABLE type OWNER TO gigadb;

--
-- TOC entry 265 (class 1259 OID 18456)
-- Name: type_id_seq; Type: SEQUENCE; Schema: public; Owner: gigadb
--

CREATE SEQUENCE type_id_seq
    START WITH 6
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE type_id_seq OWNER TO gigadb;

--
-- TOC entry 2906 (class 0 OID 0)
-- Dependencies: 265
-- Name: type_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gigadb
--

ALTER SEQUENCE type_id_seq OWNED BY type.id;


--
-- TOC entry 266 (class 1259 OID 18458)
-- Name: unit; Type: TABLE; Schema: public; Owner: gigadb
--

CREATE TABLE unit (
    id character varying(30) NOT NULL,
    name character varying(200),
    definition character varying(500)
);


ALTER TABLE unit OWNER TO gigadb;

--
-- TOC entry 2907 (class 0 OID 0)
-- Dependencies: 266
-- Name: COLUMN unit.id; Type: COMMENT; Schema: public; Owner: gigadb
--

COMMENT ON COLUMN unit.id IS 'the ID from the unit ontology';


--
-- TOC entry 2908 (class 0 OID 0)
-- Dependencies: 266
-- Name: COLUMN unit.name; Type: COMMENT; Schema: public; Owner: gigadb
--

COMMENT ON COLUMN unit.name IS 'the name of the unit (taken from the Unit Ontology)';


--
-- TOC entry 2909 (class 0 OID 0)
-- Dependencies: 266
-- Name: COLUMN unit.definition; Type: COMMENT; Schema: public; Owner: gigadb
--

COMMENT ON COLUMN unit.definition IS 'the inition taken from the unit ontology';


--
-- TOC entry 267 (class 1259 OID 18464)
-- Name: yiisession; Type: TABLE; Schema: public; Owner: gigadb
--

CREATE TABLE yiisession (
    id character(32) NOT NULL,
    expire integer,
    data text
);


ALTER TABLE yiisession OWNER TO gigadb;


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY user_command ALTER COLUMN id SET DEFAULT nextval('user_command_id_seq'::regclass);



--
-- TOC entry 2412 (class 2604 OID 18470)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY alternative_identifiers ALTER COLUMN id SET DEFAULT nextval('alternative_identifiers_id_seq'::regclass);


--
-- TOC entry 2413 (class 2604 OID 18471)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY attribute ALTER COLUMN id SET DEFAULT nextval('attribute_id_seq'::regclass);


--
-- TOC entry 2414 (class 2604 OID 18472)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY author ALTER COLUMN id SET DEFAULT nextval('author_id_seq'::regclass);

--
-- TOC entry 2479 (class 2604 OID 18508)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY author_rel ALTER COLUMN id SET DEFAULT nextval('author_rel_id_seq'::regclass);

--
-- TOC entry 2418 (class 2604 OID 18473)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset ALTER COLUMN id SET DEFAULT nextval('dataset_id_seq'::regclass);


--
-- TOC entry 2419 (class 2604 OID 18474)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset_attributes ALTER COLUMN id SET DEFAULT nextval('dataset_attributes_id_seq'::regclass);


--
-- TOC entry 2421 (class 2604 OID 18475)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset_author ALTER COLUMN id SET DEFAULT nextval('dataset_author_id_seq'::regclass);


--
-- TOC entry 2424 (class 2604 OID 18476)
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY dataset_funder ALTER COLUMN id SET DEFAULT nextval('dataset_funder_id_seq'::regclass);


--
-- TOC entry 2428 (class 2604 OID 18477)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset_log ALTER COLUMN id SET DEFAULT nextval('dataset_log_id_seq'::regclass);


--
-- TOC entry 2429 (class 2604 OID 18478)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset_project ALTER COLUMN id SET DEFAULT nextval('dataset_project_id_seq'::regclass);


--
-- TOC entry 2430 (class 2604 OID 18479)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset_sample ALTER COLUMN id SET DEFAULT nextval('dataset_sample_id_seq'::regclass);


--
-- TOC entry 2431 (class 2604 OID 18480)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset_session ALTER COLUMN id SET DEFAULT nextval('dataset_session_id_seq'::regclass);


--
-- TOC entry 2432 (class 2604 OID 18481)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset_type ALTER COLUMN id SET DEFAULT nextval('dataset_type_id_seq'::regclass);


--
-- TOC entry 2433 (class 2604 OID 18482)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY exp_attributes ALTER COLUMN id SET DEFAULT nextval('exp_attributes_id_seq'::regclass);


--
-- TOC entry 2434 (class 2604 OID 18483)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY experiment ALTER COLUMN id SET DEFAULT nextval('experiment_id_seq'::regclass);


--
-- TOC entry 2435 (class 2604 OID 18484)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY extdb ALTER COLUMN id SET DEFAULT nextval('extdb_id_seq'::regclass);


--
-- TOC entry 2436 (class 2604 OID 18485)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY external_link ALTER COLUMN id SET DEFAULT nextval('external_link_id_seq'::regclass);


--
-- TOC entry 2437 (class 2604 OID 18486)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY external_link_type ALTER COLUMN id SET DEFAULT nextval('external_link_type_id_seq'::regclass);


--
-- TOC entry 2441 (class 2604 OID 18487)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY file ALTER COLUMN id SET DEFAULT nextval('file_id_seq'::regclass);


--
-- TOC entry 2442 (class 2604 OID 18488)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY file_attributes ALTER COLUMN id SET DEFAULT nextval('file_attributes_id_seq'::regclass);


--
-- TOC entry 2443 (class 2604 OID 18489)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY file_experiment ALTER COLUMN id SET DEFAULT nextval('file_experiment_id_seq'::regclass);


--
-- TOC entry 2445 (class 2604 OID 18490)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY file_format ALTER COLUMN id SET DEFAULT nextval('file_format_id_seq'::regclass);


--
-- TOC entry 2446 (class 2604 OID 18491)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY file_relationship ALTER COLUMN id SET DEFAULT nextval('file_relationship_id_seq'::regclass);


--
-- TOC entry 2447 (class 2604 OID 18492)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY file_sample ALTER COLUMN id SET DEFAULT nextval('file_sample_id_seq'::regclass);


--
-- TOC entry 2449 (class 2604 OID 18493)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY file_type ALTER COLUMN id SET DEFAULT nextval('file_type_id_seq'::regclass);


--
-- TOC entry 2451 (class 2604 OID 18494)
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY funder_name ALTER COLUMN id SET DEFAULT nextval('funder_name_id_seq'::regclass);


--
-- TOC entry 2457 (class 2604 OID 18495)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY gigadb_user ALTER COLUMN id SET DEFAULT nextval('gigadb_user_id_seq'::regclass);


--
-- TOC entry 2459 (class 2604 OID 18496)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY image ALTER COLUMN id SET DEFAULT nextval('image_id_seq'::regclass);


--
-- TOC entry 2461 (class 2604 OID 18497)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY link ALTER COLUMN id SET DEFAULT nextval('link_id_seq'::regclass);


--
-- TOC entry 2462 (class 2604 OID 18498)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY manuscript ALTER COLUMN id SET DEFAULT nextval('manuscript_id_seq'::regclass);


--
-- TOC entry 2464 (class 2604 OID 18499)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY news ALTER COLUMN id SET DEFAULT nextval('news_id_seq'::regclass);


--
-- TOC entry 2468 (class 2604 OID 18500)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY project ALTER COLUMN id SET DEFAULT nextval('project_id_seq'::regclass);


--
-- TOC entry 2470 (class 2604 OID 18501)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY publisher ALTER COLUMN id SET DEFAULT nextval('publisher_id_seq'::regclass);


--
-- TOC entry 2471 (class 2604 OID 18502)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY relation ALTER COLUMN id SET DEFAULT nextval('relation_id_seq'::regclass);


--
-- TOC entry 2472 (class 2604 OID 18503)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY relationship ALTER COLUMN id SET DEFAULT nextval('relationship_id_seq'::regclass);


--
-- TOC entry 2474 (class 2604 OID 18504)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY rss_message ALTER COLUMN id SET DEFAULT nextval('rss_message_id_seq'::regclass);


--
-- TOC entry 2476 (class 2604 OID 18505)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY sample ALTER COLUMN id SET DEFAULT nextval('sample_id_seq'::regclass);


--
-- TOC entry 2477 (class 2604 OID 18506)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY sample_attribute ALTER COLUMN id SET DEFAULT nextval('sample_attribute_id_seq'::regclass);


--
-- TOC entry 2478 (class 2604 OID 18507)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY sample_experiment ALTER COLUMN id SET DEFAULT nextval('sample_experiment_id_seq'::regclass);


--
-- TOC entry 2479 (class 2604 OID 18508)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY sample_rel ALTER COLUMN id SET DEFAULT nextval('sample_rel_id_seq'::regclass);


--
-- TOC entry 2479 (class 2604 OID 18508)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY curation_log ALTER COLUMN id SET DEFAULT nextval('curation_log_id_seq'::regclass);

--
-- TOC entry 2481 (class 2604 OID 18509)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY search ALTER COLUMN id SET DEFAULT nextval('search_id_seq'::regclass);


--
-- TOC entry 2482 (class 2604 OID 18510)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY species ALTER COLUMN id SET DEFAULT nextval('species_id_seq'::regclass);


--
-- TOC entry 2484 (class 2604 OID 18511)
-- Name: id; Type: DEFAULT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY type ALTER COLUMN id SET DEFAULT nextval('type_id_seq'::regclass);


--
-- TOC entry 2762 (class 0 OID 18104)
-- Dependencies: 171
-- Data for Name: AuthAssignment; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY "AuthAssignment" (itemname, userid, bizrule, data) FROM stdin;
\.


--
-- TOC entry 2763 (class 0 OID 18110)
-- Dependencies: 172
-- Data for Name: AuthItem; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY "AuthItem" (name, type, description, bizrule, data) FROM stdin;
\.


--
-- TOC entry 2764 (class 0 OID 18116)
-- Dependencies: 173
-- Data for Name: YiiSession; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY "YiiSession" (id, expire, data) FROM stdin;
\.


--
-- TOC entry 2765 (class 0 OID 18122)
-- Dependencies: 174
-- Data for Name: alternative_identifiers; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY alternative_identifiers (id, sample_id, extdb_id, extdb_accession) FROM stdin;
\.


--
-- TOC entry 2910 (class 0 OID 0)
-- Dependencies: 175
-- Name: alternative_identifiers_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('alternative_identifiers_id_seq', 1, false);


--
-- TOC entry 2767 (class 0 OID 18127)
-- Dependencies: 176
-- Data for Name: attribute; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY attribute (id, attribute_name, definition, model, structured_comment_name, value_syntax, allowed_units, occurance, ontology_link, note) FROM stdin;
\.


--
-- TOC entry 2911 (class 0 OID 0)
-- Dependencies: 177
-- Name: attribute_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('attribute_id_seq', 421, true);

--
-- Data for Name: tbl_migration; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY public.tbl_migration (version, apply_time) FROM stdin;
m000000_000000_base 1541674918
\.

--
-- TOC entry 2769 (class 0 OID 18135)
-- Dependencies: 178
-- Data for Name: author; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY author (id, surname, middle_name, first_name, orcid, gigadb_user_id) FROM stdin;
\.


--
-- TOC entry 2912 (class 0 OID 0)
-- Dependencies: 179
-- Name: author_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('author_id_seq', 3788, true);


--
-- TOC entry 2771 (class 0 OID 18143)
-- Dependencies: 180
-- Data for Name: dataset; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY dataset (id, submitter_id, image_id, identifier, title, description, dataset_size, ftp_site, upload_status, excelfile, excelfile_md5, publication_date, modification_date, publisher_id, token, fairnuse) FROM stdin;
\.


--
-- TOC entry 2772 (class 0 OID 18152)
-- Dependencies: 181
-- Data for Name: dataset_attributes; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY dataset_attributes (id, dataset_id, attribute_id, value, units_id, image_id, until_date) FROM stdin;
\.


--
-- TOC entry 2913 (class 0 OID 0)
-- Dependencies: 182
-- Name: dataset_attributes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('dataset_attributes_id_seq', 35, true);


--
-- TOC entry 2774 (class 0 OID 18157)
-- Dependencies: 183
-- Data for Name: dataset_author; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY dataset_author (id, dataset_id, author_id, rank) FROM stdin;
\.


--
-- TOC entry 2914 (class 0 OID 0)
-- Dependencies: 184
-- Name: dataset_author_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('dataset_author_id_seq', 3477, true);


--
-- TOC entry 2776 (class 0 OID 18163)
-- Dependencies: 185
-- Data for Name: dataset_funder; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY dataset_funder (id, dataset_id, funder_id, grant_award, comments) FROM stdin;
\.


--
-- TOC entry 2915 (class 0 OID 0)
-- Dependencies: 186
-- Name: dataset_funder_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('dataset_funder_id_seq', 31, true);


--
-- TOC entry 2916 (class 0 OID 0)
-- Dependencies: 187
-- Name: dataset_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('dataset_id_seq', 208, true);


--
-- TOC entry 2779 (class 0 OID 18175)
-- Dependencies: 188
-- Data for Name: dataset_log; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY dataset_log (id, dataset_id, message, created_at, model, model_id, url) FROM stdin;
\.


--
-- TOC entry 2917 (class 0 OID 0)
-- Dependencies: 189
-- Name: dataset_log_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('dataset_log_id_seq', 82, true);


--
-- TOC entry 2781 (class 0 OID 18186)
-- Dependencies: 190
-- Data for Name: dataset_project; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY dataset_project (id, dataset_id, project_id) FROM stdin;
\.


--
-- TOC entry 2918 (class 0 OID 0)
-- Dependencies: 191
-- Name: dataset_project_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('dataset_project_id_seq', 125, true);


--
-- TOC entry 2783 (class 0 OID 18191)
-- Dependencies: 192
-- Data for Name: dataset_sample; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY dataset_sample (id, dataset_id, sample_id) FROM stdin;
\.


--
-- TOC entry 2919 (class 0 OID 0)
-- Dependencies: 193
-- Name: dataset_sample_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('dataset_sample_id_seq', 4353, true);


--
-- TOC entry 2785 (class 0 OID 18196)
-- Dependencies: 194
-- Data for Name: dataset_session; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY dataset_session (id, identifier, dataset, dataset_id, datasettypes, images, authors, projects, links, "externalLinks", relations, samples) FROM stdin;
\.


--
-- TOC entry 2920 (class 0 OID 0)
-- Dependencies: 195
-- Name: dataset_session_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('dataset_session_id_seq', 26, true);


--
-- TOC entry 2787 (class 0 OID 18204)
-- Dependencies: 196
-- Data for Name: dataset_type; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY dataset_type (id, dataset_id, type_id) FROM stdin;
\.


--
-- TOC entry 2921 (class 0 OID 0)
-- Dependencies: 197
-- Name: dataset_type_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('dataset_type_id_seq', 254, true);


--
-- TOC entry 2789 (class 0 OID 18209)
-- Dependencies: 198
-- Data for Name: exp_attributes; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY exp_attributes (id, exp_id, attribute_id, value, units_id) FROM stdin;
\.


--
-- TOC entry 2922 (class 0 OID 0)
-- Dependencies: 199
-- Name: exp_attributes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('exp_attributes_id_seq', 5, true);


--
-- TOC entry 2791 (class 0 OID 18217)
-- Dependencies: 200
-- Data for Name: experiment; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY experiment (id, experiment_type, experiment_name, exp_description, dataset_id) FROM stdin;
\.


--
-- TOC entry 2923 (class 0 OID 0)
-- Dependencies: 201
-- Name: experiment_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('experiment_id_seq', 3, true);


--
-- TOC entry 2793 (class 0 OID 18225)
-- Dependencies: 202
-- Data for Name: extdb; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY extdb (id, database_name, definition, database_homepage, database_search_url) FROM stdin;
\.


--
-- TOC entry 2924 (class 0 OID 0)
-- Dependencies: 203
-- Name: extdb_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('extdb_id_seq', 2, true);


--
-- TOC entry 2795 (class 0 OID 18233)
-- Dependencies: 204
-- Data for Name: external_link; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY external_link (id, dataset_id, url, external_link_type_id) FROM stdin;
\.


--
-- TOC entry 2925 (class 0 OID 0)
-- Dependencies: 205
-- Name: external_link_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('external_link_id_seq', 59, true);


--
-- TOC entry 2797 (class 0 OID 18238)
-- Dependencies: 206
-- Data for Name: external_link_type; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY external_link_type (id, name) FROM stdin;
\.


--
-- TOC entry 2926 (class 0 OID 0)
-- Dependencies: 207
-- Name: external_link_type_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('external_link_type_id_seq', 2, true);


--
-- TOC entry 2799 (class 0 OID 18243)
-- Dependencies: 208
-- Data for Name: file; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY file (id, dataset_id, name, location, extension, size, description, date_stamp, format_id, type_id, code, index4blast, download_count) FROM stdin;
\.


--
-- TOC entry 2800 (class 0 OID 18252)
-- Dependencies: 209
-- Data for Name: file_attributes; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY file_attributes (id, file_id, attribute_id, value, unit_id) FROM stdin;
\.


--
-- TOC entry 2927 (class 0 OID 0)
-- Dependencies: 210
-- Name: file_attributes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('file_attributes_id_seq', 2, true);


--
-- TOC entry 2802 (class 0 OID 18257)
-- Dependencies: 211
-- Data for Name: file_experiment; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY file_experiment (id, file_id, experiment_id) FROM stdin;
\.


--
-- TOC entry 2928 (class 0 OID 0)
-- Dependencies: 212
-- Name: file_experiment_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('file_experiment_id_seq', 1, true);


--
-- TOC entry 2804 (class 0 OID 18262)
-- Dependencies: 213
-- Data for Name: file_format; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY file_format (id, name, description) FROM stdin;
\.


--
-- TOC entry 2929 (class 0 OID 0)
-- Dependencies: 214
-- Name: file_format_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('file_format_id_seq', 40, true);


--
-- TOC entry 2930 (class 0 OID 0)
-- Dependencies: 215
-- Name: file_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('file_id_seq', 88251, true);


--
-- TOC entry 2807 (class 0 OID 18273)
-- Dependencies: 216
-- Data for Name: file_relationship; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY file_relationship (id, file_id, related_file_id, relationship_id) FROM stdin;
\.


--
-- TOC entry 2931 (class 0 OID 0)
-- Dependencies: 217
-- Name: file_relationship_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('file_relationship_id_seq', 4, true);


--
-- TOC entry 2809 (class 0 OID 18278)
-- Dependencies: 218
-- Data for Name: file_sample; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY file_sample (id, sample_id, file_id) FROM stdin;
\.


--
-- TOC entry 2932 (class 0 OID 0)
-- Dependencies: 219
-- Name: file_sample_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('file_sample_id_seq', 18915, true);


--
-- TOC entry 2811 (class 0 OID 18283)
-- Dependencies: 220
-- Data for Name: file_type; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY file_type (id, name, description) FROM stdin;
\.


--
-- TOC entry 2933 (class 0 OID 0)
-- Dependencies: 221
-- Name: file_type_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('file_type_id_seq', 109, true);


--
-- TOC entry 2813 (class 0 OID 18292)
-- Dependencies: 222
-- Data for Name: funder_name; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY funder_name (id, uri, primary_name_display, country) FROM stdin;
\.


--
-- TOC entry 2934 (class 0 OID 0)
-- Dependencies: 223
-- Name: funder_name_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('funder_name_id_seq', 6171, true);


--
-- TOC entry 2815 (class 0 OID 18301)
-- Dependencies: 224
-- Data for Name: gigadb_user; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY gigadb_user (id, email, password, first_name, last_name, affiliation, role, is_activated, newsletter, previous_newsletter_state, facebook_id, twitter_id, linkedin_id, google_id, username, orcid_id, preferred_link) FROM stdin;
\.


--
-- TOC entry 2935 (class 0 OID 0)
-- Dependencies: 225
-- Name: gigadb_user_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('gigadb_user_id_seq', 343, true);


--
-- TOC entry 2817 (class 0 OID 18314)
-- Dependencies: 226
-- Data for Name: image; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY image (id, location, tag, url, license, photographer, source) FROM stdin;
\.


--
-- TOC entry 2936 (class 0 OID 0)
-- Dependencies: 227
-- Name: image_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('image_id_seq', 220, true);


--
-- TOC entry 2819 (class 0 OID 18323)
-- Dependencies: 228
-- Data for Name: link; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY link (id, dataset_id, is_primary, link) FROM stdin;
\.


--
-- TOC entry 2937 (class 0 OID 0)
-- Dependencies: 229
-- Name: link_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('link_id_seq', 294, true);


--
-- TOC entry 2938 (class 0 OID 0)
-- Dependencies: 230
-- Name: link_prefix_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('link_prefix_id_seq', 44, true);


--
-- TOC entry 2822 (class 0 OID 18331)
-- Dependencies: 231
-- Data for Name: manuscript; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY manuscript (id, identifier, pmid, dataset_id) FROM stdin;
\.


--
-- TOC entry 2939 (class 0 OID 0)
-- Dependencies: 232
-- Name: manuscript_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('manuscript_id_seq', 284, true);


--
-- TOC entry 2824 (class 0 OID 18336)
-- Dependencies: 233
-- Data for Name: news; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY news (id, title, body, start_date, end_date) FROM stdin;
\.


--
-- TOC entry 2940 (class 0 OID 0)
-- Dependencies: 234
-- Name: news_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('news_id_seq', 3, true);


--
-- TOC entry 2826 (class 0 OID 18345)
-- Dependencies: 235
-- Data for Name: prefix; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY prefix (id, prefix, url, source) FROM stdin;
\.


--
-- TOC entry 2827 (class 0 OID 18353)
-- Dependencies: 236
-- Data for Name: project; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY project (id, url, name, image_location) FROM stdin;
\.


--
-- TOC entry 2941 (class 0 OID 0)
-- Dependencies: 237
-- Name: project_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('project_id_seq', 15, true);


--
-- TOC entry 2829 (class 0 OID 18359)
-- Dependencies: 238
-- Data for Name: publisher; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY publisher (id, name, description) FROM stdin;
\.


--
-- TOC entry 2942 (class 0 OID 0)
-- Dependencies: 239
-- Name: publisher_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('publisher_id_seq', 4, true);


--
-- TOC entry 2831 (class 0 OID 18368)
-- Dependencies: 240
-- Data for Name: relation; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY relation (id, dataset_id, related_doi, relationship_id) FROM stdin;
\.


--
-- TOC entry 2943 (class 0 OID 0)
-- Dependencies: 241
-- Name: relation_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('relation_id_seq', 84, true);


--
-- TOC entry 2833 (class 0 OID 18373)
-- Dependencies: 242
-- Data for Name: relationship; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY relationship (id, name) FROM stdin;
\.


--
-- TOC entry 2944 (class 0 OID 0)
-- Dependencies: 243
-- Name: relationship_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('relationship_id_seq', 21, true);


--
-- TOC entry 2835 (class 0 OID 18378)
-- Dependencies: 244
-- Data for Name: rss_message; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY rss_message (id, message, publication_date) FROM stdin;
\.


--
-- TOC entry 2945 (class 0 OID 0)
-- Dependencies: 245
-- Name: rss_message_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('rss_message_id_seq', 2, true);


--
-- TOC entry 2837 (class 0 OID 18384)
-- Dependencies: 246
-- Data for Name: sample; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY sample (id, species_id, name, consent_document, submitted_id, submission_date, contact_author_name, contact_author_email, sampling_protocol) FROM stdin;
\.


--
-- TOC entry 2838 (class 0 OID 18388)
-- Dependencies: 247
-- Data for Name: sample_attribute; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY sample_attribute (id, sample_id, attribute_id, value, unit_id) FROM stdin;
\.


--
-- TOC entry 2946 (class 0 OID 0)
-- Dependencies: 248
-- Name: sample_attribute_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('sample_attribute_id_seq', 30059, true);


--
-- TOC entry 2840 (class 0 OID 18396)
-- Dependencies: 249
-- Data for Name: sample_experiment; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY sample_experiment (id, sample_id, experiment_id) FROM stdin;
\.


--
-- TOC entry 2947 (class 0 OID 0)
-- Dependencies: 250
-- Name: sample_experiment_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('sample_experiment_id_seq', 2, true);


--
-- TOC entry 2948 (class 0 OID 0)
-- Dependencies: 251
-- Name: sample_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('sample_id_seq', 4344, true);


--
-- TOC entry 2843 (class 0 OID 18403)
-- Dependencies: 252
-- Data for Name: sample_rel; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY sample_rel (id, sample_id, related_sample_id, relationship_id) FROM stdin;
\.


--
-- TOC entry 2949 (class 0 OID 0)
-- Dependencies: 253
-- Name: sample_rel_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('sample_rel_id_seq', 8, true);


--
-- TOC entry 2845 (class 0 OID 18408)
-- Dependencies: 254
-- Data for Name: schemup_tables; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY schemup_tables (table_name, version, is_current, schema) FROM stdin;
\.


--
-- TOC entry 2846 (class 0 OID 18415)
-- Dependencies: 255
-- Data for Name: search; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY search (id, user_id, name, query, result) FROM stdin;
\.


--
-- TOC entry 2950 (class 0 OID 0)
-- Dependencies: 256
-- Name: search_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('search_id_seq', 27, true);


--
-- TOC entry 2848 (class 0 OID 18444)
-- Dependencies: 262
-- Data for Name: species; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY species (id, tax_id, common_name, genbank_name, scientific_name, eol_link) FROM stdin;
\.


--
-- TOC entry 2951 (class 0 OID 0)
-- Dependencies: 263
-- Name: species_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('species_id_seq', 1128853, true);


--
-- TOC entry 2850 (class 0 OID 18449)
-- Dependencies: 264
-- Data for Name: type; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY type (id, name, description) FROM stdin;
\.


--
-- TOC entry 2952 (class 0 OID 0)
-- Dependencies: 265
-- Name: type_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gigadb
--

SELECT pg_catalog.setval('type_id_seq', 16, true);


--
-- TOC entry 2852 (class 0 OID 18458)
-- Dependencies: 266
-- Data for Name: unit; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY unit (id, name, definition) FROM stdin;
\.


--
-- TOC entry 2853 (class 0 OID 18464)
-- Dependencies: 267
-- Data for Name: yiisession; Type: TABLE DATA; Schema: public; Owner: gigadb
--

COPY yiisession (id, expire, data) FROM stdin;
\.


--
-- TOC entry 2486 (class 2606 OID 18522)
-- Name: AuthAssignment_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY "AuthAssignment"
    ADD CONSTRAINT "AuthAssignment_pkey" PRIMARY KEY (itemname, userid);


--
-- TOC entry 2488 (class 2606 OID 18524)
-- Name: AuthItem_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY "AuthItem"
    ADD CONSTRAINT "AuthItem_pkey" PRIMARY KEY (name);

--
-- Name: user_command_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY user_command
    ADD CONSTRAINT user_command_pkey PRIMARY KEY (id);

--
-- TOC entry 2490 (class 2606 OID 18526)
-- Name: YiiSession_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY "YiiSession"
    ADD CONSTRAINT "YiiSession_pkey" PRIMARY KEY (id);


--
-- TOC entry 2492 (class 2606 OID 18528)
-- Name: alternative_identifiers_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY alternative_identifiers
    ADD CONSTRAINT alternative_identifiers_pkey PRIMARY KEY (id);


--
-- TOC entry 2494 (class 2606 OID 18530)
-- Name: attribute_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY attribute
    ADD CONSTRAINT attribute_pkey PRIMARY KEY (id);


--
-- TOC entry 2496 (class 2606 OID 18532)
-- Name: author_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb
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
-- TOC entry 2501 (class 2606 OID 18534)
-- Name: dataset_attributes_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset_attributes
    ADD CONSTRAINT dataset_attributes_pkey PRIMARY KEY (id);


--
-- TOC entry 2503 (class 2606 OID 18536)
-- Name: dataset_author_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset_author
    ADD CONSTRAINT dataset_author_pkey PRIMARY KEY (id);


--
-- TOC entry 2505 (class 2606 OID 18538)
-- Name: dataset_funder_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY dataset_funder
    ADD CONSTRAINT dataset_funder_pkey PRIMARY KEY (id);


--
-- TOC entry 2509 (class 2606 OID 18540)
-- Name: dataset_log_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset_log
    ADD CONSTRAINT dataset_log_pkey PRIMARY KEY (id);


--
-- TOC entry 2498 (class 2606 OID 18542)
-- Name: dataset_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset
    ADD CONSTRAINT dataset_pkey PRIMARY KEY (id);


--
-- TOC entry 2511 (class 2606 OID 18544)
-- Name: dataset_project_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset_project
    ADD CONSTRAINT dataset_project_pkey PRIMARY KEY (id);


--
-- TOC entry 2513 (class 2606 OID 18546)
-- Name: dataset_sample_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset_sample
    ADD CONSTRAINT dataset_sample_pkey PRIMARY KEY (id);


--
-- TOC entry 2515 (class 2606 OID 18548)
-- Name: dataset_session_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset_session
    ADD CONSTRAINT dataset_session_pkey PRIMARY KEY (id);


--
-- TOC entry 2517 (class 2606 OID 18550)
-- Name: dataset_type_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset_type
    ADD CONSTRAINT dataset_type_pkey PRIMARY KEY (id);


--
-- Name: curation_log_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY curation_log
    ADD CONSTRAINT curation_log_pkey PRIMARY KEY (id);

--
-- TOC entry 2545 (class 2606 OID 18552)
-- Name: email_unique; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY gigadb_user
    ADD CONSTRAINT email_unique UNIQUE (email);


--
-- TOC entry 2519 (class 2606 OID 18554)
-- Name: exp_attributes_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY exp_attributes
    ADD CONSTRAINT exp_attributes_pkey PRIMARY KEY (id);


--
-- TOC entry 2521 (class 2606 OID 18556)
-- Name: experiment_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY experiment
    ADD CONSTRAINT experiment_pkey PRIMARY KEY (id);


--
-- TOC entry 2523 (class 2606 OID 18558)
-- Name: extdb_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY extdb
    ADD CONSTRAINT extdb_pkey PRIMARY KEY (id);


--
-- TOC entry 2525 (class 2606 OID 18560)
-- Name: external_link_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY external_link
    ADD CONSTRAINT external_link_pkey PRIMARY KEY (id);


--
-- TOC entry 2527 (class 2606 OID 18562)
-- Name: external_link_type_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY external_link_type
    ADD CONSTRAINT external_link_type_pkey PRIMARY KEY (id);


--
-- TOC entry 2531 (class 2606 OID 18564)
-- Name: file_attributes_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY file_attributes
    ADD CONSTRAINT file_attributes_pkey PRIMARY KEY (id);


--
-- TOC entry 2533 (class 2606 OID 18566)
-- Name: file_experiment_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY file_experiment
    ADD CONSTRAINT file_experiment_pkey PRIMARY KEY (id);


--
-- TOC entry 2535 (class 2606 OID 18568)
-- Name: file_format_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY file_format
    ADD CONSTRAINT file_format_pkey PRIMARY KEY (id);


--
-- TOC entry 2529 (class 2606 OID 18570)
-- Name: file_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY file
    ADD CONSTRAINT file_pkey PRIMARY KEY (id);


--
-- TOC entry 2537 (class 2606 OID 18572)
-- Name: file_relationship_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY file_relationship
    ADD CONSTRAINT file_relationship_pkey PRIMARY KEY (id);


--
-- TOC entry 2539 (class 2606 OID 18574)
-- Name: file_sample_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY file_sample
    ADD CONSTRAINT file_sample_pkey PRIMARY KEY (id);


--
-- TOC entry 2541 (class 2606 OID 18576)
-- Name: file_type_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY file_type
    ADD CONSTRAINT file_type_pkey PRIMARY KEY (id);


--
-- TOC entry 2543 (class 2606 OID 18578)
-- Name: funder_name_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY funder_name
    ADD CONSTRAINT funder_name_pkey PRIMARY KEY (id);


--
-- TOC entry 2547 (class 2606 OID 18580)
-- Name: gigadb_user_facebook_id_key; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY gigadb_user
    ADD CONSTRAINT gigadb_user_facebook_id_key UNIQUE (facebook_id);


--
-- TOC entry 2549 (class 2606 OID 18582)
-- Name: gigadb_user_google_id_key; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY gigadb_user
    ADD CONSTRAINT gigadb_user_google_id_key UNIQUE (google_id);


--
-- TOC entry 2551 (class 2606 OID 18584)
-- Name: gigadb_user_linked_id_key; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY gigadb_user
    ADD CONSTRAINT gigadb_user_linked_id_key UNIQUE (linkedin_id);


--
-- TOC entry 2553 (class 2606 OID 18586)
-- Name: gigadb_user_orcid_id_key; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY gigadb_user
    ADD CONSTRAINT gigadb_user_orcid_id_key UNIQUE (orcid_id);


--
-- TOC entry 2555 (class 2606 OID 18588)
-- Name: gigadb_user_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY gigadb_user
    ADD CONSTRAINT gigadb_user_pkey PRIMARY KEY (id);


--
-- TOC entry 2557 (class 2606 OID 18590)
-- Name: gigadb_user_twitter_id_key; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY gigadb_user
    ADD CONSTRAINT gigadb_user_twitter_id_key UNIQUE (twitter_id);


--
-- TOC entry 2559 (class 2606 OID 18592)
-- Name: gigadb_user_username_key; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY gigadb_user
    ADD CONSTRAINT gigadb_user_username_key UNIQUE (username);


--
-- TOC entry 2561 (class 2606 OID 18594)
-- Name: image_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY image
    ADD CONSTRAINT image_pkey PRIMARY KEY (id);


--
-- TOC entry 2563 (class 2606 OID 18596)
-- Name: link_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY link
    ADD CONSTRAINT link_pkey PRIMARY KEY (id);


--
-- TOC entry 2569 (class 2606 OID 18598)
-- Name: link_prefix_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY prefix
    ADD CONSTRAINT link_prefix_pkey PRIMARY KEY (id);


--
-- TOC entry 2565 (class 2606 OID 18600)
-- Name: manuscript_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY manuscript
    ADD CONSTRAINT manuscript_pkey PRIMARY KEY (id);


--
-- TOC entry 2567 (class 2606 OID 18602)
-- Name: news_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY news
    ADD CONSTRAINT news_pkey PRIMARY KEY (id);


--
-- TOC entry 2571 (class 2606 OID 18604)
-- Name: project_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY project
    ADD CONSTRAINT project_pkey PRIMARY KEY (id);


--
-- TOC entry 2573 (class 2606 OID 18606)
-- Name: publisher_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY publisher
    ADD CONSTRAINT publisher_pkey PRIMARY KEY (id);


--
-- TOC entry 2575 (class 2606 OID 18608)
-- Name: relation_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY relation
    ADD CONSTRAINT relation_pkey PRIMARY KEY (id);


--
-- TOC entry 2577 (class 2606 OID 18610)
-- Name: relationship_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY relationship
    ADD CONSTRAINT relationship_pkey PRIMARY KEY (id);


--
-- TOC entry 2579 (class 2606 OID 18612)
-- Name: rss_message_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY rss_message
    ADD CONSTRAINT rss_message_pkey PRIMARY KEY (id);


--
-- TOC entry 2584 (class 2606 OID 18614)
-- Name: sample_attribute_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY sample_attribute
    ADD CONSTRAINT sample_attribute_pkey PRIMARY KEY (id);


--
-- TOC entry 2586 (class 2606 OID 18616)
-- Name: sample_experiment_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY sample_experiment
    ADD CONSTRAINT sample_experiment_pkey PRIMARY KEY (id);


--
-- TOC entry 2581 (class 2606 OID 18618)
-- Name: sample_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY sample
    ADD CONSTRAINT sample_pkey PRIMARY KEY (id);


--
-- TOC entry 2588 (class 2606 OID 18620)
-- Name: sample_rel_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY sample_rel
    ADD CONSTRAINT sample_rel_pkey PRIMARY KEY (id);


--
-- TOC entry 2590 (class 2606 OID 18622)
-- Name: search_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY search
    ADD CONSTRAINT search_pkey PRIMARY KEY (id);


--
-- TOC entry 2592 (class 2606 OID 18624)
-- Name: species_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY species
    ADD CONSTRAINT species_pkey PRIMARY KEY (id);


--
-- Name: tbl_migration_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb; Tablespace: 
--

ALTER TABLE ONLY tbl_migration
    ADD CONSTRAINT tbl_migration_pkey PRIMARY KEY (version);

--
-- TOC entry 2594 (class 2606 OID 18626)
-- Name: type_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY type
    ADD CONSTRAINT type_pkey PRIMARY KEY (id);


--
-- TOC entry 2507 (class 2606 OID 18628)
-- Name: un_dataset_funder; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY dataset_funder
    ADD CONSTRAINT un_dataset_funder UNIQUE (dataset_id, funder_id);


--
-- TOC entry 2596 (class 2606 OID 18630)
-- Name: unit_pkey; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY unit
    ADD CONSTRAINT unit_pkey PRIMARY KEY (id);


--
-- TOC entry 2598 (class 2606 OID 18632)
-- Name: yiisession_pkey1; Type: CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY yiisession
    ADD CONSTRAINT yiisession_pkey1 PRIMARY KEY (id);


--
-- TOC entry 2582 (class 1259 OID 18633)
-- Name: fki_sample_attribute_fkey; Type: INDEX; Schema: public; Owner: gigadb
--

CREATE INDEX fki_sample_attribute_fkey ON sample_attribute USING btree (attribute_id);

CREATE VIEW file_number AS (SELECT count (file.id) AS count from file);
CREATE VIEW sample_number AS (SELECT count (sample.id) AS count from sample);
CREATE VIEW homepage_dataset_type AS (SELECT type.name, count(dataset_type.id) from dataset_type, type, dataset where dataset_type.type_id=type.id and dataset_type.dataset_id=dataset.id and dataset.upload_status = 'Published' group by type.name);
GRANT SELECT ON TABLE file_number TO public;
GRANT SELECT ON TABLE homepage_dataset_type TO public;
GRANT SELECT ON TABLE sample_number TO public;
--
-- TOC entry 2499 (class 1259 OID 18634)
-- Name: identifier_idx; Type: INDEX; Schema: public; Owner: gigadb
--

CREATE UNIQUE INDEX identifier_idx ON dataset USING btree (identifier);


--
-- TOC entry 2599 (class 2606 OID 18635)
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
-- TOC entry 2600 (class 2606 OID 18640)
-- Name: alternative_identifiers_extdb_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY alternative_identifiers
    ADD CONSTRAINT alternative_identifiers_extdb_id_fkey FOREIGN KEY (extdb_id) REFERENCES extdb(id);


--
-- TOC entry 2601 (class 2606 OID 18645)
-- Name: alternative_identifiers_sample_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY alternative_identifiers
    ADD CONSTRAINT alternative_identifiers_sample_id_fkey FOREIGN KEY (sample_id) REFERENCES sample(id);


--
-- TOC entry 2605 (class 2606 OID 18650)
-- Name: dataset_attributes_attribute_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset_attributes
    ADD CONSTRAINT dataset_attributes_attribute_id_fkey FOREIGN KEY (attribute_id) REFERENCES attribute(id);


--
-- TOC entry 2606 (class 2606 OID 18655)
-- Name: dataset_attributes_dataset_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset_attributes
    ADD CONSTRAINT dataset_attributes_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES dataset(id);


--
-- TOC entry 2607 (class 2606 OID 18660)
-- Name: dataset_attributes_units_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset_attributes
    ADD CONSTRAINT dataset_attributes_units_id_fkey FOREIGN KEY (units_id) REFERENCES unit(id);


--
-- TOC entry 2608 (class 2606 OID 18665)
-- Name: dataset_author_author_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset_author
    ADD CONSTRAINT dataset_author_author_id_fkey FOREIGN KEY (author_id) REFERENCES author(id) ON DELETE CASCADE;


--
-- TOC entry 2609 (class 2606 OID 18670)
-- Name: dataset_author_dataset_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset_author
    ADD CONSTRAINT dataset_author_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES dataset(id) ON DELETE CASCADE;


--
-- TOC entry 2610 (class 2606 OID 18675)
-- Name: dataset_funder_dataset_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY dataset_funder
    ADD CONSTRAINT dataset_funder_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES dataset(id) ON DELETE CASCADE;


--
-- TOC entry 2611 (class 2606 OID 18680)
-- Name: dataset_funder_funder_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY dataset_funder
    ADD CONSTRAINT dataset_funder_funder_id_fkey FOREIGN KEY (funder_id) REFERENCES funder_name(id) ON DELETE CASCADE;


--
-- TOC entry 2602 (class 2606 OID 18685)
-- Name: dataset_image_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset
    ADD CONSTRAINT dataset_image_id_fkey FOREIGN KEY (image_id) REFERENCES image(id) ON DELETE SET NULL;


--
-- TOC entry 2612 (class 2606 OID 18690)
-- Name: dataset_log_dataset_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset_log
    ADD CONSTRAINT dataset_log_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES dataset(id) ON DELETE CASCADE;


--
-- TOC entry 2613 (class 2606 OID 18695)
-- Name: dataset_project_dataset_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset_project
    ADD CONSTRAINT dataset_project_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES dataset(id) ON DELETE CASCADE;


--
-- TOC entry 2614 (class 2606 OID 18700)
-- Name: dataset_project_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset_project
    ADD CONSTRAINT dataset_project_project_id_fkey FOREIGN KEY (project_id) REFERENCES project(id) ON DELETE CASCADE;


--
-- TOC entry 2603 (class 2606 OID 18705)
-- Name: dataset_publisher_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset
    ADD CONSTRAINT dataset_publisher_id_fkey FOREIGN KEY (publisher_id) REFERENCES publisher(id) ON DELETE SET NULL;


--
-- TOC entry 2615 (class 2606 OID 18710)
-- Name: dataset_sample_dataset_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset_sample
    ADD CONSTRAINT dataset_sample_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES dataset(id) ON DELETE CASCADE;


--
-- TOC entry 2616 (class 2606 OID 18715)
-- Name: dataset_sample_sample_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset_sample
    ADD CONSTRAINT dataset_sample_sample_id_fkey FOREIGN KEY (sample_id) REFERENCES sample(id) ON DELETE CASCADE;


--
-- TOC entry 2604 (class 2606 OID 18720)
-- Name: dataset_submitter_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset
    ADD CONSTRAINT dataset_submitter_id_fkey FOREIGN KEY (submitter_id) REFERENCES gigadb_user(id) ON DELETE RESTRICT;


--
-- TOC entry 2617 (class 2606 OID 18725)
-- Name: dataset_type_dataset_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset_type
    ADD CONSTRAINT dataset_type_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES dataset(id) ON DELETE CASCADE;


--
-- TOC entry 2618 (class 2606 OID 18730)
-- Name: dataset_type_type_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY dataset_type
    ADD CONSTRAINT dataset_type_type_id_fkey FOREIGN KEY (type_id) REFERENCES type(id) ON DELETE CASCADE;


--
-- TOC entry 2619 (class 2606 OID 18735)
-- Name: exp_attributes_attribute_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY exp_attributes
    ADD CONSTRAINT exp_attributes_attribute_id_fkey FOREIGN KEY (attribute_id) REFERENCES attribute(id);


--
-- TOC entry 2620 (class 2606 OID 18740)
-- Name: exp_attributes_exp_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY exp_attributes
    ADD CONSTRAINT exp_attributes_exp_id_fkey FOREIGN KEY (exp_id) REFERENCES experiment(id);


--
-- TOC entry 2621 (class 2606 OID 18745)
-- Name: exp_attributes_units_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY exp_attributes
    ADD CONSTRAINT exp_attributes_units_id_fkey FOREIGN KEY (units_id) REFERENCES unit(id);


--
-- TOC entry 2622 (class 2606 OID 18750)
-- Name: experiment_dataset_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY experiment
    ADD CONSTRAINT experiment_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES dataset(id);


--
-- TOC entry 2623 (class 2606 OID 18755)
-- Name: external_link_dataset_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY external_link
    ADD CONSTRAINT external_link_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES dataset(id) ON DELETE CASCADE;


--
-- TOC entry 2624 (class 2606 OID 18760)
-- Name: external_link_external_link_type_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY external_link
    ADD CONSTRAINT external_link_external_link_type_id_fkey FOREIGN KEY (external_link_type_id) REFERENCES external_link_type(id) ON DELETE CASCADE;


--
-- TOC entry 2628 (class 2606 OID 18765)
-- Name: file_attributes_attribute_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY file_attributes
    ADD CONSTRAINT file_attributes_attribute_id_fkey FOREIGN KEY (attribute_id) REFERENCES attribute(id);


--
-- TOC entry 2629 (class 2606 OID 18770)
-- Name: file_attributes_file_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY file_attributes
    ADD CONSTRAINT file_attributes_file_id_fkey FOREIGN KEY (file_id) REFERENCES file(id);


--
-- TOC entry 2630 (class 2606 OID 18775)
-- Name: file_attributes_unit_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY file_attributes
    ADD CONSTRAINT file_attributes_unit_id_fkey FOREIGN KEY (unit_id) REFERENCES unit(id);


--
-- TOC entry 2625 (class 2606 OID 18780)
-- Name: file_dataset_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY file
    ADD CONSTRAINT file_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES dataset(id) ON DELETE CASCADE;


--
-- TOC entry 2631 (class 2606 OID 18785)
-- Name: file_experiment_experiment_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY file_experiment
    ADD CONSTRAINT file_experiment_experiment_id_fkey FOREIGN KEY (experiment_id) REFERENCES experiment(id);


--
-- TOC entry 2632 (class 2606 OID 18790)
-- Name: file_experiment_file_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY file_experiment
    ADD CONSTRAINT file_experiment_file_id_fkey FOREIGN KEY (file_id) REFERENCES file(id);


--
-- TOC entry 2626 (class 2606 OID 18795)
-- Name: file_format_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY file
    ADD CONSTRAINT file_format_id_fkey FOREIGN KEY (format_id) REFERENCES file_format(id) ON DELETE CASCADE;


--
-- TOC entry 2633 (class 2606 OID 18800)
-- Name: file_relationship_file_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY file_relationship
    ADD CONSTRAINT file_relationship_file_id_fkey FOREIGN KEY (file_id) REFERENCES file(id);


--
-- TOC entry 2634 (class 2606 OID 18805)
-- Name: file_relationship_relationship_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY file_relationship
    ADD CONSTRAINT file_relationship_relationship_id_fkey FOREIGN KEY (relationship_id) REFERENCES relationship(id);


--
-- TOC entry 2635 (class 2606 OID 18810)
-- Name: file_sample_file_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY file_sample
    ADD CONSTRAINT file_sample_file_id_fkey FOREIGN KEY (file_id) REFERENCES file(id);


--
-- TOC entry 2636 (class 2606 OID 18815)
-- Name: file_sample_sample_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY file_sample
    ADD CONSTRAINT file_sample_sample_id_fkey FOREIGN KEY (sample_id) REFERENCES sample(id);


--
-- TOC entry 2627 (class 2606 OID 18820)
-- Name: file_type_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY file
    ADD CONSTRAINT file_type_id_fkey FOREIGN KEY (type_id) REFERENCES file_type(id) ON DELETE CASCADE;


--
-- TOC entry 2637 (class 2606 OID 18825)
-- Name: link_dataset_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY link
    ADD CONSTRAINT link_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES dataset(id) ON DELETE CASCADE;


--
-- TOC entry 2638 (class 2606 OID 18830)
-- Name: manuscript_dataset_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY manuscript
    ADD CONSTRAINT manuscript_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES dataset(id) ON DELETE CASCADE;


--
-- TOC entry 2639 (class 2606 OID 18835)
-- Name: relation_dataset_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY relation
    ADD CONSTRAINT relation_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES dataset(id) ON DELETE CASCADE;


--
-- TOC entry 2640 (class 2606 OID 18840)
-- Name: relation_relationship_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY relation
    ADD CONSTRAINT relation_relationship_fkey FOREIGN KEY (relationship_id) REFERENCES relationship(id);


--
-- TOC entry 2643 (class 2606 OID 18845)
-- Name: sample_attribute_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY sample_attribute
    ADD CONSTRAINT sample_attribute_fkey FOREIGN KEY (attribute_id) REFERENCES attribute(id);


--
-- TOC entry 2644 (class 2606 OID 18850)
-- Name: sample_attribute_unit_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY sample_attribute
    ADD CONSTRAINT sample_attribute_unit_id_fkey FOREIGN KEY (unit_id) REFERENCES unit(id);


--
-- TOC entry 2645 (class 2606 OID 18855)
-- Name: sample_experiment_experiment_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY sample_experiment
    ADD CONSTRAINT sample_experiment_experiment_id_fkey FOREIGN KEY (experiment_id) REFERENCES experiment(id);


--
-- TOC entry 2646 (class 2606 OID 18860)
-- Name: sample_experiment_sample_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY sample_experiment
    ADD CONSTRAINT sample_experiment_sample_id_fkey FOREIGN KEY (sample_id) REFERENCES sample(id);


--
-- TOC entry 2647 (class 2606 OID 18865)
-- Name: sample_rel_relationship_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY sample_rel
    ADD CONSTRAINT sample_rel_relationship_id_fkey FOREIGN KEY (relationship_id) REFERENCES relationship(id);


--
-- TOC entry 2648 (class 2606 OID 18870)
-- Name: sample_rel_sample_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY sample_rel
    ADD CONSTRAINT sample_rel_sample_id_fkey FOREIGN KEY (sample_id) REFERENCES sample(id);


--
-- TOC entry 2641 (class 2606 OID 18875)
-- Name: sample_species_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY sample
    ADD CONSTRAINT sample_species_id_fkey FOREIGN KEY (species_id) REFERENCES species(id) ON DELETE CASCADE;


--
-- TOC entry 2642 (class 2606 OID 18880)
-- Name: sample_submitted_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY sample
    ADD CONSTRAINT sample_submitted_id_fkey FOREIGN KEY (submitted_id) REFERENCES gigadb_user(id);


--
-- TOC entry 2649 (class 2606 OID 18885)
-- Name: search_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gigadb
--

ALTER TABLE ONLY search
    ADD CONSTRAINT search_user_id_fkey FOREIGN KEY (user_id) REFERENCES gigadb_user(id) ON DELETE RESTRICT;


--
-- TOC entry 2860 (class 0 OID 0)
-- Dependencies: 7
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- TOC entry 2873 (class 0 OID 0)
-- Dependencies: 194
-- Name: dataset_session; Type: ACL; Schema: public; Owner: gigadb
--

REVOKE ALL ON TABLE dataset_session FROM PUBLIC;
REVOKE ALL ON TABLE dataset_session FROM gigadb;
GRANT ALL ON TABLE dataset_session TO gigadb;


--
-- TOC entry 2894 (class 0 OID 0)
-- Dependencies: 235
-- Name: prefix; Type: ACL; Schema: public; Owner: gigadb
--

REVOKE ALL ON TABLE prefix FROM PUBLIC;
REVOKE ALL ON TABLE prefix FROM gigadb;
GRANT ALL ON TABLE prefix TO gigadb;
GRANT ALL ON TABLE prefix TO PUBLIC;


-- Completed on 2016-04-18 16:43:55 HKT

--
-- PostgreSQL database dump complete
--


--
-- Generated from mysql2pgsql.perl
-- http://gborg.postgresql.org/project/mysql2psql/
-- (c) 2001 - 2007 Jose M. Duarte, Joseph Speigle
--

-- warnings are printed for drop tables if they do not exist
-- please see http://archives.postgresql.org/pgsql-novice/2004-10/msg00158.php

--
-- Table structure for table AuthAssignment
--

DROP TABLE "authassignment" CASCADE\g
CREATE TABLE  "authassignment" (
   "itemname"   varchar(64) NOT NULL, 
   "userid"   varchar(64) NOT NULL, 
   "bizrule"   text, 
   "data"   text, 
   primary key ("itemname", "userid")
)  ;

--
-- Table structure for table AuthItem
--
DROP TABLE "authitem" CASCADE\g
CREATE TABLE  "authitem" (
   "name"   varchar(64) NOT NULL, 
   "type"   int NOT NULL, 
   "description"   text, 
   "bizrule"   text, 
   "data"   text, 
   primary key ("name")
)  ;


--
-- Table structure for table AuthItemChild
--

DROP TABLE "authitemchild" CASCADE\g
CREATE TABLE  "authitemchild" (
   "parent"   varchar(64) NOT NULL, 
   "child"   varchar(64) NOT NULL, 
   primary key ("parent", "child")
)  ;
CREATE INDEX "authitemchild_child_idx" ON "authitemchild" USING btree ("child");


--
-- Table structure for table YiiSession
--

DROP TABLE "yiisession" CASCADE\g
CREATE TABLE  "yiisession" (
   "id"   char(32) NOT NULL, 
   "expire"   int default NULL, 
   "data"   text, 
   primary key ("id")
);


--
-- Table structure for table User
--

DROP TABLE "users" CASCADE\g
DROP SEQUENCE "users_id_seq" CASCADE ;

CREATE SEQUENCE "users_id_seq"  START WITH 1 ;

CREATE TABLE  "users" (
   "id" integer DEFAULT nextval('"users_id_seq"') NOT NULL,
   "username"   varchar(128) NOT NULL, 
   "password"   varchar(128) NOT NULL, 
   "email"   varchar(128) NOT NULL, 
   "is_activated"    smallint default '0', 
   "name"   varchar(60) default NULL, 
   "phone"   varchar(60) default NULL, 
   "created_at"   timestamp NOT NULL default CURRENT_TIMESTAMP , 
   "updated_at"   timestamp NOT NULL default '1970-01-01 00:00:00', 
   "ip_address"   char(16) default NULL, 
   "unique_id"   char(40) default NULL, 
   "is_approved"    smallint default '0', 
   "notes"   text, 
   primary key ("id"),
 unique ("username") ,
 unique ("email") 
)   ;


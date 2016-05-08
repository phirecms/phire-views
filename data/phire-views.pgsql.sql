--
-- Views Module PostgreSQL Database for Phire CMS 2.0
--

-- --------------------------------------------------------

--
-- Table structure for table "views"
--

CREATE SEQUENCE view_id_seq START 53001;

CREATE TABLE IF NOT EXISTS "[{prefix}]views" (
  "id" integer NOT NULL DEFAULT nextval('view_id_seq'),
  "name" varchar(255),
  "group_fields" varchar(255),
  "group_style" varchar(255),
  "group_headers" integer,
  "single_fields" varchar(255),
  "single_style" varchar(255),
  "single_headers" integer,
  "models" text,
  PRIMARY KEY ("id")
) ;

ALTER SEQUENCE view_id_seq OWNED BY "[{prefix}]views"."id";
CREATE INDEX "field_field_name" ON "[{prefix}]views" ("name");

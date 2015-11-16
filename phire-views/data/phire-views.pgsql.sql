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
  PRIMARY KEY ("id")
) ;

ALTER SEQUENCE view_id_seq OWNED BY "[{prefix}]views"."id";
CREATE INDEX "field_field_name" ON "[{prefix}]views" ("name");

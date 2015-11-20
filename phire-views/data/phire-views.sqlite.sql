--
-- Views Module SQLite Database for Phire CMS 2.0
--

--  --------------------------------------------------------

--
-- Set database encoding
--

PRAGMA encoding = "UTF-8";
PRAGMA foreign_keys = ON;

-- --------------------------------------------------------

--
-- Table structure for table "views"
--

CREATE TABLE IF NOT EXISTS "[{prefix}]views" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "name" varchar,
  "group_fields" varchar,
  "group_style" varchar,
  "group_headers" integer,
  "single_fields" varchar,
  "single_style" varchar,
  "single_headers" integer,
  UNIQUE ("id")
) ;

INSERT INTO "sqlite_sequence" ("name", "seq") VALUES ('[{prefix}]views', 53000);
CREATE INDEX "field_field_name" ON "[{prefix}]views" ("name");

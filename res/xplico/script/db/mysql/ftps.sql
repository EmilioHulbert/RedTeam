--
-- Tabella delle connessioni ftp
--
CREATE TABLE IF NOT EXISTS ftps (
  id INTEGER NOT NULL AUTO_INCREMENT,
  sol_id INTEGER NOT NULL,
  pol_id INTEGER NOT NULL,
  source_id INTEGER NOT NULL,
  capture_date TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
  decoding_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  viewed_date TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
  first_visualization_user_id INTEGER NOT NULL DEFAULT 0,
  flow_info VARCHAR( 255 ) NOT NULL,
  url VARCHAR( 4096 ),
  important BOOL DEFAULT FALSE,
  username VARCHAR( 40 ),
  password VARCHAR( 40 ),
  cmd_path VARCHAR( 255 ),
  upload_num INTEGER,
  download_num INTEGER,
  PRIMARY KEY (id),
  FOREIGN KEY (pol_id) REFERENCES pols(id) ON DELETE CASCADE,
  FOREIGN KEY (sol_id) REFERENCES sols(id) ON DELETE CASCADE,
  FOREIGN KEY (source_id) REFERENCES sources(id) ON DELETE CASCADE
) TYPE = MYISAM ;

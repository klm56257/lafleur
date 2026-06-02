DROP TABLE IF EXISTS utilisation_code_promo;
DROP TABLE IF EXISTS code_promo;

CREATE TABLE IF NOT EXISTS code_promo (
  code VARCHAR(50) PRIMARY KEY,
  reduction DECIMAL(5,2) NOT NULL,
  date_expiration DATE NOT NULL
);

CREATE TABLE IF NOT EXISTS utilisation_code_promo (
  mail_login VARCHAR(191) NOT NULL,
  code VARCHAR(50) NOT NULL,
  PRIMARY KEY (mail_login, code)
);

INSERT INTO code_promo VALUES 
  ('BIENVENUE10', 10.00, '2026-12-31'),
  ('ETE20', 20.00, '2026-12-31');
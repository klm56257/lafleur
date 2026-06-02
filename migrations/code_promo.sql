-- Ajout du système de code promo
CREATE TABLE IF NOT EXISTS code_promo (
  code VARCHAR(50) PRIMARY KEY,
  reduction DECIMAL(5,2) NOT NULL,
  date_expiration DATE NOT NULL,
  nb_utilisations INT DEFAULT 0,
  max_utilisations INT DEFAULT 1
);

-- Quelques codes de test
INSERT INTO code_promo VALUES 
  ('BIENVENUE10', 10.00, '2026-12-31', 0, 1),
  ('ETE20', 20.00, '2026-12-31', 0, 1);
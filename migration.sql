USE gestion_parfumerie;

-- Clients
CREATE TABLE IF NOT EXISTS clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    telephone VARCHAR(20),
    email VARCHAR(150),
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notes TEXT
);

-- Fournisseurs
CREATE TABLE IF NOT EXISTS fournisseurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    contact VARCHAR(100),
    telephone VARCHAR(20),
    email VARCHAR(150),
    adresse TEXT,
    notes TEXT
);

-- Dépenses
CREATE TABLE IF NOT EXISTS depenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(150) NOT NULL,
    categorie VARCHAR(50) NOT NULL,
    montant DECIMAL(10,2) NOT NULL,
    date_depense DATE NOT NULL,
    mode_paiement VARCHAR(50),
    fournisseur_id INT NULL,
    notes TEXT,
    fichier_joint VARCHAR(255),
    FOREIGN KEY (fournisseur_id) REFERENCES fournisseurs(id) ON DELETE SET NULL
);

-- Factures
CREATE TABLE IF NOT EXISTS factures (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero VARCHAR(20) NOT NULL UNIQUE,
    client_id INT NULL,
    date_facture TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10,2) NOT NULL,
    remise DECIMAL(10,2) DEFAULT 0,
    mode_paiement VARCHAR(50),
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE SET NULL
);

-- Update parfums (safe alter)
SET @dbname = 'gestion_parfumerie';
SET @tablename = 'parfums';

-- Add seuil_alerte
SELECT count(*) INTO @exists FROM information_schema.columns WHERE table_schema = @dbname AND table_name = @tablename AND column_name = 'seuil_alerte';
SET @sql = IF(@exists = 0, 'ALTER TABLE parfums ADD COLUMN seuil_alerte INT DEFAULT 5 AFTER stock', 'SELECT "Column exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Add fournisseur_id
SELECT count(*) INTO @exists FROM information_schema.columns WHERE table_schema = @dbname AND table_name = @tablename AND column_name = 'fournisseur_id';
SET @sql = IF(@exists = 0, 'ALTER TABLE parfums ADD COLUMN fournisseur_id INT NULL AFTER seuil_alerte', 'SELECT "Column exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Add code_barres
SELECT count(*) INTO @exists FROM information_schema.columns WHERE table_schema = @dbname AND table_name = @tablename AND column_name = 'code_barres';
SET @sql = IF(@exists = 0, 'ALTER TABLE parfums ADD COLUMN code_barres VARCHAR(50) NULL AFTER fournisseur_id', 'SELECT "Column exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Add description
SELECT count(*) INTO @exists FROM information_schema.columns WHERE table_schema = @dbname AND table_name = @tablename AND column_name = 'description';
SET @sql = IF(@exists = 0, 'ALTER TABLE parfums ADD COLUMN description TEXT NULL AFTER code_barres', 'SELECT "Column exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Add image
SELECT count(*) INTO @exists FROM information_schema.columns WHERE table_schema = @dbname AND table_name = @tablename AND column_name = 'image';
SET @sql = IF(@exists = 0, 'ALTER TABLE parfums ADD COLUMN image VARCHAR(255) NULL AFTER description', 'SELECT "Column exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Add date_modification
SELECT count(*) INTO @exists FROM information_schema.columns WHERE table_schema = @dbname AND table_name = @tablename AND column_name = 'date_modification';
SET @sql = IF(@exists = 0, 'ALTER TABLE parfums ADD COLUMN date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER date_ajout', 'SELECT "Column exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Foreign key for fournisseur_id
SELECT count(*) INTO @exists FROM information_schema.TABLE_CONSTRAINTS WHERE table_schema = @dbname AND table_name = 'parfums' AND constraint_name = 'fk_parfum_fournisseur';
SET @sql = IF(@exists = 0, 'ALTER TABLE parfums ADD CONSTRAINT fk_parfum_fournisseur FOREIGN KEY (fournisseur_id) REFERENCES fournisseurs(id) ON DELETE SET NULL', 'SELECT "FK exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Update ventes (safe alter)
SET @tablename = 'ventes';
SELECT count(*) INTO @exists FROM information_schema.columns WHERE table_schema = @dbname AND table_name = @tablename AND column_name = 'facture_id';
SET @sql = IF(@exists = 0, 'ALTER TABLE ventes ADD COLUMN facture_id INT NULL AFTER id, ADD COLUMN client_id INT NULL AFTER facture_id', 'SELECT "Columns exist"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SELECT count(*) INTO @exists FROM information_schema.TABLE_CONSTRAINTS WHERE table_schema = @dbname AND table_name = 'ventes' AND constraint_name = 'fk_vente_facture';
SET @sql = IF(@exists = 0, 'ALTER TABLE ventes ADD CONSTRAINT fk_vente_facture FOREIGN KEY (facture_id) REFERENCES factures(id) ON DELETE CASCADE', 'SELECT "FK exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SELECT count(*) INTO @exists FROM information_schema.TABLE_CONSTRAINTS WHERE table_schema = @dbname AND table_name = 'ventes' AND constraint_name = 'fk_vente_client';
SET @sql = IF(@exists = 0, 'ALTER TABLE ventes ADD CONSTRAINT fk_vente_client FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE SET NULL', 'SELECT "FK exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

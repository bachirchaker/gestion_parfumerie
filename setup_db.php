<?php
require_once 'config.php';

$sql = "
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
";

try {
    $pdo->exec($sql);
    echo "Tables created successfully.\n";
    
    // Add columns manually catching errors if they already exist
    $columns_parfums = [
        "ALTER TABLE parfums ADD COLUMN seuil_alerte INT DEFAULT 5 AFTER stock",
        "ALTER TABLE parfums ADD COLUMN fournisseur_id INT NULL AFTER seuil_alerte",
        "ALTER TABLE parfums ADD COLUMN code_barres VARCHAR(50) NULL AFTER fournisseur_id",
        "ALTER TABLE parfums ADD COLUMN description TEXT NULL AFTER code_barres",
        "ALTER TABLE parfums ADD COLUMN image VARCHAR(255) NULL AFTER description",
        "ALTER TABLE parfums ADD COLUMN date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER date_ajout"
    ];
    
    foreach ($columns_parfums as $q) {
        try { $pdo->exec($q); } catch(PDOException $e) {}
    }
    
    // Add constraints
    try { $pdo->exec("ALTER TABLE parfums ADD CONSTRAINT fk_parfum_fournisseur FOREIGN KEY (fournisseur_id) REFERENCES fournisseurs(id) ON DELETE SET NULL"); } catch(PDOException $e) {}
    
    // Update ventes
    try { $pdo->exec("ALTER TABLE ventes ADD COLUMN facture_id INT NULL AFTER id"); } catch(PDOException $e) {}
    try { $pdo->exec("ALTER TABLE ventes ADD COLUMN client_id INT NULL AFTER facture_id"); } catch(PDOException $e) {}
    try { $pdo->exec("ALTER TABLE ventes ADD CONSTRAINT fk_vente_facture FOREIGN KEY (facture_id) REFERENCES factures(id) ON DELETE CASCADE"); } catch(PDOException $e) {}
    try { $pdo->exec("ALTER TABLE ventes ADD CONSTRAINT fk_vente_client FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE SET NULL"); } catch(PDOException $e) {}

    echo "Migration complete.\n";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

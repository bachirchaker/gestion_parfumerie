CREATE DATABASE IF NOT EXISTS gestion_parfumerie;
USE gestion_parfumerie;

CREATE TABLE IF NOT EXISTS parfums (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    marque VARCHAR(100) NOT NULL,
    categorie ENUM('Homme', 'Femme', 'Mixte') NOT NULL,
    contenance INT NOT NULL COMMENT 'en ml',
    prix_achat DECIMAL(10,2) NOT NULL,
    prix_vente DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS ventes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    parfum_id INT NOT NULL,
    quantite INT NOT NULL,
    prix_total DECIMAL(10,2) NOT NULL,
    date_vente TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parfum_id) REFERENCES parfums(id) ON DELETE CASCADE
);

-- Données de test
INSERT INTO parfums (nom, marque, categorie, contenance, prix_achat, prix_vente, stock) VALUES
('Bleu de Chanel', 'Chanel', 'Homme', 100, 70.00, 110.00, 15),
('J''adore', 'Dior', 'Femme', 50, 65.00, 95.00, 8),
('CK One', 'Calvin Klein', 'Mixte', 200, 30.00, 55.00, 20);

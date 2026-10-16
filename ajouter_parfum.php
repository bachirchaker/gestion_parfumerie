<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $marque = $_POST['marque'];
    $categorie = $_POST['categorie'];
    $contenance = $_POST['contenance'];
    $prix_achat = $_POST['prix_achat'];
    $prix_vente = $_POST['prix_vente'];
    $stock = $_POST['stock'];

    $stmt = $pdo->prepare("INSERT INTO parfums (nom, marque, categorie, contenance, prix_achat, prix_vente, stock) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$nom, $marque, $categorie, $contenance, $prix_achat, $prix_vente, $stock])) {
        header('Location: parfums.php');
        exit;
    } else {
        $erreur = "Erreur lors de l'ajout.";
    }
}

require_once 'includes/header.php';
?>

<div class="page-title">
    <h2><i class="fa-solid fa-plus-circle"></i> Ajouter un Nouveau Parfum</h2>
    <a href="parfums.php" class="btn btn-primary" style="background-color: #5e6278;"><i class="fa-solid fa-arrow-left"></i> Retour au stock</a>
</div>

<?php if(isset($erreur)) echo "<div class='alert alert-warning'><i class='fa-solid fa-triangle-exclamation'></i> $erreur</div>"; ?>

<div class="form-container">
    <form method="POST">
        <div class="form-group">
            <label>Marque</label>
            <input type="text" name="marque" placeholder="Ex: Chanel" required>
        </div>
        <div class="form-group">
            <label>Nom du parfum</label>
            <input type="text" name="nom" placeholder="Ex: Bleu de Chanel" required>
        </div>
        <div class="form-group">
            <label>Catégorie</label>
            <select name="categorie" required>
                <option value="Femme">Femme</option>
                <option value="Homme">Homme</option>
                <option value="Mixte">Mixte</option>
            </select>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label>Contenance (ml)</label>
                <input type="number" name="contenance" placeholder="Ex: 100" required>
            </div>
            <div class="form-group">
                <label>Stock initial</label>
                <input type="number" name="stock" placeholder="Ex: 20" required>
            </div>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label>Prix d'achat (€)</label>
                <input type="number" step="0.01" name="prix_achat" placeholder="0.00" required>
            </div>
            <div class="form-group">
                <label>Prix de vente (€)</label>
                <input type="number" step="0.01" name="prix_vente" placeholder="0.00" required>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; margin-top: 10px;">
            <i class="fa-solid fa-save"></i> Enregistrer le parfum
        </button>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>

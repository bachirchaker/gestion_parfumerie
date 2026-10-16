<?php
require_once 'config.php';

$succes = ''; $erreur = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $libelle = $_POST['libelle'];
    $categorie = $_POST['categorie'];
    $montant = $_POST['montant'];
    $date_depense = $_POST['date_depense'];
    $mode_paiement = $_POST['mode_paiement'];

    $stmt = $pdo->prepare("INSERT INTO depenses (libelle, categorie, montant, date_depense, mode_paiement) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$libelle, $categorie, $montant, $date_depense, $mode_paiement])) {
        $succes = "Dépense enregistrée avec succès.";
    } else {
        $erreur = "Erreur lors de l'enregistrement de la dépense.";
    }
}

require_once 'includes/header.php';
?>

<div class="page-title">
    <h2>Gestion des Dépenses</h2>
</div>

<?php if($succes) echo "<div class='alert alert-success'><i class='fa-solid fa-circle-check'></i> <div>$succes</div></div>"; ?>
<?php if($erreur) echo "<div class='alert alert-warning'><i class='fa-solid fa-triangle-exclamation'></i> <div>$erreur</div></div>"; ?>

<div class="pos-layout">
    <!-- Formulaire d'ajout -->
    <div class="form-container" style="max-width:100%; box-shadow:var(--shadow-sm);">
        <h3 style="margin-bottom:25px; border-bottom:1px solid var(--border); padding-bottom:15px;">Nouvelle Dépense</h3>
        <form method="POST">
            <div class="form-group">
                <label>Libellé</label>
                <input type="text" name="libelle" placeholder="Ex: Loyer, Électricité..." required>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                <div class="form-group">
                    <label>Catégorie</label>
                    <select name="categorie" required>
                        <option value="Loyer">Loyer</option>
                        <option value="Utilités">Électricité & Eau</option>
                        <option value="Marketing">Publicité & Marketing</option>
                        <option value="Salaires">Salaires</option>
                        <option value="Transport">Transport & Livraison</option>
                        <option value="Entretien">Entretien</option>
                        <option value="Autre">Autre</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Montant (€)</label>
                    <input type="number" step="0.01" name="montant" required>
                </div>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="date_depense" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="form-group">
                    <label>Mode de paiement</label>
                    <select name="mode_paiement" required>
                        <option value="Virement">Virement bancaire</option>
                        <option value="Carte">Carte bancaire</option>
                        <option value="Espèces">Espèces</option>
                        <option value="Chèque">Chèque</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="margin-top:10px;"><i class="fa-solid fa-check"></i> Enregistrer</button>
        </form>
    </div>

    <!-- Tableau -->
    <div class="data-table-container" style="margin-bottom:0;">
        <h3 style="margin-bottom:25px; border-bottom:1px solid var(--border); padding-bottom:15px;">Dernières Dépenses</h3>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Libellé</th>
                    <th>Catégorie</th>
                    <th>Montant</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->query("SELECT * FROM depenses ORDER BY date_depense DESC, id DESC LIMIT 10");
                while ($row = $stmt->fetch()):
                ?>
                <tr>
                    <td style="font-size:0.85rem;"><?php echo date('d/m/Y', strtotime($row['date_depense'])); ?></td>
                    <td><strong><?php echo htmlspecialchars($row['libelle']); ?></strong></td>
                    <td><span class="badge badge-neutral"><?php echo htmlspecialchars($row['categorie']); ?></span></td>
                    <td style="color:#d9534f; font-weight:600;"><?php echo number_format($row['montant'], 2, ',', ' '); ?> €</td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

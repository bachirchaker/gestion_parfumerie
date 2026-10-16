<?php
require_once 'config.php';

$succes = ''; $erreur = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $parfum_id = $_POST['parfum_id'];
    $quantite = $_POST['quantite'];
    $client_id = !empty($_POST['client_id']) ? $_POST['client_id'] : null;

    $stmt = $pdo->prepare("SELECT prix_vente, stock FROM parfums WHERE id = ?");
    $stmt->execute([$parfum_id]);
    $parfum = $stmt->fetch();

    if ($parfum && $parfum['stock'] >= $quantite) {
        $prix_total = $parfum['prix_vente'] * $quantite;

        $pdo->beginTransaction();
        try {
            // Créer une facture
            $numero_facture = 'INV-' . date('Ymd') . '-' . rand(1000, 9999);
            $stmt_fac = $pdo->prepare("INSERT INTO factures (numero, client_id, total, mode_paiement) VALUES (?, ?, ?, 'Carte Bancaire')");
            $stmt_fac->execute([$numero_facture, $client_id, $prix_total]);
            $facture_id = $pdo->lastInsertId();

            // Enregistrer la vente liée à la facture
            $stmt_vente = $pdo->prepare("INSERT INTO ventes (parfum_id, quantite, prix_total, facture_id, client_id) VALUES (?, ?, ?, ?, ?)");
            $stmt_vente->execute([$parfum_id, $quantite, $prix_total, $facture_id, $client_id]);

            // Mettre à jour le stock
            $stmt_stock = $pdo->prepare("UPDATE parfums SET stock = stock - ? WHERE id = ?");
            $stmt_stock->execute([$quantite, $parfum_id]);

            $pdo->commit();
            $succes = "Transaction validée. Facture <strong>$numero_facture</strong> générée (Total: " . number_format($prix_total, 2, ',', ' ') . " €).";
        } catch (Exception $e) {
            $pdo->rollBack();
            $erreur = "Erreur de système : " . $e->getMessage();
        }
    } else {
        $erreur = "Transaction refusée : Stock insuffisant.";
    }
}

// Data pour les selects
$stmt_parfums = $pdo->query("SELECT id, marque, nom, contenance, prix_vente, stock FROM parfums WHERE stock > 0 ORDER BY marque, nom");
$parfums = $stmt_parfums->fetchAll();

$stmt_clients = $pdo->query("SELECT id, nom, telephone FROM clients ORDER BY nom");
$clients = $stmt_clients->fetchAll();

require_once 'includes/header.php';
?>

<div class="page-title">
    <h2>Point de Vente (POS)</h2>
    <div style="font-family:var(--font-serif); font-size:1.2rem; color:var(--gold);">Terminal Paris</div>
</div>

<?php if($succes) echo "<div class='alert alert-success'><i class='fa-solid fa-circle-check'></i> <div>$succes</div></div>"; ?>
<?php if($erreur) echo "<div class='alert alert-warning'><i class='fa-solid fa-triangle-exclamation'></i> <div>$erreur</div></div>"; ?>

<div class="pos-layout">
    <!-- Colonne de gauche: Sélection -->
    <div class="pos-products">
        <h3 style="margin-bottom:25px; border-bottom:1px solid var(--border); padding-bottom:15px;">Nouvelle Transaction</h3>
        
        <form method="POST" id="posForm">
            <div class="form-group">
                <label>Client (Optionnel)</label>
                <select name="client_id">
                    <option value="">-- Client de passage --</option>
                    <?php foreach($clients as $c): ?>
                        <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['nom']); ?> (<?php echo htmlspecialchars($c['telephone']); ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="margin-top: 30px;">
                <label>Produit à facturer</label>
                <select name="parfum_id" required id="productSelect">
                    <option value="" data-price="0">-- Sélectionnez un produit --</option>
                    <?php foreach($parfums as $p): ?>
                        <option value="<?php echo $p['id']; ?>" data-price="<?php echo $p['prix_vente']; ?>">
                            <?php echo htmlspecialchars($p['marque'] . ' - ' . $p['nom']); ?> 
                            (<?php echo $p['contenance']; ?>ml) | En stock: <?php echo $p['stock']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Quantité</label>
                <input type="number" name="quantite" id="qtyInput" value="1" min="1" required>
            </div>
        </form>
    </div>

    <!-- Colonne de droite: Panier/Checkout -->
    <div class="pos-cart" style="background:var(--forest); color:var(--ivory);">
        <h3 style="color:var(--champagne); margin-bottom:25px; border-bottom:1px solid rgba(185,154,98,0.2); padding-bottom:15px;">Résumé</h3>
        
        <div style="margin-bottom: 40px;">
            <div style="display:flex; justify-content:space-between; margin-bottom:15px; color:var(--muted);">
                <span>Sous-total</span>
                <span id="subtotalDisplay">0.00 €</span>
            </div>
            <div style="display:flex; justify-content:space-between; margin-bottom:15px; color:var(--muted);">
                <span>TVA (20%)</span>
                <span>Inclus</span>
            </div>
            <div style="display:flex; justify-content:space-between; margin-top:25px; padding-top:20px; border-top:1px solid rgba(255,255,255,0.1);">
                <span style="font-family:var(--font-serif); font-size:1.5rem;">Total</span>
                <span style="font-family:var(--font-serif); font-size:2rem; color:var(--champagne); font-weight:700;" id="totalDisplay">0.00 €</span>
            </div>
        </div>

        <button type="button" class="btn btn-accent" style="width:100%; padding:20px; font-size:1.1rem;" onclick="document.getElementById('posForm').submit();">
            <i class="fa-solid fa-lock"></i> Valider & Encaisser
        </button>
    </div>
</div>

<script>
const select = document.getElementById('productSelect');
const qty = document.getElementById('qtyInput');
const subDisplay = document.getElementById('subtotalDisplay');
const totDisplay = document.getElementById('totalDisplay');

function updateTotals() {
    const price = parseFloat(select.options[select.selectedIndex].getAttribute('data-price') || 0);
    const quantity = parseInt(qty.value || 1);
    const total = price * quantity;
    const formatted = total.toFixed(2) + ' €';
    subDisplay.innerText = formatted;
    totDisplay.innerText = formatted;
}

select.addEventListener('change', updateTotals);
qty.addEventListener('input', updateTotals);
</script>

<?php require_once 'includes/footer.php'; ?>

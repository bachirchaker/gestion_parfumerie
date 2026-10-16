<?php
require_once 'config.php';
require_once 'includes/header.php';
?>

<div class="page-title">
    <h2>Catalogue des Parfums</h2>
    <a href="ajouter_parfum.php" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Ajouter un Produit</a>
</div>

<div class="data-table-container">
    <div style="display:flex; justify-content:space-between; margin-bottom:20px;">
        <h3>Inventaire</h3>
        <div style="width: 300px;">
            <input type="text" placeholder="Rechercher (Nom, Marque, Code)..." style="padding: 10px 15px; border-radius: 30px;">
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Marque & Nom</th>
                <th>Catégorie</th>
                <th>Volume</th>
                <th>Prix (Achat/Vente)</th>
                <th>Stock</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $pdo->query("SELECT * FROM parfums ORDER BY marque, nom");
            while ($row = $stmt->fetch()):
            ?>
            <tr>
                <td>
                    <div class="td-strong"><?php echo htmlspecialchars($row['marque']); ?></div>
                    <div style="color:var(--muted); font-size:0.85rem;"><?php echo htmlspecialchars($row['nom']); ?></div>
                    <?php if($row['code_barres']): ?><div style="font-size:0.7rem; color:#b5b5b5;"><i class="fa-solid fa-barcode"></i> <?php echo htmlspecialchars($row['code_barres']); ?></div><?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($row['categorie']); ?></td>
                <td><?php echo $row['contenance']; ?> ml</td>
                <td>
                    <div style="font-size:0.85rem; color:var(--muted);">A: <?php echo number_format($row['prix_achat'], 2); ?> €</div>
                    <div style="font-weight:600; color:var(--forest);">V: <?php echo number_format($row['prix_vente'], 2); ?> €</div>
                </td>
                <td>
                    <span style="font-family:var(--font-serif); font-size:1.2rem; font-weight:700; color:var(--forest);">
                        <?php echo $row['stock']; ?>
                    </span>
                </td>
                <td>
                    <?php if($row['stock'] <= 0): ?>
                        <span class="badge badge-danger">Rupture</span>
                    <?php elseif($row['stock'] <= $row['seuil_alerte']): ?>
                        <span class="badge badge-gold">Stock Bas</span>
                    <?php else: ?>
                        <span class="badge badge-emerald">En Stock</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="#" class="icon-btn" style="margin-right:10px; color:var(--gold);"><i class="fa-solid fa-pen"></i></a>
                    <a href="supprimer_parfum.php?id=<?php echo $row['id']; ?>" class="icon-btn" style="color:#d9534f;" onclick="return confirm('Archiver ce produit ?');"><i class="fa-solid fa-trash"></i></a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>

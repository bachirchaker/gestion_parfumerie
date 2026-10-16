<?php
require_once 'config.php';
require_once 'includes/header.php';
?>
<div class="page-title">
    <h2><i class="fa-solid fa-receipt"></i> Historique des Transactions</h2>
    <a href="nouvelle_vente.php" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Nouvelle Vente</a>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>N° de Facture</th>
                <th>Date et Heure</th>
                <th>Produit</th>
                <th>Qté</th>
                <th>Total Facturé</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $pdo->query("
                SELECT v.*, p.nom, p.marque 
                FROM ventes v 
                JOIN parfums p ON v.parfum_id = p.id 
                ORDER BY v.date_vente DESC
            ");
            while ($row = $stmt->fetch()):
            ?>
            <tr>
                <td><strong style="color: #a1a5b7;">#<?php echo str_pad($row['id'], 5, '0', STR_PAD_LEFT); ?></strong></td>
                <td><?php echo date('d/m/Y - H:i', strtotime($row['date_vente'])); ?></td>
                <td><strong style="color: var(--primary-color);"><?php echo htmlspecialchars($row['marque']); ?></strong> - <?php echo htmlspecialchars($row['nom']); ?></td>
                <td>
                    <span style="background: #f5f8fa; padding: 4px 10px; border-radius: 6px; font-weight: 500;">
                        <?php echo $row['quantite']; ?>
                    </span>
                </td>
                <td><strong style="color: var(--accent-color); font-size: 1.05rem;"><?php echo number_format($row['prix_total'], 2, ',', ' '); ?> €</strong></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>

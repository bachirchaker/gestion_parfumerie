<?php
require_once 'config.php';

// Statistiques
$stmt = $pdo->query("SELECT SUM(prix_total) as ca_jour, COUNT(*) as ventes_jour FROM ventes WHERE DATE(date_vente) = CURDATE()");
$ca_stats_jour = $stmt->fetch();
$ca_jour = $ca_stats_jour['ca_jour'] ?? 0;
$ventes_jour = $ca_stats_jour['ventes_jour'] ?? 0;

$stmt = $pdo->query("SELECT SUM(prix_total) as ca_mois FROM ventes WHERE MONTH(date_vente) = MONTH(CURDATE()) AND YEAR(date_vente) = YEAR(CURDATE())");
$ca_mois = $stmt->fetch()['ca_mois'] ?? 0;

$stmt = $pdo->query("SELECT SUM(montant) as depenses_mois FROM depenses WHERE MONTH(date_depense) = MONTH(CURDATE()) AND YEAR(date_depense) = YEAR(CURDATE())");
$depenses_mois = $stmt->fetch()['depenses_mois'] ?? 0;

// Bénéfice brut estimé
$stmt = $pdo->query("
    SELECT SUM(v.quantite * p.prix_achat) as cout_achat_mois 
    FROM ventes v 
    JOIN parfums p ON v.parfum_id = p.id 
    WHERE MONTH(v.date_vente) = MONTH(CURDATE()) AND YEAR(v.date_vente) = YEAR(CURDATE())
");
$cout_achat_mois = $stmt->fetch()['cout_achat_mois'] ?? 0;
$benefice_mois = $ca_mois - $depenses_mois - $cout_achat_mois;

// Stock total
$stmt = $pdo->query("SELECT SUM(stock) as total_stock, COUNT(*) as references_count FROM parfums");
$stock_data = $stmt->fetch();
$total_stock = $stock_data['total_stock'] ?? 0;
$references_count = $stock_data['references_count'] ?? 0;

// Alertes
$stmt = $pdo->query("SELECT * FROM parfums WHERE stock <= seuil_alerte LIMIT 5");
$ruptures = $stmt->fetchAll();

// Graphique 7 jours
$chart_labels_7d = [];
$chart_data_7d = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $chart_labels_7d[] = date('d/M', strtotime($date));
    $stmt = $pdo->prepare("SELECT SUM(prix_total) as ca FROM ventes WHERE DATE(date_vente) = ?");
    $stmt->execute([$date]);
    $chart_data_7d[] = $stmt->fetch()['ca'] ?? 0;
}

// Graphique Top Ventes
$stmt = $pdo->query("
    SELECT p.nom, SUM(v.quantite) as total_vendus 
    FROM ventes v 
    JOIN parfums p ON v.parfum_id = p.id 
    GROUP BY p.id 
    ORDER BY total_vendus DESC 
    LIMIT 4
");
$top_ventes = $stmt->fetchAll();
$chart_labels_top = [];
$chart_data_top = [];
foreach($top_ventes as $tv) {
    $chart_labels_top[] = $tv['nom'];
    $chart_data_top[] = $tv['total_vendus'];
}

require_once 'includes/header.php';
?>

<!-- HERO SECTION -->
<div class="hero-section">
    <div class="hero-greeting">
        <h1>Bonjour, Director.</h1>
        <p>Voici l'aperçu de la Maison pour le <?php echo date('d F Y'); ?></p>
    </div>
    <div class="hero-actions">
        <a href="nouvelle_vente.php" class="btn btn-primary"><i class="fa-solid fa-plus"></i> New Sale</a>
        <a href="ajouter_parfum.php" class="btn btn-outline"><i class="fa-solid fa-spray-can"></i> Add Product</a>
    </div>
</div>

<?php if (count($ruptures) > 0): ?>
<div class="alert alert-warning stagger-1">
    <i class="fa-solid fa-triangle-exclamation"></i>
    <div>
        <strong style="font-family:var(--font-serif); font-size:1.1rem;">Inventory Alert</strong><br>
        <?php echo count($ruptures); ?> products require immediate restocking.
    </div>
</div>
<?php endif; ?>

<!-- PREMIUM KPI CARDS -->
<div class="kpi-grid">
    <div class="kpi-card stagger-1">
        <div class="kpi-title">Today's Revenue</div>
        <div class="kpi-value"><span class="animate-counter" data-target="<?php echo floor($ca_jour); ?>">0</span> €</div>
        <div style="position:absolute; right:30px; top:35px; color:var(--gold); font-size:1.5rem;"><i class="fa-solid fa-sack-dollar"></i></div>
    </div>
    
    <div class="kpi-card stagger-2">
        <div class="kpi-title">Monthly Revenue</div>
        <div class="kpi-value"><span class="animate-counter" data-target="<?php echo floor($ca_mois); ?>">0</span> €</div>
        <div style="position:absolute; right:30px; top:35px; color:var(--emerald); font-size:1.5rem;"><i class="fa-solid fa-vault"></i></div>
    </div>
    
    <div class="kpi-card stagger-3">
        <div class="kpi-title">Est. Profit (Month)</div>
        <div class="kpi-value"><span class="animate-counter" data-target="<?php echo floor($benefice_mois); ?>">0</span> €</div>
        <div style="position:absolute; right:30px; top:35px; color:var(--muted); font-size:1.5rem;"><i class="fa-solid fa-chart-line"></i></div>
    </div>
    
    <div class="kpi-card stagger-4">
        <div class="kpi-title">Total Inventory</div>
        <div class="kpi-value"><span class="animate-counter" data-target="<?php echo $total_stock; ?>">0</span></div>
        <div style="position:absolute; right:30px; top:35px; color:var(--charcoal); font-size:1.5rem;"><i class="fa-solid fa-boxes-stacked"></i></div>
    </div>
</div>

<!-- CHARTS -->
<div class="charts-grid">
    <div class="chart-card stagger-2">
        <div class="chart-header">
            <h3>Revenue Overview</h3>
            <span class="badge badge-gold">Last 7 Days</span>
        </div>
        <canvas id="revenueChart" height="110"></canvas>
    </div>
    
    <div class="chart-card stagger-3">
        <div class="chart-header">
            <h3>Top Fragrances</h3>
        </div>
        <canvas id="topProductsChart" height="230"></canvas>
    </div>
</div>

<!-- RECENT TRANSACTIONS -->
<div class="data-table-container stagger-4">
    <h3>Recent Transactions</h3>
    <table>
        <thead>
            <tr>
                <th>Invoice</th>
                <th>Date & Time</th>
                <th>Product Details</th>
                <th>Qty</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $pdo->query("SELECT v.*, p.nom, p.marque FROM ventes v JOIN parfums p ON v.parfum_id = p.id ORDER BY v.date_vente DESC LIMIT 5");
            while ($row = $stmt->fetch()):
            ?>
            <tr>
                <td style="color:var(--muted);">#<?php echo str_pad($row['id'], 5, '0', STR_PAD_LEFT); ?></td>
                <td><?php echo date('d M Y - H:i', strtotime($row['date_vente'])); ?></td>
                <td>
                    <div class="td-strong"><?php echo htmlspecialchars($row['marque']); ?></div>
                    <div style="font-size:0.85rem; color:var(--muted);"><?php echo htmlspecialchars($row['nom']); ?></div>
                </td>
                <td><span class="badge badge-emerald"><?php echo $row['quantite']; ?></span></td>
                <td style="color:var(--gold); font-weight:600; font-size:1.1rem;"><?php echo number_format($row['prix_total'], 2, ',', ' '); ?> €</td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
// Line Chart: Revenue
const ctxRev = document.getElementById('revenueChart').getContext('2d');

// Create gradient
let gradient = ctxRev.createLinearGradient(0, 0, 0, 400);
gradient.addColorStop(0, 'rgba(185, 154, 98, 0.4)');
gradient.addColorStop(1, 'rgba(248, 245, 239, 0)');

new Chart(ctxRev, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($chart_labels_7d); ?>,
        datasets: [{
            label: 'Revenue (€)',
            data: <?php echo json_encode($chart_data_7d); ?>,
            borderColor: '#B99A62',
            backgroundColor: gradient,
            borderWidth: 3,
            pointBackgroundColor: '#10231C',
            pointBorderColor: '#B99A62',
            pointBorderWidth: 2,
            pointRadius: 5,
            pointHoverRadius: 7,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#10231C',
                titleFont: { family: 'DM Sans', size: 13 },
                bodyFont: { family: 'Cormorant Garamond', size: 16, weight: 'bold' },
                padding: 15,
                displayColors: false,
                callbacks: { label: function(context) { return context.parsed.y + ' €'; } }
            }
        },
        scales: {
            y: { beginAtZero: true, grid: { color: 'rgba(16, 35, 28, 0.05)' }, border: { display: false } },
            x: { grid: { display: false }, border: { display: false } }
        },
        animation: { duration: 2000, easing: 'easeOutQuart' }
    }
});

// Bar Chart: Top Products
const ctxTop = document.getElementById('topProductsChart').getContext('2d');
new Chart(ctxTop, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($chart_labels_top); ?>,
        datasets: [{
            label: 'Units Sold',
            data: <?php echo json_encode($chart_data_top); ?>,
            backgroundColor: '#1C3B2F',
            hoverBackgroundColor: '#B99A62',
            borderRadius: 4,
            barThickness: 25
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false }, border: { display: false } },
            y: { grid: { display: false }, border: { display: false }, ticks: { font: { family: 'DM Sans' } } }
        },
        animation: { duration: 1500, easing: 'easeOutBounce' }
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>

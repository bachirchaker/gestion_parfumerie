<?php
// Function to get current page for active state in nav
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maison Élégance | Premium Management</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="wrapper">
        <!-- LUXURY SIDEBAR -->
        <aside class="sidebar">
            <div class="sidebar-brand">
                <h2>ÉLÉGANCE</h2>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-group">
                    <h3>Overview</h3>
                    <a href="index.php" class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-border-all"></i> Dashboard
                    </a>
                    <a href="rapports.php" class="<?php echo $current_page == 'rapports.php' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-chart-line"></i> Analytics
                    </a>
                </div>

                <div class="nav-group">
                    <h3>Store</h3>
                    <a href="nouvelle_vente.php" class="<?php echo $current_page == 'nouvelle_vente.php' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-bag-shopping"></i> Point of Sale
                    </a>
                    <a href="ventes.php" class="<?php echo $current_page == 'ventes.php' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-receipt"></i> Sales History
                    </a>
                    <a href="parfums.php" class="<?php echo $current_page == 'parfums.php' || $current_page == 'ajouter_parfum.php' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-spray-can-sparkles"></i> Perfume Inventory
                    </a>
                </div>

                <div class="nav-group">
                    <h3>Management</h3>
                    <a href="clients.php" class="<?php echo $current_page == 'clients.php' ? 'active' : ''; ?>">
                        <i class="fa-regular fa-user"></i> Customers
                    </a>
                    <a href="fournisseurs.php" class="<?php echo $current_page == 'fournisseurs.php' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-boxes-packing"></i> Suppliers
                    </a>
                    <a href="depenses.php" class="<?php echo $current_page == 'depenses.php' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-credit-card"></i> Expenses
                    </a>
                </div>

                <div class="nav-group">
                    <h3>System</h3>
                    <a href="parametres.php" class="<?php echo $current_page == 'parametres.php' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-sliders"></i> Settings
                    </a>
                </div>
            </nav>
        </aside>

        <!-- MAIN LAYOUT -->
        <main class="main-content">
            <!-- TOP NAVIGATION -->
            <header class="top-header">
                <div class="breadcrumb">
                    Maison Élégance <i class="fa-solid fa-chevron-right" style="font-size:0.6rem; margin:0 10px;"></i> 
                    <span><?php echo ucfirst(str_replace('.php', '', str_replace('_', ' ', $current_page))); ?></span>
                </div>
                
                <div class="header-actions">
                    <i class="fa-solid fa-magnifying-glass icon-btn"></i>
                    <i class="fa-regular fa-bell icon-btn"></i>
                    <div class="user-profile">
                        <div style="text-align: right;">
                            <div style="font-weight: 600; font-size:0.9rem; color:var(--forest);">Director</div>
                            <div style="font-size:0.75rem; color:var(--muted);">Paris Boutique</div>
                        </div>
                        <div class="user-avatar">D</div>
                    </div>
                </div>
            </header>
            
            <!-- CONTENT AREA -->
            <div class="content-container">

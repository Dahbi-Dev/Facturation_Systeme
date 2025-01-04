<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';
include 'includes/navbar.php';

// Validate user session and get user ID
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$stmt = $con->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$user_id = $user['id'];

// Initialize filters
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01', strtotime('-1 year'));
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Query with date filtering
$query = "SELECT 
            DATE(d.date) as invoice_date,
            COUNT(DISTINCT d.data_id) as daily_invoices,
            SUM(CAST(od.qty AS DECIMAL(10,2)) * CAST(od.rate AS DECIMAL(10,2))) as daily_total,
            DATE_FORMAT(d.date, '%Y-%m') as month_group,
            GROUP_CONCAT(DISTINCT b.company_name) as clients
          FROM data d
          JOIN other_data od ON d.data_id = od.data_id
          LEFT JOIN buyer_details b ON d.client = b.buyer_id
          WHERE d.user_id = ? 
            AND d.date BETWEEN ? AND ?
          GROUP BY DATE(d.date), DATE_FORMAT(d.date, '%Y-%m')
          ORDER BY d.date ASC";

$stmt = $con->prepare($query);
$stmt->bind_param("iss", $user_id, $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();

// Initialize data arrays
$dates = [];
$daily_totals = [];
$invoice_counts = [];
$monthly_totals = [];
$monthly_labels = [];
$client_revenues = [];
$total_revenue = 0;
$total_invoices = 0;

// Process query results
while ($row = $result->fetch_assoc()) {
    $formatted_date = date('d/m/Y', strtotime($row['invoice_date']));
    $dates[] = $formatted_date;
    $daily_total = round(floatval($row['daily_total']), 2);
    $daily_totals[] = $daily_total;
    $invoice_counts[] = intval($row['daily_invoices']);
    
    $month_key = $row['month_group'];
    if (!isset($monthly_totals[$month_key])) {
        $monthly_totals[$month_key] = 0;
        $monthly_labels[] = date('M Y', strtotime($month_key . '-01'));
    }
    $monthly_totals[$month_key] += $daily_total;
    
    $clients = explode(',', $row['clients']);
    foreach ($clients as $client) {
        if (!isset($client_revenues[trim($client)])) {
            $client_revenues[trim($client)] = 0;
        }
        $client_revenues[trim($client)] += $daily_total;
    }
    
    $total_revenue += $daily_total;
    $total_invoices += $row['daily_invoices'];
}

// Calculate metrics
$average_invoice = $total_invoices > 0 ? $total_revenue / $total_invoices : 0;
$monthly_data = array_values($monthly_totals);

// Calculate growth metrics
if (count($monthly_data) >= 2) {
    $last_month = end($monthly_data);
    $previous_month = prev($monthly_data);
    $monthly_growth = $previous_month > 0 ? (($last_month - $previous_month) / $previous_month) * 100 : 0;
} else {
    $monthly_growth = 0;
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics - Tableau de Bord</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
    .chart-container {
        position: relative;
        height: 400px;
        width: 100%;
    }

    .metric-value {
        font-size: 1.8rem;
        font-weight: bold;
    }

    .growth-indicator {
        font-size: 0.9rem;
        padding: 2px 8px;
        border-radius: 4px;
    }

    .growth-positive {
        background-color: rgba(76, 175, 80, 0.1);
        color: #4CAF50;
    }

    .growth-negative {
        background-color: rgba(244, 67, 54, 0.1);
        color: #F44336;
    }
    </style>
</head>

<body>
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="app-page-title">
                <div class="page-title-wrapper">
                    <div class="page-title-heading">
                        <div class="page-title-icon">
                            <i class="pe-7s-graph text-success"></i>
                        </div>
                        <div>
                            Analyse des Performances
                            <div class="page-title-subheading">Aperçu détaillé des indicateurs financiers</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Date Filters -->
            <form method="GET" class="mb-4">
                <div class="row">
                    <div class="col-md-5">
                        <label for="start_date">Date de début:</label>
                        <input type="date" name="start_date" id="start_date" class="form-control"
                            value="<?= $start_date ?>">
                    </div>
                    <div class="col-md-5">
                        <label for="end_date">Date de fin:</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="<?= $end_date ?>">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Filtrer</button>
                    </div>
                </div>
            </form>

            <style>
            .widget-content {
                min-height: 140px;
                display: flex;
                align-items: center;
            }

            .widget-content-wrapper {
                width: 100%;
                padding: 1.25rem;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .widget-content-left {
                flex: 1;
            }

            .widget-content-right {
                text-align: right;
            }

            .widget-numbers {
                font-size: 1.8rem;
                font-weight: bold;
                margin-bottom: 0.5rem;
            }

            .widget-heading {
                font-size: 1.1rem;
                font-weight: 500;
                margin-bottom: 0.25rem;
            }

            .widget-subheading {
                opacity: 0.8;
                font-size: 0.9rem;
            }
            </style>

            <div class="row">
                <!-- Total Revenue -->
                <div class="col-md-4">
                    <div class="card mb-3 widget-content bg-midnight-bloom">
                        <div class="widget-content-wrapper text-white">
                            <div class="widget-content-left">
                                <div class="widget-heading">Chiffre d'Affaires</div>
                                <div class="widget-subheading">12 derniers mois</div>
                            </div>
                            <div class="widget-content-right">
                                <div class="widget-numbers">
                                    <?= number_format($total_revenue, 2) ?> DH
                                </div>
                                <div class="widget-subheading">
                                    <span
                                        class="growth-indicator <?= $monthly_growth >= 0 ? 'growth-positive' : 'growth-negative' ?>">
                                        <?= ($monthly_growth >= 0 ? '+' : '') . number_format($monthly_growth, 1) ?>%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Total Invoices -->
                <div class="col-md-4">
                    <div class="card mb-3 widget-content bg-arielle-smile">
                        <div class="widget-content-wrapper text-white">
                            <div class="widget-content-left">
                                <div class="widget-heading">Total Factures</div>
                                <div class="widget-subheading">Nombre de transactions</div>
                            </div>
                            <div class="widget-content-right">
                                <div class="widget-numbers"><?= number_format($total_invoices) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Average Invoice Value -->
                <div class="col-md-4">
                    <div class="card mb-3 widget-content bg-grow-early">
                        <div class="widget-content-wrapper text-white">
                            <div class="widget-content-left">
                                <div class="widget-heading">Valeur Moyenne</div>
                                <div class="widget-subheading">Par facture</div>
                            </div>
                            <div class="widget-content-right">
                                <div class="widget-numbers"><?= number_format($average_invoice, 2) ?> DH</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Charts -->
            <div class="row">
                <div class="col-md-8">
                    <div class="main-card mb-3 card">
                        <div class="card-header">
                            Évolution du Revenu
                            <div class="btn-actions-pane-right">
                                <div role="group" class="btn-group-sm btn-group">
                                    <button class="active btn btn-focus"
                                        onclick="toggleChartView('daily')">Quotidien</button>
                                    <button class="btn btn-focus" onclick="toggleChartView('monthly')">Mensuel</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="revenueChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="main-card mb-3 card">
                        <div class="card-header">Revenus par Client</div>
                        <div class="card-body">
                            <canvas id="clientsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js Logic -->
    <script>
    const dailyData = {
        labels: <?= json_encode($dates) ?>,
        datasets: [{
            label: 'Chiffre d\'Affaires Quotidien (DH)',
            backgroundColor: '#f46a6a',
            borderColor: '#f46a6a',
            data: <?= json_encode($daily_totals) ?>,
        }, ],
    };

    const monthlyData = {
        labels: <?= json_encode($monthly_labels) ?>,
        datasets: [{
            label: 'Chiffre d\'Affaires Mensuel (DH)',
            backgroundColor: '#36a2eb',
            borderColor: '#36a2eb',
            data: <?= json_encode(array_values($monthly_totals)) ?>,
        }, ],
    };

    const revenueChartConfig = {
        type: 'bar',
        data: dailyData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top'
                },
            },
        },
    };

    const clientsData = {
        labels: <?= json_encode(array_keys($client_revenues)) ?>,
        datasets: [{
            label: 'Chiffre d\'Affaires par Client (DH)',
            backgroundColor: '#ff9f40',
            borderColor: '#ff9f40',
            data: <?= json_encode(array_values($client_revenues)) ?>,
        }, ],
    };

    const clientsChartConfig = {
        type: 'pie',
        data: clientsData,
        options: {
            responsive: true
        },
    };

    const revenueChart = new Chart(document.getElementById('revenueChart').getContext('2d'), revenueChartConfig);
    const clientsChart = new Chart(document.getElementById('clientsChart').getContext('2d'), clientsChartConfig);

    function toggleChartView(view) {
        revenueChart.data = view === 'monthly' ? monthlyData : dailyData;
        revenueChart.update();
    }
    </script>
</body>

</html>
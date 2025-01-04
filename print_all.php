<?php
include 'includes/db.php';
session_start();

$username = $_SESSION['username'];
$sql = "SELECT * FROM users WHERE email = '$username'";
$sql_run = mysqli_query($con, $sql);
$row_a = mysqli_fetch_assoc($sql_run);
$user_id = $row_a['id'];

// Get company details
$sql_company = "SELECT * FROM permanent_details WHERE user_id = '$user_id'";
$result_company = mysqli_query($con, $sql_company);
$company_details = mysqli_fetch_assoc($result_company);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice Summary</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
    body {
        font-size: 14px;
    }

    .company-header {
        margin-bottom: 20px;
        border-bottom: 2px solid #333;
        padding-bottom: 15px;
    }

    .table td,
    .table th {
        padding: 0.5rem;
        vertical-align: middle;
    }

    @media print {
        .no-print {
            display: none;
        }

        .page-break {
            page-break-after: always;
        }
    }

    .total-row {
        background-color: #f8f9fa;
        font-weight: bold;
    }
    </style>
</head>

<body class="container mt-4">
    <div class="company-header">
        <h3><?php echo htmlspecialchars($company_details['company_name']); ?></h3>
        <p class="mb-0">
            <?php echo htmlspecialchars($company_details['address']); ?> |
            Tel: <?php echo htmlspecialchars($company_details['ph_number']); ?>
        </p>
    </div>

    <h4 class="mb-3">Summary of All Invoices</h4>

    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>Invoice #</th>
                <th>Date</th>
                <th>Client</th>
                <th>Service</th>
                <th class="text-right">Total (DH)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total_all_invoices = 0;
            $items_per_page = 25; // Adjust this number to fit your page
            $item_count = 0;
            
            $query = "SELECT d.*, b.company_name as client_name 
                      FROM data d 
                      LEFT JOIN buyer_details b ON d.client = b.buyer_id 
                      WHERE d.user_id='$user_id' 
                      ORDER BY d.date DESC";
            $result = mysqli_query($con, $query);
            
            while ($invoice = mysqli_fetch_assoc($result)) {
                $services_query = "SELECT product, 
                                 CAST(qty AS DECIMAL(10,2)) * CAST(rate AS DECIMAL(10,2)) as service_total 
                                 FROM other_data 
                                 WHERE data_id = '{$invoice['data_id']}'";
                $services_result = mysqli_query($con, $services_query);
                
                while ($service = mysqli_fetch_assoc($services_result)) {
                    $item_count++;
                    if ($item_count > $items_per_page) {
                        echo '</tbody></table><div class="page-break"></div>';
                        echo '<table class="table table-bordered table-sm"><tbody>';
                        $item_count = 1;
                    }
                    ?>
            <tr>
                <td><?php echo htmlspecialchars($invoice['invoice_number']); ?></td>
                <td><?php echo htmlspecialchars($invoice['date']); ?></td>
                <td><?php echo htmlspecialchars($invoice['client_name']); ?></td>
                <td><?php echo htmlspecialchars($service['product']); ?></td>
                <td class="text-right"><?php echo number_format($service['service_total'], 2); ?></td>
            </tr>
            <?php
                    $total_all_invoices += $service['service_total'];
                }
            }
            ?>
            <tr class="total-row">
                <td colspan="4" class="text-right"><strong>Total Amount:</strong></td>
                <td class="text-right"><strong><?php echo number_format($total_all_invoices, 2); ?></strong></td>
            </tr>
        </tbody>
    </table>

    <button onclick="window.print()" class="btn btn-primary no-print">Print Summary</button>

    <script>
    window.onload = function() {
        window.print();
    }
    </script>
</body>

</html>
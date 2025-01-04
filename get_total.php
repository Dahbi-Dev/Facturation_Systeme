<?php
session_start();
include 'includes/db.php';

if(isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    
    // Get user ID
    $sql = "SELECT id FROM users WHERE email = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $user_id = $user['id'];
    
    // Get period from POST
    $period = $_POST['period'] ?? 'month';
    
    // Build the date condition based on period
    $date_condition = '';
    switch($period) {
        case 'day':
            $date_condition = "DATE(d.date) = CURDATE()";
            break;
        case 'week':
            $date_condition = "DATE(d.date) >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)";
            break;
        case 'two_weeks':
            $date_condition = "DATE(d.date) >= DATE_SUB(CURDATE(), INTERVAL 2 WEEK)";
            break;
        case 'month':
            $date_condition = "DATE(d.date) >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
            break;
        case '16_months':
            $date_condition = "DATE(d.date) >= DATE_SUB(CURDATE(), INTERVAL 16 MONTH)";
            break;
        case 'year':
            $date_condition = "DATE(d.date) >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
            break;
        default:
            $date_condition = "1=1";
    }
    
    // Query to get totals
    $query = "SELECT 
                d.data_id,
                d.date,
                SUM(CAST(od.qty AS DECIMAL(10,2)) * CAST(od.rate AS DECIMAL(10,2))) as total
              FROM data d
              JOIN other_data od ON d.data_id = od.data_id
              WHERE d.user_id = ? AND $date_condition
              GROUP BY d.data_id";
              
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $total = 0;
    while($row = $result->fetch_assoc()) {
        $total += $row['total'];
    }
    
    echo $total;
} else {
    echo '0';
}
?>
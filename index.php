<?php

include 'includes/db.php';

include 'includes/header.php';

include 'includes/navbar.php';



$sql = "SELECT * FROM users WHERE email = '$username'";

$sql_run = mysqli_query($con, $sql);

$row_a = mysqli_fetch_assoc($sql_run);

$user_id = $row_a['id'];



$query_c = "SELECT * FROM data WHERE user_id ='$user_id'";

$query_run_c = mysqli_query($con, $query_c);

$number = mysqli_num_rows($query_run_c);



$query_a = "SELECT * FROM product WHERE user_id ='$user_id'";

$query_run_a = mysqli_query($con, $query_a);

$number_a = mysqli_num_rows($query_run_a);



$query_b = "SELECT * FROM buyer_details WHERE user_id ='$user_id'";

$query_run_b = mysqli_query($con, $query_b);

$number_b = mysqli_num_rows($query_run_b);





$sql1 = mysqli_query($con, "SELECT * FROM permanent_details WHERE user_id = '$user_id'");

$num1 = mysqli_num_rows($sql1);



$sql2 = mysqli_query($con, "SELECT * FROM bank_details WHERE user_id = '$user_id'");

$num2 = mysqli_num_rows($sql2);



$sql3 = mysqli_query($con, "SELECT * FROM buyer_details WHERE user_id = '$user_id'");

$num3 = mysqli_num_rows($sql3);



$sql4 = mysqli_query($con, "SELECT * FROM product WHERE user_id = '$user_id'");

$num4 = mysqli_num_rows($sql4);



$sql5 = mysqli_query($con, "SELECT * FROM logo WHERE user_id = '$user_id'");

$num5 = mysqli_num_rows($sql5);



$sql6 = mysqli_query($con, "SELECT * FROM sign WHERE user_id = '$user_id'");

$num6 = mysqli_num_rows($sql6);



?>





<div class="app-main__outer">

    <div class="app-main__inner">

        <div class="app-page-title">

            <div class="page-title-wrapper">

                <div class="page-title-heading">

                    <div class="page-title-icon">

                        <i class="pe-7s-global text-success">

                        </i>

                    </div>

                    <div>Dashboard

                        <div class="page-title-subheading">Get insights into your activity here

                        </div>

                    </div>

                </div>

                <div class="page-title-actions">



                    <?php if (($num1 == 0) or ($num2 == 0) or ($num3 == 0) or ($num4 == 0) or ($num5 == 0) or ($num6 == 0)) {
                        echo '<p class="text-danger">Enter all details to start creating invoices</p>';
                    } else { ?>
                    <a class="btn mr-3 mb-3 btn-primary" href="form.php" style="font-size:14px;">
                        <i class="fa fa-plus"></i>&nbsp; Create Invoice
                    </a>
                    <a class="btn mr-3 mb-3 btn-info" href="print_all.php" target="_blank" style="font-size:14px;">
                        <i class="fa fa-print"></i>&nbsp; Print All Invoices
                    </a>
                    <?php } ?>



                </div>

            </div>

        </div>



        <div class="tab-content">

            <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">

                <div class="main-card mb-3 card">

                    <div class="card-body">

                        <div class="row">

                            <div class="col-md-6 col-xl-3">

                                <div class="card mb-3 widget-content bg-midnight-bloom">

                                    <div class="widget-content-wrapper text-white">

                                        <div class="widget-content-left">

                                            <div class="widget-heading">Total Invoices</div>

                                            <!-- <div class="widget-subheading">Invoices created till today</div> -->

                                        </div>

                                        <div class="widget-content-right">

                                            <div class="widget-numbers text-white"><span><?php echo $number; ?></span>
                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <div class="col-md-6 col-xl-3">

                                <div class="card mb-3 widget-content bg-arielle-smile">

                                    <div class="widget-content-wrapper text-white">

                                        <div class="widget-content-left">

                                            <div class="widget-heading">Clients</div>

                                            <!-- <div class="widget-subheading">Total number of clients added</div> -->

                                        </div>

                                        <div class="widget-content-right">

                                            <div class="widget-numbers text-white"><span><?php echo $number_b; ?></span>
                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <div class="col-md-6 col-xl-3">

                                <div class="card mb-3 widget-content bg-grow-early">

                                    <div class="widget-content-wrapper text-white">

                                        <div class="widget-content-left">

                                            <div class="widget-heading">services</div>

                                            <!-- <div class="widget-subheading">The ones you've added</div> -->

                                        </div>

                                        <div class="widget-content-right">

                                            <div class="widget-numbers text-white"><span><?php echo $number_a; ?></span>
                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>


                            <div class="col-md-6 col-xl-3">

                                <div class="card mb-3 widget-content bg-premium-dark">

                                    <div class="widget-content-wrapper text-white">

                                        <div class="widget-content-left">

                                            <div class="widget-heading">Company Info</div>

                                            <!-- <div class="widget-subheading">Revenue streams</div> -->

                                        </div>

                                        <div class="widget-content-right">

                                            <div class="widget-numbers text-warning"><span>

                                                    <?php

                                                    if ($num1 == 0 && $num2 == 0) {

                                                        echo "0";
                                                    } else if ($num1 == 0 and !($num2 == 0)) {

                                                        echo "50%";
                                                    } else if ($num2 == 0 and !($num1 == 0)) {

                                                        echo "50%";
                                                    } else {

                                                        echo "100%";
                                                    }

                                                    ?>

                                                </span></div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <?php
                            // Function to get total based on time period
                            function getInvoiceTotal($user_id, $period)
                            {
                                global $con;

                                $date_condition = '';
                                switch ($period) {
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
                                while ($row = $result->fetch_assoc()) {
                                    $total += $row['total'];
                                }

                                return $total;
                            }
                            ?>

                            <div class="col-12">
                                <div class="card mb-3 widget-content bg-premium-dark">
                                    <div class="widget-content-wrapper text-white">
                                        <div class="widget-content-left">
                                            <div class="widget-heading">Total Revenue</div>
                                            <div class="form-group mt-2 m-2">
                                                <select id="period-filter"
                                                    class="form-control form-control-sm text-white bg-dark"
                                                    style="background: rgba(0, 0, 0,); border: 1px solid rgba(0, 0, 0, );"
                                                    onchange="updateTotal(this.value)">
                                                    <option value="day">Today</option>
                                                    <option value="week">Last Week</option>
                                                    <option value="two_weeks">Last 2 Weeks</option>
                                                    <option value="month" selected>This Month</option>
                                                    <option value="16_months">Last 16 Months</option>
                                                    <option value="year">This Year</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="widget-content-right">
                                            <div class="widget-numbers text-white" id="total-display">
                                                <?php echo number_format(getInvoiceTotal($user_id, 'month'), 2); ?> DH
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <script>
                            function updateTotal(period) {
                                $.ajax({
                                    url: 'get_total.php',
                                    type: 'POST',
                                    data: {
                                        period: period
                                    },
                                    success: function(response) {
                                        $('#total-display').html(parseFloat(response).toLocaleString(
                                            'fr-MA', {
                                                minimumFractionDigits: 2,
                                                maximumFractionDigits: 2
                                            }) + ' DH');
                                    }
                                });
                            }
                            </script>

                        </div>



                        <?php





                        $query = "SELECT * FROM data WHERE user_id='$user_id' ORDER BY `invoice_number` DESC ";

                        $query_run = mysqli_query($con, $query);





                        ?>



                        <table class="table table-striped table-bordered" id="table">

                            <thead align="center">

                                <tr>



                                    <th>Invoice Number</th>

                                    <th>Date</th>

                                    <th>Type</th>

                                    <th>To </th>

                                    <th>View</th>



                                </tr>

                            </thead>

                            <tbody align="center">

                                <?php



                                while ($row = mysqli_fetch_assoc($query_run)) {

                                ?>

                                <tr>

                                    <td><?php echo $row['invoice_number']; ?></td>

                                    <td><?php echo $row['date']; ?></td>

                                    <td><?php echo $row['type']; ?></td>

                                    <td>

                                        <?php

                                            $sqla = mysqli_query($con, "SELECT * FROM buyer_details WHERE buyer_id='" . $row['client'] . "'");

                                            $rowa = mysqli_fetch_assoc($sqla);

                                            echo $rowa['company_name'];

                                            ?>

                                    </td>

                                    <td>
                                        <form action="preview.php" target="_blank" method="post">

                                            <input type="hidden" name="invoice_id"
                                                value="<?php echo $row['data_id']; ?>">

                                            <button type="submit" name="pdf_btn" class="btn btn-warning"><i
                                                    class="fa fa-eye"></i></button>

                                        </form>
                                    </td>

                                    <!--    <td><form action="invoice_edit.php" method="post">

                    <input type="hidden" name="invoice_edit_id" value="<?php echo $row['data_id']; ?>">

                    <button  type="submit" name="invoice_edit_btn" class="btn btn-success"><i class="fa fa-edit"></i></button>

                </form></td> -->

                                </tr>

                                <?php



                                }



                                ?>

                            </tbody>

                        </table>





                    </div>

                </div>

            </div>

        </div>

    </div>







    <?php

    include('includes/footer.php');

    ?>



    <script>
    $(document).ready(function() {

        $('#table').DataTable({

            "lengthMenu": [
                [25, 200, 300, -1],
                [25, 200, 300, "All"]
            ],

            "order": [
                [0, "desc"]
            ],



        });

    });
    </script>
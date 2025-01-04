<?php

include 'includes/db.php';

session_start();

$invoice_id = $_POST['invoice_id'];

$sqlf = "SELECT * FROM `data` WHERE data_id = '$invoice_id'";

$resultf = mysqli_query($con, $sqlf);

$rowf = mysqli_fetch_assoc($resultf);



$user_id = $rowf["user_id"];





$invoice_number = $rowf['invoice_number'];

$client = $rowf['client'];

$project = $rowf['project'];

$workorder = $rowf['workorder'];

$discount = $rowf['discount'];

$gst = $rowf['gst'];

if (empty($rowf['date'])) {

    $date = date("jS F, Y ");
} else {

    $date = $rowf['date'];
}



$dis = '0';

$type = $rowf['type'];









$total_1 = '0';

$total_2 = '0';

$total_3 = '0';

$total_4 = '0';

$total_5 = '0';

$total_6 = '0';

$total_7 = '0';

$total_8 = '0';

$total_9 = '0';

$total_10 = '0';



$rate_1_a = '';

$rate_2_a = '';

$rate_3_a = '';

$rate_4_a = '';

$rate_5_a = '';

$rate_6_a = '';

$rate_7_a = '';

$rate_8_a = '';

$rate_9_a = '';

$rate_10_a = '';



?>



<!DOCTYPE html>

<html>

<head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <meta name="description" content="">

    <meta name="author" content="">



    <title>Invoice Preview</title>



    <!-- Custom fonts for this template-->



    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
        integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">



    <!-- Custom styles for this template-->

    <link rel="icon" href="assets/images/favicon.png" type="image/png">



    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">



    <style type="text/css">
    .container-fluid {

        margin-top: 25px;



    }



    .table2 {

        outline: 0px;

        margin-left: 300px;

    }





    table {



        border: 0.5px solid grey;

        border-spacing: 0;

        padding: 10px;

        outline: 0.5px solid grey;

        margin-left: 0px;

        border-collapse: collapse;

        min-width: 100%;

        max-width: 100%;

        text-align: left;







    }



    .table1 {



        border: 0.5px solid grey;

        border-spacing: 0;

        padding: 10px;

        outline: 0.5px solid grey;

        margin-left: 2px;

        border-collapse: collapse;

        text-align: left;









    }









    td,
    th {



        border: 0.5px solid grey;

        border-spacing: 0;

        padding: 10px;

        outline: 0.5px solid grey;

        margin-left: 2px;

        border-collapse: collapse;







    }



    .logo1 {}







    .logo {

        float: right;

    }





    @page {

        size: A4;

        margin: 0;

    }



    @media print {

        .page {



            border: initial;

            border-radius: initial;

            width: initial;

            min-height: initial;

            box-shadow: initial;

            background: initial;

            page-break-after: always;

        }

    }



    .company {

        font-size: 13px;

    }



    h2 {

        margin-bottom: -10px;

    }



    h5 {

        font-weight: bolder;

    }



    .note {

        font-size: 13px;

        margin-top: 15px;

    }
    </style>





</head>





<body id="page-top">



    <!-- Page Wrapper -->

    <div id="wrapper" style="margin:50px 30px 20px 40px;">



        <div class="container-fluid">



            <div class="logo">

                <?php



                $sqlx = "SELECT * from logo where user_id = '$user_id'";

                $resultx = mysqli_query($con, $sqlx);

                $rowx = mysqli_fetch_array($resultx);



                $image = $rowx['image'];



                ?>

                <img src='<?php echo "upload/" . $image . ""; ?>'>

            </div>



            <div class="company">



                <?php



                $sql = "SELECT * FROM permanent_details WHERE user_id='$user_id'";

                $result = $con->query($sql);





                // output data of each row

                $row = mysqli_fetch_assoc($result);



                $x1 = $row['statecode'];

                $query_2 = mysqli_query($con, "SELECT * FROM statecode WHERE state_id='$x1'");

                $row_2 = mysqli_fetch_assoc($query_2);

                $str = $row['gst'];







                echo "<h2>" . $row["company_name"] . "</h2><br>" . $row["address"] . "<br>";








                echo "Phone: " . $row["ph_number"] . "<br>Email: " . $row["email"] . "<br>";



                if (empty($row['whatsapp'])) {

                    echo "Website: " . $row["website"] . "";
                } else {

                    echo "Whatsapp: " . $row["whatsapp"] . "<br>";

                    echo "Website: " . $row["website"] . "";
                }











                if (empty($row['user_name']  and $row['designation'])) {
                } else {

                    echo "<br>Contact Person: " . $row["user_name"] . ", " . $row["designation"] . "";
                }



                ?>



            </div>

            <?php

            $type_1 = strtoupper($type);

            ?>





            <hr align="center"><br>



            <h3 style="margin-top: -20px;" align="center"><?php echo $type_1; ?></h3>

            <br>



            <?php







            // Fetch all necessary data at the start
            $sql_bank = "SELECT * FROM bank_details WHERE user_id = ?";
            $stmt_bank = $con->prepare($sql_bank);
            $stmt_bank->bind_param("s", $user_id);
            $stmt_bank->execute();
            $bank_details = $stmt_bank->get_result()->fetch_assoc();

            $sql_company = "SELECT * FROM permanent_details WHERE user_id = ?";
            $stmt_company = $con->prepare($sql_company);
            $stmt_company->bind_param("s", $user_id);
            $stmt_company->execute();
            $company_details = $stmt_company->get_result()->fetch_assoc();
            ?>

            <!-- Main Invoice Table -->
            <div class="container-fluid">
                <table class="table" style="margin-bottom: 30px;">
                    <tr style="background-color: #f8f9fa;">
                        <th style="width: 5%;">N°</th>
                        <th style="width: 80%;">Désignation</th>
                        <th style="width: 15%; text-align: right;">Total (DH)</th>
                    </tr>

                    <?php
                    $sql_services = "SELECT * FROM other_data WHERE data_id = ?";
                    $stmt_services = $con->prepare($sql_services);
                    $stmt_services->bind_param("s", $invoice_id);
                    $stmt_services->execute();
                    $result_services = $stmt_services->get_result();

                    $i = 0;
                    $total = 0;

                    while ($service = $result_services->fetch_assoc()) {
                        $amount = floatval($service['rate']);
                        $total += $amount;

                        echo "<tr>
                    <td>" . ++$i . "</td>
                    <td>
                        <strong>" . htmlspecialchars($service['product']) . "</strong><br>
                        <span style='color: #666;'>" . htmlspecialchars($service['desc']) . "</span>
                    </td>
                    <td style='text-align: right;'>" . number_format($amount, 2) . "</td>
                  </tr>";
                    }
                    ?>

                    <tr style="background-color: #f8f9fa; font-weight: bold;">
                        <td colspan="2" style="text-align: right;">Total</td>
                        <td style="text-align: right;"><?php echo number_format($total, 2); ?> DH</td>
                    </tr>
                </table>

                <!-- Footer Section -->
                <div style="margin-top: 50px; border-top: 1px solid #ddd; padding-top: 20px;">
                    <div style="display: flex; justify-content: space-between;">
                        <!-- Bank Details -->
                        <div style="width: 33%;">
                            <h6 style="font-weight: bold; color: #444;">Coordonnées Bancaires</h6>
                            <?php if ($bank_details): ?>
                            <p style="font-size: 14px; line-height: 1.5;">
                                Banque: <?php echo htmlspecialchars($bank_details["bank_name"]); ?><br>
                                RIB: <?php echo htmlspecialchars($bank_details["account_number"]); ?>
                            </p>
                            <?php endif; ?>
                        </div>

                        <!-- Company Details -->
                        <div style="width: 33%;">
                            <?php if ($company_details): ?>
                            <p style="font-size: 14px; line-height: 1.5; text-align: center;">
                                <?php echo htmlspecialchars($company_details["company_name"]); ?><br>
                                <?php echo htmlspecialchars($company_details["address"]); ?><br>
                                Tél: <?php echo htmlspecialchars($company_details["ph_number"]); ?>
                            </p>
                            <?php endif; ?>
                        </div>

                        <!-- Signature -->
                        <div style="width: 33%; text-align: right;">
                            <?php
                            $sql_sign = "SELECT image FROM sign WHERE user_id = ?";
                            $stmt_sign = $con->prepare($sql_sign);
                            $stmt_sign->bind_param("s", $user_id);
                            $stmt_sign->execute();
                            $sign_image = $stmt_sign->get_result()->fetch_assoc();

                            if ($sign_image && !empty($sign_image['image'])): ?>
                            <img src='upload/<?php echo htmlspecialchars($sign_image['image']); ?>'
                                style="max-height: 80px; margin-bottom: 10px;"><br>
                            <span style="font-weight: bold; font-size: 14px;">Signature autorisée</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>


            <?php





















            ?>















        </div>



    </div>



</body>

</html>



<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.0/jquery.min.js"></script>
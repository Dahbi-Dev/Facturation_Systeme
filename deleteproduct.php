<?php  

include 'includes/db.php';

 $id=$_POST["id"];

 $sql = mysqli_query($con, "DELETE FROM product WHERE product_id = '$id'"); 

 if($sql)  

 {  

      echo 'Service Deleted Successfully';  

 }  

 ?>
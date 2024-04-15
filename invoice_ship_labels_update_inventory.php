<?php

function drawTable($arrayOfArrays) {
    echo "<table border=1 cellspacing=1>";
    echo "<tr>";
    // loop over the first array and since using FETCH_ASSOC, 
    // use the keys to produce headers
    foreach($arrayOfArrays[0] as $key => $value ) {
          echo "<th>$key</th>";
        }
    echo "</tr>";
    foreach($arrayOfArrays as $array) {
      echo "<tr>";
        foreach($array as $data) {
            echo "<td>$data</td>";
        }   
      echo "</tr>";
    }
    echo "</table>";
}

try { // if something goes wrong, an exception is thrown
    
    // connect to legacyDB
    $dsn_spock = "mysql:host=courses;dbname=z1944667";
    $pdo_spock = new PDO($dsn_spock, "z1944667", "1999Nov09");

    // connect to legacyDB
    $dsn_legacy = "mysql:host=blitz.cs.niu.edu;port=3306;dbname=csci467";
    $pdo_legacy = new PDO($dsn_legacy, "student", "student");


    // Print invoice generically
    // Only 1 order's invoice is printed at a time therefore, the "complete order" button
    // will produce only 1 set of details that correspond to that order.
    
    // order_id based on "complete order"
    $order_id = 1;

    // SQL to extract part_num and quantity based on order_id
    $sql_current_order = "SELECT * FROM order_parts
                          WHERE order_id = '$order_id';";

    // Store details in $current_order as array for processing
    $rs_current_order = $pdo_spock->prepare($sql_current_order);
    $rs_current_order->execute(); 
    $currentOrder = $rs_current_order->fetchAll(PDO::FETCH_ASSOC);
    
    // Test
    // drawTable($currentOrder);
    




    //*************************
    // Invoice Label
    //**************************
    echo "<h4>Invoice: </h4>";
    $shipping = 5;
    $amount = 0;
    $weight = 0;
    
    // Print Invoice details
    foreach($currentOrder as $row){
        
        // 1. Print part quantity
        $qty = $row["quantity"];
          echo"$qty - ";

        // Store part number in $part_num
        $part_num = $row["part_num"]; //echo "$part_num <br/>";
                
        // SQL using $part_num to get part name, price, and weight from parts table
        $sql_current_part = "SELECT description,price,weight FROM parts
                             WHERE number = '$part_num';";

        // $currentPart holds the part name, price, and weight the current part_num from legacy
        $rs_current_part = $pdo_legacy->prepare($sql_current_part);
        $rs_current_part->execute(); 
        $currentPart = $rs_current_part->fetch(PDO::FETCH_ASSOC);
        //print_r($currentPart);

        // 2. part name printed
        $part_name = $currentPart["description"];
        echo "$part_name: " ;

        // 3. part price printed
        $part_price = $currentPart["price"];
        $part_price *= $qty;
        echo "$$part_price <br/>" ;
        
        // Keep a count of the total amount and weight
        $amount+=$part_price;
        $weight+=$currentPart["weight"];
      }    
      $invoice_total = $amount + $shipping;
      
      // 4. total amount, total weight, shipping cost, and invoice total printed
      echo "<br/>Total Item Amount: $$amount <br/>";
      echo "Total Weight: $weight lbs<br/>";
      echo "Shipping Cost: $$shipping<br/>";
      echo "Invoice Total: $$invoice_total";


      

    //*************************
    // Shipping Label
    //**************************
    echo "<h4>Shipping: </h4>";

    // SQL to extract customer_id and quantity based on order_id
    $sql_current_customer = "SELECT * FROM orders
                             WHERE order_id = '$order_id'
                             AND order_status = 'Picked';";

    // Store details in $currentCustomerSpock as array for processing
    $rs_current_customer = $pdo_spock->prepare($sql_current_customer);
    $rs_current_customer->execute(); 
    $currentCustomerSpock = $rs_current_customer->fetch(PDO::FETCH_ASSOC);
    //print_r($currentCustomer);

    $customerID = $currentCustomerSpock["customer_id"];
    //echo $customerID;

    // SQL using $customerID to get customer name, address, and email from customer table
    $sql_customer_info = "SELECT name,city,street,contact FROM customers
                          WHERE id = '$customerID';";

    // $currentCustomerLegacy holds the part name, price, and weight the current part_num from legacy
    $rs_customer_info = $pdo_legacy->prepare($sql_customer_info);
    $rs_customer_info->execute(); 
    $currentCustomerLegacy = $rs_customer_info->fetch(PDO::FETCH_ASSOC);
    //print_r($currentCustomerLegacy);

    $name = $currentCustomerLegacy["name"];
    $city = $currentCustomerLegacy["city"];
    $street  = $currentCustomerLegacy["street"];
    $contact = $currentCustomerLegacy["contact"];

    echo "$name <br/> 
          $street, $city <br/>
          order confirmation sent to: $contact <br/><br/>";
    



    //********************************************
    // Order shipped. Update inventory and status.
    //********************************************
    echo "<form action='https://students.cs.niu.edu/~z1944667/InvShipLabels.php' method='POST'>";

    echo "Order will be marked as fullfilled. ";
    echo "<input type = submit name = 'Fulfilled' value = 'Done'/>";

    if ($_POST["Fulfilled"] = "Done") {

      // SQL to update fulfilled order status
      $sql_order_status = "UPDATE orders
                           SET order_status='Shipped'
                           WHERE order_id = $order_id;";

      // Execute SQL
      $rs_order_status = $pdo_spock->prepare($sql_order_status);
      $rs_order_status->execute();    
      
      foreach ($currentOrder as $row) {
        $part_num = $row["part_num"];
        $qtyToReduce = $row["quantity"];
          
        // SQL to update quantity onhand based on order fulfilled    
        $sql_update_quantity = "UPDATE warehouse_parts
                                SET onhand=onhand-$qtyToReduce
                                WHERE part_num = $part_num;";

        // Execute SQL
        $rs_update_quantity = $pdo_spock->prepare($sql_update_quantity);
        $rs_update_quantity->execute();                           
      }
    }

    echo "<form/>";

}
catch(PDOexception $e) 
{ // handle that exceptionecho "Connection to database failed: " . 
    $e->getMessage();
}

?>

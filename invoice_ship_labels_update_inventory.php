<?php

include "functions.php";

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


    // connect to spockDB
    $pdo = connection();

    // $_POST["order_id"] produces order to print invoice and shipping label.
    $order_id = 1;

    // Get current order details from order_parts
    $currentOrder = sql_select("SELECT * FROM order_parts WHERE order_id = ?;", [$order_id]);
    
    //*************************
    // Invoice Label
    //**************************
    echo "<h4>Invoice: </h4>";
    
    // Print Invoice details
    foreach($currentOrder as $row){
        
        // 1. Print part quantity
        $qty = $row["quantity"];
          echo"$qty - ";

        // Store part number in $part_num
        $part_num = $row["part_num"]; 

        $currentPart = legacy_sql_query("SELECT description,price,weight FROM parts WHERE number = ?;", [$part_num]);
        //print_r($currentPart);

        // 2. part name printed
        $part_name = $currentPart[0]["description"];
        echo "$part_name: " ;

        // 3. part price printed
        $part_price = $currentPart[0]["price"];
        $part_price *= $qty;
        echo "$$part_price <br/>" ;
        
        // Keep a count of the total amount and weight
        $amount+=$part_price;
        $order_weight+=$currentPart[0]["weight"];
      }

      $shippingCost = get_shipping_cost_by_weight($order_weight);

      // add shipping cost to invoice total
      $invoice_total = $amount + $shippingCost;
      

      // 4. total amount, total weight, shipping cost, and invoice total printed
      echo "<br/>Total Item Amount: $$amount <br/>";
      echo "Total Weight: $order_weight lbs<br/>";
      echo "Shipping Cost: $$shippingCost<br/>";
      echo "Invoice Total: $$invoice_total";


    //*************************
    // Shipping Label
    //**************************
    echo "<h4>Shipping: </h4>";

    $currentCustomerSpock = sql_select("SELECT * FROM orders WHERE order_id = ? AND order_status = 'Paid';",  [$order_id]);
    //print_r($currentCustomerSpock);
    $customerID = $currentCustomerSpock[0]["customer_id"];

    $currentCustomerLegacy = legacy_sql_query("SELECT name,city,street,contact FROM customers WHERE id = ?;", [$customerID]);
    //print_r($currentCustomerLegacy);

    $name = $currentCustomerLegacy[0]["name"];
    $city = $currentCustomerLegacy[0]["city"];
    $street  = $currentCustomerLegacy[0]["street"];
    $contact = $currentCustomerLegacy[0]["contact"];

    echo "$name <br/> $street, $city <br/> order confirmation sent to: $contact <br/><br/>";
    
    // Send email confirmation
    send_email("yudish.sheth09@gmail.com", "Order $order_id: Confirmation", "All items in your order is packed and shipped to $street, $city");             

    //***************************************************
    // Order shipped. Update onhand inventory and status.
    //***************************************************
    echo "<form action='$url/browse_catalog.php' method='POST'>";

    echo "Order will be marked as Shipped. ";
    // Note: Would be cool if this button connected to a printer and printed the label.
    echo '<input type=submit name="Fulfilled" value="Return To Orders"/>';

    echo "<form/>";

    // Check if form is submitted
    if (isset($_POST['Fulfilled'])) {

      // Handles UPDATE statements using SQL to update a fulfilled order's status
      sql_update("UPDATE orders SET order_status='Shipped' WHERE order_id = ?;", [$order_id]);
      
      foreach ($currentOrder as $row) {
        $part_num = $row["part_num"];
        $qtyToReduce = $row["quantity"];

        sql_update("UPDATE warehouse_parts SET onhand=onhand-? WHERE part_num = ?;", [$qtyToReduce, $part_num]);
      }
    }
}
catch(PDOexception $e) 
{ // handle that exceptionecho "Connection to database failed: " . 
    $e->getMessage();
}

?>

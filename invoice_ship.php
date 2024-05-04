<html>
<center>
<head>
      <style>
			header
        {
          padding: 1px;
          width: 100%;
          background-color: white;
        }
        html {
                background-image: linear-gradient(to bottom right, #347385, #ADD8E6, #347385);
        }
        h1 {
                font-family: Monospace;
                font-size: 30px;
                text-align: center;
        }
        h2 {
                font-family: Monospace;
                font-size: 30px;
        }

        h4 {
                font-family: Monospace;
                font-size: 18px;
        }
        table {
        border-collapse: collapse;
        width: 50%;
        background-color: white;
        }
        table, tr, th, td {

        padding: 5px 2px;
        }

        td {
        text-align: center;
        font-family: Monaco, "Lucida Console", Monospace;
        }

        th {
        font-weight: bold;
        font-size: 16px;
        font-family: Monaco, "Lucida Console", Monospace;
        }

        th, td {
        border-bottom: 1px solid #000000;
        }

        #sName {
                padding: 5px 15px;
                margin: 3px 0;
                width: 15%;
                height: 5%;
                text-align: center;
        }

        #checkName {
                background-color: white;
                padding: 10px 25px;
                cursor: pointer;
                margin 4px 2px;
                font-size 10px;
                text-align: center;
                border-radius: 5px;
                opacity: .5;
                transition: 0.2s;
                font-family: Monospace;
        }

        #checkName:hover {opacity: 1}

        #redirect {
                background-color: white;
                padding: 10px 25px;
                cursor: pointer;
                margin 4px 2px;
                font-size 10px;
                text-align: center;
                border-radius: 5px;
                opacity: .5;
                transition: 0.2s;
                font-family: Monospace;
        }

        #redirect:hover {opacity: 1}
      </style>

      <header>
      <h1>Print Invoice and Shipping Label</h1>
      </header>

    </head>
  <body>
<?php

session_start();

include "functions.php";

// echo "<h1>Invoice & Shipping</h1>";
    echo "<br/>";

    // connect to spockDB
    $pdo = connection();

    // From Print Packing List, selected order_num is passed for corresponding invoice and shipping label
    $order_id = $_POST["order_num"];

    // Get current order details from order_parts
    $currentOrder = sql_select("SELECT * FROM order_parts WHERE order_id = ?;", [$order_id]);
    
    //**************************
    // Invoice Label
    //**************************
    echo "<h4>Invoice: </h4>";
    echo "<table>";

    echo '<th>';
    echo 'Description';
    echo '</th>';

    echo '<th>';
    echo 'Quantity';
    echo '</th>';

    echo '<th>';
    echo 'Price';
    echo '</th>';

    // Print Invoice details
    foreach($currentOrder as $row){

        echo "<tr>";

          // Store part number in $part_num
          $part_num = $row["part_num"]; 

          $currentPart = legacy_sql_query("SELECT description,price,weight FROM parts WHERE number = ?;", [$part_num]);
          //print_r($currentPart);
        
          // 2. part name printed
          $part_name = $currentPart[0]["description"];
          echo "<td>$part_name</td>";

          // 1. Print part quantity
          $qty = $row["quantity"];
          echo"<td>$qty</td>";

          // 3. part price printed
          $part_price = $currentPart[0]["price"];
          $part_price *= $qty;
          echo "<td>$$part_price<td>";
        
        echo "<tr>";
        
        // Keep a count of the total amount and weight
        $amount+=$part_price;
        $order_weight+=$currentPart[0]["weight"];
      }

      echo "</table>";

      $shippingCost = get_shipping_cost_by_weight($order_weight);

      // add shipping cost to invoice total
      $invoice_total = $amount + $shippingCost;
      
      echo "<br/><br/>";
      // 4. total amount, total weight, shipping cost, and invoice total printed
      echo "<table>";
      
      echo "<tr><td>Total Amount</td>      <td>$$amount </td></tr>";
      echo "<tr><td>Total Weight</td>      <td>$order_weight lbs</td></tr>";
      echo "<tr><td>Shipping Cost</td>     <td> $$shippingCost</td></tr>";
      echo "<tr><td>Invoice Total</td>     <td> $$invoice_total</td></tr>";
      
      echo "</table>";

    //*************************
    // Shipping Label
    //**************************
    echo "<h4>Shipping: </h4>";

    $currentCustomerSpock = sql_select("SELECT * FROM orders WHERE order_id = ? AND order_status = 'Paid';",  [$order_id]);
    // print_r($currentCustomerSpock);

    $name = $currentCustomerSpock[0]["customer_name"];
    $address = $currentCustomerSpock[0]["address"];
    $email = $currentCustomerSpock[0]["email"];

    // echo $name . $address . $email;

    echo "$name <br/> $address <br/> Click to:<br/><br/>";

    // Store session variables
    $_SESSION["order_id"] = $_POST["order_num"];
    $_SESSION["name"]     = $name;
    $_SESSION["address"]  = $address;
    $_SESSION["contact"]  = $email;

    //***************************************************
    // To shipment confirmation page.
    //***************************************************
    echo "<form action='$url/ship_order.php' method='POST'>";

    echo '<input type=submit name="Fulfilled" value="Confirm Shipment"/>';

    echo "<form/>";



?>

    </center>
  </body>
</html>

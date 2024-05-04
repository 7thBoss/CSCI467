<?php
include "functions.php";
?>

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
                background-color: blue;
                padding: 10px 10px;
                cursor: pointer;
                margin 4px 2px;
                font-size 10px;
                text-align: center;
                border-radius: 5px;
                opacity: .7;
                transition: 0.2s;
                font-family: Monospace;
                color: white;
        }
        a
        {
          padding-right: 50px;
        }

        #redirect:hover {opacity: 1}
      </style>

      <header>
      <?php    
                // Navigation to UpdateInventory page       
                echo "<a href='$url/UpdateInventory.php'><input type='button' id='redirect' value='Receiving'/></a>";
                
                // Navigation to PackingList page
                echo "<a href='$url/PackingList.php'><input type='button' id='redirect' value='Packing'/></a>";

                // Navigation to Invoice Ship page
                echo "<a href='$url/invoice_ship.php'><input type='button' id='redirect' value='Shipping'/></a>";
        ?>
      <h1>Shipment Confirmation</h1>
      </header>

    </head>
  <body>
<?php




session_start();

// connect to spockDB
$pdo = connection();

// $_POST["order_id"] produces order to print invoice and shipping label.
$order_id  = $_SESSION["order_id"];
$name      = $_SESSION["name"];
$address   = $_SESSION["address"];  
$email     = $_SESSION["contact"];

// Get current order details from order_parts
$currentOrder = sql_select("SELECT * FROM order_parts WHERE order_id = ?;", [$order_id]);

//*************************
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
  
  echo "<br/>";
  // 4. total amount, total weight, shipping cost, and invoice total printed
  echo "<table>";

  echo "<tr><th>Total Amount</th>
            <th>Total Weight</th>
            <th>Shipping Cost</th>
            <th>Invoice Total</th></tr>";

  echo "<tr><td>$$amount </td>";
  echo "<td>$order_weight lbs</td>";
  echo "<td>$$shippingCost</td>";
  echo "<td>$$invoice_total</td></tr>";
  
  echo "</table>";

//*************************
// Shipping Label
//**************************
echo "<h4>Shipping: </h4>";

echo "<table>";

// echo "<tr><th>Customer Name</th>
//           <th>Email</th>
//           <th>Shipping Address</th></tr>";

// echo "<tr><td>$name </td>
//           <td>$email</td>
//           <td>$address</td></tr>";

echo "<tr><th>Shipping Address</th>
          <th>Customer Name</th>
          <th>Email</th></tr>";

echo "<tr><td>$address </td>
          <td>$name</td>
          <td>$email</td></tr>";                

echo "</table><br/>";

// Send email confirmation
send_email("yudish.sheth09@gmail.com", "Order $order_id: Confirmation", "All items in your order is packed and shipped to $address");             

//***************************************************
// Order shipped. Update status.
//***************************************************
echo "<form action='$url/packinglist.php' method='POST'>";

echo "<h4>Mark order as shipped. Confirmation will be sent to customer.</h4>";
// Note: Would be cool if this button connected to a printer and printed the label.
echo '<input type=submit name="Fulfilled" id="redirect" value="Complete Order"/>';

echo "<form/>";

// Check if form is submitted
if (isset($_POST['Fulfilled'])) {

  // Handles UPDATE statements using SQL to update a fulfilled order's status
  sql_update("UPDATE orders SET order_status='Shipped' WHERE order_id = ?;", [$order_id]);
  
}

echo "<br/><br/>";



?>

    </center>
  </body>
</html>

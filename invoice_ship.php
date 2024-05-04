
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
        width: 45%;
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
        #redirect2 {
                background-color: red;
                padding: 8px 10px;
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
                echo "<a href='https://students.cs.niu.edu/~z1944667/CSCI467/UpdateInventory.php'><input type='button' id='redirect' value='Receiving'/></a>";
                
                // Navigation to PackingList page
                echo "<a href='https://students.cs.niu.edu/~z1944667/CSCI467/PackingList.php'><input type='button' id='redirect' value='Packing'/></a>";

                // Navigation to Invoice Ship page
                echo "<a href='https://students.cs.niu.edu/~z1944667/CSCI467/invoice_ship.php'><input type='button' id='redirect' value='Shipping'/></a>";
        ?>
      <h1>Print Invoice and Shipping Label</h1>

      </header>

    </head>
  <body>
<?php

session_start();

// // Navigation to UpdateInventory page
// echo "<a href='$url/UpdateInventory.php'><input type='button' id='redirect' value='Receiving'/></a>";

// // Navigation to PackingList page
// echo "<a href='$url/PackingList.php'><input type='button' id='redirect' value='Warehouse: Packing'/></a>";

// // Navigation to Invoice Ship page
// echo "<a href='$url/invoice_ship.php'><input type='button' id='redirect' value='Warehouse: Shipping'/></a>";

include "functions.php";

// echo "<h1>Invoice & Shipping</h1>";
    echo "<br/>";

    // connect to spockDB

    echo "<table>";
    // Show all orders that have been packed.
    $displayOrders = sql_select("SELECT * FROM orders where order_status = 'Packed'; ");
    // print_r($displayOrders);

    echo "<tr><th>Date</th>
              <th>Status</th>
              <th>Order ID</th>
              <th>Invoice & Shipping</th></tr>";

    foreach($displayOrders as $displayOrder){
        echo "<tr>
                <td>".$displayOrder["order_date"]."</td>
                <td>".$displayOrder["order_status"]."</td>
                <td>".$displayOrder["order_id"]." </td>
                <td>
                        <form action='".$url."/invoice_ship.php' method='POST'>
                          <input type='hidden' name='order_id' value='".$displayOrder["order_id"]."'>
                          <input type='submit' id='redirect2' value='Print Label'>
                        </form>
                </td>
        </tr>";
    }
    echo "</table>";

       if(isset($_POST["order_id"])){         

                // From Print Packing List, selected order_num is passed for corresponding invoice and shipping label
                $order_id = $_POST['order_id'];

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

                // echo $order_id;
                $currentCustomerSpock = sql_select("SELECT * FROM orders WHERE order_id = ? AND order_status = 'Packed';",  [$order_id]);
                // print_r($currentCustomerSpock);
                $name = $currentCustomerSpock[0]["customer_name"];
                $address = $currentCustomerSpock[0]["address"];
                $email = $currentCustomerSpock[0]["email"];

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
                

                // Store session variables
                $_SESSION["order_id"] = $order_id;
                $_SESSION["name"]     = $name;
                $_SESSION["address"]  = $address;
                $_SESSION["contact"]  = $email;

                //***************************************************
                // To shipment confirmation page.
                //***************************************************
                echo "<form action='$url/ship_order.php' method='POST'>";

                echo '<input type=submit name="confirm" id="redirect" value="Confirm Shipment"/>';

                echo "<form/>";
        }

?>

    </center>
  </body>
</html>

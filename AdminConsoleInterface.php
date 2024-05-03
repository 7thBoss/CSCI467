<!DOCTYPE HTML>
<html>
<head>
   <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order's Page</title>
    <style>
        /* General page style */
        body {
            font-family: Arial, sans-serif; // font for the whole page
            background-color: #f4f4f9; // Light grey background
            color: #333; // Dark grey text
            padding: 20px; // Adds some padding around the content
        }

        h2 {
            color: #444;
            margin-bottom: 20px; // Space below the heading
        }

        /* Table styles */
        table {
            width: 100%; // Full width tables
            border-collapse: collapse; // Ensures borders between cells are merged
            margin-bottom: 20px; // Space below the table
        }

        th, td {
            border: 1px solid #ccc; // Light grey border for table cells 
            text-align: left; // Aligns text to the left
            padding: 8px; // Padding inside cells 
            font-size: 16px; // Slightly larger font size to read easier
        }

        th {
            background-color: #e9e9f0; // Light grey background for headers
            color: #333; // Dark text for contrast
        }

        tr:nth-child(even) {
            background-color: #f8f8fa; // Zebra striping for rows (thought it looked nice)
        }

        /* Button and input styles */
        input[type="text"], select {
            padding: 8px;
            margin-top: 5px;
            margin-right: 10px;
             border: 1px solid #ccc;
            border-radius: 4px; // Rounded corners for inputs and selects
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #4CAF50; // Green background for submit button
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049; // Darker green on hover
        }
    </style>
</head>
    
<?php
session_start();
include 'functions.php';

/***********************************
*
* DRAW TABLE FUNCTION
*
************************************/
function draw_table($rows) {
    echo "<table border=1 cellspacing=1>";
    echo "<tr>";
    if($rows!= null && $rows[0]!=null) {
        foreach($rows[0] as $key => $item)
        {
            echo "<th>$key</th>";
        }
        echo "</tr>";

        foreach($rows as $row){
            echo "<tr>";
            foreach($row as $key => $item){
                echo "<td>$item</td>";
            }
            echo"</tr>";
        } 
    } else {
        echo "NO DATA";
    }
    echo "</table>";
}

try {
    $pdo = connection();

    // default filter values (nothing gets filtered out)
    $status = "%";
    $dateMin = "1960-01-01";
    $dateMax = "2060-01-01";
    $priceMin = 0;
    $priceMax = 999999.99;

    /**********************
    *
    * FILTERING ORDERS
    *
    ***********************/
    if( isset($_GET["filter"]) ) {
        if($_GET["status"] == "selected") {
            $status = "Selected";
        }
        if($_GET["status"] == "paid") {
            $status = "Paid";
        }
        else if($_GET["status"] == "shipped") {
            $status = "Shipped";
        }

        if(!empty($_GET["dateMin"])) {
            $dateMin = $_GET["dateMin"];
        }

        if(!empty($_GET["dateMax"])) {
            $dateMax = $_GET["dateMax"];
        }

        if(!empty($_GET["priceMin"])) {
            $priceMin = $_GET["priceMin"];
        }

        if(!empty($_GET["priceMax"])) {
            $priceMax = $_GET["priceMax"];
        }
    }
    if(isset($_GET["order_id"])) {
        echo "<a href=\"AdminConsoleInterface.php\">Go back to viewing orders</a><br/>";
        echo "<h2>Order Detail for Order Number {$_GET["order_id"]}</h2>";

    /**************************
    *
    *
    *  ORDER INFO WHEN CLICKING
    *  ON ORDER_ID LINK
    *
    *****************************/
        echo "<h3>Order Information</h3>";
        $rs = $pdo->prepare("SELECT order_id,order_status,order_date FROM orders WHERE order_id = :order_id");
        $rs->execute(array(":order_id" => $_GET["order_id"]));
        $order = $rs->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($order)) {
           // Calculate total price and total weight for the order
           $total_price = total_price($order[0]['order_id']);
           $total_weight = total_weight($order[0]['order_id']);

           // Append total price and weight to the order details
           $order[0]['total_price'] = $total_price;
           $order[0]['total_weight'] = $total_weight;

           // Draw the table with the updated order details
           draw_table($order);
       } else {
           echo "<p>No results found</p>";
       }

        echo "<h3>Items Ordered</h3>";
        $rs = $pdo->prepare("SELECT order_id,part_num,quantity FROM order_parts WHERE order_id = :order_id");
        $rs->execute(array(":order_id" => $_GET["order_id"]));
        $parts = $rs->fetchAll(PDO::FETCH_ASSOC);
        if(!empty($parts)) {
            draw_table($parts);
        }
        else {
            echo "<p> No results found</p>";
        }

        echo "<h3>Customer Information</h3>";
        $rs = $pdo->prepare("SELECT customer_name,address,email FROM orders WHERE order_id = :order_id");
        $rs->execute(array(":order_id" => $_GET["order_id"]));
        $customer = $rs->fetchAll(PDO::FETCH_ASSOC);
        if(!empty($customer)) {
            draw_table($customer);
        }
        else {
            echo "<p> No results found</p>";
        }

        return;
    }

?>
<a href="SetCharges.php"> Click here to set shipping charges</a> <br/>

</br>
    <form method="get" action="AdminConsoleInterface.php">
    <div style="text-align: left;">Filter orders by:</div> 
    <label for="status">Status</label>
    <select name="status">
    <option value="any">Any</option>
    <option value="selected">Selected</option>
    <option value="paid">Paid</option>
    <option value="shipped">Shipped</option>
    </select> <br/>
    <label for="dateMin">Date Range</label>
    <input type="date" name="dateMin"/> - 
    <input type="date" name="dateMax"/> <br/>
    <label for="priceMin">Price Range</label>
    <input type="number" name="priceMin"/> - 
    <input type="number" name="priceMax"/> <br/>
    <input type="submit" value="Apply Filters" name="filter"/>
    </form>
</div>
<?php
    
    /*****************************
    *
    *
    *  DISPLAY ORDERS
    *
    *
    ******************************/
    // display orders using filters (if any)
    echo "<h2> Orders </h2>";
    $sql = "SELECT order_id,order_status,order_date FROM orders WHERE order_status LIKE :status " .
           "AND order_date >= :dateMin AND order_date <= :dateMax;";
    $rs = $pdo->prepare($sql);
    // execute query using either filter values from $_GET or defaults
    $rs->execute(array(":status" => $status, ":dateMin" => $dateMin, ":dateMax" => $dateMax));
    $rowsOrders = $rs->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($rowsOrders)) {
    echo "<table border='1' cellspacing='1'>";
    echo "<tr><th>Order ID</th><th>Status</th><th>Ordered Date</th><th>Total Price</th><th>Total Weight</th></tr>";

    foreach ($rowsOrders as $rowOrder) {
        //calculate the shipping and handling
        $shipping_and_handling = get_shipping_cost_by_weight($total_weight);

        //Add shipping and handling to total
        $total_price += $total_weight * $shipping_and_handling;

        // Calculate total price and weight for each order
        $total_price = total_price($rowOrder['order_id']);
        $total_weight = total_weight($rowOrder['order_id']);

        echo "<tr>";
        // Display link to order details
        echo "<td><a href=\"AdminConsoleInterface.php?order_id={$rowOrder['order_id']}\">{$rowOrder['order_id']}</a></td>";
        echo "<td>{$rowOrder['order_status']}</td>";
        echo "<td>{$rowOrder['order_date']}</td>";
        echo "<td>\${$total_price}</td>";
        echo "<td>{$total_weight} lbs</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No results found</p>";
}

    echo '</br></br></br>';

}
catch(PDOexception $e) {
    echo "Connection to database failed: " . $e->getMessage();
}
?>

</html>

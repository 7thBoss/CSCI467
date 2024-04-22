<!DOCTYPE HTML>
<html>
<title>Group 2A: Admin Console Interface</title>
<body style="background-color:powderblue;">
<body><?php
session_start();
include 'functions.php';


try {
    $pdo = connection();

    // default filter values (nothing gets filtered out)
    $status = "%";
    $dateMin = "1960-01-01";
    $dateMax = "2060-01-01";
    $priceMin = 0;
    $priceMax = 999999.99;

    if( isset($_GET["filter"]) ) {
        if($_GET["status"] == "incart") {
            $status = "InCart";
        }
        if($_GET["status"] == "authorized") {
            $status = "Authorized";
        }
        else if($_GET["status"] == "completed") {
            $status = "Completed";
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


        echo "<h3>Order Information</h3>";
        $rs = $pdo->prepare("SELECT * FROM orders WHERE order_id = :order_id");
        $rs->execute(array(":order_id" => $_GET["order_id"]));
        $order = $rs->fetchAll(PDO::FETCH_ASSOC);
        $customerId = $order[0]["customer_id"];
        if(!empty($order)) {
            draw_table($order);
        }
        else {
            echo "<p>No results found</p>";
        }

        echo "<h3>Items Ordered</h3>";
        $rs = $pdo->prepare("SELECT * FROM order_parts WHERE order_id = :order_id");
        $rs->execute(array(":order_id" => $_GET["order_id"]));
        $parts = $rs->fetchAll(PDO::FETCH_ASSOC);
        if(!empty($parts)) {
            draw_table($parts);
        }
        else {
            echo "<p> No results found</p>";
        }

        echo "<h3>Customer Information</h3>";
        $rs = $pdo->prepare("SELECT * FROM customers WHERE id = :customer_id");
        $rs->execute(array(":customer_id" => $customerId));
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
    <option value="incart">InCart</option>
    <option value="authorized">Authorized</option>
    <option value="completed">Completed</option>
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

    // display orders using filters (if any)
    echo "<h2> Orders </h2>";
    $sql = "SELECT * FROM orders WHERE order_status LIKE :status " .
        "AND ordered_date >= :dateMin AND ordered_date <= :dateMax " .
        "AND price_total >= :priceMin AND price_total <= :priceMax;";
    $rs = $pdo->prepare($sql);
    // execute query using either filter values from $_GET or defaults
    $rs->execute(array(":status" => $status, ":dateMin" => $dateMin, ":dateMax" => $dateMax, ":priceMin" => $priceMin, ":priceMax" => $priceMax));
    $rowsOrders = $rs->fetchAll(PDO::FETCH_ASSOC);

    if(!empty($rowsOrders)) {
        echo "<table border=1 cellspacing=1>";
        echo "<tr>";
        foreach($rowsOrders[0] as $key => $item)
        {
            echo "<th>$key</th>";
        }
        echo "</tr>";

        foreach($rowsOrders as $rowOrder){
            echo "<tr>";
            foreach($rowOrder as $key => $item){
                if($key == "order_id") {
                    // link to details of the order
                    echo "<td> <a href=\"AdminConsoleInterface.php?order_id=$item\"> $item </a> </td>";
                }
                else {
                    echo "<td>$item</td>";
                }
            }
            echo"</tr>";
        }
        echo "</table>";
    }
    else {
        echo "<p> No results found </p> <br/>";
    }

    # Customer Table calling Function to draw table
    // echo "<h2>Customers in the Database</h2>";
    // $rs = $pdo->query("SELECT * FROM customers;");
    // $rowsCustomer = $rs->fetchAll(PDO::FETCH_ASSOC);
    // draw_table($rowsCustomer);

    echo '</div>';
    echo '</br></br></br>';

}
catch(PDOexception $e) {
    echo "Connection to database failed: " . $e->getMessage();
}
?>

</body>
</html>

<!DOCTYPE html>
<html>
   <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipping Charges</title>
    <style>
        /* General page styles */
        body {
            font-family: Arial, sans-serif; /* Sets a clean, modern font for the whole page */
            background-color: #f4f4f9; /* Light grey background for better contrast */
            color: #333; /* Dark grey text for readability */
            padding: 20px; /* Adds some padding around the content */
        }

        h2 {
            color: #444;
            margin-bottom: 20px; /* Space below the heading */
        }

        /* Table styles */
        table {
            width: 100%; /* Full width tables */
            border-collapse: collapse; /* Ensures borders between cells are merged */
            margin-bottom: 20px; /* Space below the table */
        }

        th, td {
            border: 1px solid #ccc; /* Light grey border for table cells */
            text-align: left; /* Aligns text to the left */
            padding: 8px; /* Padding inside cells */
            font-size: 16px; /* Slightly larger font size for readability */
        }

        th {
            background-color: #e9e9f0; /* Light purple background for headers */
            color: #333; /* Dark text for contrast */
        }

        tr:nth-child(even) {
            background-color: #f8f8fa; /* Zebra striping for rows */
        }

        /* Button and input styles */
        input[type="text"], select {
            padding: 8px;
            margin-top: 5px;
            margin-right: 10px;
            border: 1px solid #ccc;
            border-radius: 4px; /* Rounded corners for inputs and selects */
            box-sizing: border-box; /* Includes padding and border in the element's total width and height */
        }

        input[type="submit"] {
            background-color: #4CAF50; /* Green background for submit button */
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049; /* Darker green on hover */
        }
    </style>
</head>

<?php
include 'functions.php';
?>
<?php

    //suffix indicates where to start searching for overlap
    function detectOverlap($min, $max, $suffix) {
	echo "<br/><br/>";
        $valid = true;
        if( $_POST[$min] > $_POST[$max] ) {
            $valid = false;
        }

        $futureMin = "feesData" . $suffix;
        while(isset($_POST[$futureMin])) {
            if( $_POST[$min] == $_POST[$futureMin] ) {
                $valid = false;
            }

            // see if new entry weights are within existing entry's
            if( $_POST[$min] > $_POST[$futureMin] ) {
                $futureMax = "feesData" . ($suffix+1);
                if ($_POST[$min] <= $_POST[$futureMax]) {
                    $valid = false;
                }
            }

            // see if existing entry weights are within new entry's
            if( $_POST[$futureMin] > $_POST[$min] ) {
                if( $_POST[$futureMin] < $_POST[$max] ) {
                    $valid = false;
                }
            }

            $suffix += 3;
            $futureMin = "feesData" . $suffix;
        }

        return $valid;
    }

try {

    # set up database connection
	$pdo = connection();
    // add an inputted weight bracket
    if (isset($_POST["newWeights"])) {

        $valid = detectOverlap("newMin","newMax",1);

        if($valid) {
            $statement = $pdo->prepare(
                "insert into shipping_cost (price, min_weight, max_weight) " . 
                    "values (:price, :newMin, :newMax);"
            );
            $statement->execute(
                    array(":price" => $_POST["newPrice"], ":newMin" => $_POST["newMin"],
                    ":newMax" => $_POST["newMax"])
                );
        }
        else {
            echo "Error in new weight bracket: weights may not overlap with existing ranges";
            echo "<br/>";
        }

    }

    if (isset($_POST["changeFees"])) {
        // delete requested weight brackets (if any)
        $rs = $pdo->query("SELECT min_weight FROM shipping_cost ORDER BY min_weight ASC;");
        $minWeights = $rs->fetchAll(PDO::FETCH_COLUMN);
        $rowNum = 1;
        $deleted=array();    //stores row numbers of deleted rows
        foreach($minWeights as $minWeight) {
            $name = "delete".$minWeight;    //name of index in $_POST array
            //replace because decimals become underlines in $_POST for some reason
            $name = str_replace(".","_",$name);    
            if (isset($_POST[$name])) {
                // store which row is being deleted
                $deleted[$rowNum]=true;
                $pdo->query("DELETE FROM shipping_cost WHERE min_weight = $minWeight;");
            }
            else {
                $deleted[$rowNum] = false;
            }
            $rowNum++;
        }

        //prevent overlapping weight brackets
        //for each weight bracket
        //    if less than current max weight
        //        return error
        //    for each weight bracket except current one
        //        if min_weight equal to other min weight
        //            return error
        //       if min_weight greater than other min weight
        //            if current min weight is less than that row's max weight
        //                return error
        //            endif    
        //        endif
        //
        //    end for loop

        $currentSuffix = 1;
        $min = "feesData" . $currentSuffix;
        $max = "feesData" . ($currentSuffix+1);
        $rowNum = 1;
        $valid = true;
        // for each row in the shipping cost table
        while(isset($_POST[$min])) {
            if( $_POST[$min] > $_POST[$max] and !$deleted[$rowNum]) {
                $valid = false;
            }
            $otherRowNum = 1;
            $otherSuffix = 1;
            $otherMin = "feesData" . $otherSuffix;
            // detect any overlap with other table rows
            // for each "other" row in the table that was displayed
            while(isset($_POST[$otherMin])) {
                // if not comparing row to itself
                if($currentSuffix != $otherSuffix) {
                    if(!$deleted[$rowNum] and !$deleted[$otherRowNum]) {
                        if( $_POST[$min] == $_POST[$otherMin] ) {
                            $valid = false;
                        }

                        if( $_POST[$min] > $_POST[$otherMin] ) {
                            $otherMax = "feesData" . ($otherSuffix+1);
                            if ($_POST[$min] <= $_POST[$otherMax]) {
                                $valid = false;
                            }
                        }
                    }
                }
                $otherSuffix += 3;
                $otherMin = "feesData".$otherSuffix;
                $otherRowNum++;
            }
            if(!$valid) {
                echo "Error in input: Weight brackets may not overlap";
                echo "<br/>";
                break;    //stop searching for errors
            }

            // change min and max to match with next row
            $currentSuffix += 3;
            $min = "feesData".$currentSuffix;
            $max = "feesData" . ($currentSuffix+1);
            $rowNum++;
        }    // for each row in displayed table

        if($valid) {
            // process input
            // if a weight bracket was modified
            //        update that row in the database
            $original = $pdo->query("SELECT * FROM shipping_cost ORDER BY min_weight ASC;");
            $suffix = 1;
            $rowNum = 0;
            $min = "feesData" . $suffix;
            // for each row that may have been modified
            while(isset($_POST[$min])) {
                $rowNum++;
                //if this row hasn't already been deleted from database
                if(!$deleted[$rowNum]) {    
                    $suffix++;
                    $max = "feesData".$suffix;
                    $suffix++;
                    $price = "feesData".$suffix;

                    $origBracket = $original->fetch(PDO::FETCH_ASSOC);
                    // if a weight bracket was modified
                    // (comparisons work because sorted by min_weight)
                    if( $_POST[$min] != $origBracket["min_weight"] OR
                            $_POST[$max] != $origBracket["max_weight"] OR
                            $_POST[$price] != $origBracket["price"] ) {
                        // update the database
                        $sql = "UPDATE shipping_cost SET min_weight = ?, " .
                            "max_weight = ?, price = ? " .
                            "WHERE min_weight = CAST(? AS DECIMAL(8,2));";	//need the cast
						$stmt = $pdo->prepare($sql);
						$stmt->bindValue(1, $_POST[$min]);
						$stmt->bindValue(2, $_POST[$max]);
						$stmt->bindValue(3, $_POST[$price]);
						$stmt->bindValue(4, $origBracket["min_weight"]);
						$stmt->execute();
                    }
                    $suffix++;
                    $min = "feesData" . $suffix;
                } 
                else if ($deleted[$rowNum]) {
                    // skip row that will be deleted
                    $suffix += 3;
                    $min = "feesData" . $suffix;
                }
            }
        }    // if modified brackets don't overlap
    }    // if the form to modify shipping costs table was submitted

    // stop processing POST requests
    
    // query for shipping costs
    echo "<h2>Shipping Charges</h2>";
    $sql = "SELECT min_weight AS 'Min Weight', " .
        "max_weight AS 'Max Weight', price AS 'Price' FROM shipping_cost " .
        "ORDER BY min_weight ASC;";
    $rs = $pdo->query($sql);
    $rowsWeights = $rs->fetchAll(PDO::FETCH_ASSOC);
    echo "<table border=1 cellspacing=1>";
    echo "<tr>";

    // prints shipping costs as a tabular form
    if( !empty($rowsWeights) ) {
        foreach($rowsWeights[0] as $key => $item)
        {
            echo "<th>$key</th>";
        }
        echo "<th>mark for deletion</th>";
        echo "</tr>";

        $suffix=1;
        echo "<form method=\"post\" action=\"SetCharges.php\">";
        foreach($rowsWeights as $rowWeight){
            echo "<tr> ";
            foreach($rowWeight as $key => $item){
                echo "<td> ";
                if($suffix % 3 == 0) {        //if displaying a price value
                    echo "$";
                }
                echo "<input type=number min=0 step=0.01 value=\"$item\"" .
                    " name=\"feesData".$suffix."\" required/> </td>";
                ++$suffix;
            }
			echo "<td> <input type=checkbox name=delete".$rowWeight["Min Weight"]." value=delete /> </td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<input class=\"miniButton\" type=submit name=changeFees value=Submit required /> ";
        echo "</form>";
    }

    // form to add a row to shipping cost table
    echo "<h3>Create a new weight bracket</h3>";
    echo "<form method=\"post\" action=\"SetCharges.php\">";
    echo "<input class=\"inputTextSelect\" type=number min=0 step=0.01 placeholder=\"min weight\" name=newMin id=newMin />";
    echo "<input class=\"inputTextSelect\" type=number min=0 step=0.01 placeholder=\"max weight\" name=newMax id=newMax />";
    echo "<input class=\"inputTextSelect\" type=number min=0 step=0.01 placeholder=price name=newPrice id=newPrice />";
    echo "<input class=\"miniButton\" type=submit name=newWeights id=newWeights value=\"Create entry\"/>";
    echo "</form>";
    echo "<br></br>";

    //go back to orders
    echo "<a href=\"AdminConsoleInterface.php\">Go back to viewing orders</a><br/>";
}

catch(PDOexception $e) { 
    echo "Connection to database failed: " . $e->getMessage();
    }
?>
</body>
</html>

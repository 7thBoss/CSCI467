<!DOCTYPE html>
<html>
    <head>
		<title>Catalog</title>
		<meta charset="UTF-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<style>
			header
			{
				padding: 1px;
				width: 100%;
				background-color: white;
			}
			table
			{
				margin: 40px;
				margin-left: auto;
				margin-right: auto;
				width: 95%;
				background-color: white;
				border-collapse: collapse;
			}
			th
			{
				text-align: center;
				height: 40px;
				width: 120px;
			}
			td
			{
				text-align: center;
				border: 2px solid black;
			}
			body
			{
				margin: 0;
				padding: 0;
				background-attachment: fixed;
				background-image: linear-gradient(-50deg, green, limegreen); 
			}
			h1
			{
				padding-left:40px;
			}
		</style>
		<header>
			<h1>Welcome</h1>
		</header>
	</head>
    <body>
		<?php
			include "functions.php";
	
	
			if (!isset($_POST["order_id"]))
			{
				sql_insert("INSERT INTO orders (order_status) VALUES ('Selected')");
				$_POST["order_id"] = sql_select("SELECT order_id FROM orders ORDER BY order_id DESC")[0][0];
			}

			//Get part data from legacy database
			if (isset($_POST["search"]))
				$legacy_parts = legacy_sql_query("SELECT * FROM parts WHERE description LIKE ?", ["%".$_POST["search"]."%"]);
			else
				$legacy_parts = legacy_sql_query("SELECT * FROM parts");

			//See if search returns parts
			$good_search = false;

			//Display searchparts
			echo "<table>
					<tr>
						<th>Description</th>
						<th>Price</th>
						<th>Weight</th>
						<th>Quantity</th>
						<th>
							<form action='".$url."/browse_catalog.php' method='POST'>
								<input type='hidden' name='order_id' value='".$_POST["order_id"]."'>
								<input type='text' placeholder='Search' name='search'>
							</form>
						</th>";
						
						//Get part count
						$part_count = sql_select("SELECT COUNT(*) FROM order_parts WHERE order_id=?", [$_POST["order_id"]])[0][0];
				
						//Create checkout button if the cart is not empty, print the number of items in the cart
						if ($part_count) 
							echo "<th>
									<form action='".$url."/checkout.php' method='POST'>
										<input type='hidden' name='order_id' value='".$_POST["order_id"]."'>
										<input type='submit' value='Checkout (".$part_count.")'>
									</form>
								  </th></tr>";
						else
							echo "<th></th></tr>";
					
			foreach($legacy_parts as $legacy_part)
			{
				//Find matching part in warehouse
				$part = sql_select("SELECT * FROM warehouse_parts WHERE part_num = ?", [$legacy_part["number"]])[0];

				//If the quantity is more than 0, print it
				if ($part)
				{
					//Confirm parts were found
					$good_search = true;
				
					//Print part listing
					echo "<tr>
							<td>".$legacy_part["description"]."</td>
							<td>$".$legacy_part["price"]."</td>
							<td>".$legacy_part["weight"]." lbs</td>
							<td>".$part["quantity"]."</td>
							<td><img src='".$legacy_part["pictureURL"]."'></td>
							<td>
								<form action='".$url."/add_to_cart.php' method='POST'>
									<input type='hidden' name='order_id' value='".$_POST["order_id"]."'>
									<input type='hidden' name='part_num' value='".$legacy_part["number"]."'>
									<input type='number' min=1 max=".$part["quantity"]." name='quantity' step=1>
									<input type='submit' value='Add to Cart'>
								</form>
							</td>
						  </tr>";
				}
			}
		
			//If there are no parts that match the search, tell the user
			if (!$good_search)
				echo "<tr><th>No Results</th></tr>";
		?>
		</table>
	</body>
</html>

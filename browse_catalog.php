<!DOCTYPE html>
<html>
    <head>
		<title>467 Project</title>
		<meta charset="UTF-8"/>
		<header>
			<h1>Main Page Header</h1>
			<?php
				include "functions.php";
				
				//Get order_id so count can be gotten later
				$order_id = get_order_id($_POST["customer"]);
				
				//Create checkout button if the cart is not empty, print the number of items in the cart
				if ($order_id)
					echo "<form action='".$url."/checkout.php' method='POST'>
							<input type='hidden' name='customer' value='".$_POST["customer"]."'>
							<input type='submit' value='Checkout (".sql_select("SELECT COUNT(*) FROM order_parts WHERE order_id=?", [$order_id])[0][0].")'>
						  </form>";
			?>
		</header>
	</head>
    <body>
	<?php
		//Get part data from legacy database
		if (isset($_POST["search"]))
			$legacy_parts = legacy_sql_query("SELECT * FROM parts WHERE description LIKE ?", ["%".$_POST["search"]."%"]);
		else
			$legacy_parts = legacy_sql_query("SELECT * FROM parts");

		//See if search returns parts
		$good_search = false;

		//Display searchparts
		echo "<form action='".$url."/browse_catalog.php' method='POST'>
				<input type='hidden' name='customer' value='".$_POST["customer"]."'>
				<input type='text' name='search'>
				<input type='submit' value='Search'>
			  </form>
		
		<table><tr><th>Description</th><th>Price</th><th>Weight</th><th>Quantity</th></tr>";
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
						<td>".$legacy_part["price"]."</td>
						<td>".$legacy_part["weight"]."</td>
						<td>".$part["quantity"]."</td>
						<td><img src='".$legacy_part["pictureURL"]."'></td>
						<td>
							<form action='".$url."/add_to_cart.php' method='POST'>
								<input type='hidden' name='customer' value='".$_POST["customer"]."'>
								<input type='hidden' name='part_num' value='".$legacy_part["number"]."'>
								<input type='submit' value='Add to Cart'>
								<input type='number' min=1 max=".$part["quantity"]." name='quantity' step=1>
							</form>
						</td>
					  </tr>";
			  }
		}
		
		//If there are no parts that match the search, tell the user
		if (!$good_search)
			echo "<tr><td>No Results</td></tr>";
	?>
		</table>
	</body>
</html>

<!DOCTYPE html>
<html>
    <head>
		<title>Checkout</title>
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
				padding-top:10px;
				padding-bottom:10px;
				border: 2px solid black;
			}
			body
			{
				margin: 0;
				padding: 0;
				background-attachment: fixed;
				background-image: linear-gradient(50deg, green, limegreen); 
			}
			h1
			{
				padding-left:40px;
			}
		</style>
		<header>
			<h1>Checkout</h1>
		</header>
	</head>
    <body>
	<?php
		include "functions.php";
	
		//Get shipping and handling charge
		$shipping_and_handling = 1.00;
	
		//Get a list of parts in the cart 
		$order_id = get_order_id($_POST["customer"]);
		$order_parts = sql_select("SELECT * FROM order_parts WHERE order_id=?", [$order_id]);
	
		//Get list of parts to search through
		$parts = legacy_sql_query("SELECT * FROM parts");
	
		//Initialize totals
		$total_price = 0;
		$total_weight = 0;
	
		//List all parts in cart
		echo "<table><tr><th>Name</th><th></th><th>Quantity</th><th>Weight</th><th>Price</th></tr>";
		foreach($order_parts as $order_part)
		{	
			//Search the legacy database for the matchining part
			foreach($parts as $part)
				if ($part['number'] == $order_part["part_num"])
					$match = $part;
		
			//Get the weight of the item at quantity and add to total
			$item_weight = $match['weight'] * $order_part["quantity"];
			$total_weight += $item_weight;
		
			//Get the price of the item at quantity and add to total
			$item_price = $match['price'] * $order_part["quantity"];
			$total_price += $item_price;
		
			//Print item
			echo "<tr>
					<td>".$match['description']."</td>
					<td><img src='".$match["pictureURL"]."'></td>
					<td>".$order_part["quantity"]."</td>
					<td>".$item_weight."</td>
					<td>".$item_price."</td>
					<td><form action='".$url."/remove_from_cart.php' method='POST'>
						<input type='hidden' name='customer' value='".$_POST["customer"]."'>
						<input type='hidden' name='part_num' value='".$match["number"]."'>
						<input type='number' min=1 max=".$order_part["quantity"]." name='quantity' step=1>
						<input type='submit' value='Remove Item'>
					</form></td>
				</tr>";
		}
	
		//Calculate shipping and handling
		$shipping_and_handling = get_shipping_cost_by_weight($total_weight);
	
		//Add shipping and handling to total price
		$total_price += $total_weight * $shipping_and_handling;
	
		//Print s&h and Total prices
		echo "<tr>
				<td>Shipping and Handling:</td>
				<td></td>
				<td></td>
				<td>".$shipping_and_handling."</td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td>Total:</td>
				<td></td>
				<td>".$total_weight."</td>
				<td>".$total_price."</td>
				<td></td>
				<td></td>
			</tr>
			</table>
			<table style='width: 20%;'>
				<form action='".$url."/finalize_order.php' method='POST'>
					<input type='hidden' name='weight' value=".$total_weight.">
					<input type='hidden' name='price' value=".$total_price.">
					<input type='hidden' name='customer' value='".$_POST["customer"]."'>
				
					<tr>
						<td>
							<label for='email'>Email:</label><br>
							<input type='email' name='email' id='email' placeholder='example@mail.com' required><br>
						</td>
					</tr>
	
					<tr>
						<td>
							<label for='cc'>Credit Card:</label><br>
							<input type='text' name='cc' id='cc' placeholder='1234 5678 1234 5678' required><br>
						</td>
					</tr>
			
					<tr><td><input type='submit' value='Checkout'></td></tr>
				</form>
				<tr><td>
					<form action='".$url."/browse_catalog.php' method='POST'>
						<input type='hidden' name='customer' value='".$_POST["customer"]."'>
						<input type='submit' value='Back to Catalog'>
					</form>
				</td></tr>
			</table>";
	?>
	</body>
</html>

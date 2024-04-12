<?php
	include "functions.php";
	
	//Get a list of parts in the cart 
	$order_id = sql_select("SELECT order_id FROM orders WHERE customer_id=? AND order_status='Selected'", [$_SESSION["customer_id"]])[0]['order_id'];
	$order_parts = sql_select("SELECT * FROM order_parts WHERE order_id=?", [$order_id]);
	
	//Get list of parts to search through
	$parts = legacy_sql_query("SELECT * FROM parts");
	
	//Initialize totals
	$total_price = 0;
	$total_weight = 0;
	
	//List all parts in cart
	echo "<table><tr><th>Name</th><th>Quantity</th><th>Weight</th><th>Price</th></tr>";
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
		
		echo "<tr>
				<td>".$match['description']."</td>
				<td>".$order_part["quantity"]."</td>
				<td>".$item_weight."</td>
				<td>".$item_price."</td>
			  </tr>";
	}
	echo "<tr>
			<td>Total:</td>
			<td></td>
			<td>".$total_weight."</td>
			<td>".$total_price."</td>
		  </tr>
		</table>
	<form action='https://students.cs.niu.edu/~".$_SESSION['user']."/CSCI467/finalize_order.php' method='POST'>
		<label for='name'>Name:</label><br>
		<input type='text' name='name' id='name' placeholder='Jane Doe' required><br>
		
		<label for='email'>Email:</label><br>
		<input type='email' name='email' id='email' placeholder='example@mail.com' required><br>
		
		<label for='address'>Address:</label><br>
		<input type='text' name='address' id='address' placeholder='123 Example Street' required><br>
		
		<label for='cc'>Credit Card:</label><br>
		<input type='text' name='cc' id='cc' placeholder='1234 5678 1234 5678' required><br>
		
		<input type='hidden' name='weight' value=".$total_weight.">
		<input type='hidden' name='price' value=".$total_price.">
		
		<input type='submit' value='Checkout'>
	</form>
	<form action='https://students.cs.niu.edu/~".$_SESSION['user']."/CSCI467/browse_catalog.php' method='POST'>
		<input type='submit' value='Back to Catalog'>
	</form>";
?>


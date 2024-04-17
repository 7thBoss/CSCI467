<?php 
	include "functions.php";
		
	//Get customers to choose from
	$customers = legacy_sql_query("SELECT * FROM customers");

	//Display customers
	echo "<table><tr><th>Name</th><th>City</th><th>Street</th><th>Contact</th></tr>";
	foreach($customers as $customer)
	{
		echo "<tr>
				<td>".$customer["name"]."</td>
				<td>".$customer["city"]."</td>
				<td>".$customer["street"]."</td>
				<td>".$customer["contact"]."</td>
				<td>
					<form action='".$url."/browse_catalog.php' method='POST'>
						<input type='hidden' name='customer' value='".$customer["id"]."'>
						<input type='submit' value='Login'>
					</form>
				</td>
			  </tr>";
	}
	echo "</table>";
?>

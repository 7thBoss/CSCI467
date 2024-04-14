<!DOCTYPE html>
<html>
    <head>
		<title>467 Project</title>
		<meta charset="UTF-8"/>
		<header>
			<h1>Main Page Header</h1>
			<?php
				include "functions.php";
				echo "<form action='https://students.cs.niu.edu/~".$_SESSION['user']."/CSCI467/checkout.php' method='POST'>"
			?>
				<input type="submit" value="Checkout">
			</form>
		</header>
	</head>
    <body>
	<?php
		//Get parts to browse
		$parts = legacy_sql_query("SELECT * FROM parts");

		//Display parts
		echo "<table><tr><th>Name</th><th>Price</th><th>Weight</th></tr>";
		foreach($parts as $part)
		{
			echo "<tr>
					<td>".$part["description"]."</td>
					<td>".$part["price"]."</td>
					<td>".$part["weight"]."</td>
					<td><img src='".$part["pictureURL"]."'></td>
					<td>
						<form action='https://students.cs.niu.edu/~".$_SESSION["user"]."/CSCI467/add_to_cart.php' method='POST'>
							<input type='hidden' name='part_num' value='".$part["number"]."'>
							<input type='submit' value='Add to Cart'>
							<input type='number' name='quantity' step='1'>
						</form>
					</td>
				  </tr>";
		}
		echo "</table>";
	?>
	</body>
</html>

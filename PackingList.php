<!DOCTYPE html>
<html>

	<head>
		<title>Packing List</title>
		<meta charset='UTF-8'/>
		<meta name='viewport' content='width=device-width, initial-scale=1'>
		<style>
			header
			{
				padding: 1px;
				width: 100%;
				background-color: white;
			}
			table
			{
				
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
				padding:10px;
				border: 2px solid black;
			}
			body
			{
				margin: 0;
				padding: 0;
				background-attachment: fixed;
				background-image: linear-gradient(-50deg, lightblue, lightblue); 
			}
			h1
			{
				padding-left:40px;
			}
			h2
			{
				padding-left:30px
			}
			p
			{
				padding-left:40px;
			}
			form
			{
				padding-left:40px;
			}
		</style>
		<header>
			<h1>Packing List</h1>
		</header>
    </head>
	<?php

		//include "format.php";

		//echo '<p> This will print out the packing list </p>';

		//include 'DB_Access.php';
		include 'functions.php';



		//Refresh button to recheck for order
		//echo 'Refresh Page:  ';
		echo '<form>';
		echo 'Refresh Page:  ';
		echo '<input type="submit" value="Refresh">';
		echo '</form>';

		echo '<br>';



		//finding orders needing to be picked, order by assecending order_id

		$order = sql_select('SELECT order_id FROM orders WHERE order_status = "Paid" ORDER BY order_id ASC',[]);

		if (gettype($order[0][0]) == gettype($empty))
		{
			// if no orders have the status Paid
			echo "<h1>No orders avaible, please try again</h1>";
		}
		else 
		{

			//establising what order's packing list is being desplayed'
			if (gettype($_POST['order_num']) != gettype($empty)) //if number is passed, though submitting selection for different order or pageing though avaible orders
			{
				$order_num = $_POST['order_num'];
			}
			else // if no order number is passed, Select oldest order
			{
				$order_num = $order[0][0];
			}

			// Getting number of Orders avable to be Picked
			$total = 0;
			while (gettype($order[$total]['order_id']) != gettype($empty))
			{
				$total = $total + 1;
			}

			echo '<h2>Orders Available to be Packed:  ';
			echo $total;

			echo ' </br>';

			echo 'Select Order Number to See Packing List</h2>';
			echo '<p>Default Order is Oldest Order with Paid Status</p>';

			$end = FALSE;

			// Setting counter to 0 if no counter value is passed
			if(gettype($_POST['counter']) != gettype($empty))
			{
				$counter = $_POST['counter'];
			}
			else
			{
				$counter = 0;
			}
			$base = $counter;


			//form to select order to display packing list for
			echo '<form method="POST">';
			echo '<table>';
			echo '<tr>';
			while ($end == FALSE)
			{
				// radio butions to select new order
				echo '<td>';
				echo '<input type="radio" name="order_num" value="';
				
				echo $order[$counter]['order_id'];  //making order number/id the value it submits

				//making oldest/first order automactical selected
				if ($counter == 0)
				{
					echo '" checked>';
				}
				else
				{
					echo '">';
				}

				// print out order number next to radio button
				echo $order[$counter]['order_id'];
				echo '</td>';


				$counter = $counter + 1;

				if($counter == $base + 10) // if 10 entries have been printed end loop
				{
					$end = TRUE;
				}
				if(gettype($order[$counter]['order_id']) == gettype($empty)) // if next value does not exist in table end loop
				{
					$end = TRUE;
				}
			}
			echo '</tr>';
			echo '</table>';

			echo '<input type="submit" value="submit">'; // submit selected order number
			echo '</form>';
			
			
		
			//back button
			if ($counter > 10)
			{
		
				$new_counter = $base - 10;
				echo '<form method="POST">';

				echo '<input type="hidden" id="counter" name="counter" value="';
				echo $new_counter;
				echo '">';

				//passes order_num to allow for printed list to stay same when maipulating table of possible order numbers
				echo '<input type="hidden" id="order_num" name="order_num" value="';
				echo $order_num;
				echo '">';

				echo '<input type="submit" value="Previous">';
				echo '</form>';
			}

			// next buttion to advance to the start being the next value after the last one in the table
			if ($counter != $total)
			{
				echo '<form method="POST">';
				echo '<input type="hidden" id="counter" name="counter" value="';
				echo $counter;
				echo '">';

				//passes order_num to allow for printed list to stay same when maipulating table of possible order numbers
				echo '<input type="hidden" id="order_num" name="order_num" value="';
				echo $order_num;
				echo '">';

				echo '<input type="submit" value="Next">';
				echo '</form>';
			}
			
			//Title for packing list
			echo '<h1>Packing List for Order:   ';
			echo $order_num;
			echo '</h1>';
	
			// order_num = order_id
			//get all parts assocated with order_id 
			$parts = sql_select('SELECT * FROM order_parts WHERE order_id = ? ORDER BY part_num ASC',[$order_num]);

			// form to submit order number to print invoice and shipping label
			echo "<form method='POST' action=$url/invoice_ship.php>"; // stating here to inclue table in forms formating

			//Table to hold packing list, with part number, description, weight and quantity ordered for each part

			echo '<table border=1>';
			echo '<tr>';
			//echo '<tr>';

			// Table headers
			echo '<th>';
			echo 'Part Number';
			echo '</th>';

			echo '<th>';
			echo 'Part Description';
			echo '</th>';

			echo '<th>';
			echo 'Part Weight';
			echo '</th>';

			echo '<th>';
			echo 'Quantity Ordered';
			echo '</th>';

			echo '</tr>';

			//counter to progress though parts in the order
			$part_ctr = 0;

	
			while (gettype($parts[$part_ctr]['part_num']) != gettype($empty))
			{
				// getting part information about part_num from legacy DB
				$part = legacy_sql_query('SELECT number, description, weight FROM parts WHERE number = ?',[$parts[$part_ctr]['part_num']]);

				echo '<tr>';

				//Part Number
				echo '<td>';
				echo $part[0]['number'];
				echo '</td>';

				//Part Description
				echo '<td>';
				echo $part[0]['description'];
				echo '</td>';

				//Part Weight
				echo '<td>';
				echo $part[0]['weight'];
				echo '</td>';

				// Quantity ordered
				echo '<td>';
				echo $parts[$part_ctr]['quantity']; 
				echo '</td>';

				echo '</tr>';

				$part_ctr = $part_ctr + 1;
			}

			echo '</table>';

			

			//passes order_num to get shipping label and invoice
			echo '<input type="hidden" id="order_num" name="order_num" value="';
			echo $order_num;
			echo '">';
			// button to navigate to invoice and shipping label
			echo '<input type="submit" value="Order Picked">'; 

			echo '</form>';
	
		}
	?>
</html>
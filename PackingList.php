<!DOCTYPE html>
<html>

<?php

echo '<p> This will print out the packing list </p>';

//include 'DB_Access.php';
include 'functions.php';


// Add in ability to see list of orders avalible 
//and select a different one to be shown insted of oldest

//Checking if order number has been passed to mark as Packed
if(gettype($_POST['order_num']) != gettype($empty))
{
	//echo '<p>0</p>';
	//echo $_POST['order_num'];
	//Checking that order status is Paid as to not overwrite status from other actions
	/*$status = sql_select('SELECT order_status FROM orders WHERE order_id = ?',[$_POST['order_num']]);
	//echo ' exe'
	
	
	//echo '<p>1</p>';

	if ($status[0]['order_status'] == 'Paid')  
	//if not Paid, nothing happens as it is likly a refresh of the page that resubmited the order id
	{
		//$select_parts = $DB->prepare('SELECT part_num, quantity FROM order_parts WHERE order_id = ?');
		//$select_parts->execute([$_POST['order_num']]);

		$part = sql_select('SELECT part_num, quantity FROM order_parts WHERE order_id = ?',[$_POST['order_num']]);
		//= $select_parts->fetchAll();
		//echo '<p>2</p>';
		$part_ctr = 0;
		
		while(gettype($part[$part_ctr]['part_num']) != gettype($empty))
		{
			//$inventory = $DB->prepare('SELECT onhand FROM warehouse_parts WHERE part_num = ?');
			//$inventory->execute([$part[$part_ctr]['part_num']]);

			//echo '<p>3</p>';

			$quanity = sql_select('SELECT onhand FROM warehouse_parts WHERE part_num = ?',[$part[$part_ctr]['part_num']]);
			//= $inventory->fetchAll();
			
			// caculate new on hand amount of part
			$new_onhand = $quanity[0]['onhand'] - $part[$part_ctr]['quantity'];
			echo '<p>';
			echo $new_onhand;
			echo '</p>';

			//$update = $DB->prepare('UPDATE warehouse_parts SET onhand = ? WHERE part_num = ?');
			//$update->execute([$new_onhand,$part[$part_ctr]['part_num']]);

			//echo '<p>4</p>';

			$part_ctr = $part_ctr + 1;

			//$part = $select_parts->fetch_assoc();
		}
		sql_update('UPDATE orders SET order_status = "Picked" WHERE order_id = ?',[$_POST['order_num']]);
		//$update = $DB->prepare('UPDATE orders SET order_status = "Picked" WHERE order_id = ?');
		//$update->execute([$_POST['order_num']]);
		//echo '<p>5</p>';
	}
	*/
	
}

//Refresh button to recheck for order
echo 'Refresh Page:  ';
echo '<form>';
echo '<input type="submit" value="Refresh">';
echo '</form>';

echo '<br>';



//finding oldest order neeing to be picked

$order = sql_select('SELECT order_id FROM orders WHERE order_status = "Paid" ORDER BY order_id ASC',[]);

if (gettype($order[0][0]) == gettype($empty))
{
	// if no orders have the status Paid
	echo "<p>No orders avaible, please try again</p>";
}
else 
{
	// ************ fisrt print first 5 orders avaible with button to advance groups or in groups of 5************

	if (gettype($_POST['order_num']) != gettype($empty))
	{
		$order_num = $_POST['order_num'];
	}
	else
	{
		$order_num = $order[0][0];
	}

	// Getting number of Orders avable to be Picked
	$total = 0;
	while (gettype($order[$total]['order_id']) != gettype($empty))
	{
		$total = $total + 1;
	}

	echo '<p>Orders Available to be Packed:  ';
	echo $total;
	echo '</p>';

	//echo '</br>';

	echo '<p>Select Order Number to See Packing List</p>';

	$end = FALSE;

	if(gettype($_POST['counter']) != gettype($empty))
	{
		$counter = $_POST['counter'];
	}
	else
	{
		$counter = 0;
	}
	$base = $counter;
	
	echo '<form method="POST">';
	echo '<table>';
	echo '<tr>';
	while ($end == FALSE)
	{
		//make into radio butions to select new order
		echo '<td>';
		echo '<input type="radio" name="order_num" value="';
		//echo 'Order Number: ';
		echo $order[$counter]['order_id'];
		echo '">';
		echo $order[$counter]['order_id'];
		echo '</td>';
		//echo '   ';
		 
		if(gettype(($counter + 1) / 5) == gettype(1))
		{
			echo '</tr><tr>';
		}

		$counter = $counter + 1;

		if($counter == $base + 25)
		{
			$end = TRUE;
		}
		if(gettype($order[$counter]['order_id']) == gettype($empty))
		{
			$end = TRUE;
		}
	}
	echo '</table>';
	echo '</tr>';
	echo '<input type="submit" value="submit">';
	echo '</form>';
	echo '</br>';
	//back button
	
	if ($counter > 25)
	{
		
		$new_counter = $base - 25;
		echo '<form method="POST">';

		echo '<input type="hidden" id="counter" name="counter" value="';
		echo $new_counter;
		echo '">';

		echo '<input type="hidden" id="order_num" name="order_num" value="';
		echo $order_num;
		echo '">';

		echo '<input type="submit" value="Previous">';
		echo '</form>';
	}
	// next button
	if ($counter != $total)
	{
		echo '<form method="POST">';
		echo '<input type="hidden" id="counter" name="counter" value="';
		echo $counter;
		echo '">';
		
		echo '<input type="hidden" id="order_num" name="order_num" value="';
		echo $order_num;
		echo '">';

		echo '<input type="submit" value="Next">';
		echo '</form>';
	}
	
	

		// Thinking 25 at a time in 5 rows of 5 order number and desplaying how many out of the total number of orders available
		// $end = FALSE
		// while loop - ($end == FALSE)
			// print order Number
			// increment Counter
				// if order number is NULL 
					// set $end to TRUE
				//else if counter is eual to base + 25
					// set $end to TRUE
							// pass any needed _POST value back to see if used
							// if conter is greater than 25
								// desplay go bacck buttioon inown form that sets start to 
								// counter - 25
							// if $order[counter][0] is not NULL
								// form with next button that passes counter
	//echo 'valid';

	// ************ if order number is passed in, set order_num to it
	//		else let $order[0][0] set it
	//if (gettype($_POST['order_num']) != gettype($empty))
	//{
	//	$order_num = $_POST['order_num'];
	//}
	//else
	//{
	//	$order_num = $order[0][0];
	//}
	echo '</br>';
	echo '<p>Packing List for Order:   ';
	echo $order_num;
	echo '</p>';
	
	$parts = sql_select('SELECT * FROM order_parts WHERE order_id = ? ORDER BY part_num ASC',[$order_num]);
	
	//start builing table
	echo '<table border=1>';
	echo '<tr>';
	echo '<tr>';

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
	// need to add hedders

	$part_ctr = 0;

	
	while (gettype($parts[$part_ctr]['part_num']) != gettype($empty))
	{
		$part = legacy_sql_query('SELECT number, description, weight FROM parts WHERE number = ?',[$parts[$part_ctr]['part_num']]);
		//= $search3->fetchAll();
		//echo '<p>fetched from search3</p>';
		echo '<tr>';

		//echo '<td>Part Number: ';
		echo '<td>';
		echo $part[0]['number'];
		echo '</td>';

		//echo '<td>Part Number status: ';
		//echo $type;
		//echo '</td>';

		//echo '<td>Part Description: ';
		echo '<td>';
		echo $part[0]['description'];
		echo '</td>';

		//echo '<td>Part Weight: ';
		echo '<td>';
		echo $part[0]['weight'];
		echo '</td>';

		//echo '<td>Part Counter: ';
		echo '<td>';
		echo $part_ctr; 
		echo '</td>';

		echo '</tr>';

		$part_ctr = $part_ctr + 1;
	}

	echo '</table>';

	echo '<br><br><br>';
	
	// form button to mark order as Packed
	
	echo '<form method="POST">';

	echo '<input type="hidden" id="order_num" name="order_num" value="';
	echo $order_num;
	echo '">';

	echo '<input type="submit" value="Order Picked">'; // can change the value to desplay what we want to say

	echo '</form>';
	
}


?>


</html>
<!DOCTYPE html>
<html>
<head>
		<title>Update Inventory</title>
		<meta charset='UTF-8'/>
		<meta name='viewport' content='width=device-width, initial-scale=1'>
		<style>
			header
			{
				padding: 1px;
				background-color: white;
			}
			body
			{
				margin: 0;
				padding: 0;
				background-attachment: fixed;
				background-image: linear-gradient(36deg, #5fb2b8, #8155a6); 
			}
			h1
			{
				padding-left:40px;
			}
			h3
			{
				text-align: center;
			}
			p
			{
				padding-left: 30px;
			}
			form
			{
				text-align: center;
			}
		</style>
		<header>
			<h1>Update Inventory</h1>
		</header>
    </head>

<?php

include 'functions.php';


//Here is form to input part number or description and amount to be added to inventory
echo '<form method="POST">';
// selecting part by number or discription
echo '<h4>Part Number or Description: ';
echo '<input list="part_num" name="part_num" required>';
echo '<datalist id="part_num">';

$entry2 = legacy_sql_query('SELECT number, description FROM parts',[]); //All part avaiable from legacy DB to be stocked
$part_ctr = 0; // part counter to iterate through all parts

// making each part an option in the list
while(gettype($entry2[$part_ctr]['number']) != gettype($empty))
{
	echo '<option value="';
	echo $entry2[$part_ctr]['number']; //makes the part number be what is submited and one of the infromation desplayed
	echo '">';
	echo $entry2[$part_ctr]['description']; // desplays description as second part of information desplayed 
	echo '</option>';
	$part_ctr = $part_ctr + 1;
}
echo '</datalist>';
echo '</h4>';

//Field to enter in amount
echo '<h4>Quantity to Add to Inventory: ';
echo '<input type="number" id="amount" name="amount" min="1" required>';
echo '</h4>';

//submit button 
echo '<input type="submit">';

//end of form
echo '</form>';

echo '<p>Select part by entering part number or description</br>';
echo 'Enter amount of selected part to be added to inventory</br>';
echo 'Submit to update inventory</p>';



if(gettype($_POST['part_num']) != gettype($empty))  //using $empty as an unintelsed varable to get the NULL type
{

	$entry = sql_select('SELECT quantity FROM warehouse_parts WHERE part_num = ?', [$_POST['part_num']]);

	// if part_num is a valid part number, update both onhand and quantity of said part
	if(gettype($entry[0]['quantity']) != gettype($empty))
	{
		//caculating quantity to update to
		$new_quantity = $entry[0]['quantity'] + $_POST['amount'];

		// updating part' quantity in database
		sql_update('UPDATE warehouse_parts SET quantity = ? WHERE part_num = ?', [$new_quantity,$_POST['part_num']]);

		echo '<h3>Inventory updated successfully</h3>';

		$part = legacy_sql_query('SELECT number, description FROM parts WHERE number = ?', [$_POST['part_num']]);

		//printing out information about updated part
		echo '<h3>Part Number: ';
		echo $part[0]['number'];
		echo '<br>';

		echo 'Part Description: ';
		echo $part[0]['description'];
		echo '<br>';

		echo 'Previous Quantity: ';
		echo $entry[0]['quantity'];
		echo '<br>';

		echo 'New Quantity: ';
		echo $new_quantity; 
		echo '</h3>';
	}
	else // if not a valid part number, desplay error message
	{
		echo '<h3>An error has occured, inventory had not been updated. </br>Please try again.</h3>';
	}
}

?>


</html>
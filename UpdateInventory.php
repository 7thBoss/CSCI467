<!DOCTYPE html>
<html>

<p> This will update the inventory </p>

<?php

include 'functions.php';

    echo '<p>Part Number: ';
	echo $_POST['part_num'];
	echo '</p>';

//First checking if need to update the inventory
if(gettype($_POST['part_num']) != gettype($empty))  //using $empty as an unintelsed varable to get the NULL type
{

	$entry = sql_select('SELECT quantity, onhand FROM warehouse_parts WHERE part_num = ?', [$_POST['part_num']]);

	// if part_num is a valid part number, update both onhand and quantity of said part
	if(gettype($entry[0]['quantity']) != gettype($empty))
	{
		$new_onhand = $entry[0]['onhand'] + $_POST['amount'];
		$new_quantity = $entry[0]['quantity'] + $_POST['amount'];

		sql_update('UPDATE warehouse_parts SET quantity = ?, onhand = ? WHERE part_num = ?', [$new_quantity, $new_onhand,$_POST['part_num']]);

		echo '<p>inventory updated successfully</p>';
	}
	else // if not a valid part number, desplay error message
	{
		echo '<p>An error has occured, inventory had not been updated. </br>Please try again.</p>';
	}
}

//Here is form to input part number or description and amount to be added to inventory
echo '<form method="POST">';
// selecting part by number or discription
echo '<input list="part_num" name="part_num" required>';
echo '<datalist id="part_num">';

$entry2 = legacy_sql_query('SELECT number, description FROM parts',[]);
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

//Field to enter in amount
echo '<input type="number" id="amount" name="amount" min="1" required>';

//submit button 
echo '<input type="submit">';

//end of form
echo '</form>';

echo '<p>Select part by entering part number or description</br>';
echo 'Enter amount of selected part to be added to inventory</br>';
echo 'Submit to update inventory</p>';

?>


</html>
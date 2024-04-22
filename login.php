<!DOCTYPE html>
<?php include "functions.php"; ?>
<html>
	<table>
		<tr><td>
			<?php echo "<form action='".$url."/customer_login.php' method='POST'>"; ?>
				<input type='submit' value='Customer Login'>
			</form>
		</td></tr>
		<tr><td>
			<?php echo "<form action='".$url."/packinglist.php' method='POST'>"; ?>
				<input type='submit' value='Packer Login'>
			</form>
		</td></tr>
		<tr><td>
			<?php echo "<form action='".$url."/AdminConsoleInterface.php' method='POST'>"; ?>
				<input type='submit' value='Reciever Login'>
			</form>
		</td></tr>
		<tr><td>
			<?php echo "<form action='".$url."/' method='POST'>"; ?>
				<input type='submit' value='Admin Login'>
			</form>
		</td></tr>
	</table>
</html>

<?php
	session_start();
	
	$product_id = $_POST['product_id'];
	
	if( !isset($_SESSION['cart_items'] )  )
	{									
		echo("<p> Error: Cannot find the Cart </p>");
	}
	
	else{
		/* Remove the selected Item from the Cart */
		unset($_SESSION['cart_items'][$product_id]);
		echo("<p> Success </p>");
	}
?>
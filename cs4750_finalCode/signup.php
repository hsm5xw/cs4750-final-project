<?php
	session_start();
?>
<meta charset="utf-8" />
<?php
	
	$customer_id = $_POST['customer_id'];
	$password = $_POST['password'];
	$password_confirm = $_POST['password_confirm'];
	$first_name = $_POST['first_name'];
	$last_name = $_POST['last_name'];
	
	$street_addr = $_POST['street_addr'];
	$city = $_POST['city'];
	$state = $_POST['state'];
	$zip_code = $_POST['zip_code'];
	$phone_number = $_POST['phone_number'];
	
	$inputsArr = array($customer_id, $password, $password_confirm, $first_name, $last_name,
					$street_addr, $city, $state, $zip_code, $phone_number);

	/* Input sanity checks */
	foreach($inputsArr as $input)
	{
		/*
		echo("<script type='text/javascript'>
					window.alert('Input: $input');
			 </script>");
		*/	
		if( empty($input) )
		{
			echo("<script type='text/javascript'>
					window.alert('Empty Input: $input');
					window.location.href = 'signup_form.php';
			 </script>");
			exit;
		}
	}										

	include_once('login/librarya.php');
	$db_connection = new mysqli($SERVER, $USER, $PASS, $DATABASE);
	
	if (mysqli_connect_errno()) {
		printf("Can't connect to MySQL Server. Error code: %s\n", mysqli_connect_error());
		return null;
	}

	/* Check to see if there is a pre-existing user with the specified user ID */
	$stmt =$db_connection->stmt_init();
	$numRecords = 0;
	$result_array = array();
	
	if( $stmt->prepare("SELECT * FROM customer WHERE customer_id = ?") )
	{
		$stmt->bind_param("s", $customer_id);
		$stmt->execute();
		$stmt->bind_result( $queried_customer_id, $queried_password, $queried_first_name, $queried_last_name, $queried_phone_number );
			
		while($stmt->fetch())
		{
			$numRecords = $numRecords + 1;
		}
		$stmt->close();
	}
	
	//echo "numRecords: " . $numRecords . "";
	
	if($numRecords >= 1)
	{
		/* close DB connection */
		$db_connection->close();
	
		echo("<script>
					window.alert('Another user already exists in the system with the same user ID. \\nPlease enter a different one.');
					window.history.back();
			  </script>
			");
		exit;
	}	

	$transaction_result = TRUE;
	
	try{
		/* Begin Transaction for storing the customer's sign-up information */
		$db_connection->autocommit(FALSE);
			
		$customer_stmt =$db_connection->stmt_init();
		if( $customer_stmt->prepare("INSERT INTO customer(customer_id, password, first_name, last_name, phone_number) VALUES (?,?,?,?,?)") 
				or die("<br/> Error Building Query ! <br/>" . mysqli_error($db_connection)  )  )
		{
			$customer_stmt->bind_param("sssss", $customer_id, $password, $first_name, $last_name, $phone_number );
			$customer_stmt->execute();
			$customer_stmt->fetch();
			$customer_stmt->close();
		}
	
		$address_stmt =$db_connection->stmt_init();
		if( $address_stmt->prepare("INSERT INTO address(phone_number, street_addr, city, zip_code) VALUES (?,?,?,?)") 
				or die("<br/> Error Building Query ! <br/>" . mysqli_error($db_connection)  )  )
		{
			$address_stmt->bind_param("ssss", $phone_number, $street_addr, $city, $zip_code );
			$address_stmt->execute();
			$address_stmt->fetch();
			$address_stmt->close();
		}
	
		$region_stmt =$db_connection->stmt_init();
		if( $region_stmt->prepare("INSERT INTO region(zip_code, state) VALUES (?,?)") 
				or die("<br/> Error Building Query ! <br/>" . mysqli_error($db_connection)  )  )
		{
			$region_stmt->bind_param("ss", $zip_code, $state);
			$region_stmt->execute();
			$region_stmt->fetch();
			$region_stmt->close();
		}
		/* Commit results */	
		if(!$db_connection->commit())
		{	
			echo("<script>
					window.alert('Transaction commit failed.');
					window.location.href = 'index.php';
			</script>
		");
			exit();
		} 			
		$db_connection->autocommit(TRUE); 	/* End Transaction */
	}
	catch(Exception $e)
	{
		$transaction_result = FALSE;	
		$db_connection->rollback(); 		/* Roll back */
		$db_connection->autocommit(TRUE); 	/* End Transaction */
		$db_connection->close();			/* close DB connection */
		echo 'Caught exception: ', $e->getMessage(), "\n";
	}

	/* close DB connection */
	$db_connection->close();
	
	if( $transaction_result == TRUE){	
		/* Valid sign-up registration */
		$_SESSION['customer_id'] = $customer_id;
		
		$cart_items = array();
		$_SESSION['cart_items'] = $cart_items;
		
		echo("<script>
					window.alert('Thank you for signing-up! \\nYour account has been created.');
					window.location.href = 'index.php';
			</script>
		");
	}
	else
	{
		echo("<script>
				window.alert('An error occurred during the sign-up trasaction.');
				window.location.href = 'signup_form.php';
			</script>
		");
	}
						
?>
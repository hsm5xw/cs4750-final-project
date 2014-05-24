<?php
	session_start();
?>
<meta charset="utf-8" />
<?php
	$customer_id = $_POST['customer_id'];
	$password = $_POST['password'];
	
	if( empty($customer_id) || empty($password) )
	{
		echo("<script>
				window.alert('please enter user ID and password');
				window.location.href = 'login_form.php';
		     </script>");
		exit;
	}
	
	include_once('login/librarya.php');
	$db_connection = new mysqli($SERVER, $USER, $PASS, $DATABASE);
	
	if(mysqli_connect_errno())
	{
		echo("Can't connect to MySQL Server. Error code: " . mysqli_connect_error());
		return null;
	}
	
	$stmt =$db_connection->stmt_init();
	$numRecords = 0;
	
	if( $stmt->prepare("SELECT * FROM customer WHERE customer_id = ? AND password = ?") )
	{
		$stmt->bind_param("ss", $customer_id, $password);
		$stmt->execute();
		$stmt->bind_result( $queried_customer_id, $queried_password, $queried_first_name, $queried_last_name, $queried_phone_number );
			
		while($stmt->fetch())
		{
			$numRecords = $numRecords + 1;
		}
		$stmt->close();
	}
	
	//echo "numRecords: " . $numRecords . "";
	
	if($numRecords == 0 || $numRecords > 1)
	{
		/* close DB connection */
		$db_connection->close();
		
		echo("<script>
					window.alert('No account matched with the entered user ID and password');
					window.location.href = 'login_form.php';
			  </script>
			");
		
	}	
	else if($numRecords == 1)
	{	
		/* Valid customer_id and password */
		$_SESSION['customer_id'] = $customer_id;
		
		$cart_items = array();
		$_SESSION['cart_items'] = $cart_items;
				
		echo("<script>
				window.alert('Log-in Success: Welcome Back !');
				window.location.href = 'index.php';
			</script>"
			);
	}
	
	/* close DB connection */
	$db_connection->close();
		
?>

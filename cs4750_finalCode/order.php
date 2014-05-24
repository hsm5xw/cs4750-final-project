<?php
	session_start();
?>
<meta charset="utf-8" />
<?php
	
	$customer_id = "";
	if( !isset($_SESSION['customer_id']) )
	{
		echo("<script>
					alert('Please sign-in to place an order');
					window.location.href = 'login_form.php';
			</script>");
			exit;
	}
	else
	{
		$customer_id = $_SESSION['customer_id'];
	}
	
	$cart_items = "";
	$cart_items_size = 0;
	
	if( !isset($_SESSION['cart_items']) )
	{
		echo("<script>
					alert('The cart is empty!');
					window.location.href = 'cart.php';
			</script>");
			exit;
	}
	else
	{
		$cart_items = $_SESSION['cart_items'];
		$cart_items_size = sizeof($cart_items);
	}
	
	/* Get Payment Information from the order form */
	
	$card_number = "";
	$card_type = "";
	$card_csv = "";
	$total_order_price = 0.0;
	
	if( !isset($_POST['card_number']))
	{
		echo("<script>
				alert('Error: Card number not available');
			</script>");
		exit;
	}
	else
	{
		$card_number = $_POST['card_number'];
		echo('<p> Card number: ' . $card_number . '</p>');
	}
	if( !isset($_POST['card_type']))
	{
		echo("<script>
				alert('Error: Card Type not available');
			</script>");
		exit;
	}
	else
	{
		$card_type = $_POST['card_type'];
		echo('<p> Card Type: ' . $card_type . '</p>');
	}
	if( !isset($_POST['card_csv']))
	{
		echo("<script>
				alert('Error: Card csv not available');
			</script>");
		exit;
	}
	else
	{
		$card_csv = $_POST['card_csv'];	
		echo('<p> Card csv: ' . $card_csv . '</p>');
	}
	
	if( !isset($_POST['price_pass']))
	{
		echo("<script>
				alert('Error: Total order price not available');
			</script>");
		exit;
	}
	else
	{
		$total_order_price = $_POST['price_pass'];	
		echo('<p> Total order price: ' . $total_order_price . '</p>');
		
		if($total_order_price == 0.0)
		{
			echo('<script> 
						alert("Error: total price cannot be 0");
						window.location.href = "cart.php";						
				</script>');
			exit;
		}
	}
		
	include_once('login/libraryb.php');
	$db_connection = new mysqli($SERVER, $USER, $PASS, $DATABASE);
	
	if (mysqli_connect_errno()) {
		printf("Can't connect to MySQL Server. Error code: %s\n", mysqli_connect_error());
		return null;
	}
		
	$transaction_result = TRUE;
	
	try{
		/* Begin Transaction to place an order */
		$db_connection->autocommit(FALSE);

		/* Check the availability of each item first */
		reset($cart_items);		
		while ( list($key, $value) = each($cart_items) )
		{
			$stmt =$db_connection->stmt_init();
			$numRecords = 0;	
			$results = array();
								
			/* SELECT SQL Query */
			if( $stmt->prepare("SELECT product_id, amount_left FROM product WHERE product_id = ?") )
			{
				$stmt->bind_param("s", $key);
				$stmt->execute();
				$stmt->bind_result( $q_product_id, $q_amount_left );
			
				while( $stmt->fetch() )
				{
					$results[$numRecords]['product_id'] = $q_product_id;
					$results[$numRecords]['amount_left'] = $q_amount_left;
					
					$numRecords = $numRecords + 1;
				}
				$stmt->close();
			}
																
			if( ($numRecords != 1) || ($key !== $results[0]['product_id'] ) )
			{
				echo("<script>
							alert('Error: numRecords is 0 or greater than 1');
					</script>");
				exit;
			}
								
			else
			{
				//echo('<p> Num Records: ' . $numRecords . '</p> <br/>');
								
				/* Each row for the table */
				echo('<p>' . $key . '=>' . $value . '</p>');
				echo('<p> Key : ' . $results[0]['product_id'] . '</p>');
				echo('<p> Amount Left : ' . $results[0]['amount_left'] . '</p> <br/>');	

				/* If the order amount if greater than the amount left */ 
				if($value > $results[0]['amount_left'] )
				{
					echo("<script>
							window.alert('There are not enough products [" . $key  ."] in stock !');
							window.location.href = 'cart.php';
						</script>
					");
					exit;
				}	
			}
		} 	/* End of checking the avilability */
		
		/*FIRST, GET NEXT AVAILABLE LIST ID*/
		
		$nextListId = -1;
		
		if( $result = $db_connection->query("SELECT MAX(list_id) AS max_id FROM product_list") )
		{
			echo('<p> Num Rows '. $result->num_rows .  '</p>');
			
			while( $row = $result->fetch_assoc() )
			{
				print_r($row);
				echo('<p>' . $row['max_id']  . '</p>');
				$nextListId = $row['max_id'] + 1;
			}
			
			$result->close();
		}
		
		if($nextListId == -1)
		{
			echo("<script>
							window.alert('Error retrieving the Next Product List ID !');
					</script>
				");
			exit;
		}
		echo('<p> Next List ID: ' . $nextListId  . '</p>');

				
		$paymentMethod_insert_stmt =$db_connection->stmt_init();
			
		if( $paymentMethod_insert_stmt->prepare("INSERT INTO payment_method(credit_card_num, credit_card_type) VALUES (?,?)") 
				or die("<br/> Error Building Query ! <br/>" . mysqli_error($db_connection)  )  )
		{
			$paymentMethod_insert_stmt->bind_param("ss", $card_number, $card_type);
			$paymentMethod_insert_stmt->execute();
			$paymentMethod_insert_stmt->fetch();
			$paymentMethod_insert_stmt->close();
		}
		
		$creditCard_insert_stmt = $db_connection->stmt_init();
		if( $creditCard_insert_stmt->prepare("INSERT INTO credit_card(credit_card_num, credit_card_type, csv) VALUES (?,?,?)") 
				or die("<br/> Error Building Query ! <br/>" . mysqli_error($db_connection)  )  )
		{
			$creditCard_insert_stmt->bind_param("sss", $card_number, $card_type, $card_csv);
			$creditCard_insert_stmt->execute();
			$creditCard_insert_stmt->fetch();
			$creditCard_insert_stmt->close();
		}
		
		/* Insert items into the product list */
		reset($cart_items);		
		while ( list($key, $value) = each($cart_items) )
		{
			$insert_list_stmt =$db_connection->stmt_init();
			
			if( $insert_list_stmt->prepare("INSERT INTO product_list(list_id, product_id, amount)  VALUES (?,?,?)") 
				or die("<br/> Error Building Query ! <br/>" . mysqli_error($db_connection)  )  )
			{
				$insert_list_stmt->bind_param("sss", $nextListId, $key , $value );
				$insert_list_stmt->execute();
				$insert_list_stmt->fetch();
				$insert_list_stmt->close();
			}
			
		} 	/* End of inserting items into Product List */
		
		/* Get the payment_method_id */
		
		$pid_stmt =$db_connection->stmt_init();
		$pid_numRecords = 0;	
		$pid_results = array();
								
		/* SELECT SQL Query */
		if( $pid_stmt->prepare("SELECT payment_method_id FROM payment_method WHERE credit_card_num = ? AND credit_card_type = ?") )
		{
			$pid_stmt->bind_param("ss", $card_number, $card_type);
			$pid_stmt->execute();
			$pid_stmt->bind_result( $q_payment_method_id );
			
			$pid_stmt->fetch();
				$pid_results[$pid_numRecords]['payment_method_id'] = $q_payment_method_id;	
				echo('<p> each Payment ID' . $pid_results[$pid_numRecords]['payment_method_id']  .  '</p>');
				
				$pid_numRecords = $pid_numRecords + 1;
			$pid_stmt->close();
		}
		
		echo('<p> Num PIDs: ' . $pid_numRecords . ' </p>');
																
		if( $pid_numRecords != 1 )
		{
			echo("<script>
						alert('Error: pid_numRecords is 0 or greater than 1');
				</script>");
			exit;
		}	
			
		/* Insert orders */
		$order_date = date("Y-m-d");
		$order_status = "Not shipped yet";
		$payment_method_id = $q_payment_method_id;

		reset($cart_items);		

		$order_stmt =$db_connection->stmt_init();
			
		if( $order_stmt->prepare("INSERT INTO orders(customer_id, list_id, order_date, total_order_price, order_status, payment_method_id)  VALUES (?,?,?,?,?,?)") 
				or die("<br/> Error Building Query ! <br/>" . mysqli_error($db_connection)  )  )
		{
			$order_stmt->bind_param("ssssss", $customer_id, $nextListId , $order_date, $total_order_price, $order_status, $payment_method_id );
			$order_stmt->execute();
			$order_stmt->fetch();
			$order_stmt->close();
		}
			
		 // End of inserting items into Product List 
		
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
		$db_connection->autocommit(TRUE); 	// End Transaction 
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
		/* Valid order transaction */
			
		echo("<script>
				window.alert('Transaction Success !');
				window.location.href = 'index.php';
			</script>
		");	
			
		// de-allocate the cart items Session variable !!!!
		unset( $_SESSION['cart_items'] );
		$cart_items2 = array(); // allocate an empty cart now
		$_SESSION['cart_items'] = $cart_items2;
		
	}
	else
	{
		echo("<script>
				window.alert('An error occurred during the transaction.');
				window.location.href = 'cart.php';
			</script>
		");
	}
						
?>
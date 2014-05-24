<?php
	session_start();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <title> CS 4750 Project </title>

    <!-- Bootstrap core CSS -->
    <link href="bootstrap-3.0.2-dist/dist/css/bootstrap.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="custom/css/navbar-fixed-top.css" rel="stylesheet">

	<!-- Check Search input -->
	<script>
		function check_searchInput()
		{
			var form = document.search_form;
			var searchKey = form['searchKey'].value;
			
			if(!searchKey)
			{
				alert("Please enter a non-empty search key");
				form['searchKey'].focus();
				return false;
			}
		}
	</script>
	
	<!-- Custom Script -->
	<script>
		// Input checking	
		function check_input()
		{
			var form = document.submitOrder_form;
		
			var inputs = {
				'card_number': 'Credit Cart Number',
				'card_type': 'Card Type (For example, Visa)',
				'card_csv': 'CSV number'
			}		
			var input;
			// Check for empty inputs first 
			for(item in inputs)
			{
				input = form[item];		
				if(!input.value)
				{
					alert('Please enter ' + inputs[item]);
					input.focus();
					return false;
				}
			}	
				
		}	
	</script>
	
	<script>
	function SubmitHandler(total_price)
	{
		$valid_inputs = check_input();
		
		if($valid_inputs == true)
		{	document.forms["submitOrder_form"].price_pass.value = total_price;
			document.submitOrder_form.submit();
		}
	}	
	</script>
	
	
  </head>

  <body>

    <!-- Fixed navbar -->
	<?php include "./top-navbar.php"; ?>
	<!-- End of Nav Bar -->

	<div class="container">

		<?php
			$customer_id = "";
		
			if( !isset($_SESSION['customer_id']) )
			{
				echo("<script>
						 alert('Please sign-in to leave a product review');
						 window.location.href = 'login_form.php';
					</script>");
				exit;
			}
			else
			{
				$customer_id = $_SESSION['customer_id'];
			}
			
			/* DB Connection to get Customer Information, such as Billing Address */
			include_once('login/libraryb.php');
			$db_connection = new mysqli($SERVER, $USER, $PASS, $DATABASE);
	
			if(mysqli_connect_errno())
			{
				echo("Can't connect to MySQL Server. Error code: " . mysqli_connect_error());
				return null;
			}
	
			$stmt =$db_connection->stmt_init();
			$numRecords = 0;
		
			$results = array();
	
			if( $stmt->prepare("SELECT first_name, last_name, street_addr, city, zip_code, state FROM customer NATURAL JOIN address NATURAL JOIN region WHERE customer_id = ?") )
			{
				$stmt->bind_param("s", $customer_id);
				$stmt->execute();
				$stmt->bind_result( $q_first_name, $q_last_name, $q_street_addr, $q_city, $q_zip_code, $q_state );
			
				while( $stmt->fetch() )
				{
					$results[$numRecords]['first_name'] = $q_first_name;
					$results[$numRecords]['last_name'] = $q_last_name;
					$results[$numRecords]['street_addr'] = $q_street_addr;
					$results[$numRecords]['city'] = $q_city;
					$results[$numRecords]['zip_code'] = $q_zip_code;
					$results[$numRecords]['state'] = $q_state;
				
					$numRecords = $numRecords + 1;
				}
				$stmt->close();
			}
				
			if($numRecords != 1)
			{
				/* close DB connection */
				$db_connection->close();
				echo('<script> Error: Retrieving customer information </script>');
				exit;
			}
			
			$cart_items = $_SESSION['cart_items'];
			$cart_items_size = sizeof($cart_items);
			//echo('<h2> Cart Item Size: '. $cart_items_size . '</h2> <br/>');
							
			$total_price = 0.0;
								
			if($cart_items_size == 0)
			{
				echo('<script> 
								alert("No Items Added on the Cart");
								window.location.href = "cart.php";								
					 </script>');
			}
			else
			{
				reset($cart_items);		
				while ( list($key, $value) = each($cart_items) )
				{
					$price_stmt =$db_connection->stmt_init();
					$price_Records = 0;	
					$price_results = array();
								
					/* SELECT SQL Query */
					if( $price_stmt->prepare("SELECT product_id, price FROM product NATURAL JOIN prices WHERE product_id = ?") )
					{
						$price_stmt->bind_param("s", $key);
						$price_stmt->execute();
						$price_stmt->bind_result( $q_product_id, $q_price );
			
						while( $price_stmt->fetch() )
						{
							$price_results[$price_Records]['product_id'] = $q_product_id;
							$price_results[$price_Records]['price'] = $q_price;
					
							$price_Records = $price_Records + 1;
						}
						$price_stmt->close();
					}
																
					if( ($price_Records != 1) || ($key !== $price_results[0]['product_id'] ) )
					{
						echo("<script>
								alert('Error: numRecords is 0 or greater than 1');
							</script>");
					}						
					else
					{	
						/* Each row for the table */
						//echo('<p>' . $key . '=>' . $value . '</p> <br/>');
						//echo('<p> Key : ' . $price_results[0]['product_id'] . '</p> <br/>');
						//echo('<p> Corresponding : ' . number_format( (float)$price_results[0]['price'], 2, '.', '' ) . '</p> <br/>');
						
						$total_price = $total_price + $price_results[0]['price'] * $value;				
					}
				}
			}
					
			echo('
			<div class="row">
				<div class="span12">
					<h1> Order Form </h1> <br/>
					<div class="hero-unit">
						<!-- Form -->
						<form class="form-horizontal" role="form" id="submitOrder_form" name="submitOrder_form" method="post" onsubmit="return check_input()" action="order.php" >
							
							<div class="form-group">
								<div> <h2> <span class="glyphicon glyphicon-home"></span> Ship To </h2> </div> <br/>
							</div>
							
							<!-- user ID -->
							<div class="form-group">
								<label for="customer_id" class="col-sm-2 control-label"> User ID </label>
								<div class="col-xs-2"> <p class="form-control">' . $customer_id . '</p> </div>
							</div>
							
							<!-- Name -->
							<div class="form-group">
								<label for="customer_name" class="col-sm-2 control-label"> Name </label>
								<div class="col-xs-2"> <p class="form-control">' . $results[0]['first_name'] . ' ' . $results[0]['last_name'] . '</p> </div>
							</div>
							
							<!-- Address -->
							<div class="form-group">
								<label for="street_address" class="col-sm-2 control-label"> Address </label>
								<div class="col-xs-2"> <p class="form-control">' . $results[0]['street_addr'] . '</p> </div>
							</div>
							
							<div class="form-group">
								<label for="address2" class="col-sm-2 control-label"> </label>
								<div class="col-xs-2"> <p class="form-control">' . $results[0]['city'] . ', ' . $results[0]['state'] . ' &nbsp' . $results[0]['zip_code'] .'</p> </div>
							</div>
		
							<br />
							
							<!-- Billing Information -->
							
							<div class="form-group">
								<div> <h2> <span class="glyphicon glyphicon-usd"></span> Billing Information </h2> </div> <br/>
							</div>
							
							<!-- Credit Card Number -->
							<div class="form-group">
								<label for="card_number" class="col-sm-2 control-label"> Card Number </label>
								<div class="col-xs-3"> <input type="text" class="form-control"  id="card_number" name="card_number" placeholder="Please enter Credit Card Number" autocomplete="off"> </div>
							</div>
							
							<!-- Credit Card Type -->
							<div class="form-group">
								<label for="card_type" class="col-sm-2 control-label"> Card Type </label>
								<div class="col-xs-3"> <input type="text" class="form-control"  id="card_type" name="card_type" placeholder="Visa, Master Card, etc." autocomplete="off"> </div>
							</div>
							
							<!-- Credit Card Type -->
							<div class="form-group">
								<label for="card_csv" class="col-sm-2 control-label"> CSV Nunber </label>
								<div class="col-xs-1"> <input type="text" class="form-control"  id="card_csv" name="card_csv"> </div>
							</div>								
							<br />

							<div class="form-group">
								<div> <input type="hidden" id="price_pass" name="price_pass" value="'  . number_format( (float)$total_price, 2, '.', '' ) .  '"/> </div>
								<div> <h2> <span class="glyphicon glyphicon-usd"></span> Total Price: ' . number_format( (float)$total_price, 2, '.', '' ) .  '</h2> </div> <br/>
							</div>														
							<br />							
														
							<!-- Submit Button -->
							<div class="form-group">
								<div class="col-sm-offset-2 col-sm-10">
									<button class="btn btn-success btn-lg" type="submit" id="submit_btn"> Submit </button>
								</div>
							</div>
				
						</form>	         
					</div>
				</div> <!-- End of span 12 -->			
			</div> <!-- End of row -->
			');
			
			/* close DB connection */
			$db_connection->close();
		?>	
		
    </div> <!-- End of container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="bootstrap-3.0.2-dist/dist/js/bootstrap.min.js"></script>
  </body>
</html>

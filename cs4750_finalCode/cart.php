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
	<link href="custom/css/cart.css" rel="stylesheet"> 
	
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
	
	<!-- Custom script -->
	<script>
	
		function CancleHandler(prod_id)
		{		
			alert(prod_id);
			document.forms["delete_form"].delete_pass.value = prod_id;
		
			$.ajax({
					type: 'POST',
					url: 'cart-delete.php',
					data: {product_id: $("#delete_pass").val()  },
					
					dataType: 'html',
					success: function(data)
					{
						$('#delete_result').html(data);
						alert("Success: Deleted the Selected Item from Cart");
						window.location.href = 'cart.php';
					}
				});
		}
		

	</script>
		
	<?php
			if( !isset($_SESSION['customer_id']) )
			{
				echo("<script>
						 alert('Please sign-in to have access to the cart');
						 window.location.href = 'login_form.php';
					</script>");
				exit;
			}
	?>
	
  </head>

  <body>

    <!-- Fixed navbar -->
	<?php include "./top-navbar.php"; ?>
	<!-- End of Nav Bar -->

    <div class="container">
		<div class="row">
			<div class="span12">
				<h1> Cart </h1> <br/>
				<div class="hero-unit">
				<?php							 
					function printExistingCart()
					{
						$cart_items = $_SESSION['cart_items'];
						$cart_items_size = sizeof($cart_items);
						echo('<h2> Cart Item Size: '. $cart_items_size . '</h2> <br/>');
							
						//echo(' <h2> Print Existing Cart </h2>');
								
						if($cart_items_size == 0)
						{
							echo('<h3> No Items Added on the Cart </h3>');
						}
						else
						{
							/* Start the table */
							echo('<table class="table table-striped">
									<!-- Heading -->
									<tr class="success">
										<th> Product ID </th>
										<th> Product Name </th>								
										<th> Vendor Name </th>
										<th> Quantity </th>
										<th> Product Image </th>
										<th> Cancel </th>
									</tr>');
							
							include_once('login/libraryb.php');
							$db_connection = new mysqli($SERVER, $USER, $PASS, $DATABASE);
	
							if(mysqli_connect_errno())
							{
								echo("Can't connect to MySQL Server. Error code: " . mysqli_connect_error());
								return null;
							}
							
							reset($cart_items);		
							while ( list($key, $value) = each($cart_items) )
							{
								$stmt =$db_connection->stmt_init();
								$numRecords = 0;	
								$results = array();
								
								/* SELECT SQL Query */
								if( $stmt->prepare("SELECT product_id, product_name, vendor_name, product_url FROM product WHERE product_id = ?") )
								{
									$stmt->bind_param("s", $key);
									$stmt->execute();
									$stmt->bind_result( $q_product_id, $q_product_name, $q_vendor_name, $q_product_url);
			
									while( $stmt->fetch() )
									{
										$results[$numRecords]['product_id'] = $q_product_id;
										$results[$numRecords]['product_name'] = $q_product_name;
										$results[$numRecords]['vendor_name'] = $q_vendor_name;
										$results[$numRecords]['product_url'] = $q_product_url;
					
										$numRecords = $numRecords + 1;
									}
									$stmt->close();
								}
								
								//echo('<p> Key : ' . $key . '</p> <br/>');
								//echo('<p> Corresponding : ' . $results[0]['product_id'] . '</p> <br/>');
								
								if( ($numRecords != 1) || ($key !== $results[0]['product_id'] ) )
								{
									echo("<script>
										alert('Error: numRecords is 0 or greater than 1');
									</script>");
								}
								
								else
								{
									//echo('<p> Num Records: ' . $numRecords . '</p> <br/>');
								
									/* Each row for the table */
									//echo('<p>' . $key . '=>' . $value . '</p> <br/>');
									echo('<tr>
											<th class="col-md-2">' . $results[0]['product_id'] . '</th>
											<th class="col-md-2">' . $results[0]['product_name']   . '</th>								
											<th class="col-md-3">' . $results[0]['vendor_name']   . '</th>
											<th class="col-md-2">' . $value. '</th>
											<th class="col-md-3"> <img class="img-responsive cart-image" src=' . $results[0]['product_url'] . ' alt="product image"></th>
											<th class="col-md-1"> <a onclick="return CancleHandler(\'' . $results[0]['product_id'] . '\')">  <span class="glyphicon glyphicon-remove"> </span>  </a> </th>
										</tr>');							
								}
							}
							echo('</table>'); // close the table
							
							/* Delete the item on the Cart upon clicking the Cancel Button */
							echo('<form id="delete_form" name="delete_form" method="post" action="index.php">
										<input type="hidden" id="delete_pass" name="delete_pass" />
										<p id="delete_result" name="delete_result"> </p>
								</form>');
					
							/* Submit Button */
							echo('<form class="form-margin form-signin" role="form" action="order_form.php">
										<p class="form-signup-heading"> Place an order </p>
										<button class="btn btn-success btn-block" type="submit"> Submit </button>
								</form>');								
						}
						
					}
					/* End of Function Declarations */ 
								
					$product_id = "";
					$amount = "";
					$cart_items = $_SESSION['cart_items'];
					
					if( !isset($_POST['product_to_cart']) ||!isset($_POST['quantity']) )
					{									
						printExistingCart();
					}
					/* Add new items to the cart */
					else
					{ 
						$product_id = $_POST['product_to_cart'];
						echo '<script> alert(\'' . $product_id . '\'); </script>';
						$amount = $_POST['quantity'];
						echo '<script> alert(\'' . $amount . '\'); </script>';
						//echo '<h2>' . $product_id . '</h2>';
						//echo '<h2>' . $amount . '</h2>';
						
						/* Add the Item to the Cart */
						$_SESSION['cart_items'][$product_id] = $amount; 
												
						/* Label to show that the item has been added */
						echo('<div class="alert alert-info">
									<a href="#" class="alert-link"> Item Added: ' . $product_id . ' &nbsp &nbsp &nbsp Amount: ' . $amount . '</a>
							  </div>');
						
						printExistingCart();
					}	// end of else				
				?>
				</div>  <!-- End of hero-unit -->	
			</div> <!-- End of span 12 -->			
		</div> <!-- End of row -->
    </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="bootstrap-3.0.2-dist/dist/js/bootstrap.min.js"></script>
  </body>
</html>

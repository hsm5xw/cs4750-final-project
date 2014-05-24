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
					<h1> Order History </h1> <br/>
					<div class="hero-unit">				
					<?php					
						$customer_id = "";
		
						if( !isset($_SESSION['customer_id']) )
						{
							echo("<script>
							alert('Please sign-in to have access to the cart');
							window.location.href = 'login_form.php';
							</script>");
							exit;
						}
						else
						{
							$customer_id = $_SESSION['customer_id'];
						}
					
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
						
						if( $stmt->prepare("SELECT order_id,order_date,product_name,amount,total_order_price,order_status FROM 
											order_history NATURAL JOIN orders NATURAL JOIN product_list NATURAL JOIN product WHERE customer_id = ?") )
						{
							$stmt->bind_param("s", $customer_id);
							$stmt->execute();
							$stmt->bind_result( $q_order_id, $q_order_date, $q_product_name, $q_amount, $q_total_order_price, $q_order_status );
			
							while( $stmt->fetch() )
							{
								$results[$numRecords]['order_id'] = $q_order_id;
								$results[$numRecords]['order_date'] = $q_order_date;
								$results[$numRecords]['product_name'] = $q_product_name;
								$results[$numRecords]['amount'] = $q_amount;
								$results[$numRecords]['total_order_price'] = $q_total_order_price;
								$results[$numRecords]['order_status'] = $q_order_status;

								$numRecords = $numRecords + 1;
							}
							$stmt->close();
						}
			
						if($numRecords == 0)
						{
							echo('<h3> No order history exists </h3>');
							echo('<table class="table table-striped">
									<!-- Heading -->
									<tr class="success">
										<th> Order id </th>
										<th> Order date </th>		
										<th> Product name </th>
										<th> Amount </th>	
										<th> Total order price </th>
										<th> Order Status </th>
									</tr>
								  </table>');
							exit;
						}
			
						//echo '<script> alert(\'' . $numRecords . '\'); </script>';
						
						echo('<table class="table table-striped">
								<!-- Heading -->
								<tr class="success">
									<th> Order id </th>
									<th> Order date </th>		
									<th> Product name </th>
									<th> Amount </th>	
									<th> Total order price </th>
									<th> Order Status </th>
								</tr>');
								
						$prev_order_id = $results[0]['order_id'];
						$prev_product_name = $results[0]['product_name'];
						$prev_order_date = $results[0]['order_date'];
						$prev_total_order_price = $results[0]['total_order_price'];
						$prev_order_status = $results[0]['order_status'];
						
						/* Loop through each order history */
						for($i= 0; $i < $numRecords; $i++)
						{
							if( ($i >= 1) && ($prev_order_id === $results[$i]['order_id']) && ($prev_product_name !== $results[$i]['product_name']) )
							{
								echo('<tr>
										<td>' . ''. '</td>
										<td>' . ''. '</td>
										<td>' . $results[$i]['product_name']. '</td>				
										<td>' . $results[$i]['amount']. '</td>
										<td>' . ''. '</td>
										<td>' . ''. '</td>
									</tr>');
							}
							else{
								echo('<tr>
										<td>' . $results[$i]['order_id'] . '</td>
										<td>' . $results[$i]['order_date']. '</td>
										<td>' . $results[$i]['product_name']. '</td>				
										<td>' . $results[$i]['amount']. '</td>
										<td>' . number_format( (float)$results[$i]['total_order_price'], 2, '.', '' ). '</td>
										<td>' . $results[$i]['order_status']. '</td>
									</tr>');
							}
							$prev_order_id = $results[$i]['order_id'];
							$prev_product_name = $results[$i]['product_name'];
							$prev_order_date = $results[$i]['order_date'];
							$prev_total_order_price = $results[$i]['total_order_price'];
							$prev_order_status = $results[$i]['order_status'];
						}								
						echo('</table>');
					?>
					</div>
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

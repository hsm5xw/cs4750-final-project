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
    <link href="bootstrap-3.0.2-dist/dist//css/bootstrap.css" rel="stylesheet">
	<!-- Custom styles for this template -->
    <link href="custom/css/navbar-fixed-top.css" rel="stylesheet">
    <!-- Add custom CSS here -->
    <link href="custom/css/product_display_form.css" rel="stylesheet">

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
	
		function isNonNegativeInt(input){
			var intRegex = /^\d+$/;
			if(intRegex.test(input))
				return true;
			else
				return false;
		}
		// Input checking	
		function check_input()
		{
			var form = document.cart_submit_form;	
			var itemQuantity = form['quantity'].value;
			
			if(!itemQuantity)
			{
				alert('Empty Input: please enter enter a whole number greater than 0');
				form['quantity'].focus();
				return false;
			}
			
			var isNonNegativeInteger = isNonNegativeInt(itemQuantity);
				
			if(isNonNegativeInteger)
			{
				if(itemQuantity == 0){
					alert('Zero quantity: Please enter a whole number greater than 0');
					form.quantity.focus();
					return false;
				}
			}
			else{
					alert('Please enter a whole number greater than 0');
					form.quantity.focus();
					return false;
			}
			document.cart_submit_form.submit();
		}	
	</script>
	
	<!-- Custom script -->
	<script>
	
		function WriteHandler(prod_id)
		{		
			//alert(prod_id);
			
			document.forms["write_form"].write_pass.value = prod_id;
			document.write_form.submit();
		}
		
	</script>
	
	

  </head>

  <body>

     <!-- Fixed navbar -->
	<?php include "./top-navbar.php"; ?>
	<!-- End of Nav Bar -->
	
    <div class="container">
		<?php
			function printStarRating($star_rating)
			{
				if( ($star_rating < 1) || ($star_rating > 5) )
				{
					echo('<script> Error: The star rating must be from 1 to 5 </script>');
					exit;
				}
				else
				{
					for($i=0; $i < $star_rating; $i++)
					{
						echo('<span class="glyphicon glyphicon-star"></span>');
					}
					for($i=0; $i < (5- $star_rating); $i++)
					{
						echo('<span class="glyphicon glyphicon-star-empty"></span>');
					}
				}			
			}
			
			function printAvgStarRating($star_rating)
			{
				if( ($star_rating < 1) || ($star_rating > 5) )
				{
					echo('<script> Error: The star rating must be from 1 to 5 </script>');
					exit;
				}
				else
				{
					$star_rating = floor($star_rating);
				
					for($i=0; $i < $star_rating; $i++)
					{
						echo('<span class="glyphicon glyphicon-star"></span>');
					}
					for($i=0; $i < (5- $star_rating); $i++)
					{
						echo('<span class="glyphicon glyphicon-star-empty"></span>');
					}
				}			
			}
			
			/* End of function declarations */
		
			$product_id = "";
		
			if( !isset($_POST['ultimate_pass']) )
			{
				echo("<script>
						 window.location.href = 'search-simple.php';
					</script>");
				exit;
			}
			else
			{
				$product_id = $_POST['ultimate_pass'];
				//echo '<script> alert("good"); </script>';
				//echo($product_id); 
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
			
			if( $stmt->prepare("SELECT product_id, product_name, amount_left, vendor_name, product_description, product_url, price FROM product NATURAL JOIN prices WHERE product_id = ?") )
			{
				$stmt->bind_param("s", $product_id);
				$stmt->execute();
				$stmt->bind_result( $q_product_id, $q_product_name, $q_amount_left, $q_vendor_name, $q_product_description, $q_product_url, $q_price );
			
				while( $stmt->fetch() )
				{
					$results[$numRecords]['product_id'] = $q_product_id;
					$results[$numRecords]['product_name'] = $q_product_name;
					$results[$numRecords]['amount_left'] = $q_amount_left;
					$results[$numRecords]['vendor_name'] = $q_vendor_name;
					$results[$numRecords]['product_description'] = $q_product_description;
					$results[$numRecords]['product_url'] = $q_product_url;
					$results[$numRecords]['price'] = $q_price;
					
					$numRecords = $numRecords + 1;
				}
				$stmt->close();
			}
			
			//echo '<script> alert(\'' . $numRecords . '\'); </script>';
			
			if($numRecords != 1)
			{
				/* close DB connection */
				$db_connection->close();
				echo '<script> alert("Error: The Product does not exist. The number of queries is not 1"); </script>';
				echo("<script>
						 window.location.href = 'search-simple.php';
					  </script>");
				exit;
			}
			
			/* Display Product Information */
			
			echo('
				<div class="row">
					<div class="col-md-9">
						<div class="thumbnail">
							<img class="img-responsive" src=' . $results[0]['product_url'] . ' alt="product image">
					
							<div class="caption-full">
								<h4 class="pull-right"> ' . '$ ' . number_format( (float)$results[0]['price'], 2, '.', '' ) . ' </h4>
								<h4> <a>Product Name: '. $results[0]['product_name'] . ' </a> </h4>
								<p>  Product ID : '. $results[0]['product_id'] . ' </p>
								<p> Vendor name : '. $results[0]['vendor_name'] . ' </p>
								<p> Amount left : '. $results[0]['amount_left'] . ' </p>
								<p> Description : '. $results[0]['product_description'] . ' </p>
							</div> <!-- End of caption-full -->
				');
			
			/* DB Interfacing to Rating starts here */
			
			$review_stmt =$db_connection->stmt_init();
			$numReviews = 0;
			$totalStars = 0.0;
			
			$reviews = array();
			
			if( $review_stmt->prepare("SELECT customer_id, date, star_rating, review_content FROM product_review WHERE product_id = ?") )
			{
				$review_stmt->bind_param("s", $product_id);
				$review_stmt->execute();
				$review_stmt->bind_result( $r_customer_id, $r_date, $r_star_rating, $r_review_content );
			
				while( $review_stmt->fetch() )
				{
					$reviews[$numReviews]['customer_id'] = $r_customer_id;
					$reviews[$numReviews]['date'] = $r_date;
					$reviews[$numReviews]['star_rating'] = $r_star_rating;
					$reviews[$numReviews]['review_content'] = $r_review_content;
		
					$numReviews = $numReviews + 1;
					$totalStars = $totalStars + $r_star_rating;
				}
				$review_stmt->close();
			}
			
			
			/* Display the Average Rating Information */
			
			$avgStars = 0;
			
			if($numReviews != 0)
			{
				$avgStars = $totalStars / $numReviews;
			}
			
			if($numReviews >= 2){
					echo('
							<div class="ratings">
								<p class="pull-right">' . $numReviews . ' Reviews </p>
								<p>');
					
					printAvgStarRating($avgStars);
					
					echo(' &nbsp'
								. number_format( (float)$avgStars, 2, '.', '' ) .
								' stars </p>
							</div> <!-- End of ratings -->
						</div> <!-- End of thumbnail -->
						');
			}
			else{
					echo('
							<div class="ratings">
								<p class="pull-right">' . $numReviews . ' Review </p>
								<p>
									<span class="glyphicon glyphicon-star"></span>
									<span class="glyphicon glyphicon-star"></span>
									<span class="glyphicon glyphicon-star"></span>
									<span class="glyphicon glyphicon-star"></span>
									<span class="glyphicon glyphicon-star-empty"></span>  '
								. number_format( (float)$avgStars, 2, '.', '' ) .
								' stars </p>
							</div> <!-- End of ratings -->
						</div> <!-- End of thumbnail -->
					');
			}
			
			/* Display Product Reviews */
			
			echo('
				<div class="well">
					<div class="text-right">
						<form id="write_form" name="write_form" method="post" action="product_review_form.php">
							<input type="hidden" id="write_pass" name="write_pass" />
							<a class="btn btn-success" onclick="return WriteHandler(\'' . $product_id . '\')"> Leave a Review </a> 	
						</form>
					</div>
			');
						
			/* Loop through each customer review */
			for($i= 0; $i < $numReviews; $i++)
			{
				echo('
					<hr>
					<div class="row">
						<div class="col-md-12">');
			 
				printStarRating( $reviews[$i]['star_rating'] );
			 
				echo('&nbsp &nbsp'			. $reviews[$i]['customer_id'] . ' <span class="pull-right">' . $reviews[$i]['date'] . ' </span> 
							<p> [Review] &nbsp &nbsp &nbsp' . $reviews[$i]['review_content'] . '</p>
						</div>
					</div>
				');
			}			
			echo('					
				</div> <!-- End of Well -->
			');

			echo('
					</div> <!-- End of col-md-9 -->
			');
		
			/* close DB connection */
			$db_connection->close();
		
		
			/* Display Right hand Sidebar (Add to Cart Button Interface) */
			echo('
				<div class="col-md-3">
					<p class="lead"> Buy Now </p>
					<div class="list-group">        
						<form name="cart_submit_form" method="post" onsubmit="return check_input()" action="cart.php" >
							<input type="hidden" id="product_to_cart" name="product_to_cart" value="' . $product_id . '"/>
							<div class="list-group-item"> Quantity: <input type="text" class="form-control" id="quantity" name="quantity" placeholder="Enter a whole number" autocomplete="off"> </div>
							<button class="btn btn-warning btn-lg btn-block" type="submit"> 
							<span class="glyphicon glyphicon-shopping-cart"></span> Add to Cart 
							</button>
						</form>
					</div>
				</div> <!-- End of col-md-3 -->
			</div> <!-- End of row -->	
			');
					
		?>
    </div> <!-- /.container -->

	<!-- Footer -->
    <div class="container">
      <hr>
      <footer>
        <div class="row">
          <div class="col-lg-12">
            <p>Copyright &copy; Company 2013 </a></p>
          </div>
        </div>
      </footer>

    </div><!-- /.container -->

    <!-- JavaScript -->
    <script src="bootstrap3/js/jquery-1.10.2.js"></script>
    <script src="bootstrap3/js/bootstrap.js"></script>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="bootstrap-3.0.2-dist/dist/js/bootstrap.min.js"></script>	
	
  </body>
</html>
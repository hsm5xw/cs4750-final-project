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

	
	<script type= "text/javascript">
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
	
		function Handler(name_X)
		{		
			var prod_id = name_X.substring(1);
			//alert(prod_id);
			
			document.forms["ultimate_form"].ultimate_pass.value = prod_id;
			document.ultimate_form.submit();
		}
		
	</script>
	
		
  </head>

  <body>

    <!-- Fixed navbar -->
	<?php include "./top-navbar.php"; ?>
	<!-- End of Nav Bar -->

    <div class="container">
	<?php
		if( !isset($_POST['searchKey']) )
		{
			echo('
			<div class="page-header">
				<p class="lead"> Please enter the search key </p>
			</div>');
			exit;
		}
		else
		{
			$searchKey = $searchKey = $_POST['searchKey'];
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
		
		$results = array();
	
		if( $stmt->prepare("SELECT product_id, product_name, vendor_name, product_url, price FROM product NATURAL JOIN prices WHERE product_name LIKE CONCAT('%',?,'%')") )
		{
			$stmt->bind_param("s", $searchKey);
			$stmt->execute();
			$stmt->bind_result( $q_product_id, $q_product_name, $q_vendor_name, $q_product_url, $q_price );
			
			while( $stmt->fetch() )
			{
				$results[$numRecords]['product_id'] = $q_product_id;
				$results[$numRecords]['product_name'] = $q_product_name;
				$results[$numRecords]['vendor_name'] = $q_vendor_name;
				$results[$numRecords]['product_url'] = $q_product_url;
				$results[$numRecords]['price'] = $q_price;
				
				$numRecords = $numRecords + 1;
			}
			$stmt->close();
		}
		/* close DB connection */
		$db_connection->close();
			
		if($numRecords >= 2)
		{
			echo('
				<div class="page-header">
					<h1> Search Results </h1>
					<p class="lead">' . $numRecords  . ' results for ' . $searchKey . '</p>
				</div>');
		}
		else
		{
			echo('
				<div class="page-header">
					<h1> Search Results </h1>
					<p class="lead">' . $numRecords  . ' result for ' . $searchKey . '</p>
				</div>');
		}
			
		if($numRecords != 0)
		{
			echo('
				<div class="row">
					<div class="col-md-3"> Product Image </div>
					<div class="col-md-4"> Product Name  </div>
					<div class="col-md-3"> Vendor </div>
					<div class="col-md-2"> Price   </div>
				</div>
				<hr>');
		}	
						
		for($i= 0; $i < $numRecords; $i++)
		{
			$name_img = 'I';
			$name_img .= $results[$i]['product_id']; 
			//echo '<script> alert(\'' . $name_img . '\'); </script>';
			
			$name_link = 'L';
			$name_link .= $results[$i]['product_id'];
			//echo '<script> alert(\'' . $name_link . '\'); </script>';
			
			echo('
				<div class="row">
					<form>
						<input type="hidden" name='. $name_img . ' />
						<div class="col-md-3"> <a onclick="return Handler(\'' . $name_img . '\')"> <img class="img-responsive" src=' . $results[$i]['product_url'] . ' alt="Product Name"></a> </div>	
					</form>
					
					<form>
						<input type="hidden" name='. $name_link . ' />
						<div class="col-md-4"> <a onclick="return Handler(\'' . $name_link . '\')">' . $results[$i]['product_name'] . '</a> </div>		
					</form>
					
					<div class="col-md-3">' . $results[$i]['vendor_name'] . '</div>
					<div class="col-md-2">' . '$ ' . number_format( (float)$results[$i]['price'], 2, '.', '' )  . '</div>
				</div>
				<hr>');		
		}
			echo('
				<div class="row">
					<form id="ultimate_form" name="ultimate_form" method="post" action="product_display_form.php">
						<input type="hidden" id="ultimate_pass" name="ultimate_pass" />
					</form>
				</div>
				');	
			
	?>
    </div> <!-- /container -->

    <div class="container">

      <footer>
        <div class="row">
          <div class="col-lg-12">
            <p>Copyright &copy; Company 2013 </a></p>
          </div>
        </div>
      </footer>

    </div><!-- /.container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="bootstrap-3.0.2-dist/dist/js/bootstrap.min.js"></script>
  </body>
</html>

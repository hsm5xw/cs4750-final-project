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
			var form = document.writeReview_form;
		
			var inputs = {
				'star_rating': 'star rating',
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
		
			var star_rating = form['star_rating'].value;
			//alert('Star rating: ' + star_rating);
			var isNonNegativeInteger = isNonNegativeInt(star_rating);
				
			if(isNonNegativeInteger)
			{
				if(star_rating > 5 || star_rating < 0){
					alert('The star rating must be a whole number from 1 to 5');
					form.star_rating.focus();
					return false;
				}
			}
			else{
					alert('Please enter a whole number star rating greater than 0');
					form.star_rating.focus();
					return false;
			}
					
			if( !$.trim($("#review_content").val()) )
			{
				alert('Please enter the review content');
				return false;
			}
		}	
	</script>

	<!-- Star rating script -->
	<script>
		function starRating_setup()
		{
			$('#rateit9').rateit('step', 1);
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
			$product_id = "";
					
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
	
			if( !isset($_POST['write_pass']) )
			{
				echo("<script>
						 window.location.href = 'search-simple.php';
					</script>");
				exit;
			}
			else
			{
				$product_id = $_POST['write_pass'];
				//echo '<script> alert("good"); </script>';
				//echo($product_id); 
			}
		
			echo('
			<div class="row">
				<div class="span12">
					<h1> Write A Product Review </h1> <br/>
					<div class="hero-unit">
						<!-- Form -->
						<form class="form-horizontal" role="form" name="writeReview_form" method="post" onsubmit="return check_input()" action="product_review.php" >
								
							<!-- product ID -->
							<div class="form-group">
								<label for="product_id" class="col-sm-2 control-label"> Product ID </label>
								<div class="col-xs-2"> <p class="form-control">' . $product_id . '</p> </div>
								<div class="col-xs-2"> <input type="hidden" class="form-control"  id="product_id" name="product_id" autocomplete="off" value=' . $product_id . '> </div>
							</div>	
								
							<!-- user ID -->
							<div class="form-group">
								<label for="customer_id" class="col-sm-2 control-label"> User ID </label>
								<div class="col-xs-2"> <p class="form-control">' . $customer_id . '</p> </div>
							</div>
							
							<!-- star rating -->
							<div class="form-group">
								<label for="star_rating" class="col-sm-2 control-label"> Star Rating </label>
								<div class="col-xs-3"> <input type="text" class="form-control"  id="star_rating" name="star_rating" placeholder="Enter the Rating value from 1 to 5" autocomplete="off"> </div>
							</div>
							');

							echo('
							<!-- review_content -->
							<div class="form-group">
								<label for="review_content" class="col-sm-2 control-label"> Review Content </label>
								<div class="col-xs-5"> <textarea class="form-control" rows="3" id="review_content" name="review_content" autocomplete="off"> </textarea> </div>
							</div>
							
							</br>

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
		?>	
		
    </div> <!-- End of container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="bootstrap-3.0.2-dist/dist/js/bootstrap.min.js"></script>
  </body>
</html>

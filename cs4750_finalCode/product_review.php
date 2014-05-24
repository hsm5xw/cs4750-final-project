<?php
	session_start();
?>
<meta charset="utf-8" />
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
	
	/* Get Product Review Information */
	
	$product_id = "";
	$star_rating = "";
	$review_content = "";
	
	if( !isset($_POST['product_id']))
	{
		echo("<script>
				alert('Error: Product ID not available');
			</script>");
		exit;
	}
	else
	{
		$product_id = $_POST['product_id'];
		echo('<p> Product_id: ' . $product_id . '</p>');
	}
	
	if( !isset($_POST['star_rating']))
	{
		echo("<script>
				alert('Error: Star rating not available');
			</script>");
		exit;
	}
	else
	{
		$star_rating = $_POST['star_rating'];
		echo('<p> Star rating: ' . $star_rating . '</p>');
	}
	if( !isset($_POST['review_content']))
	{
		echo("<script>
				alert('Error: Review Content not available');
			</script>");
		exit;
	}
	else
	{
		$review_content = $_POST['review_content'];
		echo('<p> Review Content: ' . $review_content . '</p>');
	}
	
	$review_date = date("Y-m-d");
	echo('<p> Review Date: ' . $review_date . '</p>');

	include_once('login/libraryb.php');
	$db_connection = new mysqli($SERVER, $USER, $PASS, $DATABASE);
	
	if (mysqli_connect_errno()) {
		printf("Can't connect to MySQL Server. Error code: %s\n", mysqli_connect_error());
		return null;
	}
	
	$writeReview_stmt =$db_connection->stmt_init();
			
	if( $writeReview_stmt->prepare("INSERT INTO product_review(product_id, customer_id, date, review_content, star_rating)  VALUES (?,?,?,?,?)") 
				or die("<br/> Error Building Query ! <br/>" . mysqli_error($db_connection)  )  )
	{
		$writeReview_stmt->bind_param("sssss", $product_id, $customer_id , $review_date, $review_content, $star_rating);
		$writeReview_stmt->execute();
		$writeReview_stmt->fetch();
		$writeReview_stmt->close();
	}
	
	/* close DB connection */
	$db_connection->close();
	
	echo("<script>
				alert('Thank you for leaving a product review !');
				window.location.href = 'search-simple.php';
		</script>");
	
						
?>
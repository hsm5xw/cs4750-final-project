<?php
	session_start();
	
	unset( $_SESSION['customer_id'] );
	unset( $_SESSION['cart_items'] );
	
	echo("
		<script>
			window.alert('Log-out Success: Good Bye !');
			window.location.href = 'index.php';
		</script>
	")

?>
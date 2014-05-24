  
	<!-- Fixed navbar -->
    <div class="navbar navbar-default navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.php"> My Website </a>
        </div>
        <div class="navbar-collapse collapse">
          
		  <!-- Left hand nav bar -->
		  <ul class="nav navbar-nav">
            <li><a href="#about">About</a></li>
            <li><a href="#contact">Contact</a></li>
          </ul>

 <?php
	/* ********* NOT LOGGED IN ******************************* */
	if( empty($_SESSION['customer_id']) )
	{
 ?>		  
		  <!-- Right hand nav bar -->
          <ul class="nav navbar-nav navbar-right">		  
		   	<li>
				<!-- Search Product -->
				<form name="search_form" method="post" onsubmit="return check_searchInput()" class="navbar-form" role="search" action= "search-simple.php">
					<div class="form-group">
						<input type="text" class="form-control" id="searchKey" name="searchKey" placeholder="Search" autocomplete="off">
					</div>
					<button type="submit" class="btn btn-default">Submit</button>
				</form>
			</li>
			<li><a href="login_form.php"> <span class="glyphicon glyphicon-user"></span> Please Sign in </a></li>
            <li><a href="cart.php"> <span class="glyphicon glyphicon-shopping-cart"></span> Cart </a></li>
			
			<!-- Account -->
			<li class="dropdown">
              <a class="dropdown-toggle" data-toggle="dropdown"> Your Account <b class="caret"></b></a>
              <ul class="dropdown-menu">
				<li class="dropdown-header"> Not a member yet? </li>
                <li><a href="signup_form.php"> Create your account </a></li>
                <li class="divider"></li>
                <li class="dropdown-header"> Authentication </li>
                <li><a href="login_form.php"> Sign in </a></li>
              </ul>
            </li>
			
          </ul> <!-- end of Right hand nav bar -->
<?php	  
	}
	/* **************** LOGGED IN ************************** */
	else
	{
?>
	<!-- Right hand nav bar -->
          <ul class="nav navbar-nav navbar-right">		  
		   	<li>
				<!-- Search Product -->
				<form name="search_form" method="post" onsubmit="return check_searchInput()" class="navbar-form" role="search" action= "search-simple.php">
					<div class="form-group">
						<input type="text" class="form-control" id="searchKey" name="searchKey" placeholder="Search" autocomplete="off">
					</div>
					<button type="submit" class="btn btn-default">Submit</button>
				</form>
			</li>
			<li><a href="cart.php"> <span class="glyphicon glyphicon-user"></span> <?php echo("Hi ! &nbsp" . $_SESSION['customer_id'] ); ?> </a></li>
            <li><a href="cart.php"> <span class="glyphicon glyphicon-shopping-cart"></span> Cart </a></li>
			
			<!-- Account -->
			<li class="dropdown">
              <a class="dropdown-toggle" data-toggle="dropdown"> Your Account <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li class="dropdown-header"> My Account </li>
                <li><a href="cart.php"> View Cart </a></li>
                <li><a href="order_history_form.php"> Order History </a></li>
                <li class="divider"></li>
                <li class="dropdown-header"> Authentication </li>
                <li><a href="logout.php"> Sign out </a></li>
              </ul>
            </li>
			
          </ul> <!-- end of Right hand nav bar -->

<?php
	}
?>
        </div><!--/.nav-collapse -->
      </div>
    </div>
	<!-- End of Nav Bar for users NOT logged in -->


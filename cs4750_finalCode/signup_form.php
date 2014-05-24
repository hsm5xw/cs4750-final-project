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
			var form = document.signup_form;
		
			var inputs = {
				'customer_id': 'user ID',
				'password': 'password',
				'password_confirm': 'password confirmation',
				'first_name': 'first name',
				'last_name': 'last name',
				'city': 'city',
				'state': 'state',
				'zip_code': 'zip code',
				'phone_number': 'phone number'
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
			// Password Confirmation
			if( form['password'].value !== form['password_confirm'].value )
			{
				alert('Please make sure to have equal inputs for Password and Password Confirmation');
				return false;
			}			
			
		}	
	</script>
	
	<?php
			if( isset($_SESSION['customer_id']) )
			{
				echo("<script>
						 alert('You already have an account');
						 window.location.href = 'index.php';
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
				<h1> Create your account </h1> <br/>
                <div class="hero-unit">
					<!-- Form -->
                    <form class="form-horizontal" role="form" name="signup_form" method="post" onsubmit="return check_input()" action="signup.php" >
						<!-- user ID -->
						<div class="form-group">
							<label for="customer_id" class="col-sm-2 control-label"> User ID </label>
							<div class="col-xs-2"> <input type="text" class="form-control" id="customer_id" name="customer_id" placeholder="User ID" autocomplete="off"> </div>
						</div>
						<!-- password -->
						<div class="form-group">
							<label for="password" class="col-sm-2 control-label"> Password </label>
							<div class="col-xs-2"> <input type="password" class="form-control"  id="password" name="password" autocomplete="off"> </div>
						</div>
						<!-- confirm password -->
						<div class="form-group">
							<label for="password_confirm" class="col-sm-2 control-label"> Password </label>
							<div class="col-xs-2"> <input type="password" class="form-control"  id="password_confirm" name="password_confirm" autocomplete="off"> </div>
						</div>
						
						</br>
						
						<!-- First name -->
						<div class="form-group">
							<label for="first_name" class="col-sm-2 control-label"> First Name </label>
							<div class="col-xs-2"> <input type="text" class="form-control"  id="first_name" name="first_name" placeholder="First Name" autocomplete="off"> </div>
						</div>
						<!-- Last name -->
						<div class="form-group">
							<label for="last_name" class="col-sm-2 control-label"> Last Name </label>
							<div class="col-xs-2"> <input type="text" class="form-control"  id="last_name" name="last_name" placeholder="Last Name" autocomplete="off"> </div>
						</div>
						
						<br/>
						
						<!-- Street Address -->
						<div class="form-group">
							<label for="street_addr" class="col-sm-2 control-label"> Street Address </label>
							<div class="col-xs-2"> <input type="text" class="form-control"  id="street_addr" name="street_addr" autocomplete="off"> </div>
						</div>
						<!-- City -->
						<div class="form-group">
							<label for="city" class="col-sm-2 control-label"> City </label>
							<div class="col-xs-2"> <input type="text" class="form-control"  id="city" name="city" autocomplete="off"> </div>
						</div>						
						<!-- State -->
						<div class="form-group">
							<label for="state" class="col-sm-2 control-label"> State </label>
							<div class="col-xs-2"> 						
								<select class="form-control" id="state" name="state" autocomplete="off"> 
									<option value="" selected="selected">Select a State</option> 
									<option value="AL">Alabama</option> 
									<option value="AK">Alaska</option> 
									<option value="AZ">Arizona</option> 
									<option value="AR">Arkansas</option> 
									<option value="CA">California</option> 
									<option value="CO">Colorado</option> 
									<option value="CT">Connecticut</option> 
									<option value="DE">Delaware</option> 
									<option value="DC">District Of Columbia</option> 
									<option value="FL">Florida</option> 
									<option value="GA">Georgia</option> 
									<option value="HI">Hawaii</option> 
									<option value="ID">Idaho</option> 
									<option value="IL">Illinois</option> 
									<option value="IN">Indiana</option> 
									<option value="IA">Iowa</option> 
									<option value="KS">Kansas</option> 
									<option value="KY">Kentucky</option> 
									<option value="LA">Louisiana</option> 
									<option value="ME">Maine</option> 
									<option value="MD">Maryland</option> 
									<option value="MA">Massachusetts</option> 
									<option value="MI">Michigan</option> 
									<option value="MN">Minnesota</option> 
									<option value="MS">Mississippi</option> 
									<option value="MO">Missouri</option> 
									<option value="MT">Montana</option> 
									<option value="NE">Nebraska</option> 
									<option value="NV">Nevada</option> 
									<option value="NH">New Hampshire</option> 
									<option value="NJ">New Jersey</option> 
									<option value="NM">New Mexico</option> 
									<option value="NY">New York</option> 
									<option value="NC">North Carolina</option> 
									<option value="ND">North Dakota</option> 
									<option value="OH">Ohio</option> 
									<option value="OK">Oklahoma</option> 
									<option value="OR">Oregon</option> 
									<option value="PA">Pennsylvania</option> 
									<option value="RI">Rhode Island</option> 
									<option value="SC">South Carolina</option> 
									<option value="SD">South Dakota</option> 
									<option value="TN">Tennessee</option> 
									<option value="TX">Texas</option> 
									<option value="UT">Utah</option> 
									<option value="VT">Vermont</option> 
									<option value="VA">Virginia</option> 
									<option value="WA">Washington</option> 
									<option value="WV">West Virginia</option> 
									<option value="WI">Wisconsin</option> 
									<option value="WY">Wyoming</option>
								</select>	
							</div>
						</div>
						<!-- zip code -->
						<div class="form-group">
							<label for="zip_code" class="col-sm-2 control-label"> Zip code </label>
							<div class="col-xs-2"> <input type="text" class="form-control"  id="zip_code" name="zip_code" autocomplete="off"> </div>
						</div>
						
						</br>
						
						<!-- Phone number -->
						<div class="form-group">
							<label for="phone_number" class="col-sm-2 control-label"> Phone number </label>
							<div class="col-xs-2"> <input type="text" class="form-control"  id="phone_number" name="phone_number" placeholder="123-456-7890" autocomplete="off"> </div>
						</div>
						
						<!-- Terms and Conditions -->
						<div class="form-group">
							<label class="col-sm-offset-2 col-sm-10">
							<div> <label> <span class="glyphicon glyphicon-star-empty"></span> By clicking "Submit" I agree to follow Terms and Conditions </label> </div>
						</div>

						<!-- Submit Button -->
						<div class="form-group">
							<div class="col-sm-offset-2 col-sm-10">
								<button class="btn btn-success btn-lg" type="submit" id="submit_btn"> Submit </button>
							</div>
						</div>
					
					</form>	         
				</div>

			</div> <!-- End of span 12 -->
					
        </div>
    </div>


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="bootstrap-3.0.2-dist/dist/js/bootstrap.min.js"></script>
  </body>
</html>

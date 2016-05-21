<?php
	include "functions.php";
		$error = null;
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['email']) && !empty($_POST['password']))
	{
		
		$un = $_POST['email'];
		$pw = $_POST['password'];
		
		
			
		try 
		{
			//database connection with credentials
			$conn = DB_CONNECT();
			$conn2 = DB_CONNECT();
				
			// set the PDO error mode to exception
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			// query
			
			$sql = "SELECT * FROM users WHERE email=? AND password=?";
			
			$result = $conn->prepare($sql);
			
			$result->bindParam(1, $un);
			$result->bindParam(2, $pw);
			$result->execute();
			
			
			if($result->rowCount() > 0) //logged in
			{
				$row = $result->fetch(PDO::FETCH_ASSOC);
			
				//set the session variables
				$_SESSION["id"] = $row['user_id'];
				$_SESSION['name'] = $row['email'];
				$_SESSION['user_level'] = $row['user_level'];
				
					$sql2 = "SELECT company_id FROM userOwnsCompany
								WHERE user_id=?";
				
					$result2 = $conn2->prepare($sql2);
					$result2->bindParam(1, $_SESSION['id']);
					$result2->execute();
				
					//adds company to session
					if($result2->rowCount() > 0) //logged in
					{
						$row2 = $result2->fetch(PDO::FETCH_ASSOC);
						$_SESSION["cid"] = $row2['company_id'];
					}
					
					//delay to make it seem like its doing some thinking
				sleep(1);
				
				//if user logged in is the admin, take to admin panel
				if ($_SESSION['user_level'] == 1)
				{
					header("location: admin.php");
				}
				else //else, let them view listings
				{
					header("location: view.php?page=1");
				}
			}
			else
			{//error - wrong credentials
				$error = "Credentials are invalid. Please try again.";
				//header("location: login.php?error");
			}
		}
		//catch any errors
		catch(PDOException $e)
		{
			echo "Connection failed: " . $e->getMessage();
			return false;
		}	
		$conn = null;
	}
	//page will redirect before displaying HTML if credentials are valid.
?>	


<!DOCTYPE html>
	<html lang="en">

	<head>
		<?php
			$title = "Login - Bob's Listings";
			include_once "header.php";
		?>
		
	</head>

	<body id="page-top" class="index">
	
	<?php
		$page = "login";
		include_once "nav.php";

		$bg = "success";
		$buffer = true;
		
		include_once "listings.php";

	?>
		<section class="" id="login">
			<div class="container">
				<div class="row">
					<div class="col-lg-12 text-center">
						<h2>Login to your account</h2>
						<?php
							drawHR($c = "primary", $page);
						?>
					</div>
				</div>		
				<?php 
					
						if ($_SERVER['REQUEST_METHOD'] == 'POST' )
						{//if there was an error, let them know what happened
							if ($error != null)
							{
								echo '<p class="help-block text-danger text-center">';
										echo $error;
								echo '</p>';
							}
							else if (empty($_POST['email']) || empty($_POST['password']))
							{
								echo '<p class="help-block text-danger text-center">Please enter your credentials.</p>';
							}
						}	//display the form
				?>	
				<div class="row">
					<div class="col-lg-8 col-lg-offset-2">
						<form name="" id="" action="login.php" method ="POST" novalidate>
						   <div class="row control-group">
								<div class="form-group col-xs-12 floating-label-form-group controls">
									<label>Email Address</label>
									<input type="email" class="form-control" name="email" placeholder="Email Address" id="email" required data-validation-required-message="Please enter your email address.">
									<p class="help-block text-danger"></p>
								</div>
							</div>
							<div class="row control-group">
								<div class="form-group col-xs-12 floating-label-form-group controls">
									<label>Password</label>
									<input type="password" class="form-control" name="password" placeholder="Password" id="password" required data-validation-required-message="Please enter your company password.">
									<p class="help-block text-danger"></p>
								</div>
							</div>
							<br> <!-- todays date, website, description, company type (plumber, game merchant, electrician...) -->
							<div id="success"></div>
							<div class="row">
								<div class="form-group col-xs-12">
									<button type="submit" class="btn btn-success btn-lg text-center center-block">Log in to my account</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</section>
	<?php
		include_once "footer.php";
	?>
		</body>
	</html>

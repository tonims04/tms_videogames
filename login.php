<html>
<body>
<?php 
session_start();

function print_login_form(){
	echo '<h1>Login form</h1>
		<form action="" method="POST">
		User: <input type="text" name="user"><br>
		Password: <input type="password" name="password"><br>
		<input type="submit" name="enviar">
		</form>';
		echo '<br><a href="register.php">Register users</a>';
}

function user_page($name){
	echo "<h1>Welcome back, ".$name."!</h1>";
	echo '<form action="" method="POST">
		<input type="submit" name="logout" value="Log out">
		</form>';
	echo '<br><a href="change_password.php">Change password</a>';
	echo '<br><a href="delete_account.php">Delete account</a>';
}

if(isset($_POST["logout"])) {
	// remove all session variables
	session_unset();
	// destroy the session
	session_destroy();
}
if(isset($_SESSION["user"])) {
	echo "Open session detected<br>";
	user_page($_SESSION["user"]);


} elseif(isset($_POST["enviar"])){
	//Clean input
	$form_username = htmlspecialchars($_POST["user"]);
	$form_password = htmlspecialchars($_POST["password"]);

	//Connect with database
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "myDB";

	// Create connection
	$conn = mysqli_connect($servername, $username, $password, $dbname);
	// Check connection
	if (!$conn) {
	  die("Connection failed: " . mysqli_connect_error());
	}

	//Check if record exists
	$sql = "SELECT password FROM login_info WHERE username='$form_username'";
	$result = mysqli_query($conn, $sql);

	if (mysqli_num_rows($result) > 0) {
		$row = mysqli_fetch_assoc($result);
		if (password_verify($form_password, $row["password"])) {
			echo "<p style=\"color: purple;\">Correct user and password entered. Session started.<br>";
			echo "Try accessing this page from another browser tab</p>";
			user_page($_POST["user"]);
			//Store session user in a session variable
			$_SESSION["user"] = $_POST["user"];
		} else {
			echo "<p style=\"color: red;\">Known user but wrong password<br></p>";
			print_login_form();
		}

	  
	} else {
		echo "<p style=\"color: red;\">Username not found<br></p>";
		print_login_form();
	}
	
} else {
	print_login_form();
}

?>

</body>
</html>

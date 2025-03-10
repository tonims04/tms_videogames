<?php 
session_start();

function print_change_password_form(){
    echo '<h1>Change Password</h1>
        <form action="" method="POST">
        Current Password: <input type="password" name="current_password"><br>
        New Password: <input type="password" name="new_password"><br>
        Repeat New Password: <input type="password" name="new_password2"><br>
        <input type="submit" name="change_password" value="Change Password">
        </form>';
        echo '<br><a href="login.php">Cancel</a>';
}

//No session started
if(!isset($_SESSION["user"])){
	header('Location: login.php');
	exit;
} 

if(!isset($_POST["change_password"])){
	print_change_password_form();
	exit;
} 

//Clean input
$current_password = htmlspecialchars($_POST["current_password"]);
$new_password = htmlspecialchars($_POST["new_password"]);
$new_password2 = htmlspecialchars($_POST["new_password2"]);

//Check passwords match
if ($new_password != $new_password2) {
	echo "<p style=\"color: red;\">Passwords do not match!!</p>";
	print_change_password_form();
	
} else {
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

	//Check current password
	$logged_user = $_SESSION['user'];
	$sql = "SELECT password FROM login_info WHERE username='$logged_user'";
	$result = mysqli_query($conn, $sql);

	if (mysqli_num_rows($result) > 0) {
		$row = mysqli_fetch_assoc($result);
		if (password_verify($current_password, $row["password"])) {
			echo "<p style=\"color: green;\">Correct password entered. Changing password...</p>";
			$hash = password_hash($new_password, PASSWORD_DEFAULT);
			$sql = "UPDATE login_info SET password='$hash' WHERE username='$logged_user'";

			if (mysqli_query($conn, $sql)) {
			  echo "<p style=\"color: green;\">Password updated</p><br>";
			} else {
			  echo "Error updating password " . mysqli_error($conn);
			}
			echo '<br><a href="login.php">Return to user page</a>';
		} else {
			echo "<p style=\"color: red;\">Wrong current password<br></p>";
			print_change_password_form();
		}

	  
	} else {
		echo "<p style=\"color: red;\">Username does not exist<br></p>";
		//Account has been deleted?
		// remove all session variables
		session_unset();
		// destroy the session
		session_destroy();
		print_change_password_form();
	}
}


?>

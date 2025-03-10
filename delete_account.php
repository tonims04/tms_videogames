<?php 
session_start();

function print_delete_form(){
    echo '<h1>Delete account</h1>
        <form action="" method="POST">
        Current Password: <input type="password" name="current_password"><br>
        <input type="submit" name="delete" value="DELETE ACCOUNT">
        </form>';
        echo '<br><a href="login.php">Cancel</a>';
}

//No session started
if(!isset($_SESSION["user"])){
	header('Location: login.php');
	exit;
} 

if(!isset($_POST["delete"])){
	print_delete_form();
	exit;
} 

//Clean input
$current_password = htmlspecialchars($_POST["current_password"]);



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
	//echo $row["password"];
	//echo $current_password;
	if (password_verify($current_password, $row["password"])) {
		echo "<p style=\"color: green;\">Correct password entered. Deleting account...</p>";
		$sql = "DELETE FROM login_info WHERE username='$logged_user'";

		if (mysqli_query($conn, $sql)) {
			echo "<p style=\"color: green;\">Account deleted</p><br>";
			// remove all session variables
			session_unset();
			// destroy the session
			session_destroy();
		} else {
			echo "Error deleting account " . mysqli_error($conn);
		}
		echo '<br><a href="login.php">Return to main page</a>';
	} else {
		echo "<p style=\"color: red;\">Wrong current password<br></p>";
		print_delete_form();
	}

  
} else {
	echo "<p style=\"color: red;\">Username does not exist<br></p>";
	//Account has been deleted?
	// remove all session variables
	session_unset();
	// destroy the session
	session_destroy();
	print_delete_form();
}



?>

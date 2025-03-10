<?php 
session_start();

function print_register_form(){
	echo '<h1>Register form</h1>
		<form action="" method="POST">
		User: <input type="text" name="user"><br>
		Password: <input type="password" name="password"><br>
		Repeat your password: <input type="password" name="password2"><br>
		<img src="captcha.php" alt="CAPTCHA Image"><br>
		Enter CAPTCHA: <input type="text" name="captcha"><br>
		<input type="submit" name="register">
		</form>';
}

if (!isset($_POST["register"])) {
	print_register_form();
	exit;
} 

// Clean input
$form_username = htmlspecialchars($_POST["user"]);
$form_password = htmlspecialchars($_POST["password"]);
$form_password2 = htmlspecialchars($_POST["password2"]);
$form_captcha = htmlspecialchars($_POST["captcha"]);

// Check passwords match
if ($form_password != $form_password2) {
	echo "<p style=\"color: red;\">Passwords do not match!!</p>";
	print_register_form();
	exit;
}

// Check CAPTCHA
if ($form_captcha != $_SESSION['captcha']) {
    echo "<p style=\"color: red;\">Incorrect CAPTCHA!</p>";
    print_register_form();
    exit;
}

// Connect with database
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

// Check if record exists
$sql = "SELECT * FROM login_info WHERE username='$form_username'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
	$row = mysqli_fetch_assoc($result);
	echo "<p style=\"color: red;\">User already registered with id: ". $row["id"] .". Choose another one.<br></p>";
	print_register_form();
} else {
	// Register the user
	$hash = password_hash($form_password, PASSWORD_DEFAULT);
	$sql = "INSERT INTO login_info (username, password) VALUES ('$form_username', '$hash')";

	if (mysqli_query($conn, $sql)) {
		echo "<p style=\"color: green;\">New user registered successfully</p>";
		echo '<br><a href="login.php">Go to login</a>';
	} else {
		echo "Error: " . $sql . "<br>" . mysqli_error($conn);
	}
}
?>

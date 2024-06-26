<?php

require_once 'config/config.php';

// Define variables and initialize with empty values
$username = $password = $confirm_password = "";

$username_err = $password_err = $confirm_password_err = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

	// Check if username is empty
	if (empty(trim($_POST['username']))) {
		$username_err = "Please enter a username.";

		// Check if username already exist
	} else {

		// Prepare a select statement
		$sql = 'SELECT id FROM users WHERE username = ?';

		if ($stmt = $mysql_db->prepare($sql)) {
			// Set parameter
			$param_username = trim($_POST['username']);

			// Bind param variable to prepares statement
			$stmt->bind_param('s', $param_username);

			// Attempt to execute statement
			if ($stmt->execute()) {

				// Store executed result
				$stmt->store_result();

				if ($stmt->num_rows == 1) {
					$username_err = 'This username is already taken.';
				} else {
					$username = trim($_POST['username']);
				}
			} else {
				echo "Oops! ${$username}, something went wrong. failed to execute query";
			}

			// Close statement
			$stmt->close();
		} else {
            echo "Oops! something went wrong. failed to prepare statement";
		}
	}

	// Validate password
	if (empty(trim($_POST["password"]))) {
		$password_err = "Please enter a password.";
	} elseif (strlen(trim($_POST["password"])) < 6) {
		$password_err = "Password must have at least 6 characters.";
	} else {
		$password = trim($_POST["password"]);
	}

	// Validate confirm password
	if (empty(trim($_POST["confirm_password"]))) {
		$confirm_password_err = "Please confirm password.";
	} else {
		$confirm_password = trim($_POST["confirm_password"]);
		if (empty($password_err) && ($password != $confirm_password)) {
			$confirm_password_err = "Password did not match.";
		}
	}

	// Check input error before inserting into database

	if (empty($username_err) && empty($password_err) && empty($confirm_err)) {

		// Prepare insert statement
		$sql = 'INSERT INTO users (username, password) VALUES (?,?)';

		if ($stmt = $mysql_db->prepare($sql)) {

			// Set parameter
			$param_username = $username;
			$param_password = password_hash($password, PASSWORD_DEFAULT); // Created a password

			// Bind param variable to prepares statement
			$stmt->bind_param('ss', $param_username, $param_password);

			// Attempt to execute
			if ($stmt->execute()) {
				// Redirect to login page
				header('location: ./login.php');
				// echo "Will  redirect to login page";
			} else {
				echo "Something went wrong. Try signing in again.";
			}

			// Close statement
			$stmt->close();
		}

		// Close connection
		$mysql_db->close();
	}
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sign in</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .wrapper {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        .wrapper h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group.has_error {
            color: red; 
        }
    </style>
</head>

<body>
    <main>
        <section class="container wrapper">
            <h2 class="display-4">Sign Up</h2>
            <p class="lead text-center">Please fill in your credentials.</p>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <div class="form-group <?php echo (!empty($username_err)) ? 'has_error' : ''; ?>">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" class="form-control"
                        value="<?php echo $username ?>">
                    <span class="help-block"><?php echo $username_err; ?></span>
                </div>

                <div class="form-group <?php echo (!empty($password_err)) ? 'has_error' : ''; ?>">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control"
                        value="<?php echo $password ?>">
                    <span class="help-block"><?php echo $password_err; ?></span>
                </div>

                <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has_error' : ''; ?>">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control"
                        value="<?php echo $confirm_password; ?>">
                    <span class="help-block"><?php echo $confirm_password_err; ?></span>
                </div>

                <div class="form-group">
                    <input type="submit" class="btn btn-success btn-block" value="Sign In">
                    <input type="reset" class="btn btn-primary btn-block" value="Clear">
                </div>
                <p class="text-center">Already have an account? <a href="login.php">Login here</a>.</p>
            </form>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>
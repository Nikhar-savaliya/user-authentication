<?php

// Initialize sessions
session_start();

// Check if the user is already logged in, if yes then redirect him to welcome page
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
  header("location: welcome.php");
  exit;
}

// Include config file
require_once "config/config.php";

// Define variables and initialize with empty values
$username = $password = '';
$username_err = $password_err = '';

// Process submitted form data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // Check if username is empty
  if (empty(trim($_POST['username']))) {
    $username_err = 'Please enter username.';
  } else {
    $username = trim($_POST['username']);
  }

  // Check if password is empty
  if (empty(trim($_POST['password']))) {
    $password_err = 'Please enter your password.';
  } else {
    $password = trim($_POST['password']);
  }

  // Validate credentials
  if (empty($username_err) && empty($password_err)) {
    // Prepare a select statement
    $sql = 'SELECT id, username, password FROM users WHERE username = ?';

    if ($stmt = $mysql_db->prepare($sql)) {

      // Set parameter
      $param_username = $username;

      // Bind param to statement
      $stmt->bind_param('s', $param_username);

      // Attempt to execute
      if ($stmt->execute()) {

        // Store result
        $stmt->store_result();

        // Check if username exists. Verify user exists then verify
        if ($stmt->num_rows == 1) {
          // Bind result into variables
          $stmt->bind_result($id, $username, $hashed_password);

          if ($stmt->fetch()) {
            if (password_verify($password, $hashed_password)) {

              // Start a new session
              session_start();

              // Store data in sessions
              $_SESSION['loggedin'] = true;
              $_SESSION['id'] = $id;
              $_SESSION['username'] = $username;

              // Redirect to user to page
              header('location: welcome.php');
            } else {
              // Display an error for password mismatch
              $password_err = 'Invalid password';
            }
          }
        } else {
          $username_err = "Username does not exists.";
        }
      } else {
        echo "Oops! Something went wrong please try again";
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
            <h2 class="display-4">Login</h2>
            <p class="lead text-center">Please fill this form to login.</p>
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

                <div class="form-group">
                    <input type="submit" class="btn btn-primary btn-block" value="Login">
                </div>
                <p class="text-center">Don't have an account? <a href="register.php">Sign up</a>.</p>
            </form>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>


</html>
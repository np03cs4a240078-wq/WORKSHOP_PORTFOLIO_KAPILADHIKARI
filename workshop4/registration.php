
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>User Registration</title>
<link rel="stylesheet" href="style.css">
</head>
<?php
// Initialize variables
$name = $email = $password = $confirm_password = "";
$nameErr = $emailErr = $passwordErr = $confirmErr = "";
$successMsg = "";
$fileError = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Trim inputs
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";
     //name
    if ($name === "") {
        $nameErr = "Name is required";
    }

    // Email
    if ($email === "") {
        $emailErr = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailErr = "Invalid email format";
    }

    // Password
    if ($password === "") {
        $passwordErr = "Password is required";
    } elseif (strlen($password) < 8) {
        $passwordErr = "Password must be at least 8 characters long";
    } elseif (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
        $passwordErr = "Password must contain at least one special character";
    }

    //  Ree Password
    if ($confirm_password === "") {
        $confirmErr = "Please confirm your password";
    } elseif ($password !== $confirm_password) {
        $confirmErr = "Passwords do not match";
    }

    // for errors, natra  proceed to save
    if (!$nameErr && !$emailErr && !$passwordErr && !$confirmErr) {
        $file = "users.json";

        // Read previous users ya files
        if (file_exists($file)) {
            $jsonData = file_get_contents($file);
            if ($jsonData === false) {
                $fileError = "Failed to read user data file.";
            } else {
                $users = json_decode($jsonData, true);
                if (!is_array($users)) {
                    $users = [];
                }
            }
        } else {
            $users = [];
        }

        if (!$fileError) {
            // naya data
            $newUser = [
                "name" => $name,
                "email" => $email,
                "password" => password_hash($password, PASSWORD_DEFAULT)
            ];

            // naya user
            $users[] = $newUser;

            // re writing back
            $jsonEncoded = json_encode($users, JSON_PRETTY_PRINT);
            if ($jsonEncoded === false) {
                $fileError = "Failed to encode user data.";
            } else {
                $writeResult = file_put_contents($file, $jsonEncoded);
                if ($writeResult === false) {
                    $fileError = "Failed to write user data file.";
                }
            }
        }

        if (!$fileError) {
            $successMsg = "Registration successful!";
            // Clear form fields
            $name = $email = $password = $confirm_password = "";
            header("Location: ". $_SERVER['PHP_SELF'] . "?success=1");
            exit();
        }

    }
}
?>
<body>

<form method="POST" action="">
    <h2>User Registration</h2>

    <?php if ($successMsg): ?>
        <div class="success"><?= htmlspecialchars($successMsg) ?></div>
    <?php endif; ?>

    <?php if ($fileError): ?>
        <div class="file-error"><?= htmlspecialchars($fileError) ?></div>
    <?php endif; ?>

    <label for="name">Full Name:</label>
    <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>" />
    <span class="error"><?= $nameErr ?></span>

    <label for="email">Email Address:</label>
    <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" />
    <span class="error"><?= $emailErr ?></span>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" />
    <span class="error"><?= $passwordErr ?></span>

    <label for="confirm_password">Confirm Password:</label>
    <input type="password" id="confirm_password" name="confirm_password" />
    <span class="error"><?= $confirmErr ?></span>

    <input type="submit" value="Submit" />
</form>

</body>
</html>



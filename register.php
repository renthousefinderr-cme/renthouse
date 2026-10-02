<?php

include "db.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {

    header("Location: index.html");
    exit();

}


/* Get form values */

$username  = trim($_POST["username"] ?? "");
$password  = $_POST["password"] ?? "";
$phone     = trim($_POST["phone"] ?? "");
$address   = trim($_POST["address"] ?? "");
$user_type = $_POST["user_type"] ?? "";


/* Check all fields */

if (
    empty($username) ||
    empty($password) ||
    empty($phone) ||
    empty($address) ||
    empty($user_type)
) {

    die("Please fill all fields.");

}


/* Check phone number */

if (!preg_match("/^[0-9]{10}$/", $phone)) {

    die("Please enter a valid 10-digit phone number.");

}


/* Check Bapatla address */

if (stripos($address, "Bapatla") === false) {

    die("This application is available only for Bapatla.");

}


/* Check username already exists */

$check = $conn->prepare(
    "SELECT id FROM users WHERE username = ?"
);

$check->bind_param(
    "s",
    $username
);

$check->execute();

$result = $check->get_result();


if ($result->num_rows > 0) {

    die("Username already exists. Please choose another username.");

}

$check->close();


/* Insert normal password */

$stmt = $conn->prepare(
    "INSERT INTO users
    (username, password, phone, address, user_type)
    VALUES (?, ?, ?, ?, ?)"
);

$stmt->bind_param(
    "sssss",
    $username,
    $password,
    $phone,
    $address,
    $user_type
);


/* Registration */

if ($stmt->execute()) {

    header("Location: index.html?registered=success");
    exit();

} else {

    die("Registration failed: " . $stmt->error);

}


$stmt->close();
$conn->close();

?>

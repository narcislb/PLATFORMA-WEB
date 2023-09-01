<?php
// database connection credentials
$host = "localhost";
$username = "your_username";
$password = "your_password";
$database = "your_database";

// connect to database
$conn = mysqli_connect($host, $username, $password, $database);

// check if connection was successful
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

// initialize variables
$company_name = "";
$email = "";
$password = "";
$address = "";
$phone_number = "";
$service_description = "";
$service_area = "";
$errors = array();

// process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // retrieve form data
  $company_name = mysqli_real_escape_string($conn, $_POST["company_name"]);
  $email = mysqli_real_escape_string($conn, $_POST["email"]);
  $password = mysqli_real_escape_string($conn, $_POST["password"]);
  $address = mysqli_real_escape_string($conn, $_POST["address"]);
  $phone_number = mysqli_real_escape_string($conn, $_POST["phone_number"]);
  $service_description = mysqli_real_escape_string($conn, $_POST["service_description"]);
  $service_area = mysqli_real_escape_string($conn, $_POST["service_area"]);

  // validate form data
  if (empty($company_name)) {
    array_push($errors, "Company name is required");
  }
  if (empty($email)) {
    array_push($errors, "Email is required");
  }
  if (empty($password)) {
    array_push($errors, "Password is required");
  }
  if (empty($address)) {
    array_push($errors, "Address is required");
  }
  if (empty($phone_number)) {
    array_push($errors, "Phone number is required");
  }
  if (empty($service_description)) {
    array_push($errors, "Service description is required");
  }
  if (empty($service_area)) {
    array_push($errors, "Service area is required");
  }

  // if no errors, save data to database
  if (count($errors) == 0) {
    $sql = "INSERT INTO service_providers (company_name, email, password, address, phone_number, service_description, service_area)
            VALUES ('$company_name', '$email', '$password', '$address', '$phone_number', '$service_description', '$service_area')";

    if (mysqli_query($conn, $sql)) {
      // redirect to login page
      header("Location: login.php");
      exit();
    } else {
      array_push($errors, "Error: " . mysqli_error($conn));
    }
  }
}

// close database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Service Provider Registration</title>
</head>
<body>
  <h1>Service Provider Registration</h1>

  <?php if (count($errors) > 0): ?>
    <div>
      <?php foreach ($errors as $error): ?>
        <p><?php echo $error; ?></p>
      <?php endforeach ?>
    </div>
  <?php endif ?>

  <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <label for="company_name">Company Name:</label><br>
    <input type="text" name="company_name" value="<?php echo $company_name; ?>"><br>

    <label for="email">Email:</label><br>
    <input type="email" name="email" value="<?php echo $email; ?>"><br>

   

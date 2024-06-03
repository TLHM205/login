
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbusertuilahiep";

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data from POST
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

// Validate inputs
if (empty($email) && empty($password)) {
    header("Location: login.php?error=" . urlencode("Vui lòng nhập email và mật khẩu"));
    exit();
}
if (empty($email)) {
    header("Location: login.php?error=" . urlencode("Vui lòng nhập email"));
    exit();
}
if (empty($password)) {
    header("Location: login.php?error=" . urlencode("Vui lòng nhập mật khẩu"));
    exit();
}

// Prepare SQL statement
$sql = "SELECT password FROM tblusertuilahiep WHERE email = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

// Bind parameters and execute
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    if (password_verify($password, $user['password'])) {
        header("Location: homepage.php");
        exit();
    } else {
        header("Location: login.php?error=" . urlencode("Mật khẩu không đúng"));
        exit();
    }
} else {
    header("Location: login.php?error=" . urlencode("Email không tồn tại"));
    exit();
}

// Close statement and connection
$stmt->close();
$conn->close();
?>

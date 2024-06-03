
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
$confirm_password = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';

// Validate inputs
if (empty($email) && empty($password) && empty($confirm_password)) {
    header("Location: signup.php?error=" . urlencode("Vui lòng nhập tất cả các thông tin"));
    exit();
}
if(empty($email)){
    header("Location: signup.php?error=" . urlencode("Vui lòng nhập email"));
    exit();
}
if(empty($password)){
    header("Location: signup.php?error=" . urlencode("Vui lòng nhập mật khẩu"));
    exit();
}
if(empty($confirm_password)){
    header("Location: signup.php?error=" . urlencode("Vui lòng nhập mật khẩu xác nhận"));
    exit();
}
if ($password !== $confirm_password) {
    header("Location: signup.php?error=" . urlencode("Mật khẩu xác nhận không trùng khớp"));
    exit();
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: signup.php?error=" . urlencode("Định dạng email không hợp lệ"));
    exit();
}

// Check if email already exists
$sql = "SELECT email FROM tblusertuilahiep WHERE email = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    header("Location: signup.php?error=" . urlencode("Email đã được sử dụng"));
    exit();
}

// Hash password
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

// Insert new user
$sql = "INSERT INTO tblusertuilahiep (email, password) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("ss", $email, $passwordHash);
$success = $stmt->execute();

if ($success && $stmt->affected_rows === 1) {
    header("Location: login.php?success=" . urlencode("Đăng ký thành công, vui lòng đăng nhập"));
    exit();
} else {
    header("Location: signup.php?error=" . urlencode("Lỗi khi đăng ký người dùng"));
    exit();
}

// Close statement and connection
$stmt->close();
$conn->close();
?>

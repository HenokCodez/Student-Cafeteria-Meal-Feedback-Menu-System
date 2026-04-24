<?php
session_start();
require_once '../config/db.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: ../admin/manage_menu.php');
    } else {
        header('Location: ../student/menu.php');
    }
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    // Backend validation
    if (empty($username) || empty($email) || empty($password)) {
        $error = 'Please fill all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email format.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } else {
        // Prevent duplicate emails
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows > 0) {
            $error = 'This email is already registered. Please login.';
        } else {
            // Hash password securely
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = 'student'; // Force student role on registration
            
            $insert = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            $insert->bind_param("ssss", $username, $email, $hashed_password, $role);
            
            if ($insert->execute()) {
                $success = 'Successfully registered! You can now <a href="login.php">Log In</a>.';
            } else {
                $error = 'Something went wrong. Please try again.';
            }
        }
    }
}

// Logic finished. Now safe to output HTML headers.
$path = '../';
require_once '../includes/header.php';
?>

<div class="max-w-md mx-auto mt-6 mb-12">
    <div class="text-center mb-10">
        <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 mb-2">Create Account.</h2>
        <p class="text-gray-500 text-sm">Join the student cafeteria portal today.</p>
    </div>

    <?php if($error): ?>
        <div class="bg-red-50 border border-red-200 text-red-600 text-sm p-4 rounded-xl mb-6 text-center shadow-sm">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    <?php if($success): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 text-sm p-4 rounded-xl mb-6 text-center shadow-sm">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <div class="bg-white border border-gray-100 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-8">
        <!-- Use frontend JS validation onsubmit -->
        <form action="" method="POST" class="space-y-4" onsubmit="return validateRegistration()">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Full Name</label>
                <input type="text" name="username" required class="w-full bg-slate-50 text-gray-900 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Address</label>
                <input type="email" name="email" required class="w-full bg-slate-50 text-gray-900 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                <input type="password" name="password" id="password" required class="w-full bg-slate-50 text-gray-900 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" required class="w-full bg-slate-50 text-gray-900 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition">
            </div>

            <button type="submit" class="w-full bg-indigo-600 text-white font-bold rounded-xl px-4 py-3 mt-4 hover:bg-indigo-700 transition duration-200 shadow-lg shadow-indigo-600/20">
                Register
            </button>
        </form>
        
        <div class="mt-8 border-t border-gray-100 pt-6 text-center text-sm text-gray-500">
            Already have an account? <a href="login.php" class="text-indigo-600 font-semibold hover:text-indigo-800 transition">Sign In</a>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

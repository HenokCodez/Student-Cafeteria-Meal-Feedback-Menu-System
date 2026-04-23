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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];

                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header('Location: ../admin/manage_menu.php');
                } else {
                    header('Location: ../student/menu.php');
                }
                exit;
            } else {
                $error = 'Invalid email or password.';
            }
        } else {
            $error = 'Invalid email or password.';
        }
    }
}

// Logic finished. Now safe to output HTML headers.
$path = '../';
require_once '../includes/header.php';
?>

<div class="max-w-md mx-auto mt-10">
    <div class="text-center mb-10">
        <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 mb-2">Welcome back.</h2>
        <p class="text-gray-500 text-sm">Sign in to your student cafeteria account.</p>
    </div>

    <?php if($error): ?>
        <div class="bg-red-50 border border-red-200 text-red-600 text-sm p-4 rounded-xl mb-6 text-center shadow-sm">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <div class="bg-white border border-gray-100 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-8">
        <form action="" method="POST" class="space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                <input type="email" name="email" required placeholder="example@astu.edu" class="w-full bg-slate-50 text-gray-900 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition placeholder-gray-400">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                <input type="password" name="password" required class="w-full bg-slate-50 text-gray-900 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition">
            </div>

            <button type="submit" class="w-full bg-indigo-600 text-white font-bold rounded-xl px-4 py-3 mt-4 hover:bg-indigo-700 transition duration-200 shadow-lg shadow-indigo-600/20">
                Sign In
            </button>
        </form>
        
        <div class="mt-8 border-t border-gray-100 pt-6 text-center text-sm text-gray-500">
            Don't have an account? <a href="register.php" class="text-indigo-600 font-semibold hover:text-indigo-800 transition">Create one</a>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

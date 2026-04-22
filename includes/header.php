<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Establish dynamic base path to easily link CSS and JS across directories
$base_path = isset($path) ? $path : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cafeteria System</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
      /* Apple-inspired adjustments */
      html { scroll-behavior: smooth; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }
      body { background-color: #f8fafc; color: #0f172a; }
      *:focus { outline: none; }
    </style>
    <!-- Keep JS validation -->
    <script src="<?php echo $base_path; ?>assets/js/script.js" defer></script>
</head>
<body class="antialiased min-h-screen flex flex-col bg-slate-50 text-gray-900">
    <!-- Navbar -->
    <nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-xl border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex-shrink-0 flex items-center">
                    <span class="text-xl font-extrabold tracking-tight text-indigo-600">ASTU Menu</span>
                </div>
                <div class="flex items-center space-x-4 sm:space-x-8">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <?php if($_SESSION['role'] === 'admin'): ?>
                            <a href="<?php echo $base_path; ?>admin/manage_menu.php" class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition">Manage Menu</a>
                            <a href="<?php echo $base_path; ?>admin/view_feedback.php" class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition">Feedbacks</a>
                        <?php else: ?>
                            <a href="<?php echo $base_path; ?>student/menu.php" class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition">Menu</a>
                            <a href="<?php echo $base_path; ?>student/history.php" class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition">History</a>
                        <?php endif; ?>
                        <a href="<?php echo $base_path; ?>auth/logout.php" class="text-xs font-medium px-4 py-2 rounded-full border border-gray-300 text-gray-700 hover:bg-gray-100 transition">Logout</a>
                    <?php else: ?>
                        <a href="<?php echo $base_path; ?>auth/login.php" class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition">Sign In</a>
                        <a href="<?php echo $base_path; ?>auth/register.php" class="text-sm font-medium px-4 py-2 bg-indigo-600 text-white rounded-full hover:bg-indigo-700 shadow-sm transition transform hover:-translate-y-0.5">Create Account</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    <main class="flex-grow w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

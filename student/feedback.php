<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header('Location: ../auth/login.php');
    exit;
}

if (!isset($_GET['item_id'])) {
    $path = '../';
    require_once '../includes/header.php';
    echo "<p class='error'>No menu item selected to review.</p>";
    require_once '../includes/footer.php';
    exit;
}

$item_id = intval($_GET['item_id']);

// Check if item exists
$stmt = $conn->prepare("SELECT name FROM menu_items WHERE id = ?");
$stmt->bind_param("i", $item_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $path = '../';
    require_once '../includes/header.php';
    echo "<p class='error'>Menu item not found.</p>";
    require_once '../includes/footer.php';
    exit;
}

$item = $result->fetch_assoc();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);
    $user_id = $_SESSION['user_id'];

    if ($rating < 1 || $rating > 5) {
        $msg = "<p class='error'>Rating must be between 1 and 5.</p>";
    } else {
        $insert = $conn->prepare("INSERT INTO feedback (user_id, menu_item_id, rating, comment) VALUES (?, ?, ?, ?)");
        $insert->bind_param("iiis", $user_id, $item_id, $rating, $comment);
        
        if ($insert->execute()) {
            $msg = "<p class='success'>Thank you! Your feedback has been recorded safely.</p>";
        } else {
            $msg = "<p class='error'>Database Error: Failed to submit feedback.</p>";
        }
    }
}

// Logic finished. Now safe to output HTML headers.
$path = '../';
require_once '../includes/header.php';
?>

<div class="max-w-2xl mx-auto mt-6 mb-12">
    <div class="mb-8">
        <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 mb-2">Leave Feedback.</h2>
        <p class="text-gray-500 text-sm">You are currently rating: <strong class="text-indigo-600 font-bold ml-1"><?php echo htmlspecialchars($item['name']); ?></strong></p>
    </div>

    <?php if($msg): ?>
        <div class="mb-6">
            <?php echo str_replace("class='success'", "class='bg-green-50 border border-green-200 text-green-700 text-sm p-4 rounded-xl text-center shadow-sm block'", str_replace("class='error'", "class='bg-red-50 border border-red-200 text-red-600 text-sm p-4 rounded-xl text-center shadow-sm block'", $msg)); ?>
        </div>
    <?php endif; ?>

    <div class="bg-white border border-gray-100 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-8">
        <form action="" method="POST" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Your Rating</label>
                <div class="relative">
                    <select name="rating" required class="w-full bg-slate-50 text-gray-900 border border-gray-200 rounded-xl px-4 py-3 appearance-none focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition">
                        <option value="5">★★★★★ - Excellent and Delicious</option>
                        <option value="4">★★★★☆ - Good</option>
                        <option value="3">★★★☆☆ - Average / Okay</option>
                        <option value="2">★★☆☆☆ - Needs Improvement</option>
                        <option value="1">★☆☆☆☆ - Terrible</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Additional Comments</label>
                <textarea name="comment" rows="5" required placeholder="What did you like or dislike?" class="w-full bg-slate-50 text-gray-900 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition placeholder-gray-400 resize-none"></textarea>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 mt-8 pt-4 border-t border-gray-100">
                <button type="submit" class="flex-1 bg-indigo-600 text-white font-bold rounded-xl px-4 py-3 hover:bg-indigo-700 transition duration-200 shadow-md shadow-indigo-600/20">
                    Submit Rating
                </button>
                <a href="menu.php" class="flex-1 flex justify-center items-center bg-gray-100 text-gray-700 font-bold rounded-xl px-4 py-3 hover:bg-gray-200 transition duration-200">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

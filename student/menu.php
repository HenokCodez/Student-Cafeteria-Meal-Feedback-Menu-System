<?php
session_start();
require_once '../config/db.php';

// Check if user is logged in and is a student
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header('Location: ../auth/login.php');
    exit;
}

// Fetch menu sorted by day and meal
$query = "SELECT * FROM menu_items ORDER BY FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), meal_type";
$result = $conn->query($query);

// Logic finished. Now safe to output HTML headers.
$path = '../';
require_once '../includes/header.php';
?>

<div class="mb-12">
    <h2 class="text-4xl font-extrabold tracking-tight text-gray-900 mb-2">Weekly Menu.</h2>
    <p class="text-gray-500 text-lg">Browse this week's meals and drop your feedback.</p>
</div>

<?php if ($result->num_rows > 0): ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="bg-white border border-gray-100 rounded-[1.5rem] p-6 flex flex-col justify-between hover:-translate-y-1 hover:border-indigo-200 transition duration-300 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgb(79,70,229,0.1)] group">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2"><?php echo htmlspecialchars($row['name']); ?></h3>
                    <div class="inline-block px-3 py-1 bg-indigo-50 border border-indigo-100 rounded-full text-[11px] font-bold tracking-wider text-indigo-600 uppercase mb-4">
                        <?php echo $row['day_of_week']; ?> &middot; <?php echo $row['meal_type']; ?>
                    </div>
                    <p class="text-sm text-gray-500 leading-relaxed truncate whitespace-normal line-clamp-3">
                        <?php echo nl2br(htmlspecialchars($row['description'])); ?>
                    </p>
                </div>
                <div class="mt-8">
                    <a href="feedback.php?item_id=<?php echo $row['id']; ?>" class="inline-flex w-full justify-center items-center px-4 py-2.5 bg-gray-50 border border-gray-200 text-gray-700 text-sm font-bold rounded-xl hover:bg-indigo-50 hover:text-indigo-700 hover:border-indigo-200 transition duration-200">
                        Leave Feedback
                    </a>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
<?php else: ?>
    <div class="bg-white border border-dashed border-gray-300 rounded-3xl p-12 text-center flex flex-col items-center justify-center shadow-sm">
        <p class="text-gray-900 font-bold mb-1">No items on the menu</p>
        <p class="text-sm text-gray-500">Check back later when the admin updates the meals.</p>
    </div>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>

<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header('Location: ../auth/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Get all feedback for this specific student user
$stmt = $conn->prepare("SELECT m.name AS item_name, f.rating, f.comment, f.created_at FROM feedback f JOIN menu_items m ON f.menu_item_id = m.id WHERE f.user_id = ? ORDER BY f.created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Logic finished. Now safe to output HTML headers.
$path = '../';
require_once '../includes/header.php';
?>

<div class="mb-10">
    <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 mb-2">My Feedback History.</h2>
    <p class="text-gray-500 text-sm">Review all the ratings and comments you've submitted.</p>
</div>

<?php if ($result->num_rows > 0): ?>
    <div class="bg-white border border-gray-200 rounded-3xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-gray-200 text-xs uppercase tracking-wider text-gray-500">
                        <th class="px-6 py-4 font-bold">Menu Item</th>
                        <th class="px-6 py-4 font-bold">Rating</th>
                        <th class="px-6 py-4 font-bold">Comment</th>
                        <th class="px-6 py-4 font-bold">Date Submitted</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900"><?php echo htmlspecialchars($row['item_name']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                    <?php echo $row['rating']; ?> / 5
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate" title="<?php echo htmlspecialchars($row['comment']); ?>">
                                <?php echo htmlspecialchars($row['comment']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                <?php echo date('M d, Y', strtotime($row['created_at'])); ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php else: ?>
    <div class="bg-white border border-dashed border-gray-300 rounded-3xl p-12 text-center flex flex-col items-center shadow-sm">
        <p class="text-gray-900 font-bold mb-1">No feedback yet.</p>
        <p class="text-sm text-gray-500">Check the menu to review your meals.</p>
    </div>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>

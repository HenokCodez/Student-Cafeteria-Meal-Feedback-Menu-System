<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

$msg = '';

if (isset($_GET['delete_id'])) {
    $del_id = intval($_GET['delete_id']);
    $del_stmt = $conn->prepare("DELETE FROM feedback WHERE id = ?");
    $del_stmt->bind_param("i", $del_id);
    if($del_stmt->execute()){
         header('Location: view_feedback.php?msg=deleted');
         exit;
    }
}

if (isset($_GET['msg']) && $_GET['msg'] === 'deleted') {
    $msg = "<p class='success'>Feedback permanently deleted.</p>";
}

// Join query to get user info and meal info for admins
$query = "SELECT f.id, u.username, u.email, m.name as meal_name, f.rating, f.comment, f.created_at
          FROM feedback f
          JOIN users u ON f.user_id = u.id
          JOIN menu_items m ON f.menu_item_id = m.id
          ORDER BY f.created_at DESC";
$result = $conn->query($query);

// Logic finished. Now safe to output HTML headers.
$path = '../';
require_once '../includes/header.php';
?>

<div class="mb-10">
    <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 mb-2">Student Feedback.</h2>
    <p class="text-gray-500 text-sm">Read and moderate all ratings seamlessly.</p>
</div>

<?php if($msg): ?>
    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 text-sm p-4 rounded-xl text-center shadow-sm block">
        <?php echo str_replace("<p class='success'>", "", str_replace("</p>", "", $msg)); ?>
    </div>
<?php endif; ?>

<?php if ($result->num_rows > 0): ?>
    <div class="bg-white border border-gray-200 rounded-3xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-gray-200 text-xs uppercase tracking-wider text-gray-500">
                        <th class="px-6 py-4 font-bold">Student</th>
                        <th class="px-6 py-4 font-bold">Meal Item</th>
                        <th class="px-6 py-4 font-bold">Score</th>
                        <th class="px-6 py-4 font-bold">Comment</th>
                        <th class="px-6 py-4 font-bold">Date</th>
                        <th class="px-6 py-4 font-bold text-right">Controls</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900"><?php echo htmlspecialchars($row['username']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?php echo htmlspecialchars($row['meal_name']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                    <?php echo $row['rating']; ?> / 5
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate" title="<?php echo htmlspecialchars($row['comment']); ?>">
                                <?php echo htmlspecialchars($row['comment']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400"><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <a href="view_feedback.php?delete_id=<?php echo $row['id']; ?>" onclick="return confirmAction();" class="inline-flex items-center justify-center px-4 py-2 text-xs font-bold text-red-600 bg-red-50 border border-red-100 rounded-lg hover:bg-red-600 hover:text-white transition duration-200">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php else: ?>
    <div class="bg-white border border-dashed border-gray-300 rounded-3xl p-12 text-center shadow-sm">
        <p class="text-gray-500">No feedback has been submitted yet by any user.</p>
    </div>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>

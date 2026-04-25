<?php
session_start();
require_once '../config/db.php';

// strict security check for admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

$msg = '';

// Handle Delete logic
if (isset($_GET['delete_id'])) {
    $del_id = intval($_GET['delete_id']);
    $del_stmt = $conn->prepare("DELETE FROM menu_items WHERE id = ?");
    $del_stmt->bind_param("i", $del_id);
    if($del_stmt->execute()){
        header('Location: manage_menu.php?msg=deleted');
        exit;
    }
}

// Show success on redirect
if (isset($_GET['msg']) && $_GET['msg'] === 'deleted') {
    $msg = "<p class='success'>Menu Item successfully deleted.</p>";
}

// Handle Add Item Create logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $desc = trim($_POST['description']);
    $day = $_POST['day_of_week'];
    $type = $_POST['meal_type'];

    if (!empty($name) && !empty($day) && !empty($type)) {
        $insert = $conn->prepare("INSERT INTO menu_items (name, description, day_of_week, meal_type) VALUES (?, ?, ?, ?)");
        $insert->bind_param("ssss", $name, $desc, $day, $type);
        if ($insert->execute()) {
            $msg = "<p class='success'>New meal added to the menu!</p>";
        } else {
            $msg = "<p class='error'>Failed insertion in database.</p>";
        }
    } else {
        $msg = "<p class='error'>Name, day, and type are mostly required.</p>";
    }
}

// Fetch current menus
$result = $conn->query("SELECT * FROM menu_items ORDER BY id DESC");

// Logic finished. Now safe to output HTML headers.
$path = '../';
require_once '../includes/header.php';
?>

<div class="mb-10">
    <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 mb-2">Manage Menu.</h2>
    <p class="text-gray-500 text-sm">Add or remove items from the weekly cafeteria board.</p>
</div>

<?php if($msg): ?>
    <div class="mb-6">
        <?php echo str_replace("class='success'", "class='bg-green-50 border border-green-200 text-green-700 text-sm p-4 rounded-xl text-center shadow-sm block'", str_replace("class='error'", "class='bg-red-50 border border-red-200 text-red-600 text-sm p-4 rounded-xl text-center shadow-sm block'", $msg)); ?>
    </div>
<?php endif; ?>

<div class="bg-white border border-gray-200 rounded-3xl p-8 mb-12 shadow-sm">
    <h3 class="text-xl font-bold text-gray-900 mb-6">Add New Meal</h3>
    <form action="" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Meal Name</label>
            <input type="text" name="name" required class="w-full bg-slate-50 text-gray-900 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Day of Week</label>
            <div class="relative">
                <select name="day_of_week" class="w-full bg-slate-50 text-gray-900 border border-gray-200 rounded-xl px-4 py-3 appearance-none focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition">
                    <option value="Monday">Monday</option>
                    <option value="Tuesday">Tuesday</option>
                    <option value="Wednesday">Wednesday</option>
                    <option value="Thursday">Thursday</option>
                    <option value="Friday">Friday</option>
                    <option value="Saturday">Saturday</option>
                    <option value="Sunday">Sunday</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-400">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Meal Description</label>
            <input type="text" name="description" required class="w-full bg-slate-50 text-gray-900 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Meal Type</label>
            <div class="relative">
                <select name="meal_type" class="w-full bg-slate-50 text-gray-900 border border-gray-200 rounded-xl px-4 py-3 appearance-none focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition">
                    <option value="Breakfast">Breakfast</option>
                    <option value="Lunch">Lunch</option>
                    <option value="Dinner">Dinner</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-400">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>
        </div>

        <div class="md:col-span-2 pt-2">
            <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition duration-200 shadow-md shadow-indigo-600/20">
                Save to Menu
            </button>
        </div>
    </form>
</div>

<h3 class="text-xl font-bold text-gray-900 mb-6">Current Menu Items</h3>
<?php if ($result->num_rows > 0): ?>
    <div class="bg-white border border-gray-200 rounded-3xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-gray-200 text-xs uppercase tracking-wider text-gray-500">
                        <th class="px-6 py-4 font-bold">Item Name</th>
                        <th class="px-6 py-4 font-bold">Type</th>
                        <th class="px-6 py-4 font-bold">Day</th>
                        <th class="px-6 py-4 font-bold text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900"><?php echo htmlspecialchars($row['name']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?php echo $row['meal_type']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?php echo $row['day_of_week']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <a href="manage_menu.php?delete_id=<?php echo $row['id']; ?>" onclick="return confirmAction();" class="inline-flex items-center justify-center px-4 py-2 text-xs font-bold text-red-600 bg-red-50 border border-red-100 rounded-lg hover:bg-red-600 hover:text-white transition duration-200">
                                    Remove
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
        <p class="text-gray-500">The menu is currently empty.</p>
    </div>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>

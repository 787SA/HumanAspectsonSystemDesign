<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'inventory_staff') {
    header("Location: sign-in.php");
    exit();
}

// Include database connection
include 'db.php';

// Fetch data for widgets
$totalStock = $pdo->query("SELECT SUM(quantity) AS total FROM inventory")->fetch()['total'];
$criticalStock = $pdo->query("SELECT COUNT(*) AS critical FROM inventory WHERE quantity < reorder_level")->fetch()['critical'];
$recentOrders = $pdo->query("SELECT * FROM orders ORDER BY order_date DESC LIMIT 5")->fetchAll();

// Fetch inventory data
$inventoryItems = $pdo->query("SELECT * FROM inventory")->fetchAll();

// Fetch suppliers
$suppliers = $pdo->query("SELECT * FROM suppliers")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'add') {
        // Handle adding a new inventory item
        $productName = $_POST['product_name'];
        $quantity = $_POST['quantity'];
        $reorderLevel = $_POST['reorder_level'];
        $supplierId = $_POST['supplier_id'];
        $stmt = $pdo->prepare("INSERT INTO inventory (product_name, quantity, reorder_level, supplier_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$productName, $quantity, $reorderLevel, $supplierId]);
        // Redirect to avoid form resubmission
        header("Location: inventory_staff_dashboard.php");
        exit();
    } elseif ($_POST['action'] == 'delete') {
        // Handle deleting an inventory item
        $itemId = $_POST['item_id'];
        $stmt = $pdo->prepare("DELETE FROM inventory WHERE id = ?");
        $stmt->execute([$itemId]);
        // Redirect to avoid form resubmission
        header("Location: inventory_staff_dashboard.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Staff Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #fff;
            color: #333;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        header {
            background-color: #d10a1f;
            color: white;
            padding: 10px 20px;
            text-align: center;
        }
        nav ul {
            list-style: none;
            padding: 0;
            display: flex;
            justify-content: center;
            gap: 20px;
        }
        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }
        main {
            flex: 1; 
            padding: 20px;
        }
        section {
            margin-bottom: 20px;
        }
        h2 {
            color: #d10a1f;
        }
        .table-container {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        .th {
            background-color: #f2f2f2;
        }
        .widget {
            background-color: #f9f9f9;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        form {
            background-color: #f9f9f9;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        form label {
            display: block;
            margin-bottom: 5px;
        }
        form input[type="text"],
        form input[type="number"],
        form button,
        form select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        form button {
            background-color: #d10a1f;
            color: white;
            font-weight: bold;
            cursor: pointer;
            border: none;
        }
        form button:hover {
            background-color: #b3091c;
        }
        a.edit-button {
            display: inline-block;
            padding: 10px;
            background-color: #d10a1f;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-left: 5px;
        }
        a.edit-button:hover {
            background-color: #b3091c;
        }
        button {
            background-color: #d10a1f;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            padding: 10px;
            margin: 0;
        }
        button:hover {
            background-color: #b3091c;
        }
        footer {
            background-color: #d10a1f;
            color: white;
            padding: 10px 20px;
            text-align: center;
            margin-top: auto;
        }
    </style>
  
    <script src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
    <script>
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'en',
                includedLanguages: 'ar,zh,cs,da,nl,en,fi,fr,de,el,hi,hu,id,it,ja,ko,no,pl,pt,ru,es,sv,th,tr,vi',
                layout: google.translate.TranslateElement.InlineLayout.SIMPLE
            }, 'google_translate_element');
        }
    </script>
</head>
<body>
    <header>
        <h1>Inventory Staff Dashboard</h1>
        <nav>
            <ul>
                <li><a href="#inventory">Inventory</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <section id="overview">
            <h2>Overview</h2>
            <div class="widget">
                <h3>Total Stock Levels</h3>
                <p data-translate><?php echo $totalStock; ?></p>
            </div>
            <div class="widget">
                <h3>Critical Stock Alerts</h3>
                <p data-translate><?php echo $criticalStock; ?> items need replenishment</p>
            </div>
            <div class="widget">
                <h3>Recent Orders</h3>
                <ul>
                    <?php foreach ($recentOrders as $order): ?>
                        <li data-translate><?php echo $order['order_date'] . ' - ' . ($order['order_details'] ?? 'No details available'); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </section>
        <section id="inventory">
            <h2 data-translate>Inventory Management</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th data-translate>Product Name</th>
                            <th data-translate>Quantity</th>
                            <th data-translate>Reorder Level</th>
                            <th data-translate>Supplier</th>
                            <th data-translate>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($inventoryItems as $item): ?>
                            <tr>
                                <td data-translate><?php echo $item['product_name']; ?></td>
                                <td data-translate><?php echo $item['quantity']; ?></td>
                                <td data-translate><?php echo $item['reorder_level']; ?></td>
                                <td data-translate><?php echo $item['supplier_id']; ?></td>
                                <td>
                                    <form method="POST" action="inventory_staff_dashboard.php" style="display:inline-block;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                        <button type="submit" data-translate>Delete</button>
                                    </form>
                                    <a href="edit_inventory.php?id=<?php echo $item['id']; ?>" class="edit-button" data-translate>Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <h3 data-translate>Add New Inventory Item</h3>
            <form method="POST" action="inventory_staff_dashboard.php">
                <input type="hidden" name="action" value="add">
                <label for="product_name" data-translate>Product Name:</label>
                <input type="text" id="product_name" name="product_name" required>
                <label for="quantity" data-translate>Quantity:</label>
                <input type="number" id="quantity" name="quantity" required>
                <label for="reorder_level" data-translate>Reorder Level:</label>
                <input type="number" id="reorder_level" name="reorder_level" required>
                <label for="supplier_id" data-translate>Supplier:</label>
                <select id="supplier_id" name="supplier_id" required>
                    <?php foreach ($suppliers as $supplier): ?>
                        <option value="<?php echo $supplier['id']; ?>" data-translate><?php echo $supplier['supplier_name']; ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" data-translate>Add Item</button>
            </form>
        </section>
    </main>
    <footer>
        <div id="google_translate_element"></div>
        <button onclick="googleTranslateElementInit()">Translate</button>
    </footer>
</body>
</html>

           

   





            


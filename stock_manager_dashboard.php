<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'stock_manager') {
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

// Fetch supplier orders with supplier details
$supplierOrders = $pdo->query("SELECT so.*, s.supplier_name, s.contact_info FROM supplier_orders so JOIN suppliers s ON so.supplier_id = s.id ORDER BY order_date DESC")->fetchAll();

// Fetch suppliers
$suppliers = $pdo->query("SELECT * FROM suppliers")->fetchAll();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'add':
            // Handle adding a new inventory item
            $stmt = $pdo->prepare("INSERT INTO inventory (product_name, quantity, reorder_level, supplier_id) VALUES (?, ?, ?, ?)");
            $stmt->execute([$_POST['product_name'], $_POST['quantity'], $_POST['reorder_level'], $_POST['supplier_id']]);
            break;
        case 'order':
            // Handle ordering supplies
            $arrivalDateTime = $_POST['arrival_date'] . ' ' . $_POST['arrival_time'];
            $stmt = $pdo->prepare("INSERT INTO supplier_orders (product_name, quantity, supplier_id, number_of_supplies, arrival_date) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$_POST['product_name'], $_POST['quantity'], $_POST['supplier_id'], $_POST['number_of_supplies'], $arrivalDateTime]);
            break;
        case 'delete':
            // Handle deleting an order
            $stmt = $pdo->prepare("DELETE FROM supplier_orders WHERE id = ?");
            $stmt->execute([$_POST['order_id']]);
            break;
    }
    // Redirect to avoid form resubmission
    header("Location: stock_manager_dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Manager Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #fff;
            color: #333;
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
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
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
        form input, form select, form button {
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
        footer {
            background-color: #d10a1f;
            color: white;
            padding: 10px 20px;
            text-align: center;
        }
    </style>
    <!-- Add this script tag -->
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
        <h1>Stock Manager Dashboard</h1>
        <nav>
            <ul>
                <li><a href="#overview">Overview</a></li>
                <li><a href="#inventory">Inventory</a></li>
                <li><a href="#reports">Reports</a></li>
                <li><a href="#suppliers">Suppliers</a></li>
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
                                <td data-translate><?php echo htmlspecialchars($item['product_name']); ?></td>
                                <td data-translate><?php echo htmlspecialchars($item['quantity']); ?></td>
                                <td data-translate><?php echo htmlspecialchars($item['reorder_level']); ?></td>
                                <td data-translate><?php echo htmlspecialchars($item['supplier_id']); ?></td>
                                <td>
                                    <a href="edit_inventory.php?id=<?php echo $item['id']; ?>" data-translate>Edit</a>
                                    <a href="delete_inventory.php?id=<?php echo $item['id']; ?>" data-translate>Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <h3 data-translate>Add New Inventory Item</h3>
            <form method="POST" action="stock_manager_dashboard.php">
                <input type="hidden" name="action" value="add">
                <label for="product_name" data-translate>Product Name:</label>
                <input type="text" id="product_name" name="product_name" required>
                <label for="quantity" data-translate>Quantity:</label>
                <input type="number" id="quantity" name="quantity" required>
                <label for="reorder_level" data-translate>Reorder Level:</label>
                <input type="number" id="reorder_level" name="reorder_level" required>
                <label for="supplier_id" data-translate>Supplier ID:</label>
                <input type="number" id="supplier_id" name="supplier_id" required>
                <button type="submit" data-translate>Add Item</button>
            </form>
        </section>
        <section id="reports">
            <h2 data-translate>Reports and Analytics</h2>
            
        </section>
        <section id="suppliers">
            <h2 data-translate>Supplier Coordination</h2>
            <div class="supplier-container">
                <?php foreach ($suppliers as $supplier): ?>
                    <div class="supplier-card">
                        <h3 data-translate><?= htmlspecialchars($supplier['supplier_name']); ?></h3>
                        <p data-translate>Contact: <?= htmlspecialchars($supplier['contact_info']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
            <h3 data-translate>Add Supply Information</h3>
            <form method="POST" action="stock_manager_dashboard.php">
                <input type="hidden" name="action" value="order">
                <label for="product_name" data-translate>Product Name:</label>
                <select id="product_name" name="product_name" required>
                    <option value="chicken" data-translate>Chicken</option>
                    <option value="beef" data-translate>Beef</option>
                    <option value="lamb" data-translate>Lamb</option>
                    <option value="fish" data-translate>Fish</option>
                    <option value="fruit" data-translate>Fruit</option>
                    <option value="veg" data-translate>Veg</option>
                </select>
                <label for="quantity" data-translate>Quantity (Crates):</label>
                <input type="number" id="quantity" name="quantity" required>
                <label for="supplier_id" data-translate>Supplier:</label>
                <select id="supplier_id" name="supplier_id" required>
                    <?php foreach ($suppliers as $supplier): ?>
                        <option value="<?= $supplier['id']; ?>" data-translate><?= htmlspecialchars($supplier['supplier_name']); ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="number_of_supplies" data-translate>No of Supplies:</label>
                <input type="number" id="number_of_supplies" name="number_of_supplies" required>
                <label for="arrival_date" data-translate>Arrival Date:</label>
                <input type="date" id="arrival_date" name="arrival_date" required>
                <label for="arrival_time" data-translate>Arrival Time:</label>
                <input type="time" id="arrival_time" name="arrival_time" required>
                <button type="submit" data-translate>Order Supplies</button>
            </form>
            <h3 data-translate>Recent Supplier Orders</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th data-translate>Product Name</th>
                            <th data-translate>Quantity</th>
                            <th data-translate>Supplier</th>
                            <th data-translate>Arrival Date</th>
                            <th data-translate>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($supplierOrders as $order): ?>
                            <tr>
                                <td data-translate><?= htmlspecialchars($order['product_name']); ?></td>
                                <td data-translate><?= htmlspecialchars($order['quantity']); ?></td>
                                <td data-translate><?= htmlspecialchars($order['supplier_name']); ?></td>
                                <td data-translate><?= htmlspecialchars($order['arrival_date']); ?></td>
                                <td>
                                    <form method="POST" action="stock_manager_dashboard.php" style="display:inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="order_id" value="<?= $order['id']; ?>">
                                        <button type="submit" data-translate onclick="return confirm('Are you sure you want to delete this order?');">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
    <footer>
        <div id="google_translate_element"></div>
        <button onclick="googleTranslateElementInit()">Translate</button>
    </footer>
</body>
</html>






<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'kitchen_inventory_staff') {
    header("Location: sign-in.php");
    exit();
}

// Include database connection
include 'db.php';

// Fetch data for widgets
$totalStock = $pdo->query("SELECT SUM(quantity) AS total FROM inventory")->fetch()['total'];
$criticalStock = $pdo->query("SELECT COUNT(*) AS critical FROM inventory WHERE quantity < reorder_level")->fetch()['critical'];

// Fetch inventory data
$inventoryItems = $pdo->query("SELECT * FROM inventory")->fetchAll();

// Define recipes
$recipes = [
    'Chicken and Chips' => ['chicken' => 0.5, 'veg' => 0.25],
    'Steak and Chips' => ['beef' => 1, 'veg' => 0.25],
    'Fish and Chips' => ['fish' => 0.5, 'veg' => 0.25],
    'Lamb Curry' => ['lamb' => 1],
    'Apple Pie' => ['fruit' => 0.5]
];

// Calculate maximum number of dishes
$maxDishes = [];
foreach ($recipes as $dish => $ingredients) {
    $maxDishes[$dish] = PHP_INT_MAX;
    foreach ($ingredients as $ingredient => $quantity) {
        foreach ($inventoryItems as $item) {
            if (strtolower($item['product_name']) === strtolower($ingredient)) {
                $maxDishes[$dish] = min($maxDishes[$dish], floor($item['quantity'] / $quantity));
                break;
            }
        }
    }
}

// Handle updating inventory
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $itemId = $_POST['item_id'];
    if ($_POST['action'] === 'add_one') {
        $stmt = $pdo->prepare("UPDATE inventory SET quantity = quantity + 1 WHERE id = ?");
        $stmt->execute([$itemId]);
    } elseif ($_POST['action'] === 'subtract_one') {
        $stmt = $pdo->prepare("UPDATE inventory SET quantity = quantity - 1 WHERE id = ?");
        $stmt->execute([$itemId]);
    }
    // Redirect to avoid form resubmission
    header("Location: kitchen_inventory_staff.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitchen Inventory Staff Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            color: #333;
        }
        header {
            background-color: #1E90FF;
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
            color: #1E90FF;
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
        th {
            background-color: #f2f2f2;
        }
        .widget, form {
            background-color: #f9f9f9;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        form label {
            display: block;
            margin-bottom: 5px;
        }
        form input, form button {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        form button, .edit-button {
            background-color: #1E90FF;
            color: white;
            font-weight: bold;
            cursor: pointer;
            border: none;
        }
        form button:hover, .edit-button:hover {
            background-color: #1C86EE;
        }
        .edit-button {
            padding: 10px;
            margin-left: 5px;
            border-radius: 5px;
        }
        footer {
            background-color: #1E90FF;
            color: white;
            padding: 10px 20px;
            text-align: center;
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
        <h1>Kitchen Inventory Staff Dashboard</h1>
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
                <p data-translate><?= htmlspecialchars($totalStock); ?></p>
            </div>
            <div class="widget">
                <h3>Critical Stock Alerts</h3>
                <p data-translate><?= htmlspecialchars($criticalStock); ?> items need replenishment</p>
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
                                <td data-translate><?= htmlspecialchars($item['product_name']); ?></td>
                                <td data-translate><?= htmlspecialchars($item['quantity']); ?></td>
                                <td data-translate><?= htmlspecialchars($item['reorder_level']); ?></td>
                                <td data-translate><?= htmlspecialchars($item['supplier_id']); ?></td>
                                <td>
                                    <form method="POST" action="kitchen_inventory_staff.php" style="display:inline-block;">
                                        <input type="hidden" name="action" value="add_one">
                                        <input type="hidden" name="item_id" value="<?= $item['id']; ?>">
                                        <button type="submit" data-translate>Add One</button>
                                    </form>
                                    <form method="POST" action="kitchen_inventory_staff.php" style="display:inline-block;">
                                        <input type="hidden" name="action" value="subtract_one">
                                        <input type="hidden" name="item_id" value="<?= $item['id']; ?>">
                                        <button type="submit" data-translate>Subtract One</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
        <section id="recipes">
            <h2 data-translate>Recipe Ratios</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th data-translate>Dish</th>
                            <th data-translate>Max Dishes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($maxDishes as $dish => $quantity): ?>
                            <tr>
                                <td data-translate><?= htmlspecialchars($dish); ?></td>
                                <td data-translate><?= htmlspecialchars($quantity); ?></td>
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

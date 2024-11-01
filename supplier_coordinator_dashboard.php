<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'supplier_coordinator') {
    header("Location: sign-in.php");
    exit();
}

include 'db.php';

// Fetch supplier orders with supplier details
$supplierOrders = $pdo->query("
    SELECT so.*, s.supplier_name, s.contact_info
    FROM supplier_orders so
    JOIN suppliers s ON so.supplier_id = s.id
    ORDER BY order_date DESC
")->fetchAll();

// Fetch suppliers
$suppliers = $pdo->query("SELECT * FROM suppliers")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'order') {
        $productName = $_POST['product_name'];
        $quantity = $_POST['quantity'];
        $supplierId = $_POST['supplier_id'];
        $numberOfSupplies = $_POST['number_of_supplies'];
        $arrivalDateTime = $_POST['arrival_date'] . ' ' . $_POST['arrival_time'];
        $stmt = $pdo->prepare("INSERT INTO supplier_orders (product_name, quantity, supplier_id, number_of_supplies, arrival_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$productName, $quantity, $supplierId, $numberOfSupplies, $arrivalDateTime]);
        header("Location: supplier_coordinator_dashboard.php");
        exit();
    } elseif ($_POST['action'] == 'delete') {
        $orderId = $_POST['order_id'];
        $stmt = $pdo->prepare("DELETE FROM supplier_orders WHERE id = ?");
        $stmt->execute([$orderId]);
        header("Location: supplier_coordinator_dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Coordinator Dashboard</title>
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
        <h1>Supplier Coordinator Dashboard</h1>
        <nav>
            <ul>
                <li><a href="#orders">Orders</a></li>
                <li><a href="logout.php">Logout</a></li> 
            </ul>
        </nav>
    </header>
    <main>
        <section id="suppliers">
            <h2>Suppliers</h2>
            <div class="supplier-container">
                <?php foreach ($suppliers as $supplier): ?>
                    <div class="supplier-card">
                        <h3 data-translate><?= htmlspecialchars($supplier['supplier_name']); ?></h3>
                        <p data-translate>Contact: <?= htmlspecialchars($supplier['contact_info']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
        <section id="add-supply">
            <h2 data-translate>Add Supply Information</h2>
            <form method="POST" action="supplier_coordinator_dashboard.php">
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
                        <option value="<?= htmlspecialchars($supplier['id']); ?>" data-translate><?= htmlspecialchars($supplier['supplier_name']); ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="arrival_date" data-translate>Arrival Date:</label>
                <input type="date" id="arrival_date" name="arrival_date" required>
                <label for="arrival_time" data-translate>Arrival Time:</label>
                <input type="time" id="arrival_time" name="arrival_time" required>
                <button type="submit" data-translate>Add Supply</button>
            </form>
        </section>
        <section id="view-supplies">
            <h2 data-translate>View Added Supplies</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th data-translate>Product Name</th>
                            <th data-translate>Quantity</th>
                            <th data-translate>Supplier</th>
                            <th data-translate>Arrival Date and Time</th>
                            <th data-translate>Contact Info</th>
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
                                <td data-translate><?= htmlspecialchars($order['contact_info']); ?></td>
                                <td>
                                    <form method="POST" action="supplier_coordinator_dashboard.php" style="display: inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['id']); ?>">
                                        <button type="submit" data-translate>Cancel</button>
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

        




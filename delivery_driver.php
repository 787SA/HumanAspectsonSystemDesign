<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'delivery_driver') {
    header("Location: sign-in.php");
    exit();
}

// Include database connection
include 'db.php';

// Fetch orders data
$orders = $pdo->query("SELECT * FROM orders WHERE order_status = 'pending' OR order_status = 'in_progress'")->fetchAll();

// Handle updating order status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $orderId = $_POST['order_id'];
    $status = ($_POST['action'] === 'complete') ? 'completed' : 'cancelled';
    $stmt = $pdo->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
    $stmt->execute([$status, $orderId]);
    // Redirect to avoid form resubmission
    header("Location: delivery_driver.php");
    exit();
}

// Fetch completed orders count for today
$completedOrdersCount = $pdo->query("SELECT COUNT(*) FROM orders WHERE order_status = 'completed' AND DATE(order_updated_at) = CURDATE()")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Driver Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #ffffff;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        header {
            background-color: #1E90FF;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        nav ul {
            list-style: none;
            padding: 0;
            display: flex;
            gap: 20px;
        }
        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }
        main {
            flex: 1; /* Ensure main content takes up remaining space */
            padding: 20px;
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
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
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
        form button {
            background-color: #1E90FF;
            color: white;
            font-weight: bold;
            cursor: pointer;
            border: none;
            padding: 5px 10px;
        }
        form button:hover {
            background-color: #1C86EE;
        }
        footer {
            background-color
            footer {
            background-color: #1E90FF;
            color: white;
            padding: 10px 20px;
            text-align: center;
            margin-top: auto; /* Push footer to the bottom */
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
        <h1>Delivery Driver Dashboard</h1>
        <nav>
            <ul>
                <li><a href="logout.php" data-translate>Logout</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <section id="overview">
            <h2>Overview</h2>
            <div class="widget">
                <h3>Total Orders Completed Today</h3>
                <p data-translate><?= htmlspecialchars($completedOrdersCount); ?></p>
            </div>
        </section>
        <section id="orders">
            <h2 data-translate>Orders</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th data-translate>Order Number</th>
                            <th data-translate>Name</th>
                            <th data-translate>Address</th>
                            <th data-translate>Email</th>
                            <th data-translate>Order Status</th>
                            <th data-translate>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td data-translate><?= htmlspecialchars($order['order_number']); ?></td>
                                <td data-translate><?= htmlspecialchars($order['customer_name']); ?></td>
                                <td data-translate><?= htmlspecialchars($order['customer_address']); ?></td>
                                <td data-translate><?= htmlspecialchars($order['customer_email']); ?></td>
                                <td data-translate><?= htmlspecialchars($order['order_status']); ?></td>
                                <td>
                                    <form method="POST" action="delivery_driver.php" style="display:inline-block;">
                                        <input type="hidden" name="action" value="complete">
                                        <input type="hidden" name="order_id" value="<?= $order['id']; ?>">
                                        <button type="submit" data-translate>Complete</button>
                                    </form>
                                    <form method="POST" action="delivery_driver.php" style="display:inline-block;">
                                        <input type="hidden" name="action" value="cancel">
                                        <input type="hidden" name="order_id" value="<?= $order['id']; ?>">
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

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dish = $_POST['dish'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $address = $_POST['address'] ?? '';
    $collection = isset($_POST['collection']) ? 1 : 0;
    $completion_time = date("Y-m-d H:i:s", strtotime("+40 minutes"));

    // Prepare order details
    $orderDetails = "$dish for $name";

    // Insert order into database
    $stmt = $pdo->prepare("INSERT INTO orders (customer_id, stock_id, quantity, total_price, order_date, order_status, order_updated_at, order_details) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([uniqid(), $dish, 1, 0, date("Y-m-d H:i:s"), 'pending', $completion_time, $orderDetails]);

    echo "Order placed successfully. Estimated completion time is around 30-40 minutes.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Place Order</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
        }
        .order-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }
        .order-container h2 {
            margin-bottom: 20px;
            color: #1E90FF;
        }
        .order-container input[type="text"],
        .order-container input[type="email"],
        .order-container select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .order-container button {
            width: 100%;
            padding: 10px;
            background-color: #1E90FF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .order-container button:hover {
            background-color: #1C86EE;
        }
    </style>
</head>
<body>
    <div class="order-container">
        <h2>Place Your Order</h2>
        <form method="POST" action="customer_order.php">
            <label for="dish">Dish:</label>
            <select id="dish" name="dish" required>
                <option value="Chicken and Chips">Chicken and Chips</option>
                <option value="Steak and Chips">Steak and Chips</option>
                <option value="Fish and Chips">Fish and Chips</option>
                <option value="Lamb Curry">Lamb Curry</option>
                <option value="Apple Pie">Apple Pie</option>
            </select>
            <input type="text" name="name" placeholder="Your Name" required>
            <input type="email" name="email" placeholder="Your Email" required>
            <input type="text" name="address" placeholder="Your Address">
            <label>
                <input type="checkbox" name="collection"> Collection
            </label>
            <button type="submit">Place Order</button>
        </form>
    </div>
</body>
</html>



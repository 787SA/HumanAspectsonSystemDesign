<?php
include 'db.php'; // Include database connection

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_name = $_POST['item_name'];
    $quantity = $_POST['quantity'];
    $unit_price = $_POST['unit_price'];

    // Insert stock into database
    $sql = "INSERT INTO stocks (item_name, quantity, unit_price) VALUES ('$item_name', $quantity, $unit_price)";
    if ($conn->query($sql) === TRUE) {
        echo "New stock item added successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<h2>Add New Stock</h2>
<form method="POST" action="">
    Item Name: <input type="text" name="item_name" required><br>
    Quantity: <input type="number" name="quantity" required><br>
    Unit Price: <input type="text" name="unit_price" required><br>
    <input type="submit" value="Add Stock">
</form>

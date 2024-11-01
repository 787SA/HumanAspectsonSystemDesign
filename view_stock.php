<?php
include 'db.php'; // Include database connection

// Fetch stock items from database
$sql = "SELECT * FROM stocks";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock List</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%; /* Force full width */
            table-layout: fixed; /* Fix column width to make table take up full width */
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
            word-wrap: break-word; /* Ensure long text wraps */
        }
        th {
            background-color: #f2f2f2;
            width: 25%; /* Make sure each column takes up a fixed percentage */
        }
        td {
            width: 25%; /* Make sure each column takes up a fixed percentage */
        }
    </style>
</head>
<body>

<h2>Stock List</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Item Name</th>
        <th>Quantity</th>
        <th>Unit Price</th>
    </tr>

    <?php
    if ($result->num_rows > 0) {
        // Output data of each row
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . $row["id"]. "</td>
                    <td>" . $row["item_name"]. "</td>
                    <td>" . $row["quantity"]. "</td>
                    <td>" . $row["unit_price"]. "</td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='4'>No stock items found</td></tr>";
    }
    ?>
</table>

</body>
</html>

<?php
// Passwords for different roles
$stockManagerPassword = 'SMPASSWORD';  // Replace with the actual password for the stock manager
$inventoryStaffPassword = 'ISPASSWORD';  // Replace with the actual password for the inventory staff
$supplierCoordinatorPassword = 'SCPASSWORD';  // Replace with the actual password for the supplier coordinator
$kitchenInventoryStaffPassword = 'KISPASSWORD';  // Replace with the actual password for the kitchen inventory staff
$deliveryDriverPassword = 'DDPASSWORD';  // Replace with the actual password for the delivery driver
$kitchenManagerPassword = 'KMPASSWORD';  // Replace with the actual password for the kitchen manager

// Hash the passwords
$stockManagerPasswordHash = password_hash($stockManagerPassword, PASSWORD_BCRYPT);
$inventoryStaffPasswordHash = password_hash($inventoryStaffPassword, PASSWORD_BCRYPT);
$supplierCoordinatorPasswordHash = password_hash($supplierCoordinatorPassword, PASSWORD_BCRYPT);
$kitchenInventoryStaffPasswordHash = password_hash($kitchenInventoryStaffPassword, PASSWORD_BCRYPT);
$deliveryDriverPasswordHash = password_hash($deliveryDriverPassword, PASSWORD_BCRYPT);
$kitchenManagerPasswordHash = password_hash($kitchenManagerPassword, PASSWORD_BCRYPT);

// Display the hashed passwords
echo "Stock Manager Hashed Password: " . $stockManagerPasswordHash . "<br>";
echo "Inventory Staff Hashed Password: " . $inventoryStaffPasswordHash . "<br>";
echo "Supplier Coordinator Hashed Password: " . $supplierCoordinatorPasswordHash . "<br>";
echo "Kitchen Inventory Staff Hashed Password: " . $kitchenInventoryStaffPasswordHash . "<br>";
echo "Delivery Driver Hashed Password: " . $deliveryDriverPasswordHash . "<br>";
echo "Kitchen Manager Hashed Password: " . $kitchenManagerPasswordHash . "<br>";
?>


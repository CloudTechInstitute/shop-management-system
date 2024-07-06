<?php
include ('components/connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the item ID from the POST request
    $id = intval($_POST['id']);

    // Prepare and execute the delete statement
    $stmt = $conn->prepare("DELETE FROM items WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Redirect to a success page or back to the items list with a success message
        header("Location: items.php?message=Item+deleted+successfully");
        exit();
    } else {
        // Redirect to an error page or back to the items list with an error message
        header("Location: items.php?error=Error+deleting+item");
        exit();
    }

}

$conn->close();
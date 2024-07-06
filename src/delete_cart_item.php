<?php
include ('components/connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the item ID from the POST request
    $id = intval($_POST['id']);

    // Prepare and execute the delete statement
    $stmt = $conn->prepare("DELETE FROM cart WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Redirect to a success page or back to the cart with a success message
        header("Location: cart.php?message=Item+deleted+successfully");
        exit();
    } else {
        // Redirect to an error page or back to the cart with an error message
        header("Location: cart.php?error=Error+deleting+item");
        exit();
    }

}

// Close the database connection
$conn->close();
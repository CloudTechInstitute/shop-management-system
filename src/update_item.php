<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('location: login.php');
    exit();
}
?>
<?php
include ('components/connection.php');

// Check if the form is submitted
if (isset($_POST['add_btn'])) {
    // Validate input
    $item = $_POST['item'];
    $quantity = $_POST['quantity'];

    if (empty($item) || $quantity <= 0) {
        echo "<script>alert('Please enter valid item name and quantity');</script>";
    } else {
        // Fetch current quantity from the database
        $stmt = $conn->prepare("SELECT quantity FROM items WHERE item = ?");
        $stmt->bind_param("s", $item);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $current_quantity = intval($row['quantity']);

            // Calculate new quantity
            $new_quantity = $current_quantity + $quantity;

            // Update the item's quantity
            $update_stmt = $conn->prepare("UPDATE items SET quantity = ? WHERE item = ?");
            $update_stmt->bind_param("is", $new_quantity, $item);

            if ($update_stmt->execute()) {
                echo "<script>alert('Item updated successfully');</script>";
            } else {
                echo "<script>alert('Error updating item: " . $conn->error . "');</script>";
            }

            // Close the update statement
            $update_stmt->close();
        } else {
            echo "<script>alert('Item not found');</script>";
        }

        // Close the select statement
        $stmt->close();
    }
}
?>
<?php
// Fetch item details for the form
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Prepare SQL statement to fetch item details
    $stmt = $conn->prepare("SELECT * FROM items WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $item = $result->fetch_assoc();
    } else {
        header("Location: items.php?error=Item+not+found");
        exit();
    }

    // Close the statement
    $stmt->close();
} else {
    header("Location: items.php?error=Invalid+request");
    exit();
}
?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="./output.css" rel="stylesheet" />
    <link rel="stylesheet" href="bootstrap-icons/font/bootstrap-icons.css" />
</head>

<body>
    <div class="bg-[#1D1D42] h-screen">
        <!--top header begins  -->
        <?php include 'components/topheader.php'; ?>
        <!--top header ends  -->
        <div class="flex gap-6 justify-between px-8 items-start">
            <!-- left sidebar start -->
            <?php include 'components/leftsidebar.php'; ?>
            <!-- left sidebar ends -->

            <div class="bg-[#141432] p-4 flex-1 h-[620px] rounded-md overflow-x-hidden overflow-y-auto">
                <!-- children starts here -->
                <div class="text-white font-semibold uppercase mb-6">Update Stock</div>
                <div class="grid grid-cols-2 gap-6">
                    <form method="post" action="update_item.php">
                        <input type="text" placeholder="Item name"
                            class="outline outline-1 outline-[#524EEE] p-2 rounded-md bg-transparent text-white w-full mb-3"
                            autocomplete="off" name="item" value="<?php echo htmlspecialchars($item['item']); ?>"
                            readonly />
                        <div class="flex gap-6 justify-between items-center">
                            <input type="number" placeholder="Enter quantity"
                                class="outline outline-1 outline-[#524EEE] p-2 rounded-md bg-transparent text-white w-full mb-3"
                                autocomplete="off" name="quantity" required />
                        </div>

                        <input type="submit" value="Update"
                            class="bg-[#524EEE] w-full p-2 rounded-md hover:bg-[#403d9a]" name="add_btn" />
                    </form>
                </div>
            </div>
            <!-- children end here -->

            <!--right sidebar start  -->
            <?php include 'components/rightsidebar.php'; ?>
            <!-- right sidebar ends -->
        </div>
    </div>
</body>

</html>
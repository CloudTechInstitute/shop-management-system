<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('location: login.php');
    exit();
}
include ('components/connection.php');

// Fetch items from cart, calculate total amount and count number of items
$sql = "SELECT *, 
               (SELECT SUM(amount) FROM cart) AS total_amount, 
               (SELECT COUNT(*) FROM cart) AS total_items 
        FROM cart 
        ORDER BY id DESC";
$result = $conn->query($sql);
$total_amount = 0;
$total_items = 0;
$items_string = "";
$cart_items = array();


if ($result) {
    // Check if there are any items in the cart
    if ($result->num_rows > 0) {
        // Fetch all cart items
        while ($row = $result->fetch_assoc()) {
            $cart_items[] = $row;
        }

        // Calculate total amount and total items
        $total_amount = $cart_items[0]['total_amount'];
        $total_items = $cart_items[0]['total_items'];

        // Build items string
        foreach ($cart_items as $cart_item) {
            $items_string .= $cart_item['item'] . ' (' . $cart_item['quantity'] . '), ';
        }
        $items_string = rtrim($items_string, ', ');
    }
} else {
    echo "Error fetching cart items: " . $conn->error;
}

if (isset($_POST['submit_order'])) {
    $user_name = $_POST['user_name'];
    $user_phone = $_POST['user_phone'];
    $total_amount = $_POST['total_amount'];
    $total_items = $_POST['total_items'];
    $items_string = $_POST['items_string'];
    $date = date('Y-m-d');
    $transaction_id = "INV" . date("Y") . date("m") . date("d") . rand(1000000000, 9999999999);
    $logedUser = $_SESSION['user'];
    $amount_paid = $_SESSION['amount_paid'];

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Fetch cart items
        $cart_items_query = "SELECT item, quantity, cost, unit AS selling_price, amount FROM cart";
        $cart_items_result = $conn->query($cart_items_query);
        $cart_items = $cart_items_result->fetch_all(MYSQLI_ASSOC);

        foreach ($cart_items as $cart_item) {
            $item = $cart_item['item'];
            $quantity = $cart_item['quantity'];
            $cost = $cart_item['cost'];
            $selling_price = $cart_item['selling_price'];
            $amount_new = $cart_item['amount'];
            $profit = ($selling_price - $cost) * $quantity;

            // Fetch current item quantity from items table
            $item_quantity_query = "SELECT quantity FROM items WHERE item = '$item'";
            $item_quantity_result = $conn->query($item_quantity_query);

            // Check if query execution was successful
            if (!$item_quantity_result) {
                throw new Exception("Error fetching item quantity: " . $conn->error);
            }

            $item_quantity_row = $item_quantity_result->fetch_assoc();
            $current_quantity = $item_quantity_row['quantity'];

            // Deduct the quantity
            $new_quantity = $current_quantity - $quantity;

            // Update the items table
            $update_items_query = "UPDATE items SET quantity = '$new_quantity' WHERE item = '$item'";
            if (!$conn->query($update_items_query)) {
                throw new Exception("Error updating item quantity: " . $conn->error);
            }

            // Insert into profits table
            $sql = "INSERT INTO profits (transaction_id, item, quantity, cost, selling, profit, user, amount, date)
                    VALUES ('$transaction_id', '$item', '$quantity', '$cost', '$selling_price', '$profit', '$logedUser', '$amount_paid', '$date')";
            if (!$conn->query($sql)) {
                throw new Exception("Error inserting into profits: " . $conn->error);
            }
        }

        // Insert data into transactions table
        $sql = "INSERT INTO transactions (transaction_id, user_name, user_phone, items, total_amount, total_items, user, date) 
                VALUES ('$transaction_id', '$user_name', '$user_phone', '$items_string', '$total_amount', '$total_items', '$logedUser', '$date')";
        if (!$conn->query($sql)) {
            throw new Exception("Error inserting into transactions: " . $conn->error);
        }

        // Clear the cart
        $sql = "DELETE FROM cart";
        if (!$conn->query($sql)) {
            throw new Exception("Error clearing cart: " . $conn->error);
        }

        // Commit transaction
        $conn->commit();

        echo "<script>alert('Order placed successfully.');</script>";
        echo "<script>window.location.href='receipt.php?transaction_id=$transaction_id';</script>";

    } catch (Exception $e) {
        // An error occurred; rollback the transaction
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="./output.css" rel="stylesheet" />
    <link rel="stylesheet" href="bootstrap-icons/font/bootstrap-icons.css" />
    <title>Tema Toiletries Depot</title>
</head>

<body>
    <div class="bg-[#1D1D42] h-screen">
        <!--topheader start  -->
        <?php include 'components/topheader.php'; ?>
        <!-- top header ends -->
        <div class="flex gap-6 justify-between px-8 items-start">
            <!--left sidebar start  -->
            <?php include 'components/leftsidebar.php'; ?>
            <!-- left sidebar ends -->

            <div class="bg-[#141432] p-4 flex-1 h-[620px] rounded-md overflow-x-hidden overflow-y-auto">
                <!-- children starts here -->
                <div class="text-white font-semibold uppercase mb-6">CART</div>
                <div class="p-4 bg-[#242358] rounded-md">
                    <div class="flex justify-between gap-4">
                        <div class="w-full">
                            <div class="relative overflow-x-auto sm:rounded-lg">
                                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                    <thead class="text-xs text-[#141432] uppercase bg-[#524EEE]">
                                        <tr>
                                            <th scope="col" class="px-6 py-3">Item</th>
                                            <th scope="col" class="px-6 py-3">Quantity</th>
                                            <th scope="col" class="px-6 py-3">Unit</th>
                                            <th scope="col" class="px-6 py-3">Amount</th>
                                            <th scope="col" class="px-6 py-3"> </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (count($cart_items) > 0) {
                                            foreach ($cart_items as $row) { ?>
                                                <tr class='border-b'>
                                                    <th scope='row' class='px-6 py-4 font-medium text-white whitespace-nowrap'>
                                                        <?php echo htmlspecialchars($row['item']) ?>
                                                    </th>
                                                    <th scope='row' class='px-6 py-4 font-medium text-white whitespace-nowrap'>
                                                        <?php echo htmlspecialchars($row['quantity']) ?>
                                                    </th>
                                                    <td class='px-6 py-4'>
                                                        <?php echo htmlspecialchars($row['unit']) ?>
                                                    </td>
                                                    <td class='px-6 py-4'> Ghc
                                                        <?php echo htmlspecialchars($row['amount']) ?>.00
                                                    </td>
                                                    <td class='px-6 py-4'>
                                                        <form action="delete_cart_item.php" method="post"
                                                            onsubmit="return confirm('Are you sure you want to delete this item?');">
                                                            <input type="hidden" name="id"
                                                                value="<?php echo htmlspecialchars($row['id']); ?>">
                                                            <button type="submit"
                                                                style="background: none; border: none; padding: 0;">
                                                                <i class="bi bi-x" style="font-size: 1.5rem; color: red"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php }
                                        } else {
                                            echo "<tr><td class='px-6 py-4'>No items in cart.</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="w-[40%]">
                            <div class="bg-[#141432] p-4 rounded-md mb-5">
                                <div class="mb-1 font-semibold text-white">Order Summary</div>
                                <hr />
                                <div class="flex justify-between items-center mb-3 mt-2 text-[#524EEE]">
                                    <div class="text-xs font-semibold"># of Items</div>
                                    <div class="text-xs font-semibold"><?php echo htmlspecialchars($total_items); ?>
                                    </div>
                                </div>
                                <div class="flex justify-between text-[#524EEE]">
                                    <div class="text-xs">SubTotal</div>
                                    <div class="text-xs font-semibold"><?php echo htmlspecialchars($total_amount); ?>
                                    </div>
                                </div>
                                <div class="flex justify-between mb-5 text-[#524EEE]">
                                    <div class="text-xs">Tax</div>
                                    <div class="text-xs font-semibold">Ghc 0</div>
                                </div>
                                <hr />
                                <div class="flex justify-between items-center mt-3 text-white">
                                    <div class="text-xs font-bold">Order Total</div>
                                    <div class="text-md font-bold">
                                        <?php echo "Ghc " . htmlspecialchars($total_amount) . ".00"; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-[#141432] p-4 rounded-md">
                                <div class="mb-1 font-semibold text-white">User Details</div>
                                <hr />
                                <div class="mt-4">
                                    <form method="post" action="cart.php">
                                        <input type="hidden" name="total_amount"
                                            value="<?php echo htmlspecialchars($total_amount); ?>" />
                                        <input type="hidden" name="total_items"
                                            value="<?php echo htmlspecialchars($total_items); ?>" />
                                        <input type="hidden" name="items_string"
                                            value="<?php echo htmlspecialchars($items_string); ?>" />
                                        <input type="text" name="user_name" placeholder="Name"
                                            class="outline outline-1 outline-[#524EEE] p-2 rounded-md bg-transparent text-white w-full mb-3"
                                            required />
                                        <input type="text" name="user_phone" placeholder="Phone Number"
                                            class="outline outline-1 outline-[#524EEE] p-2 rounded-md bg-transparent text-white w-full mb-3"
                                            required />
                                        <input type="text" name="amount_paid" placeholder="Amount Paid"
                                            class="outline outline-1 outline-[#524EEE] p-2 rounded-md bg-transparent text-white w-full mb-3"
                                            required />
                                        <input type="submit" name="submit_order" value="Continue"
                                            class="bg-[#524EEE] p-2 rounded-md hover:bg-[#403d9a]" />
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
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
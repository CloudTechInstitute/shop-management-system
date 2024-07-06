<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('location: login.php');
    exit();
}
include ('components/connection.php');
?>

<?php
$search_query = "";
if (isset($_POST['search'])) {
    $search_query = mysqli_real_escape_string($conn, $_POST['search']);
}

// Fetch items from the items table with an optional search filter
$sql = "SELECT * FROM items";
if (!empty($search_query)) {
    $sql .= " WHERE item LIKE '%$search_query%'";
}
$sql .= " ORDER BY id DESC";
$result = $conn->query($sql);
?>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cart_add'])) {
    // Get form data
    $item = mysqli_real_escape_string($conn, $_POST['product']);
    $quantity = (int) $_POST['cart_qnty'];
    $item_quantity = $_POST['quantity'];
    $unit = (float) $_POST['item_price'];
    $cost = (float) $_POST['item_cost'];
    $amount = $quantity * $unit;

    if ($quantity <= 0) {
        echo "<script>alert('Quantity must be greater than zero.');</script>";
    } else if ($quantity > $item_quantity) {
        echo "<script>alert('Not enough stock of " . htmlspecialchars($item) . " available, there is only " . htmlspecialchars($item_quantity) . " available');</script>";
    } else {
        // Create SQL query
        $sql = "INSERT INTO cart (item, quantity, unit, cost, amount) VALUES ('$item', '$quantity', '$unit', '$cost', '$amount')";

        // Execute query
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Item added to cart successfully');</script>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="output.css" rel="stylesheet" />
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
                <div class="mb-6 flex justify-between items-center">
                    <p class="text-white font-semibold uppercase">All Items</p>
                    <form class="flex gap-4" method="post" action="index.php">
                        <input type="text" name="search" placeholder="Search for item..."
                            class="outline outline-1 outline-[#524EEE] p-2 rounded-md bg-transparent text-white w-full"
                            value="<?php echo htmlspecialchars($search_query); ?>" />
                        <input type="submit" value="Search" class="bg-[#524EEE] p-2 rounded-md hover:bg-[#403d9a]" />
                    </form>
                </div>
                <div class="grid grid-cols-5 gap-3">
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) { ?>
                    <div class="p-2 outline outline-1 outline-[#524EEE] rounded-lg">
                        <div class="p-1 bg-[#aeabfd71] rounded-md w-44 h-44">
                            <img src="assets/<?php echo htmlspecialchars($row['image']); ?>" alt="Item Image"
                                class="w-full h-full object-cover" />
                        </div>
                        <p class="text-sm text-white "><?php echo htmlspecialchars($row['item']); ?></p>
                        <div class="flex justify-between items-center">
                            <p class="text-lg text-[#524EEE] mb-1 font-semibold">
                                <?php echo htmlspecialchars($row['selling']) . ".00"; ?>
                            </p>
                            <p class="text-sm text-white mb-1 ">
                                <?php
                                        $checkQuantity = $row['quantity'];
                                        if ($checkQuantity <= 0) {
                                            echo "Out of Stock";
                                        } else {
                                            echo htmlspecialchars($checkQuantity);
                                        }
                                        ?>
                            </p>
                        </div>
                        <form method="post" action="index.php">
                            <div class="flex items-center justify-between gap-4">
                                <input type="hidden" value="<?php echo htmlspecialchars($row['selling']); ?>"
                                    class="outline outline-1 outline-[#524EEE] p-2 rounded-md bg-transparent text-white w-full"
                                    name="item_price" />
                                <input type="hidden" value="<?php echo htmlspecialchars($row['quantity']); ?>"
                                    class="outline outline-1 outline-[#524EEE] p-2 rounded-md bg-transparent text-white w-full"
                                    name="quantity" />
                                <input type="hidden" value="<?php echo htmlspecialchars($row['cost']); ?>"
                                    class="outline outline-1 outline-[#524EEE] p-2 rounded-md bg-transparent text-white w-full"
                                    name="item_cost" />
                                <input type="hidden" value="<?php echo htmlspecialchars($row['item']); ?>"
                                    class="outline outline-1 outline-[#524EEE] p-2 rounded-md bg-transparent text-white w-full"
                                    name="product" />
                                <input type="number" value="1"
                                    class="outline outline-1 outline-[#524EEE] p-2 rounded-md bg-transparent text-white w-full"
                                    name="cart_qnty" />
                                <button class="bg-[#524EEE] p-2 rounded-md hover:bg-[#403d9a]" type="submit"
                                    name="cart_add"><i class="bi bi-plus"></i></button>
                            </div>
                        </form>
                    </div>
                    <?php }
                    } else {
                        echo "<p class='text-white'>No items found</p>";
                    }
                    ?>
                </div>
            </div>
            <!-- children end here -->
            <!-- right sidebar start  -->
            <?php include 'components/rightsidebar.php'; ?>
            <!-- right sidebar ends -->
        </div>
    </div>
</body>

</html>
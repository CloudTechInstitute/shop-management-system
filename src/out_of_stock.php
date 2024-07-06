<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('location: login.php');
    exit();
}
?>
<?php
include ('components/connection.php'); ?>
<?php
$sql = "SELECT * FROM items ORDER BY id DESC";
$result = $conn->query($sql);
?>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cart_add'])) {
    // Get form data
    $item = mysqli_real_escape_string($conn, $_POST['product']);
    $quantity = (int) $_POST['cart_qnty'];
    $unit = (float) $_POST['item_price'];
    $amount = $quantity * $unit;

    // Create SQL query
    $sql = "INSERT INTO cart (item, quantity, unit, amount) VALUES ('$item', $quantity, $unit, $amount)";

    // Execute query
    if ($conn->query($sql) === TRUE) {
        echo "Item added to cart successfully.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
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
                <div class=" mb-6 flex justify-between items-center">
                    <p class="text-white font-semibold uppercase">items getting out of stock</p>

                </div>
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-[#141432] uppercase bg-[#524EEE]">
                            <tr>
                                <th scope="col" class="px-6 py-3">Product</th>
                                <th scope="col" class="px-6 py-3">Quantity</th>
                                <th scope="col" class="px-6 py-3">Price</th>
                                <th scope="col" class="px-6 py-3">Uploaded On</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            include ('components/connection.php');

                            // Fetch transactions from the database
                            $sql = "SELECT * FROM items WHERE `quantity` <= 20";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr class='border-b hover:bg-[#242358]'>";
                                    echo "<th scope='row' class='px-6 py-4 font-medium text-white whitespace-nowrap'>" . htmlspecialchars($row['item']) . "</th>";
                                    echo "<td class='px-6 py-4'>" . htmlspecialchars($row['quantity']) . "</td>";
                                    echo "<td class='px-6 py-4'> Ghc " . htmlspecialchars($row['selling']) . ".00</td>";
                                    echo "<td class='px-6 py-4'>" . htmlspecialchars($row['date']) . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' class='text-white text-center'>No items found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
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
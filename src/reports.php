<?php

session_start();
if (!isset($_SESSION['user'])) {
    header('location: login.php');
    exit();
}
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                <div class="flex gap-6 mb-2">
                    <a href="items.php" class="w-full">
                        <div
                            class="p-2 outline outline-1 outline-[#524EEE] bg-[#6f6aff34] text-white rounded-md w-full">
                            <p class="mb-2">All Items</p>
                            <p class="mb-2 text-2xl font-bold">
                                <?php
                                $query = "SELECT COUNT(*) FROM `items`";
                                $res = mysqli_query($conn, $query);
                                $row = mysqli_fetch_array($res);
                                $total = $row[0];
                                echo $total;
                                ?>
                            </p>
                        </div>
                    </a>
                    <a href="out_of_stock.php" class="w-full">
                        <div
                            class="p-2 outline outline-1 outline-yellow-600 bg-[#f8ff6a34] text-white rounded-md w-full">
                            <p class="mb-2">Getting Finish</p>
                            <p class="mb-2 text-2xl font-bold">
                                <?php
                                $query = "SELECT COUNT(*) FROM `items` WHERE `quantity` <= 20";
                                $res = mysqli_query($conn, $query);
                                $row = mysqli_fetch_array($res);
                                $total = $row[0];
                                echo $total;
                                ?>
                            </p>
                        </div>
                    </a>
                    <a href="top_products.php" class="w-full">
                        <div
                            class="p-2 outline outline-1 outline-purple-500 bg-[#ff6af034] text-white rounded-md w-full">
                            <p class="mb-2">Top Product</p>
                            <p class="mb-2 text-2xl">
                                <?php
                                $query = "SELECT item, COUNT(item) AS occurrence 
                            FROM profits 
                            GROUP BY item 
                            ORDER BY occurrence DESC 
                            LIMIT 1";

                                $res = mysqli_query($conn, $query);

                                if ($res) {
                                    $row = mysqli_fetch_assoc($res);
                                    if ($row) {
                                        $most_occurred_item = $row['item'];
                                        echo htmlspecialchars($most_occurred_item);
                                    } else {
                                        echo "No data found.";
                                    }
                                } else {
                                    echo "Error: " . mysqli_error($conn);
                                }
                                ?>
                            </p>
                        </div>
                    </a>
                    <a href="outOfStock.php" class="w-full">
                        <div class="p-2 outline outline-1 outline-red-500 bg-[#ff223834] text-white rounded-md w-full">
                            <p class="mb-2">Out Of Stock</p>
                            <p class="mb-2 text-2xl font-bold">
                                <?php
                                $query = "SELECT COUNT(*) FROM `items` WHERE `quantity` <= 0";
                                $res = mysqli_query($conn, $query);
                                $row = mysqli_fetch_array($res);
                                $total = $row[0];
                                echo $total;
                                ?>
                            </p>
                        </div>
                    </a>
                    <a href="profits.php" class="w-full">
                        <div
                            class="p-2 outline outline-1 outline-indigo-500 bg-indigo-900 text-white rounded-md w-full">
                            <p class="mb-2">Profits Today</p>
                            <p class="mb-2 text-2xl font-bold">
                                <?php
                                $date = date('Y-m-d');
                                $query = "SELECT SUM(profit) AS total_profit FROM profits WHERE date = '$date'";
                                $res = mysqli_query($conn, $query);

                                if ($res) {
                                    $row = mysqli_fetch_assoc($res);
                                    $total = $row['total_profit'];
                                    echo "Ghc " . $total . ".00";
                                } else {
                                    echo "Error: " . mysqli_error($conn);
                                }
                                ?>
                            </p>
                        </div>
                    </a>
                </div>
                <div class="relative w-full h-96">
                    <canvas id="myChart"></canvas>
                </div>
            </div>
            <!-- children end here -->

            <!--right sidebar start  -->
            <?php include 'components/rightsidebar.php'; ?>
            <!-- right sidebar ends -->
        </div>
    </div>

    <!-- scripts -->
    <!-- <script>
        const ctx = document.getElementById('myChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Arrests', 'Patrol', 'Interception', 'Neutralize'],
                datasets: [{
                    label: '# of tasks',
                    data: [12, 40, 3, 5],
                    borderWidth: 2,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)'
                    ],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        display: false
                    }
                }
            }
        });
    </script> -->
</body>

</html>
<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('location: login.php');
    exit();
}
?>
<?php
include ('components/connection.php');
$search_query = "";
if (isset($_POST['search'])) {
    $search_query = mysqli_real_escape_string($conn, $_POST['search']);
}

// Fetch items from the items table with an optional search filter
$sql = "SELECT * FROM transactions";
if (!empty($search_query)) {
    $sql .= " WHERE item LIKE '%$search_query%'";
}
$sql .= " ORDER BY id DESC";
$result = $conn->query($sql);
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
                <div class="flex items-center justify-between">
                    <div class="text-white font-semibold uppercase mb-6">
                        sales
                    </div>
                    <!-- <form class="flex gap-4" method="post" action="transactions.php">
                        <input type="text" name="search" placeholder="Search for item..."
                            class="outline outline-1 outline-[#524EEE] p-2 rounded-md bg-transparent text-white w-full"
                            value="<?php //echo htmlspecialchars($search_query); ?>" />
                        <input type="submit" value="Search" class="bg-[#524EEE] p-2 rounded-md hover:bg-[#403d9a]" />
                    </form> -->
                </div>
                <div class="flex justify-between mb-4 items-center gap-4">
                    <form method="get" action="transactions.php" class="flex gap-6 items-center justify-between">
                        <div class="text-white">Sort by:</div>
                        <select name="sort"
                            class="outline outline-1 outline-[#524EEE] text-[#524EEE] p-2 rounded-md bg-transparent ">
                            <option value="">None</option>
                            <option value="day">Day</option>
                            <option value="week">Week</option>
                            <option value="month">Month</option>
                            <option value="year">Year</option>
                            <option value="name">Name</option>
                        </select>

                        <input type="date" name="specific_date"
                            class="outline outline-1 outline-[#524EEE] p-2 rounded-md bg-transparent text-[#524EEE]">
                        <input type="month" name="specific_month"
                            class="outline outline-1 outline-[#524EEE] p-2 rounded-md bg-transparent text-[#524EEE]">
                        <input type="number" name="specific_year" placeholder="Year"
                            class="outline outline-1 outline-[#524EEE] p-2 rounded-md bg-transparent text-[#524EEE]"
                            min="2000" max="<?php echo date('Y'); ?>">
                        <button type="submit" class="bg-[#524EEE] p-2 rounded-md hover:bg-[#403d9a]">Sort</button>

                    </form>
                    <div class="text-white"><span class="text-xs">Total Sales:</span><span
                            class="font-bold uppercase text-xl"></span>
                        <?php

                        // Initialize total sales variable
                        $total_sales = 0;

                        // Get the selected sort option and specific date, month, and year
                        $sort = isset($_GET['sort']) ? $_GET['sort'] : '';
                        $specific_date = isset($_GET['specific_date']) ? $_GET['specific_date'] : '';
                        $specific_month = isset($_GET['specific_month']) ? $_GET['specific_month'] : '';
                        $specific_year = isset($_GET['specific_year']) ? $_GET['specific_year'] : '';

                        // Construct SQL query based on the selected sort option and specific date, month, and year
                        switch ($sort) {
                            case 'day':
                                if ($specific_date) {
                                    $sql = "SELECT * FROM transactions WHERE date = '$specific_date' ORDER BY date DESC";
                                } else {
                                    $sql = "SELECT * FROM transactions WHERE date = CURDATE() ORDER BY date DESC";
                                }
                                break;
                            case 'week':
                                $sql = "SELECT * FROM transactions WHERE YEARWEEK(date, 1) = YEARWEEK(CURDATE(), 1) ORDER BY date DESC";
                                break;
                            case 'month':
                                if ($specific_month) {
                                    $month = date('m', strtotime($specific_month));
                                    $year = date('Y', strtotime($specific_month));
                                    $sql = "SELECT * FROM transactions WHERE YEAR(date) = '$year' AND MONTH(date) = '$month' ORDER BY date DESC";
                                } else {
                                    $sql = "SELECT * FROM transactions WHERE YEAR(date) = YEAR(CURDATE()) AND MONTH(date) = MONTH(CURDATE()) ORDER BY date DESC";
                                }
                                break;
                            case 'year':
                                if ($specific_year) {
                                    $sql = "SELECT * FROM transactions WHERE YEAR(date) = '$specific_year' ORDER BY date DESC";
                                } else {
                                    $sql = "SELECT * FROM transactions WHERE YEAR(date) = YEAR(CURDATE()) ORDER BY date DESC";
                                }
                                break;
                            case 'name':
                                $sql = "SELECT * FROM transactions ORDER BY user_name ASC";
                                break;
                            default:
                                $sql = "SELECT * FROM transactions ORDER BY id DESC";
                                break;
                        }

                        // Execute query and calculate total sales
                        if (!empty($sql)) {
                            $result = $conn->query($sql);
                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $total_sales += floatval($row['total_amount']);
                                }
                            } else {
                                echo "No transactions found.";
                            }
                        } else {
                            echo "No valid SQL query.";
                        }

                        // Display total sales
                        echo "Ghc " . number_format($total_sales, 2);
                        ?>
                    </div>
                </div>
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-[#141432] uppercase bg-[#524EEE]">
                            <tr>
                                <th scope="col" class="px-6 py-3">Transaction ID</th>
                                <th scope="col" class="px-6 py-3">Products</th>
                                <th scope="col" class="px-6 py-3">Customer (name and phone)</th>
                                <th scope="col" class="px-6 py-3">Amount (total)</th>
                                <th scope="col" class="px-6 py-3">Sold by</th>
                                <th scope="col" class="px-6 py-3">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Reset $result to use it again for fetching data
                            if (!empty($sql)) {
                                $result = $conn->query($sql);
                                if ($result && $result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) { ?>
                                        <tr class='border-b hover:bg-[#242358]'
                                            onclick="location.href='receipt.php?transaction_id=<?php echo htmlspecialchars($row['transaction_id']); ?>'">
                                            <th scope='row' class='px-6 py-4 font-medium text-white whitespace-nowrap'>
                                                <?php echo htmlspecialchars($row['transaction_id']); ?>
                                            </th>
                                            <th scope='row' class='px-6 py-4 font-medium text-white whitespace-nowrap'>
                                                <?php echo htmlspecialchars($row['items']); ?>
                                            </th>
                                            <td class='px-6 py-4'>
                                                <?php echo htmlspecialchars($row['user_name']) . " (" . htmlspecialchars($row['user_phone']) . ")"; ?>
                                            </td>
                                            <td class='px-6 py-4'> Ghc <?php echo htmlspecialchars($row['total_amount']); ?>.00
                                            </td>
                                            <td class='px-6 py-4'> <?php echo htmlspecialchars($row['user']); ?>
                                            </td>
                                            <td class='px-6 py-4'><?php echo htmlspecialchars($row['date']); ?> </td>
                                        </tr>
                                    <?php }
                                } else {
                                    echo "<tr>
                                            <td colspan='5' class='text-white text-center'>No transactions found.</td>
                                        </tr>";
                                }
                            } else {
                                echo "<tr>
                                        <td colspan='5' class='text-white text-center'>No transactions found.</td>
                                    </tr>";
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
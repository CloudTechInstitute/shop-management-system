<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('location: login.php');
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
                <div class="text-white font-semibold uppercase mb-6">
                    Profits
                </div>
                <div class="flex justify-between items-center">

                    <!-- Form for sorting -->
                    <form method="get" action="profits.php" class="flex gap-6 items-center justify-between mb-4">
                        <div class="text-white">Sort by:</div>
                        <select name="sort"
                            class="outline outline-1 outline-[#524EEE] text-[#524EEE] p-2 rounded-md bg-transparent ">
                            <option value="day">Day</option>
                            <option value="month">Month</option>
                            <option value="year">Year</option>
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

                    <!-- Total Profit Calculation -->
                    <div class="text-white font-semibold uppercase mb-4">
                        Total Profit:
                        <?php
                        include ('components/connection.php');

                        // Initialize total profit variable
                        $total_profit = 0;

                        // Get the selected sort option and specific date, month, and year
                        $sort = isset($_GET['sort']) ? $_GET['sort'] : '';
                        $specific_date = isset($_GET['specific_date']) ? $_GET['specific_date'] : '';
                        $specific_month = isset($_GET['specific_month']) ? $_GET['specific_month'] : '';
                        $specific_year = isset($_GET['specific_year']) ? $_GET['specific_year'] : '';

                        // Construct SQL query based on the selected sort option and specific date, month, and year
                        switch ($sort) {
                            case 'day':
                                if ($specific_date) {
                                    $sql = "SELECT * FROM profits WHERE date = '$specific_date' ORDER BY date DESC";
                                } else {
                                    $sql = "SELECT * FROM profits WHERE date = CURDATE() ORDER BY date DESC";
                                }
                                break;
                            case 'month':
                                if ($specific_month) {
                                    $month = date('m', strtotime($specific_month));
                                    $year = date('Y', strtotime($specific_month));
                                    $sql = "SELECT * FROM profits WHERE YEAR(date) = '$year' AND MONTH(date) = '$month' ORDER BY date DESC";
                                } else {
                                    $sql = "SELECT * FROM profits WHERE YEAR(date) = YEAR(CURDATE()) AND MONTH(date) = MONTH(CURDATE()) ORDER BY date DESC";
                                }
                                break;
                            case 'year':
                                if ($specific_year) {
                                    $sql = "SELECT * FROM profits WHERE YEAR(date) = '$specific_year' ORDER BY date DESC";
                                } else {
                                    $sql = "SELECT * FROM profits WHERE YEAR(date) = YEAR(CURDATE()) ORDER BY date DESC";
                                }
                                break;
                            default:
                                $sql = "SELECT * FROM profits ORDER BY id DESC";
                                break;
                        }

                        // Execute query and calculate total profit
                        if (!empty($sql)) {
                            $result = $conn->query($sql);
                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $total_profit += floatval($row['profit']);
                                }
                            } else {
                                echo "No transactions found.";
                            }
                        } else {
                            echo "No valid SQL query.";
                        }

                        // Display total profit
                        echo "Ghc " . number_format($total_profit, 2);
                        ?>
                    </div>
                </div>

                <!-- Table for displaying profits -->
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-[#141432] uppercase bg-[#524EEE]">
                            <tr>
                                <th scope="col" class="px-6 py-3">Product</th>
                                <th scope="col" class="px-6 py-3">Quantity Sold</th>
                                <th scope="col" class="px-6 py-3">Profit</th>
                                <th scope="col" class="px-6 py-3">By</th>
                                <th scope="col" class="px-6 py-3">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Reset $result to use it again for fetching data
                            if (!empty($sql)) {
                                $result = $conn->query($sql);
                                if ($result && $result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr class='border-b hover:bg-[#242358]'>";
                                        echo "<th scope='row' class='px-6 py-4 font-medium text-white whitespace-nowrap'>" . htmlspecialchars($row['item']) . "</th>";
                                        echo "<th scope='row' class='px-6 py-4 font-medium text-white whitespace-nowrap'>" . htmlspecialchars($row['quantity']) . "</th>";
                                        echo "<td class='px-6 py-4'>Ghc " . htmlspecialchars($row['profit']) . ".00</td>";
                                        echo "<td class='px-6 py-4'>" . htmlspecialchars($row['user']) . "</td>";
                                        echo "<td class='px-6 py-4'>" . htmlspecialchars($row['date']) . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='4' class='text-white text-center'>No transactions found.</td></tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' class='text-white text-center'>No valid SQL query.</td></tr>";
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
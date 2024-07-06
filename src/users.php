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
$search_query = "";
if (isset($_POST['search'])) {
    $search_query = mysqli_real_escape_string($conn, $_POST['search']);
}

// Fetch items from the items table with an optional search filter
$sql = "SELECT * FROM users";
if (!empty($search_query)) {
    $sql .= " WHERE username LIKE '%$search_query%'";
}
$sql .= " ORDER BY id DESC";
$result = $conn->query($sql);
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
                    <p class="text-white font-semibold uppercase">All Users</p>
                    <form class="flex gap-4" method="post" action="items.php">
                        <input type="text" name="search" placeholder="Search for item..."
                            class="outline outline-1 outline-[#524EEE] p-2 rounded-md bg-transparent text-white w-full"
                            value="<?php echo htmlspecialchars($search_query); ?>" />
                        <input type="submit" value="Search" class="bg-[#524EEE] p-2 rounded-md hover:bg-[#403d9a]" />
                    </form>
                </div>
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-[#524EEE] uppercase ">
                            <tr>
                                <th scope="col" class="px-6 py-3">Username</th>
                                <th scope="col" class="px-6 py-3">Password</th>
                                <th scope="col" class="px-6 py-3">Date Created</th>
                                <th scope="col" class="px-6 py-3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr class='border-b hover:bg-[#242358]'>";
                                    echo "<th scope='row' class='px-6 py-4 font-medium text-white whitespace-nowrap'>" . htmlspecialchars($row['username']) . "</th>";
                                    // echo "<td class='px-6 py-4'>" . htmlspecialchars($row['username']) . "</td>";
                                    echo "<td class='px-6 py-4'> " . htmlspecialchars($row['password']) . "</td>";
                                    // echo "<td class='px-6 py-4'> " . htmlspecialchars($row['selling']) . "</td>";
                                    echo "<td class='px-6 py-4'>" . htmlspecialchars($row['date']) . "</td>";

                                    if ($_SESSION['user'] == 'admin') {
                                        echo "<td class='px-6 py-4'>";
                                        echo "<a href='edit_user.php?id=" . $row['id'] . "' class='text-blue-500 hover:text-blue-700'>Edit</a>";
                                        echo "</td>";
                                        echo "<td class='px-6 py-4'>";
                                        echo "<form action='delete_user.php' method='post' onsubmit=\"return confirm('Are you sure you want to delete this item?');\">";
                                        echo "<input type='hidden' name='id' value='" . $row['id'] . "'>";
                                        echo "<button type='submit' class='text-red-500 hover:text-red-700'>Delete</button>";
                                        echo "</form>";
                                        echo "</td>";
                                    }
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7' class='text-white text-center'>No users found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
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
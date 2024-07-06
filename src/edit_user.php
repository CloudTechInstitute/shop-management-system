<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('location: login.php');
    exit();
} ?>
<?php
include ('components/connection.php');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the item details from the POST request
    $id = intval($_POST['id']);
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and execute the update statement
    $sql = "UPDATE users SET username = '$username', password = '$password' WHERE id = '$id'";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        // Redirect to a success page or back to the items list with a success message
        header("Location: users.php?message=user+updated+successfully");
        exit();
    } else {
        // Redirect to an error page or back to the edit form with an error message
        header("Location: edit_user.php?id=$id&error=Error+updating+item");
        exit();
    }

}

// Fetch item details for the form
if (isset($_GET['id'])) {
    // Get the item ID from the GET request
    $id = intval($_GET['id']);

    // Fetch the item details from the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
    } else {
        // Redirect if item not found
        header("Location: users.php?error=user+not+found");
        exit();
    }

    // Close the statement
    $stmt->close();
} else {
    // Redirect if ID is not provided
    header("Location: users.php?error=Invalid+request");
    exit();
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
                    <p class="text-white font-semibold uppercase">edit item</p>
                </div>
                <form action="edit_user.php" method="post">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($user['id']); ?>">
                    <label for="username" class="text-white text-sm">Username:</label>
                    <input type="text"
                        class="outline outline-1 outline-[#524EEE] p-2 rounded-md bg-transparent text-white w-full mb-3"
                        name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required /><br>

                    <label for="password" class="text-white text-sm">Password:</label>
                    <input type="text"
                        class="outline outline-1 outline-[#524EEE] p-2 rounded-md bg-transparent text-white w-full mb-3"
                        name="password" value="<?php echo ($user['password']); ?>" required /><br>
                    <button type="submit" class="bg-[#524EEE] p-2 rounded-md hover:bg-[#403d9a]">Update User</button>
                </form>
            </div>
            <!-- children end here -->


            <!--right sidebar start  -->
            <?php include 'components/rightsidebar.php'; ?>
            <!-- right sidebar ends -->
        </div>
    </div>
</body>

</html>
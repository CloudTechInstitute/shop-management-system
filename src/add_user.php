<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('location: login.php');
    exit();
}

include ('components/connection.php');

// add user
if (isset($_POST['add_btn'])) {
    $user = $_POST['user'];
    $password = $_POST['password'];
    $date = date('Y-m-d');

    // Insert data into database
    $sql = "INSERT INTO users (username, password, date) VALUES ('$user', '$password', '$date')";

    if ($conn->query($sql) === TRUE) {
        echo "<script> alert('New user created successfully')</script>";
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
                <div class="text-white font-semibold uppercase mb-6">Add user</div>
                <div class="grid grid-cols-2 gap-6">
                    <form method="post" action="add_user.php">
                        <input type="text" placeholder="User name"
                            class="outline outline-1 outline-[#524EEE] p-2 rounded-md bg-transparent text-white w-full mb-3"
                            autocomplete="off" name="user" required />
                        <input type="text" placeholder="Enter user password"
                            class="outline outline-1 outline-[#524EEE] p-2 rounded-md bg-transparent text-white w-full mb-3"
                            autocomplete="off" name="password" required />
                        <input type="submit" value="Add" class="bg-[#524EEE] w-full p-2 rounded-md hover:bg-[#403d9a]"
                            name="add_btn" />
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
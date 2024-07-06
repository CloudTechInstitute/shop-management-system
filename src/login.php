<?php
session_start();
include ('components/connection.php');

if (isset($_POST['login_btn'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // // Hash the password for comparison
    // $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Use prepared statement to prevent SQL injection
    $sql = "SELECT * FROM users WHERE `username` = '$username' AND `password` = '$password'";
    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        // // Verify the hashed password
        // if (password_verify($password, $user['password'])) {
        $_SESSION['rank'] = $user['rank'];
        // $_SESSION['lastname'] = $user['lastname'];
        $_SESSION['user'] = $user['username'];
        // $_SESSION['unique_id'] = $user['unique_id'];
        // $_SESSION['adminID'] = $_SESSION['staff_id'];
        header('Location:index.php');
        exit;
        // } else {
        //     echo '<script>alert("Invalid password. Please try again.")</script>';
        //}
    } else {
        echo '<script>alert("Invalid username and password. Please try again.")</script>';
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    </head>

    <body>
        <div class="flex h-screen items-center justify-center">
            <div class="p-2 outline outline-1 outline-[#1D1D42] w-[30%]">
                <p class="text-2xl font-bold text-blue-900 text-center mb-5">Tema Toiletries Depot</p>
                <form method="post" action="login.php">
                    <div class="mb-2">
                        <input type="text" id="username"
                            class="p-2 outline outline-1 outline-[#1D1D42] rounded-md w-full"
                            placeholder="enter username" required name="username" />
                    </div>
                    <div class="mb-2">
                        <input type="password" id="password"
                            class="p-2 outline outline-1 outline-[#1D1D42] rounded-md w-full" placeholder="•••••••••"
                            required name="password" />
                    </div>
                    <button type="submit" class="text-white bg-[#1D1D42] p-2 rounded-md w-full"
                        name="login_btn">Submit</button>
                </form>
            </div>
        </div>
    </body>

</html>
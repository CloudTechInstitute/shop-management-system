<?php

session_start();
if (!isset($_SESSION['user'])) {
    header('location: login.php');
    exit();
}

include ('components/connection.php');
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_btn'])) {
    $item = $_POST['item'];
    $quantity = $_POST['quantity'];
    $cost = $_POST['c_price'];
    $selling = $_POST['s_price'];
    $date = date('Y-m-d H:i:s');

    // Image upload handling
    $target_dir = "assets/";
    $target_file = $target_dir . basename($_FILES["file"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["file"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "<script> alert('File is not an image.')</script>";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["file"]["size"] > 10000000) {
        echo "<script> alert('Sorry, your file is too large.')</script>";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if (
        $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif"
    ) {
        echo "<script> alert(' Sorry, only JPG, JPEG, PNG & GIF files are allowed.')</script>";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "<script> alert('Sorry, your file was not uploaded.')</script>";
        // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            $image = basename($_FILES["file"]["name"]);

            // Insert data into database
            $sql = "INSERT INTO items (item, quantity, cost, selling, image, date) VALUES ('$item', $quantity, $cost, $selling, '$image', '$date')";

            if ($conn->query($sql) === TRUE) {
                echo "<script> alert('New record created successfully')</script>";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            echo "<script> alert('Sorry, there was an error uploading your file.')</script>";
        }
    }
}


?>

<?php
$sql = "SELECT item, quantity, selling FROM items ORDER BY id DESC";
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
                <div class="text-white font-semibold uppercase mb-6">Add Item</div>
                <div class="grid grid-cols-2 gap-6">
                    <form method="post" action="additem.php" enctype="multipart/form-data">
                        <input type="text" placeholder="Item name"
                            class="outline outline-1 outline-[#524EEE] p-2 rounded-md bg-transparent text-white w-full mb-3"
                            autocomplete="off" name="item" />
                        <div class="flex gap-6 justify-between items-center">
                            <input type="number" placeholder="Quantity"
                                class="outline outline-1 outline-[#524EEE] p-2 rounded-md bg-transparent text-white w-full mb-3"
                                autocomplete="off" name="quantity" />
                            <input type="number" placeholder="Cost Price"
                                class="outline outline-1 outline-[#524EEE] p-2 rounded-md bg-transparent text-white w-full mb-3"
                                autocomplete="off" name="c_price" />
                        </div>
                        <div class="flex gap-6 justify-between items-center">
                            <input type="file" class=" outline outline-1 outline-[#524EEE] p-2 rounded-md bg-transparent text-white
                                w-full mb-3" autocomplete="off" name="file" />
                            <input type="number" placeholder="Selling Price"
                                class="outline outline-1 outline-[#524EEE] p-2 rounded-md bg-transparent text-white w-full mb-3"
                                autocomplete="off" name="s_price" />
                        </div>
                        <input type="submit" value="Add" class="bg-[#524EEE] w-full p-2 rounded-md hover:bg-[#403d9a]"
                            name="add_btn" />
                    </form>
                    <div class="">
                        <p class="text-[#524EEE] font-semibold text-lg uppercase">Recent Items</p>

                        <div class="">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-[#141432] uppercase bg-[#524EEE]">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Product name</th>
                                        <th scope="col" class="px-6 py-3">Stock</th>
                                        <th scope="col" class="px-6 py-3">Price</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr class='border-b hover:bg-[#242358]'>";
                                            echo "<th scope='row' class='px-6 py-4 font-medium text-white whitespace-nowrap'>" . htmlspecialchars($row["item"]) . "</th>";
                                            echo "<td class='px-6 py-4'>" . htmlspecialchars($row["quantity"]) . "</td>";
                                            echo "<td class='px-6 py-4'>" . htmlspecialchars($row["selling"]) . ".00</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='3' class='px-6 py-4 text-center'>No items found</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
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
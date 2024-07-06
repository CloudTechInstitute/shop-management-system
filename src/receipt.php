<?php

session_start();
if (!isset($_SESSION['user'])) {
    header('location: login.php');
    exit();
}
?>
<?php
include ('components/connection.php');

if (isset($_GET['transaction_id'])) {
    $transaction_id = $_GET['transaction_id'];

    // Fetch transaction details
    $sql = "SELECT * FROM transactions WHERE transaction_id = '$transaction_id'";
    $transaction_result = $conn->query($sql);

    if ($transaction_result->num_rows > 0) {
        $transaction = $transaction_result->fetch_assoc();
    } else {
        echo "No transaction found.";
        exit;
    }

    // Fetch items from profits
    $sql = "SELECT * FROM profits WHERE transaction_id = '$transaction_id'";
    $profits_result = $conn->query($sql);

    if ($profits_result->num_rows > 0) {
        $profits = [];
        while ($row = $profits_result->fetch_assoc()) {
            $profits[] = $row;
        }
    } else {
        echo "No items found in profits.";
        exit;
    }
} else {
    echo "No transaction ID provided.";
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="./output.css" rel="stylesheet" />
    <link rel="stylesheet" href="bootstrap-icons/font/bootstrap-icons.css" />
    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            #printableArea,
            #printableArea * {
                visibility: visible;
            }

            #printableArea {
                position: absolute;
                left: 0;
                top: 0;
            }
        }
    </style>
</head>

<body>
    <div class="bg-[#1D1D42] h-screen">
        <!--topheader start  -->
        <?php include 'components/topheader.php'; ?>
        <!-- top header ends -->
        <div class="flex gap-6 justify-between px-8 items-start">
            <!--left sidebar start  -->
            <?php include 'components/leftsidebar.php'; ?>
            <!-- left sidebar ends -->

            <div class="bg-[#141432] p-4 flex-1 h-[620px] rounded-md overflow-x-hidden overflow-y-auto w-full">

                <!-- receipt starts here -->
                <div id="printableArea" class="outline outline-1 outline-white w-[70%]">
                    <div class="bg-blue-400 p-8 ">
                        <p class="uppercase mb-2 text-2xl font-bold text-center">tema toiletries depot</p>
                        <p class=" mb-4 text-xs text-center">0201724729 - Tema Community 2, Kwantabisa Park opposite
                            Hollard Insurance</p>
                        <hr />
                        <div class="flex justify-between mb-2 mt-4">
                            <p class=" text-2xl uppercase">official receipt</p>
                            <p class=" text-2xl">Ghc
                                <?php echo htmlspecialchars($transaction['total_amount']); ?>.00
                            </p>
                        </div>
                        <div class="flex justify-between">
                            <p class=""><?php echo htmlspecialchars($transaction['date']); ?></p>
                            <p class="">Order ID:
                                <?php echo htmlspecialchars($transaction['transaction_id']); ?>
                            </p>
                            <p class="">
                                Cash Payment
                            </p>
                        </div>
                    </div>
                    <div class="flex justify-between items-center w-full">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-[#141432] uppercase bg-[#524EEE]">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Item</th>
                                    <th scope="col" class="px-6 py-3">Quantity</th>
                                    <th scope="col" class="px-6 py-3">Unit</th>
                                    <th scope="col" class="px-6 py-3">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($profits as $profit) { ?>
                                    <tr class='border-b'>
                                        <th scope='row' class='px-6 py-4 font-medium e whitespace-nowrap'>
                                            <?php echo htmlspecialchars($profit['item']); ?>
                                        </th>
                                        <td scope='row' class='px-6 py-4  whitespace-nowrap'>
                                            <?php echo htmlspecialchars($profit['quantity']); ?>
                                        </td>
                                        <td class='px-6 py-4'>
                                            <?php echo htmlspecialchars($profit['selling']); ?>
                                        </td>

                                        <td class='px-6 py-4'>
                                            <?php
                                            $cost = htmlspecialchars($profit['selling']);
                                            $qnty = htmlspecialchars($profit['quantity']);
                                            $amnt = $cost * $qnty;
                                            echo $amnt;
                                            ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="flex justify-between items-center w-full px-6 py-4">
                        <p class="text-white">Total Items: <?php echo htmlspecialchars($transaction['total_items']); ?>
                        </p>
                    </div>
                    <div class="flex justify-between items-center w-full px-6 mb-6">
                        <p class="text-white">Customer: <span
                                class="uppercase"><?php echo htmlspecialchars($transaction['user_name']); ?></span></p>
                        <p class="text-white">Customer phone:
                            <?php echo htmlspecialchars($transaction['user_phone']); ?>
                        </p>
                    </div>
                    <hr>
                    <div class="flex justify-between items-center w-full px-6 py-4">
                        <p class="text-white text-xs">Issued by: <span
                                class="uppercase"><?php echo htmlspecialchars($transaction['user']); ?></span>
                        </p>
                        <div class="flex justify-between items-center gap-6">
                            <div>
                                <p class="text-white"><span class="">Amount paid</span>
                                </p>
                                <p class="text-white mb-4"><span class="">Payable</span>
                                </p>
                                <p class="text-white font-bold text-lg"><span class="">Balance</span>
                                </p>
                            </div>

                            <div>
                                <p class="text-white"><span
                                        class=""><?php echo htmlspecialchars($profit['amount']); ?></span>
                                </p>
                                <p class="text-white mb-4"><span
                                        class=""><?php echo htmlspecialchars($transaction['total_amount']); ?></span>
                                </p>

                                <p class="text-white font-bold text-lg"><span
                                        class=""><?php echo $profit['amount'] - $transaction['total_amount']; ?></span>
                                </p>
                            </div>

                        </div>

                    </div>
                </div>
                <button onclick="printDiv('printableArea')"
                    class="mt-4 bg-[#524EEE] p-2 rounded-md hover:bg-[#403d9a] text-white w-[30%]">Print
                    Receipt</button>
                <!-- receipt ends here -->
            </div>
            <!--right sidebar start  -->
            <?php include 'components/rightsidebar.php'; ?>
            <!-- right sidebar ends -->
        </div>
    </div>
    <script>
        function printDiv(divName) {
            var printContents = document.getElementById(divName).innerHTML;
            var newWindow = window.open('', 'Print-Window');
            newWindow.document.open();
            newWindow.document.write('<html><head><title>Print</title>');
            newWindow.document.write('<link rel="stylesheet" href="bootstrap-icons/font/bootstrap-icons.css">');
            newWindow.document.write('<link href="./output.css" rel="stylesheet">');
            newWindow.document.write('</head><body onload="window.print()">' + printContents + '</body></html>');
            newWindow.document.close();

        }
    </script>
</body>

</html>
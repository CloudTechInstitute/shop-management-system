<div class="flex-none">
    <?php if ($_SESSION['user'] == 'admin') { ?>
    <div class="grid grid-cols-2 justify-between gap-4">
        <a href="./additem.php">
            <div class="text-[#524EEE] hover:text-[#524EEE]">
                <div
                    class="p-6 bg-[#141432] rounded-md flex items-center justify-center hover:bg-[#524EEE] hover:text-[#141432]">
                    <i class="bi bi-plus" style="font-size: 2rem"></i>
                </div>
                <p class="text-center text-sm text-[#524EEE]">Add Item</p>
            </div>
        </a>
        <a href="./cart.php">
            <div class="text-[#524EEE] hover:text-[#524EEE]">
                <div
                    class="p-6 bg-[#141432] rounded-md flex items-center justify-center hover:bg-[#524EEE] hover:text-[#141432]">
                    <i class="bi bi-cart-fill" style="font-size: 2rem"></i>
                </div>
                <p class="text-center text-sm text-[#524EEE]">Cart</p>
            </div>
        </a>
        <a href="./items.php">
            <div class="text-[#524EEE] hover:text-[#524EEE]">
                <div
                    class="p-6 bg-[#141432] rounded-md flex items-center justify-center hover:bg-[#524EEE] hover:text-[#141432]">
                    <i class="bi bi-eye-fill" style="font-size: 2rem"></i>
                </div>
                <p class="text-center text-sm text-[#524EEE]">Items</p>
            </div>
        </a>
        <a href="./reports.php">
            <div class="text-[#524EEE] hover:text-[#524EEE]">
                <div
                    class="p-6 bg-[#141432] rounded-md flex items-center justify-center hover:bg-[#524EEE] hover:text-[#141432]">
                    <i class="bi bi-arrow-left-right" style="font-size: 2rem"></i>
                </div>
                <p class="text-center text-sm text-[#524EEE]">Reports</p>
            </div>
        </a>
        <a href="./add_user.php">
            <div class="text-[#524EEE] hover:text-[#524EEE]">
                <div
                    class="p-6 bg-[#141432] rounded-md flex items-center justify-center hover:bg-[#524EEE] hover:text-[#141432]">
                    <i class="bi bi-person" style="font-size: 2rem"></i>
                </div>
                <p class="text-center text-sm text-[#524EEE]">Add User</p>
            </div>
        </a>
        <a href="./users.php">
            <div class="text-[#524EEE] hover:text-[#524EEE]">
                <div
                    class="p-6 bg-[#141432] rounded-md flex items-center justify-center hover:bg-[#524EEE] hover:text-[#141432]">
                    <i class="bi bi-person" style="font-size: 2rem"></i>
                </div>
                <p class="text-center text-sm text-[#524EEE]">All Users</p>
            </div>
        </a>
    </div>
</div>
<?php } else { ?>
<div class="grid grid-cols-2 justify-between gap-4">
    <!-- <a href="./additem.php">
            <div class="text-[#524EEE] hover:text-[#524EEE]">
                <div
                    class="p-6 bg-[#141432] rounded-md flex items-center justify-center hover:bg-[#524EEE] hover:text-[#141432]">
                    <i class="bi bi-plus" style="font-size: 2rem"></i>
                </div>
                <p class="text-center text-sm text-[#524EEE]">Add Item</p>
            </div>
        </a> -->
    <a href="./cart.php">
        <div class="text-[#524EEE] hover:text-[#524EEE]">
            <div
                class="p-6 bg-[#141432] rounded-md flex items-center justify-center hover:bg-[#524EEE] hover:text-[#141432]">
                <i class="bi bi-cart-fill" style="font-size: 2rem"></i>
            </div>
            <p class="text-center text-sm text-[#524EEE]">Cart</p>
        </div>
    </a>
    <a href="./items.php">
        <div class="text-[#524EEE] hover:text-[#524EEE]">
            <div
                class="p-6 bg-[#141432] rounded-md flex items-center justify-center hover:bg-[#524EEE] hover:text-[#141432]">
                <i class="bi bi-eye-fill" style="font-size: 2rem"></i>
            </div>
            <p class="text-center text-sm text-[#524EEE]">Items</p>
        </div>
    </a>
    <!-- <a href="./reports.php">
        <div class="text-[#524EEE] hover:text-[#524EEE]">
            <div
                class="p-6 bg-[#141432] rounded-md flex items-center justify-center hover:bg-[#524EEE] hover:text-[#141432]">
                <i class="bi bi-arrow-left-right" style="font-size: 2rem"></i>
            </div>
            <p class="text-center text-sm text-[#524EEE]">Reports</p>
        </div>
    </a> -->
    <!-- <a href="./add_user.php">
        <div class="text-[#524EEE] hover:text-[#524EEE]">
            <div
                class="p-6 bg-[#141432] rounded-md flex items-center justify-center hover:bg-[#524EEE] hover:text-[#141432]">
                <i class="bi bi-person" style="font-size: 2rem"></i>
            </div>
            <p class="text-center text-sm text-[#524EEE]">Add User</p>
        </div>
    </a> -->
</div>
<?php } ?>
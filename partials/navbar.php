<?php
$base_path = isset($base_path) ? $base_path : '.';
?>

<div class="header-container">
    <div class="logo">
        <a href="<?php echo $base_path; ?>/index.php">
            <img src="<?php echo $base_path; ?>/public/images/SwapMoto.png" alt="Logo" />
        </a>
    </div>
    <div class="header-button">
        <ul>
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if ($_SESSION['IDroli'] != 2): ?>
                    <li>
                        <a href="<?php echo $base_path; ?>/public/cart.php">
                            <img src="<?php echo $base_path; ?>/public/icons/shopping-cart.png" alt="Cart">
                            <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                                <span class="cart-count"><?php echo count($_SESSION['cart']); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li><a href="<?php echo $base_path; ?>/public/profile.php"><img src="<?php echo $base_path; ?>/public/icons/avatar.png" alt="User"></a></li>
                <?php endif; ?>
                <li><button type="button" onclick="location.href='<?php echo $base_path; ?>/public/logout.php'">Sign Out</button></li>
            <?php else: ?>
                <li><button type="button" onclick="location.href='<?php echo $base_path; ?>/public/login.php'">Sign In</button></li>
                <li><button type="button" onclick="location.href='<?php echo $base_path; ?>/public/register.php'">Register</button></li>
            <?php endif; ?>
        </ul>
    </div>
</div>
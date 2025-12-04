<div class="header">
    <h1>اتوماسیون اداری لیافو</h1>
    <div class="user-info">
        <span><?php echo $_SESSION['username']; ?></span> |
        <a href="logout.php" style="color: white;">خروج</a>
    </div>
</div>
<div class="nav-menu">
    <ul>
        <li><a href="../index.php?page=dashboard">داشبورد</a></li>
        <li><a href="../index.php?page=invoices">فاکتورها</a></li>
        <?php if (hasRole('system_manager') || hasRole('senior_manager') || hasRole('province_manager')): ?>
            <li><a href="../index.php?page=manage_users">مدیریت کاربران</a></li>
        <?php endif; ?>
    </ul>
</div>
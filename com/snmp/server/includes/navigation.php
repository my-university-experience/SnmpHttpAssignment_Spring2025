<?php
// Get the current page name for highlighting active menu item
$currentFile = basename($_SERVER['PHP_SELF']);
$relativePath = getRelativePath();
?>

<nav class="modern-nav">
    <div class="nav-container">
        <ul class="nav-list">
            <li><a href="<?php echo $relativePath; ?>snmp_manager/index.php" class="nav-link<?php echo ($currentFile == 'index.php') ? ' active' : ''; ?>">Home</a></li>
            <li><a href="<?php echo $relativePath; ?>snmp_manager/pages/system_group.php" class="nav-link<?php echo ($currentFile == 'system_group.php') ? ' active' : ''; ?>">System Group</a></li>
            <li><a href="<?php echo $relativePath; ?>snmp_manager/pages/tcp_connections.php" class="nav-link<?php echo ($currentFile == 'tcp_connections.php') ? ' active' : ''; ?>">TCP Connections</a></li>
            <li><a href="<?php echo $relativePath; ?>snmp_manager/pages/icmp_stats.php" class="nav-link<?php echo ($currentFile == 'icmp_stats.php') ? ' active' : ''; ?>">ICMP Statistics</a></li>
        </ul>
    </div>
</nav>
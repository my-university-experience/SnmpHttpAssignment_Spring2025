<?php
// Include configuration file
require_once('config.php');

// Include header
include_once('includes/header.php');
?>

<h2>Welcome to SNMP Manager</h2>

<div class="dashboard">
    <p>This SNMP Manager allows you to monitor and manage your network devices using the Simple Network Management Protocol (SNMP).</p>
    
    <div class="features">
        <h3>Features:</h3>
        <ul>
            <li><strong>System Group Information:</strong> View and edit system information like contact, name, and location.</li>
            <li><strong>TCP Connection Table:</strong> Monitor current TCP connections on your system.</li>
            <li><strong>ICMP Statistics:</strong> View detailed ICMP statistics using different SNMP methods.</li>
        </ul>
    </div>
    
    <div class="status-panel">
        <h3>SNMP Agent Status</h3>
        <?php
        // Check if SNMP agent is responding
        $agent_status = false;
        try {
            $session = new SNMP($snmp_version, $snmp_host, $snmp_community_read, $snmp_timeout, $snmp_retries);
            $sysName = $session->get($system_oids['sysName']);
            $agent_status = true;
        } catch (Exception $e) {
            $agent_status = false;
        }
        ?>
        
        <div class="<?php echo $agent_status ? 'success-message' : 'error-message'; ?>">
            <?php if ($agent_status): ?>
                <p>SNMP Agent is active and responding at <?php echo $snmp_host; ?></p>
            <?php else: ?>
                <p>SNMP Agent is not responding. Please check your SNMP configuration and make sure the agent is running.</p>
                <p>Configuration: Host: <?php echo $snmp_host; ?>, Community: <?php echo $snmp_community_read; ?></p>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="quick-links">
        <h3>Quick Navigation</h3>
        <div class="button-group">
            <a href="pages/system_group.php" class="button">System Group</a>
            <a href="pages/tcp_connections.php" class="button">TCP Connections</a>
            <a href="pages/icmp_stats.php" class="button">ICMP Statistics</a>
        </div>
    </div>
</div>

<?php
// Include footer
include_once('includes/footer.php');
?>
<?php
// Include configuration file
require_once('../config.php');

// Include header
include_once('../includes/header.php');

// Process form submission for updating SNMP values
$update_message = '';
$update_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Create SNMP session with write community
        $session = new SNMP($snmp_version, $snmp_host, $snmp_community_write, $snmp_timeout, $snmp_retries);
        
        // Update sysContact if provided
        if (isset($_POST['sysContact']) && !empty($_POST['sysContact'])) {
            $session->set($system_oids['sysContact'], 's', $_POST['sysContact']);
        }
        
        // Update sysName if provided
        if (isset($_POST['sysName']) && !empty($_POST['sysName'])) {
            $session->set($system_oids['sysName'], 's', $_POST['sysName']);
        }
        
        // Update sysLocation if provided
        if (isset($_POST['sysLocation']) && !empty($_POST['sysLocation'])) {
            $session->set($system_oids['sysLocation'], 's', $_POST['sysLocation']);
        }
        
        $update_message = 'SNMP values updated successfully!';
        $update_success = true;
    } catch (Exception $e) {
        $update_message = 'Error updating SNMP values: ' . $e->getMessage();
        $update_success = false;
    }
}

// Get current system group values
$system_values = [];
$snmp_error = '';

try {
    // Create SNMP session with read community
    $session = new SNMP($snmp_version, $snmp_host, $snmp_community_read, $snmp_timeout, $snmp_retries);
    
    // Get each value from the system group
    foreach ($system_oids as $name => $oid) {
        $value = $session->get($oid);
        // Clean up the value (remove STRING: prefix, etc.)
        $system_values[$name] = cleanSnmpValue($value);
    }
} catch (Exception $e) {
    $snmp_error = 'Error retrieving SNMP values: ' . $e->getMessage();
}
?>

<h2>System Group Information</h2>

<?php if (!empty($update_message)): ?>
    <div class="<?php echo $update_success ? 'success-message' : 'error-message'; ?>">
        <?php echo $update_message; ?>
    </div>
<?php endif; ?>

<?php if (!empty($snmp_error)): ?>
    <div class="error-message">
        <?php echo $snmp_error; ?>
    </div>
<?php else: ?>
    <!-- Display System Group Information -->
    <div class="system-info">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Value</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>System Description</td>
                    <td><?php echo htmlspecialchars($system_values['sysDescr'] ?? 'N/A'); ?></td>
                    <td>A textual description of the entity</td>
                </tr>
                <tr>
                    <td>System Object ID</td>
                    <td><?php echo htmlspecialchars($system_values['sysObjectID'] ?? 'N/A'); ?></td>
                    <td>The vendor's authoritative identification of the network management subsystem</td>
                </tr>
                <tr>
                    <td>System Uptime</td>
                    <td><?php echo htmlspecialchars($system_values['sysUpTime'] ?? 'N/A'); ?></td>
                    <td>The time since the network management portion of the system was last re-initialized</td>
                </tr>
                <tr>
                    <td>System Contact</td>
                    <td><?php echo htmlspecialchars($system_values['sysContact'] ?? 'N/A'); ?></td>
                    <td>The contact person for this managed node</td>
                </tr>
                <tr>
                    <td>System Name</td>
                    <td><?php echo htmlspecialchars($system_values['sysName'] ?? 'N/A'); ?></td>
                    <td>An administratively-assigned name for this managed node</td>
                </tr>
                <tr>
                    <td>System Location</td>
                    <td><?php echo htmlspecialchars($system_values['sysLocation'] ?? 'N/A'); ?></td>
                    <td>The physical location of this node</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Form to update editable values -->
    <h3>Update System Information</h3>
    <form id="snmpUpdateForm" method="post" action="">
        <div class="form-group">
            <label for="sysContact">System Contact:</label>
            <input type="text" id="sysContact" name="sysContact" value="<?php echo htmlspecialchars($system_values['sysContact'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label for="sysName">System Name:</label>
            <input type="text" id="sysName" name="sysName" value="<?php echo htmlspecialchars($system_values['sysName'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label for="sysLocation">System Location:</label>
            <input type="text" id="sysLocation" name="sysLocation" value="<?php echo htmlspecialchars($system_values['sysLocation'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <input type="submit" value="Update Values">
        </div>
    </form>
<?php endif; ?>

<!-- Page Navigation -->
<div class="page-navigation">
    <a href="../index.php">&larr; Home</a>
    <a href="tcp_connections.php">TCP Connections &rarr;</a>
</div>

<?php
// Include footer
include_once('../includes/footer.php');
?>
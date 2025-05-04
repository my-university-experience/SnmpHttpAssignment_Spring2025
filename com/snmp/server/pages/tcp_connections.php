<?php
// Include configuration file
require_once('../config.php');

// Include header
include_once('../includes/header.php');

// Function to get service name from port
function getServiceName($port) {
    $services = [
        80 => 'HTTP',
        443 => 'HTTPS',
        3306 => 'MySQL',
        135 => 'RPC',
        139 => 'NetBIOS',
        445 => 'SMB',
        27017 => 'MongoDB',
        5228 => 'XMPP',
        8080 => 'HTTP-Alt',
        33060 => 'MySQLx',
        20 => 'FTP-Data',
        21 => 'FTP',
        22 => 'SSH',
        23 => 'Telnet',
        25 => 'SMTP',
        53 => 'DNS',
        110 => 'POP3',
        143 => 'IMAP',
        3389 => 'RDP',
        5432 => 'PostgreSQL'
    ];
    return isset($services[$port]) ? $services[$port] : '';
}

// Function to get connection state description
function getStateDescription($state) {
    $states = [
        1 => 'closed',
        2 => 'listen',
        3 => 'synSent',
        4 => 'synReceived',
        5 => 'established',
        6 => 'finWait1',
        7 => 'finWait2',
        8 => 'closeWait',
        9 => 'lastAck',
        10 => 'closing',
        11 => 'timeWait',
        12 => 'deleteTCB'
    ];
    return isset($states[$state]) ? $states[$state] : 'unknown';
}

// Get TCP Connection Table
$tcp_connections = [];
$snmp_error = '';

try {
    // Create SNMP session
    $session = new SNMP($snmp_version, $snmp_host, $snmp_community_read, $snmp_timeout, $snmp_retries);
    
    // Get TCP connection table
    $tcp_raw_data = $session->walk($tcp_conn_table_oid);
    
    // Process the data
    $connections = [];
    
    foreach ($tcp_raw_data as $oid => $value) {
        // Extract the connection identifier from the OID
        // Format: iso.3.6.1.2.1.6.13.1.{column}.{localIP}.{localPort}.{remoteIP}.{remotePort}
        $parts = explode('.', $oid);
        if (count($parts) >= 13) {
            $column_index = $parts[9]; // The column index (1-5)
            $local_ip = implode('.', array_slice($parts, 10, 4)); // Local IP address
            $local_port = $parts[14]; // Local port
            $remote_ip = implode('.', array_slice($parts, 15, 4)); // Remote IP address
            $remote_port = $parts[19]; // Remote port
            
            // Create a unique connection ID
            $connection_id = $local_ip . ':' . $local_port . '-' . $remote_ip . ':' . $remote_port;
            
            // Initialize connection if not exists
            if (!isset($connections[$connection_id])) {
                $connections[$connection_id] = [
                    'state' => '',
                    'localAddress' => $local_ip,
                    'localPort' => $local_port,
                    'remAddress' => $remote_ip,
                    'remPort' => $remote_port
                ];
            }
            
            // Clean up the value
            $clean_value = cleanSnmpValue($value);
            
            // Store the state value
            if ($column_index == 1) {
                $connections[$connection_id]['state'] = $clean_value;
            }
        }
    }
    
    // Display the connections
    echo '<div class="container mt-4">';
    echo '<h2>TCP Connections</h2>';
    
    if (empty($connections)) {
        echo '<div class="alert alert-info">No TCP connections found.</div>';
    } else {
        echo '<div class="table-responsive">';
        echo '<table class="table table-striped table-bordered">';
        echo '<thead class="thead-dark">';
        echo '<tr>';
        echo '<th>Local Address</th>';
        echo '<th>Local Port</th>';
        echo '<th>Service</th>';
        echo '<th>Remote Address</th>';
        echo '<th>Remote Port</th>';
        echo '<th>State</th>';
        echo '<th>Description</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        foreach ($connections as $conn) {
            $state_class = '';
            switch ($conn['state']) {
                case 2: // listen
                    $state_class = 'table-success';
                    break;
                case 5: // established
                    $state_class = 'table-primary';
                    break;
                case 8: // closeWait
                    $state_class = 'table-warning';
                    break;
                case 11: // timeWait
                    $state_class = 'table-secondary';
                    break;
            }
            
            echo '<tr class="' . $state_class . '">';
            echo '<td>' . htmlspecialchars($conn['localAddress']) . '</td>';
            echo '<td>' . htmlspecialchars($conn['localPort']) . '</td>';
            echo '<td>' . getServiceName($conn['localPort']) . '</td>';
            echo '<td>' . htmlspecialchars($conn['remAddress']) . '</td>';
            echo '<td>' . htmlspecialchars($conn['remPort']) . '</td>';
            echo '<td>' . getStateDescription($conn['state']) . '</td>';
            echo '<td>' . getServiceName($conn['remPort']) . '</td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    }
    
    echo '</div>';
    
} catch (Exception $e) {
    $snmp_error = 'Error retrieving TCP connection table: ' . $e->getMessage();
}
?>

<h2>TCP Connection Table</h2>

<div class="page-description">
    <p>This page displays all active TCP connections on your system, including:</p>
    <ul>
        <li>Web browsing sessions</li>
        <li>Email connections</li>
        <li>Remote access sessions</li>
        <li>Application connections to servers</li>
        <li>Background services connections</li>
    </ul>
    <p>Use the refresh controls below to update the connection list in real-time.</p>
</div>

<div class="refresh-controls">
    <button id="refreshButton" class="btn btn-primary">Refresh Now</button>
    
    <div class="auto-refresh">
        <label for="refreshInterval">Auto-refresh interval:</label>
        <select id="refreshInterval" class="form-select">
            <option value="0">Off</option>
            <option value="5">5 seconds</option>
            <option value="10">10 seconds</option>
            <option value="30">30 seconds</option>
            <option value="60">1 minute</option>
        </select>
        <span id="refreshFeedback" class="refresh-status"></span>
    </div>
</div>

<?php if (!empty($snmp_error)): ?>
    <div class="error-message">
        <?php echo $snmp_error; ?>
    </div>
<?php endif; ?>

<!-- Page Navigation -->
<div class="page-navigation">
    <a href="system_group.php" class="btn btn-secondary">&larr; System Group</a>
    <a href="icmp_stats.php" class="btn btn-secondary">ICMP Statistics &rarr;</a>
</div>

<style>
.state-listen {
    color: #28a745;
    font-weight: bold;
}
.state-established {
    color: #007bff;
    font-weight: bold;
}
.state-timeWait {
    color: #6c757d;
}
.state-closeWait {
    color: #ffc107;
}
.refresh-controls {
    margin: 20px 0;
    padding: 10px;
    background-color: #f8f9fa;
    border-radius: 5px;
}
.auto-refresh {
    display: inline-block;
    margin-left: 20px;
}
.refresh-status {
    margin-left: 10px;
    color: #6c757d;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const refreshButton = document.getElementById('refreshButton');
    const refreshInterval = document.getElementById('refreshInterval');
    const refreshFeedback = document.getElementById('refreshFeedback');
    let refreshTimer = null;

    function refreshPage() {
        refreshFeedback.textContent = 'Refreshing...';
        refreshFeedback.style.display = 'inline';
        location.reload();
    }

    refreshButton.addEventListener('click', refreshPage);

    refreshInterval.addEventListener('change', function() {
        const interval = parseInt(this.value);
        
        if (refreshTimer) {
            clearInterval(refreshTimer);
            refreshTimer = null;
        }
        
        if (interval > 0) {
            refreshTimer = setInterval(refreshPage, interval * 1000);
            refreshFeedback.textContent = `Auto-refresh enabled (${interval}s)`;
            refreshFeedback.style.display = 'inline';
        } else {
            refreshFeedback.textContent = '';
            refreshFeedback.style.display = 'none';
        }
    });
});
</script>

<?php
// Include footer
include_once('../includes/footer.php');
?>
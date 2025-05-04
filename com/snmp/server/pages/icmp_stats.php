<?php
// Include configuration file
require_once('../config.php');

// Include header
include_once('../includes/header.php');

// Function to get ICMP statistics using snmp2_get method
function getIcmpStatsByGet($host, $community) {
    global $icmp_oids, $icmp_base_oid;
    $stats = [];
    $error = '';
    
    try {
        $session = new SNMP(1, $host, $community);
        $session->valueretrieval = SNMP_VALUE_PLAIN;
        
        // Loop through all ICMP OIDs (1-26)
        foreach ($icmp_oids as $suffix => $name) {
            $oid = $icmp_base_oid . '.' . $suffix . '.0'; // Add .0 for scalar objects
            try {
                $value = $session->get($oid);
                if ($value !== false) {
                    $stats[$name] = $value;
                }
            } catch (Exception $e) {
                // Skip this OID if it's not available
                continue;
            }
        }
        
        if (empty($stats)) {
            $error = 'No ICMP statistics available. The SNMP agent might not support ICMP MIB or the OIDs are not accessible.';
        }
    } catch (Exception $e) {
        $error = 'Error retrieving ICMP statistics: ' . $e->getMessage();
    }
    
    return ['stats' => $stats, 'error' => $error];
}

// Function to get ICMP statistics using snmp2_walk method
function getIcmpStatsByWalk($host, $community) {
    global $icmp_base_oid, $icmp_oids;
    $stats = [];
    $error = '';
    
    try {
        $session = new SNMP(1, $host, $community);
        $session->valueretrieval = SNMP_VALUE_PLAIN;
        
        // Try walking each individual OID
        foreach ($icmp_oids as $suffix => $name) {
            $oid = $icmp_base_oid . '.' . $suffix;
            try {
                $walk_results = $session->walk($oid);
                if ($walk_results !== false) {
                    foreach ($walk_results as $walk_oid => $value) {
                        // Extract the OID suffix number
                        preg_match('/\.1\.3\.6\.1\.2\.1\.5\.(\d+)/', $walk_oid, $matches);
                        
                        if (isset($matches[1])) {
                            $suffix = $matches[1];
                            $name = isset($icmp_oids[$suffix]) ? $icmp_oids[$suffix] : "icmpItem$suffix";
                            $stats[$name] = $value;
                        }
                    }
                }
            } catch (Exception $e) {
                // Skip this OID if it's not available
                continue;
            }
        }
        
        if (empty($stats)) {
            // If walk fails, try getting individual OIDs like the GET method
            foreach ($icmp_oids as $suffix => $name) {
                $oid = $icmp_base_oid . '.' . $suffix . '.0';
                try {
                    $value = $session->get($oid);
                    if ($value !== false) {
                        $stats[$name] = $value;
                    }
                } catch (Exception $e) {
                    // Skip this OID if it's not available
                    continue;
                }
            }
            
            if (empty($stats)) {
                $error = 'No ICMP statistics available. The SNMP agent might not support ICMP MIB or the OIDs are not accessible.';
            }
        }
    } catch (Exception $e) {
        $error = 'Error retrieving ICMP statistics by WALK: ' . $e->getMessage();
    }
    
    return ['stats' => $stats, 'error' => $error];
}

// Get ICMP stats using both methods
$get_results = getIcmpStatsByGet($snmp_host, $snmp_community_read);
$walk_results = getIcmpStatsByWalk($snmp_host, $snmp_community_read);
?>

<h2>The ICMP Statistics as Request in Part1 – PHP Page 3</h2>

<div class="refresh-controls">
    <button id="refreshButton" onclick="refreshStats()">Refresh Now</button>
    
    <div class="auto-refresh">
        <label for="refreshInterval">Auto-refresh:</label>
        <select id="refreshInterval" onchange="handleAutoRefresh(this.value)">
            <option value="0">Off</option>
            <option value="5">5 seconds</option>
            <option value="10">10 seconds</option>
            <option value="30">30 seconds</option>
            <option value="60">1 minute</option>
        </select>
        <span id="refreshFeedback" style="display: none;"></span>
    </div>
</div>

<div class="tables-container" style="display: flex; justify-content: center;">
    <table border="1" cellpadding="5" cellspacing="0" style="background: #b2dfdb; margin-right: 20px;">
        <caption style="font-weight: bold; font-size: 1.1em; padding: 8px;">Method1: By Get</caption>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Value</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 1;
            foreach ($get_results['stats'] as $name => $value):
                // Try to get the SNMP type prefix (e.g., Counter32: value)
                $snmp_type = '';
                $snmp_value = $value;
                if (preg_match('/^(\w+):\s*(.*)$/', $value, $matches)) {
                    $snmp_type = $matches[1];
                    $snmp_value = $matches[2];
                } else {
                    $snmp_type = 'Counter32'; // fallback
                }
            ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo htmlspecialchars($name); ?></td>
                <td><?php echo $snmp_type . ': ' . htmlspecialchars($snmp_value); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <table border="1" cellpadding="5" cellspacing="0" style="background: #b2dfdb;">
        <caption style="font-weight: bold; font-size: 1.1em; padding: 8px;">Method2: By Walk</caption>
        <thead>
            <tr>
                <th>Item #</th>
                <th>Name</th>
                <th>Value</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 1;
            foreach ($walk_results['stats'] as $name => $value):
                // Remove SNMP type prefix if present
                $snmp_value = $value;
                if (preg_match('/^(\w+):\s*(.*)$/', $value, $matches)) {
                    $snmp_value = $matches[2];
                }
            ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo htmlspecialchars($name); ?></td>
                <td><?php echo htmlspecialchars($snmp_value); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Page Navigation -->
<div class="page-navigation">
    <a href="tcp_connections.php">&larr; TCP Connections</a>
    <a href="../index.php">Home &rarr;</a>
</div>

<script>
let autoRefreshInterval = null;

function refreshStats() {
    // Show loading feedback
    const feedback = document.getElementById('refreshFeedback');
    feedback.textContent = 'Refreshing...';
    feedback.style.display = 'inline';
    
    // Fetch new data from the AJAX endpoint
    fetch('icmp_stats_data.php')
        .then(response => response.text())
        .then(html => {
            // Replace the tables-container with the new HTML
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            const newTables = tempDiv.querySelector('.tables-container');
            if (newTables) {
                document.querySelector('.tables-container').innerHTML = newTables.innerHTML;
            }
            // Show success feedback
            feedback.textContent = 'Refreshed successfully!';
            setTimeout(() => {
                feedback.style.display = 'none';
            }, 2000);
        })
        .catch(error => {
            feedback.textContent = 'Error refreshing data';
            console.error('Error:', error);
        });
}

function handleAutoRefresh(interval) {
    // Clear any existing interval
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
        autoRefreshInterval = null;
    }
    
    // Set new interval if not "Off"
    if (interval > 0) {
        autoRefreshInterval = setInterval(refreshStats, interval * 1000);
    }
}

// Initialize auto-refresh if an interval is selected
document.addEventListener('DOMContentLoaded', function() {
    const intervalSelect = document.getElementById('refreshInterval');
    if (intervalSelect.value > 0) {
        handleAutoRefresh(intervalSelect.value);
    }
});
</script>

<?php
// Include footer
include_once('../includes/footer.php');
?>
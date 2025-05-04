<?php
// SNMP Configuration
$snmp_host = "127.0.0.1"; // Use explicit IPv4 address instead of hostname
$snmp_community_read = "public"; // Community string for read operations
$snmp_community_write = "private"; // Community string for write operations
$snmp_version = 1; // Using SNMP version 2c (1 = SNMPv2c)
$snmp_timeout = 1000000; // Timeout in microseconds
$snmp_retries = 5; // Number of retries

// MIB OIDs
$system_oids = [
    'sysDescr' => '1.3.6.1.2.1.1.1.0',
    'sysObjectID' => '1.3.6.1.2.1.1.2.0',
    'sysUpTime' => '1.3.6.1.2.1.1.3.0',
    'sysContact' => '1.3.6.1.2.1.1.4.0',
    'sysName' => '1.3.6.1.2.1.1.5.0',
    'sysLocation' => '1.3.6.1.2.1.1.6.0'
    // Not including sysServices (1.3.6.1.2.1.1.7.0) as per requirements
];

// TCP Connection Table OID
$tcp_conn_table_oid = '1.3.6.1.2.1.6.13'; // tcpConnTable

// ICMP Group OIDs (Group 5)
$icmp_base_oid = '1.3.6.1.2.1.5';

// ICMP Group Names for Method 1 (Get)
$icmp_oids = [
    '1' => 'icmpInMsgs',
    '2' => 'icmpInErrors',
    '3' => 'icmpInDestUnreachs',
    '4' => 'icmpInTimeExcds',
    '5' => 'icmpInParmProbs',
    '6' => 'icmpInSrcQuenchs',
    '7' => 'icmpInRedirects',
    '8' => 'icmpInEchos',
    '9' => 'icmpInEchoReps',
    '10' => 'icmpInTimestamps',
    '11' => 'icmpInTimestampReps',
    '12' => 'icmpInAddrMasks',
    '13' => 'icmpInAddrMaskReps',
    '14' => 'icmpOutMsgs',
    '15' => 'icmpOutErrors',
    '16' => 'icmpOutDestUnreachs',
    '17' => 'icmpOutTimeExcds',
    '18' => 'icmpOutParmProbs',
    '19' => 'icmpOutSrcQuenchs',
    '20' => 'icmpOutRedirects',
    '21' => 'icmpOutEchos',
    '22' => 'icmpOutEchoReps',
    '23' => 'icmpOutTimestamps',
    '24' => 'icmpOutTimestampReps',
    '25' => 'icmpOutAddrMasks',
    '26' => 'icmpOutAddrMaskReps'
];

// Page navigation
$pages = [
    'snmp_manager/index.php' => 'Home',
    'snmp_manager/pages/system_group.php' => 'System Group',
    'snmp_manager/pages/tcp_connections.php' => 'TCP Connections',
    'snmp_manager/pages/icmp_stats.php' => 'ICMP Statistics'
];

// Helper function to display error messages
function displayError($message) {
    echo '<div class="error-message">';
    echo '<p><strong>Error:</strong> ' . $message . '</p>';
    echo '</div>';
}

// Helper function to clean SNMP values (remove data type like Counter32, INTEGER)
function cleanSnmpValue($value) {
    // Split by first space which usually separates the type from value
    $parts = explode(' ', $value, 2);
    if (count($parts) > 1) {
        return trim($parts[1]);
    }
    return trim($value);
}
?>
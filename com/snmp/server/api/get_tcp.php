<?php
// Include configuration file
require_once('../config.php');

// Set content type to JSON
header('Content-Type: application/json');

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

// Response object
$response = [
    'success' => false,
    'message' => '',
    'data' => null
];

// Get TCP Connection Table
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
    
    // Convert connections to array and add service names and state descriptions
    $result = [];
    foreach ($connections as $conn) {
        $result[] = [
            'localAddress' => $conn['localAddress'],
            'localPort' => $conn['localPort'],
            'localService' => getServiceName($conn['localPort']),
            'remoteAddress' => $conn['remAddress'],
            'remotePort' => $conn['remPort'],
            'remoteService' => getServiceName($conn['remPort']),
            'stateCode' => $conn['state'],
            'state' => getStateDescription($conn['state'])
        ];
    }
    
    $response['success'] = true;
    $response['data'] = $result;
    
} catch (Exception $e) {
    $response['message'] = 'Error retrieving TCP connection table: ' . $e->getMessage();
}

// Output JSON response
echo json_encode($response);
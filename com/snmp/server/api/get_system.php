<?php
// Include configuration file
require_once('../config.php');

// Set content type to JSON
header('Content-Type: application/json');

// Response object
$response = [
    'success' => false,
    'message' => '',
    'data' => null
];

// Get current system group values
try {
    // Create SNMP session with read community
    $session = new SNMP($snmp_version, $snmp_host, $snmp_community_read, $snmp_timeout, $snmp_retries);
    
    // Get each value from the system group
    $system_values = [];
    foreach ($system_oids as $name => $oid) {
        $value = $session->get($oid);
        // Clean up the value (remove STRING: prefix, etc.)
        $system_values[$name] = cleanSnmpValue($value);
    }
    
    $response['success'] = true;
    $response['data'] = [
        'values' => $system_values,
        'descriptions' => [
            'sysDescr' => 'A textual description of the entity',
            'sysObjectID' => 'The vendor\'s authoritative identification of the network management subsystem',
            'sysUpTime' => 'The time since the network management portion of the system was last re-initialized',
            'sysContact' => 'The contact person for this managed node',
            'sysName' => 'An administratively-assigned name for this managed node',
            'sysLocation' => 'The physical location of this node'
        ]
    ];
} catch (Exception $e) {
    $response['message'] = 'Error retrieving SNMP values: ' . $e->getMessage();
}

// Output JSON response
echo json_encode($response);
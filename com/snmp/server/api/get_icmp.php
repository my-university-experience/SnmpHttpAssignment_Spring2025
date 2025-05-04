<?php
// Include configuration file
require_once('../config.php');

// Set content type to JSON
header('Content-Type: application/json');

// Function to get ICMP statistics
function getIcmpStats($host, $community) {
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
                    // Clean the value, removing Counter32: or INTEGER: prefixes
                    $clean_value = $value;
                    if (preg_match('/^(\w+):\s*(.*)$/', $value, $matches)) {
                        $clean_value = $matches[2];
                    }
                    
                    $stats[] = [
                        'id' => $suffix,
                        'name' => $name,
                        'value' => $clean_value
                    ];
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

// Response object
$response = [
    'success' => false,
    'message' => '',
    'data' => null
];

// Get ICMP stats
$result = getIcmpStats($snmp_host, $snmp_community_read);

if (!empty($result['error'])) {
    $response['message'] = $result['error'];
} else {
    $response['success'] = true;
    $response['data'] = $result['stats'];
}

// Output JSON response
echo json_encode($response);
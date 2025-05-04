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

// Get parameters
$name = isset($_GET['name']) ? $_GET['name'] : '';
$value = isset($_GET['value']) ? $_GET['value'] : '';

// Validate input
if (empty($name) || empty($value)) {
    $response['message'] = 'Error: Name and value parameters are required';
    echo json_encode($response);
    exit;
}

// Map parameter names to OIDs
$valid_fields = [
    'sysContact' => $system_oids['sysContact'],
    'sysName' => $system_oids['sysName'],
    'sysLocation' => $system_oids['sysLocation']
];

// Check if the field is valid and editable
if (!isset($valid_fields[$name])) {
    $response['message'] = 'Error: Invalid or non-editable field: ' . $name;
    echo json_encode($response);
    exit;
}

try {
    // Create SNMP session with write community
    $session = new SNMP($snmp_version, $snmp_host, $snmp_community_write, $snmp_timeout, $snmp_retries);
    
    // Disable PHP error output
    $old_error_reporting = error_reporting();
    error_reporting(0);
    
    // Update the value
    $oid = $valid_fields[$name];
    $result = $session->set($oid, 's', $value);
    
    // Restore error reporting
    error_reporting($old_error_reporting);
    
    if ($result === false) {
        throw new Exception("SNMP set operation failed. Check SNMP agent settings and community string.");
    }
    
    $response['success'] = true;
    $response['message'] = 'Successfully updated ' . $name . ' to: ' . $value;
    
    // Get the updated value to confirm
    $session = new SNMP($snmp_version, $snmp_host, $snmp_community_read, $snmp_timeout, $snmp_retries);
    $updated_value = cleanSnmpValue($session->get($oid));
    $response['data'] = ['field' => $name, 'value' => $updated_value];
    
} catch (Exception $e) {
    $response['message'] = 'Error updating SNMP value: ' . $e->getMessage();
}

// Output JSON response
echo json_encode($response);
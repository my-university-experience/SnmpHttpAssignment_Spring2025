<?php
// For testing, just acknowledge the update
header('Content-Type: text/plain');
if (isset($_GET['name']) && isset($_GET['value'])) {
    $name = $_GET['name'];
    $value = $_GET['value'];
    
    // Check if system_data.txt exists, create if it doesn't
    if (!file_exists('system_data.txt')) {
        file_put_contents('system_data.txt', "admin@example.com\nTestSystem\nTest Lab");
    }
    
    // Make sure file is readable and writable
    chmod('system_data.txt', 0666);
    
    // Read current values
    $systemValues = file('system_data.txt', FILE_IGNORE_NEW_LINES);
    $sysContact = isset($systemValues[0]) ? $systemValues[0] : "admin@example.com";
    $sysName = isset($systemValues[1]) ? $systemValues[1] : "TestSystem";
    $sysLocation = isset($systemValues[2]) ? $systemValues[2] : "Test Lab";
    
    // Update the appropriate value
    if ($name == "sysContact") {
        $sysContact = $value;
    } else if ($name == "sysName") {
        $sysName = $value;
    } else if ($name == "sysLocation") {
        $sysLocation = $value;
    }
    
    // Write back to file
    if (file_put_contents('system_data.txt', "$sysContact\n$sysName\n$sysLocation") === false) {
        echo "Error: Failed to write to file";
    } else {
        echo "Successfully updated $name to: $value";
    }
} else {
    echo "Error: Missing parameters";
}
?>
<?php
// For testing, return sample data
header('Content-Type: text/plain');

// Read stored values from file
$systemValues = file('system_data.txt', FILE_IGNORE_NEW_LINES);
$sysContact = isset($systemValues[0]) ? $systemValues[0] : "admin@example.com";
$sysName = isset($systemValues[1]) ? $systemValues[1] : "TestSystem";
$sysLocation = isset($systemValues[2]) ? $systemValues[2] : "Test Lab";

echo "System Group:\n";
echo "System Description: Test System Description\n";
echo "System Object ID: 1.3.6.1.4.1.2021.250.10\n";
echo "System Uptime: 123456789\n";
echo "System Contact: $sysContact\n";
echo "System Name: $sysName\n";
echo "System Location: $sysLocation\n";
?>
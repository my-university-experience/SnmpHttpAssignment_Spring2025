<?php
// For testing, return sample data
header('Content-Type: text/plain');

echo "TCP Connection Table:\n";
echo "LocalAddress: 127.0.0.1, LocalPort: 80, RemoteAddress: 192.168.1.100, RemotePort: 12345, State: ESTABLISHED\n";
echo "LocalAddress: 127.0.0.1, LocalPort: 443, RemoteAddress: 192.168.1.101, RemotePort: 54321, State: ESTABLISHED\n";
?>
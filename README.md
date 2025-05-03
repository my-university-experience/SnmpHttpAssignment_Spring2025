# SNMP Manager Application

## Overview
This Java application implements a client that communicates with PHP pages via HTTP to retrieve and display SNMP data. It was developed as part of the Computer Networks 2 (10636455) course assignment at An-Najah National University.

## Features
- Three-tabbed interface for displaying different types of SNMP data:
  - **System Group Tab**: Displays system information and allows updating of sysContact, sysName, and sysLocation values
  - **TCP Table Tab**: Shows TCP connection information
  - **ICMP Statistics Tab**: Displays ICMP statistics

## Requirements
- Java Development Kit (JDK) 8 or higher
- XAMPP (or WAMP) server for PHP backend
- SNMP service enabled on the local machine

## Project Structure
- **Java Client Application**:
  - `Main.java`: Application entry point
  - `HttpConnection.java`: Handles HTTP communication with PHP pages
  - `MainFrame.java`: Main application window with tabbed interface
  - `SystemPanel.java`: Panel for displaying and updating System Group data
  - `TcpPanel.java`: Panel for displaying TCP connection table
  - `IcmpPanel.java`: Panel for displaying ICMP statistics

- **PHP Backend** (place in XAMPP htdocs/snmp/):
  - `get_system.php`: Returns System Group data
  - `get_tcp.php`: Returns TCP Table data
  - `get_icmp.php`: Returns ICMP Statistics
  - `update_system.php`: Handles updates to system values
  - `system_data.txt`: Stores persistent system data

## Setup Instructions
1. **Set up XAMPP**:
   - Install XAMPP if not already installed
   - Copy the PHP files to `C:\xampp\htdocs\snmp\` directory
   - Start Apache server in XAMPP Control Panel

2. **Enable SNMP Service** (Windows):
   - Go to Control Panel > Programs > Turn Windows features on or off
   - Check "Simple Network Management Protocol (SNMP)" and install
   - Configure SNMP service to use community strings "public" for read and "private" for write

3. **Run the Java Application**:
   - Compile and run the application using your preferred Java IDE
   - The application will communicate with the PHP pages to retrieve and display SNMP data

## Usage
- **System Group Tab**:
  - Click "Get System Data" to retrieve the system information
  - Enter new values in the text fields and click the corresponding update buttons to modify system values

- **TCP Table Tab**:
  - Click "Get TCP Table" to view the current TCP connections

- **ICMP Statistics Tab**:
  - Click "Get ICMP Statistics" to view ICMP statistics
  - Run commands like `ping www.google.com` or `tracert www.amazon.com` to see changes in ICMP statistics

## Notes
- The application requires a properly configured SNMP service on the local machine
- Community strings must be set correctly for read and write access
- Make sure XAMPP's Apache server is running before using the application

## Developer
- Yazan Al-Sedih
- Computer Engineering Department
- An-Najah National University
- Spring 2025

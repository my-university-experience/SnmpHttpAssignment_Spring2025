# SNMP Manager Application

## Overview
This Java application implements a client that communicates with PHP pages via HTTP to retrieve and display SNMP data. It was developed as part of the Computer Networks 2 (10636455) course assignment at An-Najah National University.

## Features
- Three-tabbed interface for displaying different types of SNMP data:
  - **System Group Tab**: Displays system information and allows updating of sysContact, sysName, and sysLocation values
  - **TCP Table Tab**: Shows TCP connection information 
  - **ICMP Statistics Tab**: Displays ICMP statistics in a single table

## Screenshots

### System Group Tab
![System Group Tab](https://github.com/user-attachments/assets/0e3586c0-342a-4c34-bcd8-1d5e912aa7b3)

*The System Group tab displays system information and allows updating of contact, name, and location values*

### TCP Connections Tab
![TCP Connections Tab](https://github.com/user-attachments/assets/1468e603-26f0-45ee-af16-d988a26f70e1)

*The TCP Connections tab displays all active TCP connections on the system*

### ICMP Statistics Tab
![ICMP Statistics Tab](https://github.com/user-attachments/assets/58934100-2180-4a4b-a4c1-9d45050473a0)

*The ICMP Statistics tab displays ICMP protocol statistics in a single table*

## Requirements
- Java Development Kit (JDK) 8 or higher
- XAMPP (or WAMP) server for PHP backend
- SNMP service enabled on the local machine
- JSON library for Java (org.json)

## Project Structure
- **Java Client Application**:
  - `Main.java`: Application entry point
  - `HttpConnection.java`: Handles HTTP communication with PHP API endpoints
  - `MainFrame.java`: Main application window with tabbed interface
  - `SystemPanel.java`: Panel for displaying and updating System Group data
  - `TcpPanel.java`: Panel for displaying TCP connection table
  - `IcmpPanel.java`: Panel for displaying ICMP statistics
- **PHP Backend** (place in XAMPP htdocs folder):
  - **API Folder**:
    - `get_system.php`: Returns System Group data in JSON format
    - `update_system.php`: Handles updates to system values
    - `get_tcp.php`: Returns TCP Table data in JSON format
    - `get_icmp.php`: Returns ICMP Statistics in JSON format
  - **Pages Folder**:
    - Web interface HTML/PHP pages for Part 1 of the assignment
  - `config.php`: Configuration settings for SNMP connections

## Setup Instructions
1. **Set up XAMPP**:
   - Install XAMPP if not already installed
   - Copy the project files to `C:\xampp\htdocs\snmp_manager\` directory
   - Start Apache server in XAMPP Control Panel

2. **Enable SNMP Service** (Windows):
   - Go to Control Panel > Programs > Turn Windows features on or off
   - Check "Simple Network Management Protocol (SNMP)" and install
   - Open Services (services.msc)
   - Find SNMP Service and go to Properties > Security
   - Add "public" community with READ permissions
   - Add "private" community with READ/WRITE permissions
   - Restart the SNMP service

3. **Run the Java Application**:
   - Make sure the org.json library is in your classpath
   - Compile and run the application using your preferred Java IDE
   - The application will communicate with the PHP API endpoints to retrieve and display SNMP data

## Usage
- **System Group Tab**:
  - Click "Get System Data" to retrieve the system information
  - Enter new values in the text fields and click the corresponding update buttons to modify system values
- **TCP Table Tab**:
  - Click "Get TCP Table" to view the current TCP connections
- **ICMP Statistics Tab**:
  - Click "Get ICMP Statistics" to view ICMP statistics
  - Run commands like `ping www.google.com` or `tracert www.amazon.com` to see changes in ICMP statistics

## Implementation Details
- The Java client communicates with PHP API endpoints using HTTP requests
- The PHP API endpoints communicate with the SNMP agent using PHP's SNMP extension
- Data is exchanged in JSON format between the Java client and PHP API
- The application demonstrates both GET and SET operations using SNMP

## Related Projects
- [SNMP_Manager Web Interface](https://github.com/University-Experience/SNMP_Manager) - The web interface implementation (Part 1 of the assignment)

## Notes
- The application requires a properly configured SNMP service on the local machine
- Community strings must be set correctly for read and write access
- Make sure XAMPP's Apache server is running before using the application
- For ICMP statistics, the Java client displays a single table as required by Part 2 of the assignment, while the PHP web interface displays two tables (Get and Walk methods)

## Developer
- Yazan Al-Sedih
- Computer Engineering Department
- An-Najah National University
- Spring 2025

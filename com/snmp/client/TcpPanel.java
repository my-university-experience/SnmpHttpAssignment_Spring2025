package com.snmp.client;

import org.json.JSONArray;
import org.json.JSONObject;
import javax.swing.*;
import javax.swing.table.DefaultTableModel;
import java.awt.*;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;

public class TcpPanel extends JPanel {
    private JTable tcpTable;
    private DefaultTableModel tableModel;
    private JButton refreshBtn;
    private HttpConnection connection;
    private JPanel statusPanel;
    private JLabel statusLabel;

    public TcpPanel() {
        connection = new HttpConnection();
        initializeUI();
    }

    private void initializeUI() {
        setLayout(new BorderLayout(10, 10));

        // Create table for TCP connections
        String[] columnNames = {"Local Address", "Local Port", "Service", "Remote Address", "Remote Port", "State", "Description"};
        tableModel = new DefaultTableModel(columnNames, 0);
        tcpTable = new JTable(tableModel);
        tcpTable.setRowHeight(25);
        tcpTable.getTableHeader().setFont(new Font("SansSerif", Font.BOLD, 12));

        // Set column widths
        tcpTable.getColumnModel().getColumn(0).setPreferredWidth(120); // Local Address
        tcpTable.getColumnModel().getColumn(1).setPreferredWidth(80);  // Local Port
        tcpTable.getColumnModel().getColumn(2).setPreferredWidth(100); // Service
        tcpTable.getColumnModel().getColumn(3).setPreferredWidth(120); // Remote Address
        tcpTable.getColumnModel().getColumn(4).setPreferredWidth(80);  // Remote Port
        tcpTable.getColumnModel().getColumn(5).setPreferredWidth(100); // State
        tcpTable.getColumnModel().getColumn(6).setPreferredWidth(120); // Description

        // Add scrollpane for the table
        JScrollPane scrollPane = new JScrollPane(tcpTable);
        add(scrollPane, BorderLayout.CENTER);

        // Create control panel
        JPanel controlPanel = new JPanel(new FlowLayout(FlowLayout.LEFT));
        refreshBtn = new JButton("Get TCP Table");
        refreshBtn.setFont(new Font("SansSerif", Font.BOLD, 12));
        refreshBtn.addActionListener(e -> fetchTcpData());
        controlPanel.add(refreshBtn);

        // Add description
        JPanel descPanel = new JPanel(new BorderLayout());
//        JTextArea descArea = new JTextArea(
//                "This panel displays active TCP connections from your system. " +
//                        "These include web browsing sessions, email connections, and " +
//                        "background service connections."
//        );
//        descArea.setWrapStyleWord(true);
//        descArea.setLineWrap(true);
//        descArea.setEditable(false);
//        descArea.setBackground(getBackground());
//        descArea.setFont(new Font("SansSerif", Font.ITALIC, 12));
//        descPanel.add(descArea, BorderLayout.CENTER);
        descPanel.add(controlPanel, BorderLayout.SOUTH);
        add(descPanel, BorderLayout.NORTH);

        // Add status panel
        statusPanel = new JPanel(new FlowLayout(FlowLayout.LEFT));
        statusLabel = new JLabel("Ready");
        statusPanel.add(statusLabel);
        add(statusPanel, BorderLayout.SOUTH);
    }

    public void fetchTcpData() {
        try {
            // Update status
            statusLabel.setText("Fetching TCP connection data...");

            // Clear table
            tableModel.setRowCount(0);

            // Get data from server
            JSONObject response = connection.getTcpTable();

            if (response.getBoolean("success")) {
                JSONArray connections = response.getJSONArray("data");

                // Add each connection to the table
                for (int i = 0; i < connections.length(); i++) {
                    JSONObject conn = connections.getJSONObject(i);
                    tableModel.addRow(new Object[]{
                            conn.getString("localAddress"),
                            conn.getString("localPort"),
                            conn.getString("localService"),
                            conn.getString("remoteAddress"),
                            conn.getString("remotePort"),
                            conn.getString("state"),
                            conn.getString("remoteService")
                    });
                }

                // Update status
                statusLabel.setText("Found " + connections.length() + " TCP connections");
            } else {
                JOptionPane.showMessageDialog(this,
                        "Error: " + response.getString("message"),
                        "TCP Data Error",
                        JOptionPane.ERROR_MESSAGE);
                statusLabel.setText("Error fetching TCP data");
            }
        } catch (Exception ex) {
            JOptionPane.showMessageDialog(this,
                    "Error: " + ex.getMessage(),
                    "Connection Error",
                    JOptionPane.ERROR_MESSAGE);
            statusLabel.setText("Connection error");
            ex.printStackTrace();
        }
    }
}
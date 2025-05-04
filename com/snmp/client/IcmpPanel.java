package com.snmp.client;

import org.json.JSONArray;
import org.json.JSONObject;
import javax.swing.*;
import javax.swing.table.DefaultTableModel;
import java.awt.*;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;

public class IcmpPanel extends JPanel {
    private JTable icmpTable;
    private DefaultTableModel tableModel;
    private JButton refreshBtn;
    private HttpConnection connection;
    private JPanel statusPanel;
    private JLabel statusLabel;

    public IcmpPanel() {
        connection = new HttpConnection();
        initializeUI();
    }

    private void initializeUI() {
        setLayout(new BorderLayout(10, 10));

        // Create table for ICMP statistics
        String[] columnNames = {"ID", "Name", "Value"};
        tableModel = new DefaultTableModel(columnNames, 0);
        icmpTable = new JTable(tableModel);
        icmpTable.setRowHeight(25);
        icmpTable.getTableHeader().setFont(new Font("SansSerif", Font.BOLD, 12));

        // Set column widths
        icmpTable.getColumnModel().getColumn(0).setPreferredWidth(50);  // ID
        icmpTable.getColumnModel().getColumn(1).setPreferredWidth(200); // Name
        icmpTable.getColumnModel().getColumn(2).setPreferredWidth(100); // Value

        // Add scrollpane for the table
        JScrollPane scrollPane = new JScrollPane(icmpTable);
        add(scrollPane, BorderLayout.CENTER);

        // Create control panel
        JPanel controlPanel = new JPanel(new FlowLayout(FlowLayout.LEFT));
        refreshBtn = new JButton("Get ICMP Statistics");
        refreshBtn.setFont(new Font("SansSerif", Font.BOLD, 12));
        refreshBtn.addActionListener(e -> fetchIcmpData());
        controlPanel.add(refreshBtn);

        // Create auto-refresh option
        JLabel autoRefreshLabel = new JLabel("Auto-refresh: ");
        controlPanel.add(autoRefreshLabel);

        String[] refreshOptions = {"Off", "5 seconds", "10 seconds", "30 seconds"};
        JComboBox<String> refreshComboBox = new JComboBox<>(refreshOptions);
        refreshComboBox.addActionListener(e -> {
            String selected = (String)refreshComboBox.getSelectedItem();
            if (selected.equals("Off")) {
                // Turn off auto-refresh logic
            } else {
                // Parse time and set up auto-refresh timer
                int seconds = Integer.parseInt(selected.split(" ")[0]);
                // Set up timer
            }
        });
        controlPanel.add(refreshComboBox);

        // Add description
        JPanel descPanel = new JPanel(new BorderLayout());
//        JTextArea descArea = new JTextArea(
//                "This panel displays ICMP (Internet Control Message Protocol) statistics. " +
//                        "These values change when you run network tools like ping or tracert. " +
//                        "Try running 'ping www.google.com' in a command prompt to see changes."
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

    public void fetchIcmpData() {
        try {
            // Update status
            statusLabel.setText("Fetching ICMP statistics...");

            // Clear table
            tableModel.setRowCount(0);

            // Get data from server
            JSONObject response = connection.getIcmpStats();

            if (response.getBoolean("success")) {
                JSONArray stats = response.getJSONArray("data");

                // Add each statistic to the table
                for (int i = 0; i < stats.length(); i++) {
                    JSONObject stat = stats.getJSONObject(i);
                    tableModel.addRow(new Object[]{
                            // Convert id to String to avoid Integer casting issue
                            String.valueOf(stat.getInt("id")),
                            stat.getString("name"),
                            stat.getString("value")
                    });
                }

                // Apply alternating row colors for better readability
                icmpTable.setDefaultRenderer(Object.class, new javax.swing.table.DefaultTableCellRenderer() {
                    @Override
                    public Component getTableCellRendererComponent(JTable table, Object value, boolean isSelected, boolean hasFocus, int row, int column) {
                        Component c = super.getTableCellRendererComponent(table, value, isSelected, hasFocus, row, column);
                        if (!isSelected) {
                            c.setBackground(row % 2 == 0 ? new Color(240, 248, 255) : Color.WHITE);
                        }
                        return c;
                    }
                });

                // Update status
                statusLabel.setText("Found " + stats.length() + " ICMP statistics");
            } else {
                JOptionPane.showMessageDialog(this,
                        "Error: " + response.getString("message"),
                        "ICMP Data Error",
                        JOptionPane.ERROR_MESSAGE);
                statusLabel.setText("Error fetching ICMP data");
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
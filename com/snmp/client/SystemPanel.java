package com.snmp.client;

import org.json.JSONObject;
import javax.swing.*;
import javax.swing.table.DefaultTableModel;
import java.awt.*;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;

public class SystemPanel extends JPanel {
    private JTable systemTable;
    private DefaultTableModel tableModel;
    private JTextField contactField, nameField, locationField;
    private JButton updateContactBtn, updateNameBtn, updateLocationBtn, refreshBtn;
    private HttpConnection connection;

    public SystemPanel() {
        connection = new HttpConnection();
        initializeUI();
    }

    private void initializeUI() {
        setLayout(new BorderLayout(10, 10));

        // Create table for system info
        String[] columnNames = {"Property", "Value", "Description"};
        tableModel = new DefaultTableModel(columnNames, 0);
        systemTable = new JTable(tableModel);
        systemTable.setRowHeight(30);
        systemTable.getTableHeader().setFont(new Font("SansSerif", Font.BOLD, 14));

        JScrollPane scrollPane = new JScrollPane(systemTable);
        add(scrollPane, BorderLayout.CENTER);

        // Create editable fields panel
        JPanel editPanel = new JPanel(new GridLayout(4, 3, 5, 5));
        editPanel.setBorder(BorderFactory.createTitledBorder("Update System Information"));

        editPanel.add(new JLabel("System Contact:"));
        contactField = new JTextField();
        editPanel.add(contactField);
        updateContactBtn = new JButton("Update Contact");
        editPanel.add(updateContactBtn);

        editPanel.add(new JLabel("System Name:"));
        nameField = new JTextField();
        editPanel.add(nameField);
        updateNameBtn = new JButton("Update Name");
        editPanel.add(updateNameBtn);

        editPanel.add(new JLabel("System Location:"));
        locationField = new JTextField();
        editPanel.add(locationField);
        updateLocationBtn = new JButton("Update Location");
        editPanel.add(updateLocationBtn);

        refreshBtn = new JButton("Get System Data");
        editPanel.add(new JLabel(""));
        editPanel.add(refreshBtn);

        add(editPanel, BorderLayout.SOUTH);

        // Add event listeners
        refreshBtn.addActionListener(e -> fetchSystemData());

        updateContactBtn.addActionListener(e -> updateSystemValue("sysContact", contactField.getText()));
        updateNameBtn.addActionListener(e -> updateSystemValue("sysName", nameField.getText()));
        updateLocationBtn.addActionListener(e -> updateSystemValue("sysLocation", locationField.getText()));
    }

    public void fetchSystemData() {
        try {
            JSONObject response = connection.getSystemData();

            if (response.getBoolean("success")) {
                // Clear table
                tableModel.setRowCount(0);

                JSONObject data = response.getJSONObject("data");
                JSONObject values = data.getJSONObject("values");
                JSONObject descriptions = data.getJSONObject("descriptions");

                // Add rows to table
                addSystemRow("System Description", values.getString("sysDescr"),
                        descriptions.getString("sysDescr"));
                addSystemRow("System Object ID", values.getString("sysObjectID"),
                        descriptions.getString("sysObjectID"));
                addSystemRow("System Uptime", values.getString("sysUpTime"),
                        descriptions.getString("sysUpTime"));
                addSystemRow("System Contact", values.getString("sysContact"),
                        descriptions.getString("sysContact"));
                addSystemRow("System Name", values.getString("sysName"),
                        descriptions.getString("sysName"));
                addSystemRow("System Location", values.getString("sysLocation"),
                        descriptions.getString("sysLocation"));

                // Update editable fields
                contactField.setText(values.getString("sysContact"));
                nameField.setText(values.getString("sysName"));
                locationField.setText(values.getString("sysLocation"));
            } else {
                JOptionPane.showMessageDialog(this, "Error: " + response.getString("message"),
                        "SNMP Error", JOptionPane.ERROR_MESSAGE);
            }
        } catch (Exception ex) {
            JOptionPane.showMessageDialog(this, "Error: " + ex.getMessage(),
                    "Connection Error", JOptionPane.ERROR_MESSAGE);
            ex.printStackTrace();
        }
    }

    private void addSystemRow(String property, String value, String description) {
        tableModel.addRow(new Object[]{property, value, description});
    }

    private void updateSystemValue(String property, String value) {
        try {
            JSONObject response = connection.updateSystemValue(property, value);

            if (response.getBoolean("success")) {
                JOptionPane.showMessageDialog(this, "Successfully updated " + property,
                        "Update Successful", JOptionPane.INFORMATION_MESSAGE);
                fetchSystemData(); // Refresh data
            } else {
                JOptionPane.showMessageDialog(this, "Error: " + response.getString("message"),
                        "Update Failed", JOptionPane.ERROR_MESSAGE);
            }
        } catch (Exception ex) {
            JOptionPane.showMessageDialog(this, "Error: " + ex.getMessage(),
                    "Connection Error", JOptionPane.ERROR_MESSAGE);
            ex.printStackTrace();
        }
    }
}
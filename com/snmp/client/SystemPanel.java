package com.snmp.client;

import javax.swing.*;
import java.awt.*;

// System Panel (Tab 1)
public class SystemPanel extends JPanel {
    private HttpConnection httpConnection;
    private JTextArea dataDisplay;
    private JTextField sysContactField, sysNameField, sysLocationField;
    private JButton getDataButton, updateContactButton, updateNameButton, updateLocationButton;

    public SystemPanel(HttpConnection httpConnection) {
        this.httpConnection = httpConnection;
        setLayout(new BorderLayout());

        // Create components
        dataDisplay = new JTextArea();
        dataDisplay.setEditable(false);
        JScrollPane scrollPane = new JScrollPane(dataDisplay);

        JPanel controlPanel = new JPanel(new GridLayout(4, 3));

        getDataButton = new JButton("Get System Data");
        sysContactField = new JTextField(20);
        sysNameField = new JTextField(20);
        sysLocationField = new JTextField(20);
        updateContactButton = new JButton("Update Contact");
        updateNameButton = new JButton("Update Name");
        updateLocationButton = new JButton("Update Location");

        // Add components to panels
        controlPanel.add(new JLabel("System Contact:"));
        controlPanel.add(sysContactField);
        controlPanel.add(updateContactButton);

        controlPanel.add(new JLabel("System Name:"));
        controlPanel.add(sysNameField);
        controlPanel.add(updateNameButton);

        controlPanel.add(new JLabel("System Location:"));
        controlPanel.add(sysLocationField);
        controlPanel.add(updateLocationButton);

        controlPanel.add(getDataButton);

        // Add panels to main panel
        add(scrollPane, BorderLayout.CENTER);
        add(controlPanel, BorderLayout.SOUTH);

        // Add action listeners
        getDataButton.addActionListener(e -> {
            String data = httpConnection.getSystemData();
            dataDisplay.setText(data);

            // Parse data to update fields
            for (String line : data.split("\n")) {
                if (line.contains("System Contact:")) {
                    sysContactField.setText(line.substring(line.indexOf(":") + 1).trim());
                } else if (line.contains("System Name:")) {
                    sysNameField.setText(line.substring(line.indexOf(":") + 1).trim());
                } else if (line.contains("System Location:")) {
                    sysLocationField.setText(line.substring(line.indexOf(":") + 1).trim());
                }
            }
        });

        updateContactButton.addActionListener(e -> {
            String newContact = sysContactField.getText();
            String result = httpConnection.updateSystemValue("sysContact", newContact);
            JOptionPane.showMessageDialog(this, result);
            // Refresh data after update
            getDataButton.doClick();
        });

        updateNameButton.addActionListener(e -> {
            String newName = sysNameField.getText();
            String result = httpConnection.updateSystemValue("sysName", newName);
            JOptionPane.showMessageDialog(this, result);
            // Refresh data after update
            getDataButton.doClick();
        });

        updateLocationButton.addActionListener(e -> {
            String newLocation = sysLocationField.getText();
            String result = httpConnection.updateSystemValue("sysLocation", newLocation);
            JOptionPane.showMessageDialog(this, result);
            // Refresh data after update
            getDataButton.doClick();
        });
    }
}
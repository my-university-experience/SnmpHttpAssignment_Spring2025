package com.snmp.client;

import javax.swing.*;
import java.awt.*;

// ICMP Panel (Tab 3)
public class IcmpPanel extends JPanel {
    private HttpConnection httpConnection;
    private JTextArea dataDisplay;
    private JButton getDataButton;

    public IcmpPanel(HttpConnection httpConnection) {
        this.httpConnection = httpConnection;
        setLayout(new BorderLayout());

        // Create components
        dataDisplay = new JTextArea();
        dataDisplay.setEditable(false);
        JScrollPane scrollPane = new JScrollPane(dataDisplay);

        getDataButton = new JButton("Get ICMP Statistics");

        // Add components to panel
        add(scrollPane, BorderLayout.CENTER);
        add(getDataButton, BorderLayout.SOUTH);

        // Add action listener
        getDataButton.addActionListener(e -> {
            String data = httpConnection.getIcmpStats();
            dataDisplay.setText(data);
        });
    }
}

package com.snmp.client;

import javax.swing.*;
import java.awt.*;

// TCP Panel (Tab 2)
public class TcpPanel extends JPanel {
    private HttpConnection httpConnection;
    private JTextArea dataDisplay;
    private JButton getDataButton;

    public TcpPanel(HttpConnection httpConnection) {
        this.httpConnection = httpConnection;
        setLayout(new BorderLayout());

        // Create components
        dataDisplay = new JTextArea();
        dataDisplay.setEditable(false);
        JScrollPane scrollPane = new JScrollPane(dataDisplay);

        getDataButton = new JButton("Get TCP Table");

        // Add components to panel
        add(scrollPane, BorderLayout.CENTER);
        add(getDataButton, BorderLayout.SOUTH);

        // Add action listener
        getDataButton.addActionListener(e -> {
            String data = httpConnection.getTcpTable();
            dataDisplay.setText(data);
        });
    }
}

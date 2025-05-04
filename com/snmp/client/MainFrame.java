package com.snmp.client;

import javax.swing.*;
import java.awt.*;

public class MainFrame extends JFrame {
    private JTabbedPane tabbedPane;
    private SystemPanel systemPanel;
    private TcpPanel tcpPanel;
    private IcmpPanel icmpPanel;
    private JLabel statusLabel;

    public MainFrame() {
        setTitle("SNMP Manager");
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        setSize(800, 600);
        setLocationRelativeTo(null);

        initializeUI();
    }

    private void initializeUI() {
        // Create tabbed pane
        tabbedPane = new JTabbedPane();

        // Create panels
        systemPanel = new SystemPanel();
        tcpPanel = new TcpPanel();
        icmpPanel = new IcmpPanel();

        // Add panels to tabbed pane
        tabbedPane.addTab("System Group", systemPanel);
        tabbedPane.addTab("TCP Table", tcpPanel);
        tabbedPane.addTab("ICMP Statistics", icmpPanel);

        // Add tabbed pane to frame
        add(tabbedPane, BorderLayout.CENTER);

        // Create status bar
        JPanel statusPanel = new JPanel();
        statusPanel.setBorder(BorderFactory.createEtchedBorder());
        statusLabel = new JLabel("Ready");
        statusPanel.add(statusLabel);
        add(statusPanel, BorderLayout.SOUTH);

        // Add tab change listener to update status
        tabbedPane.addChangeListener(e -> {
            int selectedIndex = tabbedPane.getSelectedIndex();
            if (selectedIndex == 0) {
                statusLabel.setText("System Group selected");
            } else if (selectedIndex == 1) {
                statusLabel.setText("TCP Table selected");
                // Load TCP data when selecting this tab
                tcpPanel.fetchTcpData();
            } else if (selectedIndex == 2) {
                statusLabel.setText("ICMP Statistics selected");
                // Load ICMP data when selecting this tab
                icmpPanel.fetchIcmpData();
            }
        });

        // Initialize with system data on startup
        SwingUtilities.invokeLater(() -> {
            try {
                systemPanel.fetchSystemData();
            } catch (Exception e) {
                statusLabel.setText("Error loading System data");
                e.printStackTrace();
            }
        });
    }
}
package com.snmp.client;

import javax.swing.*;

public class MainFrame extends JFrame {
    private SystemPanel systemPanel;
    private TcpPanel tcpPanel;
    private IcmpPanel icmpPanel;
    private HttpConnection httpConnection;

    public MainFrame() {
        super("SNMP Manager");
        httpConnection = new HttpConnection();

        // Set up the frame
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        setSize(800, 600);

        // Create tabbed pane
        JTabbedPane tabbedPane = new JTabbedPane();

        // Create panels for each tab
        systemPanel = new SystemPanel(httpConnection);
        tcpPanel = new TcpPanel(httpConnection);
        icmpPanel = new IcmpPanel(httpConnection);

        // Add panels to tabbed pane
        tabbedPane.addTab("System Group", systemPanel);
        tabbedPane.addTab("TCP Table", tcpPanel);
        tabbedPane.addTab("ICMP Statistics", icmpPanel);

        // Add tabbed pane to frame
        add(tabbedPane);
    }
}

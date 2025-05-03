package com.snmp.client;

import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.URL;
import java.net.URLEncoder;
import java.nio.charset.StandardCharsets;

public class HttpConnection {
    private static final String BASE_URL = "http://localhost/snmp/";

    public String getSystemData() {
        // Send HTTP request to get system group data
        return sendRequest("get_system.php");
    }

    public String getTcpTable() {
        // Send HTTP request to get TCP table
        return sendRequest("get_tcp.php");
    }

    public String getIcmpStats() {
        // Send HTTP request to get ICMP statistics
        return sendRequest("get_icmp.php");
    }

    public String updateSystemValue(String name, String value) {
        try {
            // URL encode the parameters to handle special characters
            String encodedName = URLEncoder.encode(name, StandardCharsets.UTF_8);
            String encodedValue = URLEncoder.encode(value, StandardCharsets.UTF_8);

            // Send HTTP request to update a system value
            return sendRequest("update_system.php?name=" + encodedName + "&value=" + encodedValue);
        } catch (Exception e) {
            e.printStackTrace();
            return "Error: " + e.getMessage();
        }
    }

    private String sendRequest(String endpoint) {
        HttpURLConnection conn = null;
        try {
            URL url = new URL(BASE_URL + endpoint);
            conn = (HttpURLConnection) url.openConnection();
            conn.setRequestMethod("GET");
            conn.setConnectTimeout(5000); // 5 seconds timeout

            int responseCode = conn.getResponseCode();
            if (responseCode != HttpURLConnection.HTTP_OK) {
                return "Error: Server returned HTTP response code: " + responseCode + " for URL: " + url;
            }

            BufferedReader in = new BufferedReader(new InputStreamReader(conn.getInputStream()));
            String inputLine;
            StringBuilder response = new StringBuilder();

            while ((inputLine = in.readLine()) != null) {
                response.append(inputLine).append("\n");
            }
            in.close();

            return response.toString();
        } catch (Exception e) {
            e.printStackTrace();
            return "Error: " + e.getMessage();
        } finally {
            if (conn != null) {
                conn.disconnect();
            }
        }
    }
}
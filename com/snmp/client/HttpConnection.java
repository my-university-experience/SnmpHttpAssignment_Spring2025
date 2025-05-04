package com.snmp.client;

import org.json.JSONObject;
import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.net.HttpURLConnection;
import java.net.URL;
import java.net.URLEncoder;
import java.nio.charset.StandardCharsets;

public class HttpConnection {
    private static final String BASE_URL = "http://localhost/snmp_manager/api/";

    /**
     * Retrieves system group information from the SNMP manager API
     * @return JSONObject containing system data
     * @throws Exception if connection or parsing fails
     */
    public JSONObject getSystemData() throws Exception {
        String response = sendRequest("get_system.php", "GET", null);

        // Debug: Print the raw response
        System.out.println("Raw system data response: " + response);

        // Check if response is valid JSON before parsing
        if (response != null && !response.trim().isEmpty() && response.trim().startsWith("{")) {
            return new JSONObject(response);
        } else {
            throw new Exception("Invalid JSON response received from server: " + response);
        }
    }

    /**
     * Retrieves TCP connection table from the SNMP manager API
     * @return JSONObject containing TCP connection data
     * @throws Exception if connection or parsing fails
     */
    public JSONObject getTcpTable() throws Exception {
        String response = sendRequest("get_tcp.php", "GET", null);

        // Debug: Print the raw response
        System.out.println("Raw TCP table response: " + response);

        // Check if response is valid JSON before parsing
        if (response != null && !response.trim().isEmpty() && response.trim().startsWith("{")) {
            return new JSONObject(response);
        } else {
            throw new Exception("Invalid JSON response received from server: " + response);
        }
    }

    /**
     * Retrieves ICMP statistics from the SNMP manager API
     * @return JSONObject containing ICMP statistics
     * @throws Exception if connection or parsing fails
     */
    public JSONObject getIcmpStats() throws Exception {
        String response = sendRequest("get_icmp.php", "GET", null);

        // Debug: Print the raw response
        System.out.println("Raw ICMP stats response: " + response);

        // Check if response is valid JSON before parsing
        if (response != null && !response.trim().isEmpty() && response.trim().startsWith("{")) {
            return new JSONObject(response);
        } else {
            throw new Exception("Invalid JSON response received from server: " + response);
        }
    }

    /**
     * Updates a system value through the SNMP manager API
     * @param name The name of the system property (sysContact, sysName, sysLocation)
     * @param value The new value to set
     * @return JSONObject containing update status and result
     * @throws Exception if connection or parsing fails
     */
    public JSONObject updateSystemValue(String name, String value) throws Exception {
        try {
            // URL encode the parameters to handle special characters
            String params = "name=" + URLEncoder.encode(name, StandardCharsets.UTF_8) +
                    "&value=" + URLEncoder.encode(value, StandardCharsets.UTF_8);

            // Send HTTP request to update a system value
            String response = sendRequest("update_system.php", "GET", params);

            // Debug: Print the raw response
            System.out.println("Raw update response: " + response);

            // Check if response is valid JSON before parsing
            if (response != null && !response.trim().isEmpty() && response.trim().startsWith("{")) {
                return new JSONObject(response);
            } else {
                throw new Exception("Invalid JSON response received from server: " + response);
            }
        } catch (Exception e) {
            System.err.println("Error in updateSystemValue: " + e.getMessage());
            e.printStackTrace();
            throw e;
        }
    }

    /**
     * Sends HTTP request to the specified endpoint
     * @param endpoint API endpoint to call
     * @param method HTTP method (GET or POST)
     * @param params URL parameters for GET or POST body
     * @return Raw response as string
     * @throws Exception if connection fails
     */
    private String sendRequest(String endpoint, String method, String params) throws Exception {
        HttpURLConnection conn = null;
        try {
            String fullUrl = BASE_URL + endpoint;
            if (params != null && method.equals("GET")) {
                fullUrl += "?" + params;
            }

            // Debug: Print the URL being requested
            System.out.println("Requesting URL: " + fullUrl);

            URL url = new URL(fullUrl);
            conn = (HttpURLConnection) url.openConnection();
            conn.setRequestMethod(method);
            conn.setConnectTimeout(5000); // 5 seconds timeout

            if (params != null && method.equals("POST")) {
                conn.setDoOutput(true);
                conn.setRequestProperty("Content-Type", "application/x-www-form-urlencoded");

                try (OutputStream os = conn.getOutputStream()) {
                    byte[] input = params.getBytes(StandardCharsets.UTF_8);
                    os.write(input, 0, input.length);
                }
            }

            int responseCode = conn.getResponseCode();
            System.out.println("Response code: " + responseCode);

            if (responseCode != HttpURLConnection.HTTP_OK) {
                // Try to read error stream
                try (BufferedReader errorReader = new BufferedReader(
                        new InputStreamReader(conn.getErrorStream()))) {
                    String errorLine;
                    StringBuilder errorResponse = new StringBuilder();
                    while ((errorLine = errorReader.readLine()) != null) {
                        errorResponse.append(errorLine);
                    }
                    System.err.println("Error response: " + errorResponse);
                    throw new Exception("Server returned HTTP response code: " + responseCode +
                            " for URL: " + url + "\nError: " + errorResponse);
                } catch (Exception e) {
                    throw new Exception("Server returned HTTP response code: " + responseCode +
                            " for URL: " + url);
                }
            }

            try (BufferedReader in = new BufferedReader(
                    new InputStreamReader(conn.getInputStream(), StandardCharsets.UTF_8))) {
                String inputLine;
                StringBuilder response = new StringBuilder();

                while ((inputLine = in.readLine()) != null) {
                    response.append(inputLine);
                }

                return response.toString();
            }
        } catch (Exception e) {
            System.err.println("Error in sendRequest: " + e.getMessage());
            e.printStackTrace();
            throw e;
        } finally {
            if (conn != null) {
                conn.disconnect();
            }
        }
    }
}
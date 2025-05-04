<?php
require_once('../config.php');

function getIcmpStatsByGet($host, $community) {
    global $icmp_oids, $icmp_base_oid;
    $stats = [];
    $error = '';
    try {
        $session = new SNMP(1, $host, $community);
        $session->valueretrieval = SNMP_VALUE_PLAIN;
        foreach ($icmp_oids as $suffix => $name) {
            $oid = $icmp_base_oid . '.' . $suffix . '.0';
            try {
                $value = $session->get($oid);
                if ($value !== false) {
                    $stats[$name] = $value;
                }
            } catch (Exception $e) { continue; }
        }
    } catch (Exception $e) { $error = $e->getMessage(); }
    return ['stats' => $stats, 'error' => $error];
}

function getIcmpStatsByWalk($host, $community) {
    global $icmp_base_oid, $icmp_oids;
    $stats = [];
    $error = '';
    try {
        $session = new SNMP(1, $host, $community);
        $session->valueretrieval = SNMP_VALUE_PLAIN;
        foreach ($icmp_oids as $suffix => $name) {
            $oid = $icmp_base_oid . '.' . $suffix;
            try {
                $walk_results = $session->walk($oid);
                if ($walk_results !== false) {
                    foreach ($walk_results as $walk_oid => $value) {
                        preg_match('/\.1\.3\.6\.1\.2\.1\.5\.(\d+)/', $walk_oid, $matches);
                        if (isset($matches[1])) {
                            $suffix = $matches[1];
                            $name = isset($icmp_oids[$suffix]) ? $icmp_oids[$suffix] : "icmpItem$suffix";
                            $stats[$name] = $value;
                        }
                    }
                }
            } catch (Exception $e) { continue; }
        }
        if (empty($stats)) {
            foreach ($icmp_oids as $suffix => $name) {
                $oid = $icmp_base_oid . '.' . $suffix . '.0';
                try {
                    $value = $session->get($oid);
                    if ($value !== false) {
                        $stats[$name] = $value;
                    }
                } catch (Exception $e) { continue; }
            }
        }
    } catch (Exception $e) { $error = $e->getMessage(); }
    return ['stats' => $stats, 'error' => $error];
}

$get_results = getIcmpStatsByGet($snmp_host, $snmp_community_read);
$walk_results = getIcmpStatsByWalk($snmp_host, $snmp_community_read);

?>
<div class="tables-container" style="display: flex; justify-content: center;">
    <table border="1" cellpadding="5" cellspacing="0" style="background: #b2dfdb; margin-right: 20px;">
        <caption style="font-weight: bold; font-size: 1.1em; padding: 8px;">Method1: By Get</caption>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Value</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 1;
            foreach ($get_results['stats'] as $name => $value):
                $snmp_type = '';
                $snmp_value = $value;
                if (preg_match('/^(\w+):\s*(.*)$/', $value, $matches)) {
                    $snmp_type = $matches[1];
                    $snmp_value = $matches[2];
                } else {
                    $snmp_type = 'Counter32';
                }
            ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo htmlspecialchars($name); ?></td>
                <td><?php echo $snmp_type . ': ' . htmlspecialchars($snmp_value); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <table border="1" cellpadding="5" cellspacing="0" style="background: #b2dfdb;">
        <caption style="font-weight: bold; font-size: 1.1em; padding: 8px;">Method2: By Walk</caption>
        <thead>
            <tr>
                <th>Item #</th>
                <th>Name</th>
                <th>Value</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 1;
            foreach ($walk_results['stats'] as $name => $value):
                $snmp_value = $value;
                if (preg_match('/^(\w+):\s*(.*)$/', $value, $matches)) {
                    $snmp_value = $matches[2];
                }
            ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo htmlspecialchars($name); ?></td>
                <td><?php echo htmlspecialchars($snmp_value); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div> 
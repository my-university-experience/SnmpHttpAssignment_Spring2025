<?php
// Get the current page name
$currentPage = basename($_SERVER['PHP_SELF']);
require_once(__DIR__ . '/../config.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SNMP Manager</title>
    <link rel="stylesheet" href="<?php echo getRelativePath(); ?>snmp_manager/styles.css">
</head>
<body>
    <header class="modern-header">
        <div class="header-container">
            <div class="header-logo">🌐</div>
            <h1>SNMP Manager</h1>
        </div>
    </header>
    
    <?php include_once(__DIR__ . '/navigation.php'); ?>
    
    <div class="container">

<?php
// Helper function to get the correct relative path to the root
function getRelativePath() {
    $depth = substr_count($_SERVER['PHP_SELF'], '/') - 1;
    $path = '';
    
    for ($i = 0; $i < $depth; $i++) {
        $path .= '../';
    }
    
    return $path;
}
?>
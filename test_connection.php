<?php
require_once 'Database.php';

try {
    $db = get_db();
    
    // Test 1: Check connection
    echo "✓ Database connected successfully!<br>";
    
    // Test 2: Show current database
    $result = $db->fetch("SELECT DATABASE() as current_db");
    echo "✓ Current database: " . $result['current_db'] . "<br>";
    
    // Test 3: Check if incidents table exists
    $result = $db->fetch("SHOW TABLES LIKE 'incidents'");
    if ($result) {
        echo "✓ Table 'incidents' exists!<br>";
        
        // Test 4: Describe table structure
        $columns = $db->fetchAll("DESCRIBE incidents");
        echo "✓ Table structure:<br>";
        echo "<pre>";
        print_r($columns);
        echo "</pre>";
        
        // Test 5: Count records
        $count = $db->fetch("SELECT COUNT(*) as total FROM incidents");
        echo "✓ Total incidents: " . $count['total'] . "<br>";
        
        // Test 6: Try to select from incidents
        $incidents = $db->fetchAll("SELECT * FROM incidents LIMIT 5");
        echo "✓ Sample query successful! Found " . count($incidents) . " records<br>";
        
    } else {
        echo "✗ Table 'incidents' NOT found!<br>";
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage();
}
?>
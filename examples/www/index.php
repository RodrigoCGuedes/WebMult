<?php
require_once 'Mult.php';

// Initial configurations
$server = 'http://caddy:80'; // Server base URL
$class = 'worker.php'; // Worker endpoint
$instances = 3; // Number of parallel requests

// Create an instance of the Mult class
$mult = new Mult($class, $server, $instances);

// Add parameters
$mult->addParameter(0, 'api_key', '123456');
$mult->addParameter(0, 'user', 'Mercury');

$mult->addParameter(1, 'api_key', '123456');
$mult->addParameter(1, 'user', 'Venus');

$mult->addParameter(2, 'api_key', '123456');
$mult->addParameter(2, 'user', 'Earth');

echo "<!DOCTYPE html>";
echo "<html lang='en'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>PHP Multi Request Results</title>";
echo "</head>";
echo "<body>";

echo "<h2>Configs:</h2>";

// Display configurations in a structured format (HTML table)
$mult->configs();

echo "<h2>Starting requests...</h2>";

// Execute multithreaded requests
$results = $mult->run(30); // Timeout of 30 seconds

// Display results in a formatted HTML table
echo "<h3>Results:</h3>";
echo "<table border='1'>";
echo "<tr><th>Instance</th><th>Status</th><th>Response</th><th>HTTP Code</th></tr>";

foreach ($results as $key => $result) {
    echo "<tr>";
    echo "<td>Instance $key</td>";
    if (isset($result['error'])) {
        echo "<td>Error</td>";
        echo "<td>{$result['error']}</td>";
    } else {
        echo "<td>Success</td>";
        echo "<td>{$result['response']}</td>";
    }
    echo "<td>{$result['http_code']}</td>";
    echo "</tr>";
}

echo "</table>";
echo "</body>";
echo "</html>";
?>

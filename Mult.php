<?php
/**
 * Mult.php
 * 
 * A class for managing multiple parallel HTTP requests using cURL.
 * 
 * @package    WebMult
 * @author     Rodrigo Guedes
 * @version    1.0
 * @license    MIT License
 * @link       https://github.com/RodrigoCGuedes/WebMult
 * 
 * Description:
 * This class allows executing multiple parallel HTTP requests with customizable parameters
 * for each instance. It leverages cURL's multi handle for efficient concurrent connections.
 * 
 * Features:
 * - Configure the target class, server, number of instances, and parameters.
 * - Display configuration in a table format.
 * - Execute parallel requests with HTTP/2 support.
 * - Process and handle responses and errors for each instance.
 */

class Mult
{
    // Properties to store configuration details
    private $class; // The class to execute on the server
    private $server; // The base URL of the server
    private $instances; // Number of parallel instances to run
    private $parameters; // Parameters for each instance

    // Constructor to initialize the class with default values
    public function __construct($class, $server, $instances, $parameters = [])
    {
        $this->class = $class;
        $this->server = rtrim($server, '/'); // Ensure no trailing slash in the server URL
        $this->instances = $instances; 
        $this->parameters = $parameters; // Parameters for each instance
    }

    // Setters for class properties
    public function setClass($class)
    {
        $this->class = $class;
    }

    public function setServer($server)
    {
        $this->server = rtrim($server, '/');
    }

    public function setInstances($instances)
    {
        $this->instances = $instances;
    }

    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }

    // Adds a new parameter to a specific instance
    public function addParameter(int $instance, string $key, string $value)
    {
        $this->parameters[$instance][$key] = $value;
    }

    // Getters for class properties
    public function getClass()
    {
        return $this->class;
    }

    public function getServer()
    {
        return $this->server;
    }

    public function getInstances()
    {
        return $this->instances;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    // Displays configuration details in a table format
    public function configs()
    {
        echo "<table border='1'>";
        echo "<tr><th>Config</th><th>Value</th></tr>";
        echo "<tr><td>Class</td><td>" . $this->class . "</td></tr>";
        echo "<tr><td>Server</td><td>" . $this->server . "</td></tr>";
        echo "<tr><td>Instances</td><td>" . $this->instances . "</td></tr>";

        // Iterates over each instance and displays its parameters
        foreach ($this->parameters as $instance => $params) {
            echo "<tr><td>Parameters[$instance]</td><td>" . http_build_query($params) . "</td></tr>";
        }

        echo "</table>";
    }

    // Prepares and adds a cURL handle for a specific instance to the multi handle
    private function handle($multiHandle, $timeout, $instance)
    {
        $url = $this->server . "/" . $this->class;

        // Appends query parameters to the URL if available
        if (!empty($this->parameters[$instance])) {
            $url .= "?" . http_build_query($this->parameters[$instance]);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url); // Sets the URL to fetch
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout); // Set the timeout for the request

        // Use HTTP/2 for faster connections
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);

        // Add the handle to the multi handle for parallel execution
        curl_multi_add_handle($multiHandle, $ch);

        return $ch;
    }

    // Executes the multi cURL requests
    private function mult_exec($multiHandle)
    {
        $running = null;

        // Loop until all requests are complete
        do {
            curl_multi_exec($multiHandle, $running);
            curl_multi_select($multiHandle); // Wait for activity on any connection
        } while ($running);
    }

    // Processes the results of all completed requests
    private function results($multiHandle, $curlHandles)
    {
        $results = [];
        foreach ($curlHandles as $ch) {
            // Check for errors or successful responses
            if (curl_errno($ch)) {
                $results[] = [
                    "error" => curl_error($ch), // Store the error message
                    "http_code" => curl_getinfo($ch, CURLINFO_HTTP_CODE), // HTTP status code
                ];
            } else {
                $results[] = [
                    "response" => curl_multi_getcontent($ch), // Get the response body
                    "http_code" => curl_getinfo($ch, CURLINFO_HTTP_CODE), // HTTP status code
                ];
            }

            // Remove the handle from the multi handle and close it
            curl_multi_remove_handle($multiHandle, $ch);
            curl_close($ch);
        }
        return $results;
    }

    // Runs all instances in parallel and returns the results
    public function run($timeout = 30)
    {
        $multiHandle = curl_multi_init(); // Initialize a multi cURL handle
        $curlHandles = [];

        // Prepare and add handles for each instance
        for ($i = 0; $i < $this->instances; $i++) {
            $curlHandles[] = $this->handle($multiHandle, $timeout, $i);
        }

        $this->mult_exec($multiHandle); // Execute all requests

        $results = $this->results($multiHandle, $curlHandles); // Process the results

        curl_multi_close($multiHandle); // Close the multi handle

        return $results;
    }
}

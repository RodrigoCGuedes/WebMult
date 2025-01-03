# <h1 align="center">WebMult</h1>
<p align="center">
  <i>A powerful PHP class for running tasks in parallel using HTTPS requests, similar to multithreading.</i>
</p>
<p align="center">
  <img src="https://img.shields.io/badge/PHP-%3E=7.4-blue" alt="PHP Version">
  <img src="https://img.shields.io/badge/HTTPS/2-Compatible-brightgreen" alt="HTTPS/2 Compatible">
  <img src="https://img.shields.io/github/license/RodrigoCGuedes/WebMult" alt="License">
</p>

---

## <h2>Introduction</h2>
WebMult is a PHP class that enables parallel execution of tasks by leveraging multiple HTTPS requests, analogous to multithreading. Each "thread" corresponds to a separate HTTPS request, allowing for efficient parallel execution of tasks.

---

## <h2>🚀 How It Works</h2>
WebMult uses the `cURL` library to send multiple requests concurrently, simulating multithreaded execution.  
It is especially useful for scenarios where:
- Tasks can be distributed across multiple HTTPS requests.
- Parallel processing is required in PHP environments.

---

## <h2>⚠️ Compatibility</h2>
PHP's built-in development server **does not support HTTPS/2 natively**, which is essential for optimal performance. You must use a web server that supports HTTPS/2, such as:
- <strong>Caddy</strong> (Recommended)
- Apache
- Nginx

---

## <h2>🛠️ Configuration</h2>

### **Requirements**
<ul>
  <li><strong>PHP 7.4+</strong> (recommended: PHP 8.0 or higher).</li>
  <li><strong>cURL</strong> extension enabled.</li>
  <li>A web server with HTTPS/2 support.</li>
</ul>

### **Steps to Set Up**
<ol>
  <li><strong>Install and Configure Your Server</strong></li>
  <p>If using <strong>Caddy</strong>, a sample <code>Caddyfile</code> could look like this:</p>
  <pre>
:80 {
    root * /app/public
    php_fastcgi php:9000
    file_server
}

:443 {
    root * /app/public
    php_fastcgi php:9000
    file_server
    tls internal
}
  </pre>

  <li><strong>Clone the Repository</strong></li>
  <p>Clone the repository to your project directory:</p>
  <pre><code>git clone https://github.com/RodrigoCGuedes/WebMult.git</code></pre>

  <li><strong>Include the Class</strong></li>
  <p>Add the class to your PHP project:</p>
  <pre><code>require 'path/to/Mult.php';</code></pre>

  <li><strong>Initialize WebMult</strong></li>
  <p>Set up and run WebMult:</p>
  <pre><code>
$mult = new Mult(
    "worker.php",
    "https://caddy:443",
    3,
    [
        0 => ['api_key' => '123456', 'user' => 'Mercury'],
        1 => ['api_key' => '123456', 'user' => 'Venus'],
        2 => ['api_key' => '123456', 'user' => 'Earth']
    ]
);

$results = $mult->run();
print_r($results);
  </code></pre>
</ol>

---

## <h2>Example Output</h2>

### <h3>Configurations:</h3>
<table>
  <thead>
    <tr>
      <th>Config</th>
      <th>Value</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>Class</td>
      <td>worker.php</td>
    </tr>
    <tr>
      <td>Server</td>
      <td>https://caddy:443</td>
    </tr>
    <tr>
      <td>Instances</td>
      <td>3</td>
    </tr>
    <tr>
      <td>Parameters[0]</td>
      <td>api_key=123456&user=Mercury</td>
    </tr>
    <tr>
      <td>Parameters[1]</td>
      <td>api_key=123456&user=Venus</td>
    </tr>
    <tr>
      <td>Parameters[2]</td>
      <td>api_key=123456&user=Earth</td>
    </tr>
  </tbody>
</table>

### <h3>Results:</h3>
<table>
  <thead>
    <tr>
      <th>Instance</th>
      <th>Status</th>
      <th>Response</th>
      <th>HTTP Code</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>Instance 0</td>
      <td>Success</td>
      <td>{"status":"success","message":"Hello, Mercury"}</td>
      <td>200</td>
    </tr>
    <tr>
      <td>Instance 1</td>
      <td>Success</td>
      <td>{"status":"success","message":"Hello, Venus"}</td>
      <td>200</td>
    </tr>
    <tr>
      <td>Instance 2</td>
      <td>Success</td>
      <td>{"status":"success","message":"Hello, Earth"}</td>
      <td>200</td>
    </tr>
  </tbody>
</table>

---

## <h2>License</h2>
This project is licensed under the [MIT License](LICENSE).

---


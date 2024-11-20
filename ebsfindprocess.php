<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Process Manager</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        form {
            margin-bottom: 20px;
        }
        input[type="text"], input[type="submit"] {
            padding: 10px;
            margin-right: 10px;
            font-size: 16px;
        }
    </style>
</head>
<body>

<h1>Process Manager</h1>

<!-- Form -->
<form method="POST">
    <label for="processname">Enter Process Name:</label>
    <input type="text" name="processname" id="processname" placeholder="e.g., chrome.exe" required>
    <input type="submit" value="Search & Start Process">
</form>

<?php

// Process list initialization
$processList = [];

// Get process list
exec("tasklist /FO CSV /NH", $output);

foreach ($output as $line) {
    $processList[] = str_getcsv($line); // Parse CSV lines into arrays
}

// Display process list in HTML table
if (!empty($processList)) {
    echo "<h2>Running Processes</h2>";
    echo "<table>";
    echo "<thead><tr><th>Process Name</th><th>PID</th><th>Session Name</th><th>Memory Usage</th></tr></thead>";
    echo "<tbody>";

    foreach ($processList as $process) {
        echo "<tr>";
        foreach ($process as $value) {
            echo "<td>" . htmlspecialchars($value) . "</td>";
        }
        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";
}

// Process search and start logic
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $searchProcess = $_POST['processname'];

    if (empty($searchProcess)) {
        echo "Invalid process name.";
        exit;
    }

    foreach ($processList as $processInfo) {
        $processName = $processInfo[0];
        $pid = $processInfo[1];

        if (stripos($processName, $searchProcess) !== false) {
            // Get executable path using WMIC
            $wmiOutput = [];
            exec("wmic process where \"processid=$pid\" get executablepath", $wmiOutput);

            if (!empty($wmiOutput[1])) {
                $executablePath = trim($wmiOutput[1]);

                // Start the process
                exec("start \"\" \"$executablePath\"");

                echo "<p>Process Path: " . htmlspecialchars($executablePath) . "</p>";
                echo "<p>Process started successfully.</p>";
            } else {
                echo "<p>Process path not found.</p>";
            }
            break;
        }
    }
}
?>

</body>
</html>
s

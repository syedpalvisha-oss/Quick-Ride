<?php

$filesToShare = [
    'routes/web.php',
    'routes/console.php',
    'app/Models/Ride.php',
    'app/Http/Controllers/RideController.php',
    'resources/views/ride.blade.php',
    'resources/views/history.blade.php',
    'resources/views/home.blade.php',
];

// Find migration files related to rides
$migrationsDir = 'database/migrations';
$migrations = array_diff(scandir($migrationsDir), ['.', '..']);
foreach ($migrations as $migration) {
    if (strpos($migration, 'rides_table') !== false) {
        $filesToShare[] = $migrationsDir.'/'.$migration;
    }
}

$outputContent = "# QuickRide Project Source Code\n\n";

foreach ($filesToShare as $filePath) {
    if (file_exists($filePath)) {
        $content = file_get_contents($filePath);
        $outputContent .= "### `{$filePath}`\n";

        // determine language
        $lang = 'php';
        if (strpos($filePath, '.blade.php') !== false) {
            $lang = 'html';
        }

        $outputContent .= "```{$lang}\n";
        $outputContent .= $content;
        $outputContent .= "\n```\n\n";
    }
}

file_put_contents('QuickRide_Sharing.md', $outputContent);
echo 'File created successfully!';

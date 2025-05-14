<?php

// Define the old and new namespaces
$oldNamespace = 'Liamtseva\\Cinema';
$newNamespace = 'AnimeSite';

// Function to recursively find all PHP files
function findPhpFiles($dir) {
    $result = [];
    $files = scandir($dir);
    
    foreach ($files as $file) {
        if ($file === '.' || $file === '..' || $file === 'vendor' || $file === 'node_modules') {
            continue;
        }
        
        $path = $dir . '/' . $file;
        
        if (is_dir($path)) {
            $result = array_merge($result, findPhpFiles($path));
        } elseif (pathinfo($path, PATHINFO_EXTENSION) === 'php') {
            $result[] = $path;
        }
    }
    
    return $result;
}

// Function to update namespace in a file
function updateNamespace($file, $oldNamespace, $newNamespace) {
    $content = file_get_contents($file);
    
    // Replace namespace declarations
    $content = str_replace(
        "namespace {$oldNamespace}",
        "namespace {$newNamespace}",
        $content
    );
    
    // Replace use statements
    $content = str_replace(
        "use {$oldNamespace}\\",
        "use {$newNamespace}\\",
        $content
    );
    
    // Replace fully qualified class names in strings (like in AdminPanelProvider)
    $content = str_replace(
        "'{$oldNamespace}\\",
        "'{$newNamespace}\\",
        $content
    );
    
    $content = str_replace(
        "\"{$oldNamespace}\\",
        "\"{$newNamespace}\\",
        $content
    );
    
    file_put_contents($file, $content);
    
    return true;
}

// Find all PHP files
$files = findPhpFiles('.');
$count = 0;

// Update namespace in each file
foreach ($files as $file) {
    if (updateNamespace($file, $oldNamespace, $newNamespace)) {
        echo "Updated: {$file}\n";
        $count++;
    }
}

echo "Total files updated: {$count}\n";

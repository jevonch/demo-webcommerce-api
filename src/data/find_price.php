<?php

// Function to get list of files in the current directory
function getFileList($dir) {
    $files = scandir($dir);
    $fileList = array();

    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            $fileList[] = $file;
        }
    }

    return $fileList;
}

// Function to check if a file contains a certain word
function fileContainsWord($filename, $word) {
    $content = file_get_contents($filename);
    if ($content === FALSE) {
        return FALSE; // Error reading file
    }

    return strpos($content, $word) !== FALSE;
}

// Directory where your files are located
$directory = "./";

// Get list of files in the directory
$fileList = getFileList($directory);

// Word to search for in the files
$wordToSearch = "price";

// Array to store files containing the word
$filesWithWord = array();

// Iterate through each file
foreach ($fileList as $file) {
    // Check if file contains the word
    if (fileContainsWord($directory . $file, $wordToSearch)) {
        // If true, add the file to the array
        $filesWithWord[] = $file;
    }
}

// Function to recursively process JSON data
function processJsonRecursively(&$data) {
    if (is_array($data)) {
        foreach ($data as $key => &$value) {
            if ($key === 'price' || $key === 'sale_price') {
                $value *= 1000;
            } else {
                processJsonRecursively($value);
            }
        }
    } elseif (is_object($data)) {
        foreach ($data as $key => &$value) {
            if ($key === 'price' || $key === 'sale_price') {
                $value *= 1000;
            } else {
                processJsonRecursively($value);
            }
        }
    }
}

// Output the list of files containing the word
if (!empty($filesWithWord)) {
    echo "Files containing the word '$wordToSearch':\n";
    foreach ($filesWithWord as $file) {
        // Path to your JSON file
        $jsonFilePath = $file;

        // Read JSON file contents
        $jsonData = file_get_contents($jsonFilePath);

        if ($jsonData === FALSE) {
            die("Failed to read JSON file.");
        }

        // Decode JSON data
        $jsonDecoded = json_decode($jsonData, TRUE);

        if ($jsonDecoded === NULL) {
            die("Failed to decode JSON.");
        }

        // Process JSON data recursively
        processJsonRecursively($jsonDecoded);
        // Encode modified JSON data back to string
        $jsonEncoded = json_encode($jsonDecoded, JSON_PRETTY_PRINT);

        if ($jsonEncoded === FALSE) {
            die("Failed to encode JSON.");
        }

        // Write modified JSON back to file
        $result = file_put_contents($jsonFilePath, $jsonEncoded);

        if ($result === FALSE) {
            die("Failed to write modified JSON back to file.");
        }

        echo "<br> JSON file $file successfully modified and saved.";
    }
} else {
    echo "No files contain the word '$wordToSearch'.\n";
}

?>

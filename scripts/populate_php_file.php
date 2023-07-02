<?php
function populatePage($dataFilePath, &...$args){
    if (file_exists($dataFilePath)) {
        $jsonData = file_get_contents($dataFilePath);
        if ($jsonData !== false) {
            $data = json_decode($jsonData, true);
            if ($data !== null) {

                foreach ($args as &$arg) {
                    $arg = $data[$arg];
                }

            } else {
                echo "Error: Unable to decode the JSON file.";
            }
        } else {
            echo "Error: Unable to read the JSON file.";
        }
    } else {
        echo "Error: JSON file not found.";
    }
}
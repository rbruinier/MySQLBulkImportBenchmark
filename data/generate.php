<?php

/*
 * Generates a CSV with random person information.
 *
 * $numberOfPeople = number of records to generate
 * $filename = filename of output file. In case file already exists it will be overwritten
 */

$numberOfPeople = 2000000;
$filename = 'people.csv';

function randomString($minLength = 3, $maxLength = 20) {
    $length = rand($minLength, $maxLength);

    $result = chr(rand(65, 90));

    for ($i = 1; $i < $length; $i++) {
        $result .= chr(rand(97, 122));
    }

    return $result;
}

$engine = getenv('ENGINE');

echo "$engine: Generating $numberOfPeople person records; might take a bit\n";

$file = fopen($filename, 'w');

for ($id = 1; $id <= $numberOfPeople; $id += 1) {
    $data = [
        'id' => $id,
        'firstname' => '"' . randomString() . '"',
        'lastname' => '"' . randomString() . '"',
        'birthday' => '"' . date("Y-m-d", rand(-1577923200, 1514764800)) . '"',
        'street' => '"' . randomString() . '"',
        'city' => '"' . randomString() . '"',
        'zipcode' => '"' . randomString(5, 5) . '"'
    ];

    $row = implode(',', $data);

    fwrite($file, $row . "\n");
}

fclose($file);

echo "$engine: Finished generating person records\n";

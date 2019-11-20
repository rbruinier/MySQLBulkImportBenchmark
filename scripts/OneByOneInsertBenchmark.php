<?php

require_once("BenchmarkRunner.php");

class OneByOneInsertBenchmark implements Benchmark {
    private $file;

    function identifier(): string {
        return "one by one insert";
    }

    function prepare($db, string $filename) {
        $this->file = fopen($filename, 'r');
    }

    function cleanup() {
        fclose($this->file);
    }

    function resultMultiplier(): float {
        return 50;
    }

    function run($db, int $totalNumberOfRecords) {
        $counter = 0;
        $maxRecords = $totalNumberOfRecords / $this->resultMultiplier();

        while (!feof($this->file)) {
            $row = str_replace(array("\n"), '', fgets($this->file));
        
            $insertQuery = "
                INSERT INTO
                    person (
                        id,
                        firstname,
                        lastname,
                        birthday,
                        street,
                        city,
                        zipcode
                    )
                    VALUES (
                        $row
                    )
                ";
        
            $db->query($insertQuery);
        
            $counter++;
        
            if ($counter >= $maxRecords) {
                return;
            }
        }
    }
}

<?php

require_once("BenchmarkRunner.php");

class BatchInsertBenchmark implements Benchmark {
    private $file;

    function identifier(): string {
        return "batch insert";
    }

    function prepare($db, string $filename) {
        $this->file = fopen($filename, 'r');
    }

    function cleanup() {
        fclose($this->file);
    }

    function resultMultiplier(): float {
        return 1;
    }

    function run($db, int $totalNumberOfRecords) {
        $counter = 0;
        $rows = [];
        $maxRecords = $totalNumberOfRecords / $this->resultMultiplier();
        $batchSize = 500;

        while (!feof($this->file)) {
            $rows[] = "(" . str_replace(array("\n"), '', fgets($this->file)) . ")";
        
            $counter++;
        
            if (($counter % $batchSize) == 0) {
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
                    VALUES 
                    ";

                $insertQuery .= implode(",", $rows);
        
                $db->query($insertQuery);
        
                $rows = [];
            }
        
            if ($counter >= $maxRecords) {
                return;
            }
        }
    }
}

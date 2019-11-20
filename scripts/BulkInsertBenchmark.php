<?php

require_once("BenchmarkRunner.php");

class BulkInsertBenchmark implements Benchmark {
    private $filename;

    function identifier(): string {
        return "bulk insert";
    }

    function prepare($db, string $filename) {
        $this->filename = $filename;
    }

    function cleanup() {
    }

    function resultMultiplier(): float {
        return 1;
    }

    function run(Database $db, int $totalNumberOfRecords) {
        $query = '
            LOAD DATA LOCAL INFILE \'' . $this->filename . '\'
            INTO TABLE person
            FIELDS TERMINATED BY \',\' ENCLOSED BY \'"\' LINES TERMINATED BY \'\n\'
        ';
        
        $db->query($query);
    }
}


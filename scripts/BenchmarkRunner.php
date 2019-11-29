<?php

require_once("Database.php");

interface Benchmark {
    function identifier(): string;
    function prepare(Database $db, string $filename);
    function run(Database $db, int $totalNumberOfRecords);
    function cleanup();
    function resultMultiplier(): float;
}

class BenchmarkRunner {
    private $db;
    private $filename;

    public function __construct(Database $db, string $filename) {
        $this->db = $db;
        $this->filename = $filename;
    }

    public function run(array $benchmarks, string $filename) {
        $engine = getenv('ENGINE');

        $totalNumberOfRecords = $this->getNumberOfLinesInFile($filename);

        /** @var $benchmark Benchmark */
        foreach ($benchmarks as $benchmark) {
            $identifier = $benchmark->identifier();

            echo "$engine: Running benchmarks for '$identifier'\n";

            $durations = $this->runPostIndicesBenchmark($benchmark, $totalNumberOfRecords);

            $multiplier = $benchmark->resultMultiplier();

            $totalDuration = round($durations['total'] * $multiplier, 2);
            $insertDuration = round($durations['insert'] * $multiplier, 2);
            $indicesDuration = round($durations['indices'] * $multiplier, 2);

            if ($multiplier == 1) {
                echo "$engine: $identifier with indices added after import took $totalDuration seconds (import: $insertDuration; indices: $indicesDuration)\n";
            } else {
                echo "$engine: $identifier with indices added after import took $totalDuration seconds (import: $insertDuration; indices: $indicesDuration) (extrapolated intervals)\n";
            }

            $duration = round($this->runPreIndicesBenchmark($benchmark, $totalNumberOfRecords) * $multiplier, 2);

            if ($multiplier == 1) {
                echo "$engine: $identifier with indices already in place took $duration seconds\n";
            } else {
                echo "$engine: $identifier with indices already in place took $duration seconds (extrapolated intervals)\n";
            }
        }
    }

    private function runPostIndicesBenchmark(Benchmark $benchmark, $totalNumberOfRecords): array {
        $this->dropPersonTable();
        $this->createPersonTable();

        $benchmark->prepare($this->db, $this->filename);

        $startTime = microtime(true);

        $benchmark->run($this->db, $totalNumberOfRecords);

        $insertDuration = microtime(true) - $startTime;

        $startTime = microtime(true);

        $this->addPersonIndices();

        $indicesDuration = microtime(true) - $startTime;

        $benchmark->cleanup();

        return [
            'insert' => $insertDuration,
            'indices' => $indicesDuration,
            'total' => $insertDuration + $indicesDuration
        ];
    }

    private function runPreIndicesBenchmark(Benchmark $benchmark, $totalNumberOfRecords): float {
        $this->dropPersonTable();
        $this->createPersonTable();
        $this->addPersonIndices();

        $benchmark->prepare($this->db, $this->filename);

        $startTime = microtime(true);

        $benchmark->run($this->db, $totalNumberOfRecords);

        $duration = microtime(true) - $startTime;

        $benchmark->cleanup();

        return $duration;
    }

    private function getNumberOfLinesInFile(string $filename) {
        $file = fopen($filename, "r");

        $lineCount = 0;
        while (!feof($file)) {
            $line = fgets($file);

            $lineCount++;
        }

        fclose($file);

        return $lineCount;
    }

    private function dropPersonTable() {
        $this->db->query('DROP TABLE IF EXISTS person;');
    }

    private function createPersonTable() {
        $query = "
            CREATE TABLE person (
                id INT UNSIGNED NOT NULL,
                firstname VARCHAR(30) NOT NULL,
                lastname VARCHAR(30) NOT NULL,
                birthday DATETIME NOT NULL,
                street VARCHAR(30) NOT NULL,
                city VARCHAR(30) NOT NULL,
                zipcode CHAR(5) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ";
        
        $this->db->query($query);
    }

    private function addPersonIndices() {
        $query = "
            ALTER TABLE person 
                ADD INDEX `lastname` (`lastname`), 
                ADD INDEX `birthday` (`birthday`),
                ADD INDEX `city` (`city`);
            ";
        
        $this->db->query($query);
    }
}

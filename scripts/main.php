<?php

require_once("Database.php");
require_once("BulkInsertBenchmark.php");
require_once("OneByOneInsertBenchmark.php");
require_once("BatchInsertBenchmark.php");
require_once("BenchmarkRunner.php");

$benchmarks = [
    new BulkInsertBenchmark(),
    new BatchInsertBenchmark(),
    new OneByOneInsertBenchmark()
];

$database = new Database();

$database->init();

$filename = "./data/people.csv";

$benchmarkRunner = new BenchmarkRunner($database, $filename);

$benchmarkRunner->run($benchmarks, $filename);

# MySQL Bulk Import Benchmark

Some scripts to benchmark the performance of several ways of importing big data sets.

## How to run

### Requirements

You will need to have docker installed.

Note: I have only tested this on osx (catalina) but everything is run inside
docker containers so it should work on most platforms that support docker.

### Run

Use the terminal and go to the root folder of this repo and run

'''./run.sh```

This will launch a mysql container, wait for it to be ready and run several benchmarks. 
Results are printed to the console. Once finished it will stop all containers.

Jeeb personal accounting software
===========
Jeeb is a Persian personal accounting software which was launched as a SAAS startup in 2012 in Iran and retired at May 2025. The project was outdated and could not be maintained anymore. The source code is published as open source with a docker to make it possible for old users and fans to run it.

### Warning
This code is outdated and not secure, do not use it if you concern about your data and safety.

### How to build
```
docker-compose build
docker-compose up -d
```

### How to run
```
docker-compose up -d
```

### How to stop
```
docker-compose down
```

### How to import the data
if you have a sql export, you should replace the export.sql file with your export file.

> To stop all containers AND kill all volumes `docker-compose down --volumes`

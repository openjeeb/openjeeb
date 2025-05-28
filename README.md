Jeeb personal accounting software
===========
Jeeb is a Persian personal accounting software which was launched as a SAAS startup in 2012 in Tehran and retired at May 2025. The project was outdated and could not be maintained anymore. The source code is published as open source with a docker to make it possible for old users and fans to run it.

### Warning
This code is outdated and not secure, do not use it if you concern about your data and safety.

### How to build
```
docker build
```

### How to run
```
docker compose up -d
```
After running the docker you must go to localhost to see the openjeeb web interface:
```
http://localhost
```
if you like you can set a host name for it, you should modify the hosts file of your operating system and add this line to it:
```
127.0.0.1 jeeb.local
```
after that you can access it like this:
```
http://jeeb.local
```

### How to stop
```
docker down
```
> To stop all containers AND kill all volumes `docker-compose down --volumes`

### How to import the data
if you have a sql export, you should replace the export.sql file with your export file.

## License
[GNU General Public License v3.0](./LICENSE)

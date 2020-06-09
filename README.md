# supervisor adminpanel

### add this to your routes
```
srebb_supervisor:
    resource: "@SrebbSupervisorBundle"
    type:     annotation
    prefix:   "supervisor"
```

### the config looks like this
```
srebb_supervisor:
    connection_timeout: 10
    server_list:
        localServer:
            host: 127.0.0.1
        otherServer:
            host: 192.168.1.20

```

##
##

### Featurerequests 
- Make the curl timeout configurable
- Short curl timeout on first request, longer timeout on further requests.
This makes sure, the page loads fast on init, even if some servers are down.

- show process uptime
- show logs
- show passed time since last refresh (per server)
- stop / start / restart groups
- show pending actions. Usefull if the refreshrate is low
- bind to path via env?
- Allow symfony 3 and 5
- Use wildcard subdomains if requested by config. This speeds up quite much.

```
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
``` 
- handle request error
- add tests
- check servicelocator

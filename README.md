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

<?php

namespace Srebb\Bundle\SupervisorBundle\Server;

class ServerFactory
{
    public function getServer(string $serverName, array $serverData, array $globalUpdateInterval)
    {
        return new Server($serverName, $serverData, $globalUpdateInterval);
    }
}

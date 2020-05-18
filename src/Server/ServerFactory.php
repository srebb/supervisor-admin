<?php

namespace Srebb\Bundle\SupervisorBundle\Server;

class ServerFactory
{
    public function getServer(string $serverName, array $serverData)
    {
        return new Server($serverName, $serverData);
    }
}

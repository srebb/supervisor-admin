<?php

namespace Srebb\Bundle\SupervisorBundle\Server;

use Srebb\Bundle\SupervisorBundle\Exception\ServerNotFoundException;

class ServerContainer
{
    private $serverStack = [];

    public function __construct(ServerFactory $serverFactory, array $serverList = [])
    {
        foreach ($serverList as $serverName => $serverData) {
            $server = $serverFactory->getServer($serverName, $serverData);

            $this->serverStack[$server->getNameHash()] = $server;
        }
    }

    public function getServerStack()
    {
        return $this->serverStack;
    }

    public function getServerStackAsArray()
    {
        $result = [];

        foreach ($this->serverStack as $server) {
            $result[$server->getNameHash()] = $server->getAsArray();
        }

        return $result;
    }

    /**
     * @param string $nameHash
     *
     * @return Server
     * @throws ServerNotFoundException
     */
    public function getServerByNameHash(string $nameHash): Server
    {
        if (isset($this->serverStack[$nameHash])) {
            return $this->serverStack[$nameHash];
        }

        throw new ServerNotFoundException(sprintf('No server with hash "%s" found', $nameHash));
    }
}

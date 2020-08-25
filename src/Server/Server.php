<?php

namespace Srebb\Bundle\SupervisorBundle\Server;

use fXmlRpc\Transport\HttpAdapterTransport;
use Http\Message\MessageFactory\DiactorosMessageFactory;
use Http\Adapter\Guzzle6\Client as Guzzle6Client;
use Supervisor\Connector\XmlRpc;
use Supervisor\Supervisor;
use fXmlRpc\Client;
use GuzzleHttp\Client as GuzzleClient;

class Server
{
    /**
     * @var string
     */
    private $serverName;
    
    /**
     * @var string
     */
    private $nameHash;
    
    /**
     * @var string
     */
    private $host;

    /**
     * @var Supervisor
     */
    private $supervisor;

    public function __construct(string $serverName, array $serverData)
    {
        $this->serverName = $serverName;
        $this->nameHash   = md5($serverName);
        $this->host       = $serverData['host'];

        $this->supervisor = $this->createSupervisor();
    }

    private function createSupervisor()
    {
        $guzzleClient = new GuzzleClient(['timeout' => 10]);

        $client = new Client(
            sprintf('http://%s:9001/RPC2', $this->host),
            new HttpAdapterTransport(
                new DiactorosMessageFactory(),
                new Guzzle6Client($guzzleClient)
            )
        );

        $connector = new XmlRpc($client);

        return new Supervisor($connector);
    }

    public function getAsArray()
    {
        return [
            'serverName' => $this->serverName,
            'nameHash'   => $this->nameHash,
            'host'       => $this->host,
            'supervisor' => $this->supervisor,
        ];
    }

    /**
     * @return string
     */
    public function getServerName(): string
    {
        return $this->serverName;
    }

    /**
     * @return string
     */
    public function getNameHash(): string
    {
        return $this->nameHash;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    public function getSupervisorVersion(): string
    {
        return $this->supervisor->getSupervisorVersion();
    }

    public function getAllProcessInfo()
    {
        $allProcessInfo = $this->supervisor->getAllProcessInfo();

        foreach ($allProcessInfo as $key => $processInfo) {
            $allProcessInfo[$key]['uptime_seconds'] = $processInfo['now'] - $processInfo['start'];

            $description                           = $processInfo['description'] ?? [];
            $descriptionParts                      = explode(',', $description);
            $allProcessInfo[$key]['description_0'] = $descriptionParts[0] ?? '';
            $allProcessInfo[$key]['description_1'] = $descriptionParts[1] ?? '';
            $allProcessInfo[$key]['description_2'] = $descriptionParts[2] ?? '';
            $allProcessInfo[$key]['out_log']       = $this->supervisor
                ->tailProcessStdoutLog($processInfo['group'] . ':' . $processInfo['name'], 0, 0);
            $allProcessInfo[$key]['err_log']       = $this->supervisor
                ->tailProcessStderrLog($processInfo['group'] . ':' . $processInfo['name'], 0, 0);
        }

        $sortedProcessInfo = $this->sortProcesses($allProcessInfo);

        return $sortedProcessInfo;
    }

    public function stopAllProcesses()
    {
        try {
            $result = $this->supervisor->stopAllProcesses();
        } catch (\Exception $e) {
            return false;
        }
        return $result;
    }

    public function startAllProcesses()
    {
        return $this->supervisor->startAllProcesses();
    }

    public function stopProcess($processName)
    {
        try {
            $result = $this->supervisor->stopProcess($processName);
        } catch (\Exception $e) {
            return false;
        }
        return $result;

        return $this->supervisor->stopProcess($processName);
    }

    public function startProcess($processName)
    {
        return $this->supervisor->startProcess($processName);
    }

    public function getConsumerLog(string $name, int $offset = -2000 , int $length = 2000)
    {
        return $this->supervisor->tailProcessStdoutLog($name, $offset, $length);
    }

    public function getConsumerErrorLog(string $name, int $offset = -2000 , int $length = 2000)
    {
        return $this->supervisor->tailProcessStderrLog($name, $offset, $length);
    }

    private function sortProcesses($allProcessInfo)
    {
        $names  = array_column($allProcessInfo, 'name');
        $groups = array_column($allProcessInfo, 'group');
        array_multisort($groups, SORT_NATURAL, $names, SORT_NATURAL, $allProcessInfo);

        return $allProcessInfo;
    }
}

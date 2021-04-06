<?php


namespace App\Services;


use App\Entity\Main\Tenant;
use Doctrine\Persistence\ManagerRegistry;

class SwitchTenantManager
{
    private $event;

    public function __construct(ManagerRegistry $event)
    {
        $this->event = $event;
        $this->defaultManager = $this->event->getManager();
    }

    public function reconnect() {
        // Testing with first instance
        $tenantData = $this->defaultManager->getRepository(Tenant::class)->find(1);
        $connection = $this->event->getConnection('tenant');
        if (!is_null($tenantData)) {
            $connection->changeParams($tenantData->getDbName(), $tenantData->getDbUser(), $tenantData->getDbPassword());
            $connection->reconnect();
        }
    }
}
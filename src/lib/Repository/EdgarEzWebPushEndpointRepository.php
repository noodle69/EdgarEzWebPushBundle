<?php

namespace Edgar\EzWebPush\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\ORMException;
use Edgar\EzWebPushBundle\Entity\EdgarEzWebPushEndpoint;

class EdgarEzWebPushEndpointRepository extends EntityRepository
{
    public function save(int $userId, string $endpoint, string $authToken, string $publicKey): bool
    {
        try {
            $webPushEndpoint = new EdgarEzWebPushEndpoint();
            $webPushEndpoint->setUserId($userId);
            $webPushEndpoint->setEndpoint($endpoint);
            $webPushEndpoint->setAuthToken($authToken);
            $webPushEndpoint->setPublicKey($publicKey);
            $this->getEntityManager()->persist($webPushEndpoint);
            $this->getEntityManager()->flush();
            return true;
        } catch (ORMException $e) {
            return false;
        }
    }

    public function delete(int $userId, string $endpoint): bool
    {
        try {
            $webPushEndpoint = $this->findOneBy([
                'userId' => $userId,
                'endpoint' => $endpoint,
            ]);

            if (!$webPushEndpoint) {
                return false;
            }

            $this->getEntityManager()->remove($webPushEndpoint);
            $this->getEntityManager()->flush();
            return true;
        } catch (ORMException $e) {
            return false;
        }
    }
}

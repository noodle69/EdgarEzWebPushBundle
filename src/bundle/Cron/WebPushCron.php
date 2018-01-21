<?php

namespace Edgar\EzWebPushBundle\Cron;

use Doctrine\ORM\EntityManager;
use Edgar\Cron\Cron\AbstractCron;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WebPushCron extends AbstractCron
{

    public function __construct(
        ?string $name = null,
        EntityManager $entityManager
    ) {
        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setName('edgarez:webpush:run')
            ->setDescription('Execute webpush notifications');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    }
}

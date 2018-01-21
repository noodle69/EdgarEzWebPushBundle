<?php

namespace Edgar\EzWebPushBundle\Command;

use Minishlink\WebPush\VAPID;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class WebPushCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('edgar:webpush:vapidkeys')
            ->setDescription('Generate your VAPID keys for eZ Platform WebPush.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $keys = VAPID::createVapidKeys();
        $io->success('Your VAPID keys have been generated!');
        $io->writeln(sprintf('Your public key is: <info>%s</info> ', $keys['publicKey']));
        $io->writeln(sprintf('Your private key is: <info>%s</info>', $keys['privateKey']));
        $io->newLine(1);
    }
}

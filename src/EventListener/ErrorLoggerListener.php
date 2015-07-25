<?php
/**
 * This class is part of our implementation to log errors in console commands:
 * http://symfony.com/doc/current/cookbook/console/logging.html
 * 
 */
namespace Sideclick\CoreBundle\EventListener;

use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Psr\Log\LoggerInterface;

class ErrorLoggerListener
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onConsoleTerminate(ConsoleTerminateEvent $event)
    {
        $statusCode = $event->getExitCode();
        $command = $event->getCommand();

        if ($statusCode === 0) {
            return;
        }

        if ($statusCode > 255) {
            $statusCode = 255;
            $event->setExitCode($statusCode);
        }

        $this->logger->warning(sprintf(
            'Command `%s` exited with status code %d',
            $command->getName(),
            $statusCode
        ));
    }
}
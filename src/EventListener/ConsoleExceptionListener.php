<?php
/**
 * This class is part of our implementation to log errors in console commands:
 * http://symfony.com/doc/current/cookbook/console/logging.html
 * 
 */

namespace Sideclick\CoreBundle\EventListener;

use Symfony\Component\Console\Event\ConsoleExceptionEvent;
use Psr\Log\LoggerInterface;

class ConsoleExceptionListener
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onConsoleException(ConsoleExceptionEvent $event)
    {
        $command = $event->getCommand();
        $exception = $event->getException();

        $message = sprintf(
            '%s: %s (uncaught exception) at %s line %s while running console command `%s`',
            get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $command->getName()
        );

        $this->logger->error($message, array('exception' => $exception));
    }
}
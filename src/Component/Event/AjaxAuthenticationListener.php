<?php
namespace Sideclick\CoreBundle\Component\Event;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

/**
 * This Listener ensures that a Forbidden response is returned on Ajax Responses
 * when there is an Authentication Exception.  This class is courtesy of:
 * https://gist.github.com/xanf/1015146
 * 
 * @todo This class makes no distinction between a user not being logged in VS
 * being logged in and actually not having access to the URL.  This disctinction
 * should probably be made so that we don't reload the page on the JS side when
 * we should actually just be returning a forbidden response?
 * 
 */
class AjaxAuthenticationListener
{

    /**
     * Handles security related exceptions.
     *
     * @param GetResponseForExceptionEvent $event An GetResponseForExceptionEvent instance
     */
    public function onCoreException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $request = $event->getRequest();

        if ($request->isXmlHttpRequest()) {
            if ($exception instanceof AuthenticationException || $exception instanceof AccessDeniedException) {
                $event->setResponse(new Response('', 403));
            }
        }
    }
}
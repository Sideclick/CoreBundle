<?php
namespace Sc\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Sc\CoreBundle\Entity\IncomingPanaceaSms;

class PanaceaSmsController extends Controller
{


    /**
     * Processes an incoming Panacea Mobile SMS as per their documentation:
     * http://www.panaceamobile.com/documentation/forwarding-sms-to-apps/
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function processIncomingSmsAction(Request $request)
    {
        // log this incoming sms
        $logger = $this->get('logger');
        $logger->info(
            'Processing incoming Panacea SMS, querystring: '
            . $request->getQueryString()
        );

        // create a new record
        $incomingPanaceaSms = new IncomingPanaceaSms();
        
        // populate it with all the data, as well as the entire qureystring
        $incomingPanaceaSms
            ->setCharset(trim($request->query->get('charset')))
            ->setCode(trim($request->query->get('code')))
            ->setFrom(trim($request->query->get('from')))
            ->setMessage(trim($request->query->get('message')))
            ->setTo(trim($request->query->get('to')))
            ->setQueryString(trim($request->getQueryString()))
        ;
        
        // save it
        $em = $this->getDoctrine()->getManager();
        $em->persist($incomingPanaceaSms);
        $em->flush();
        
        return new Response();
    }
}

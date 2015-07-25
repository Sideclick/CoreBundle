<?php

// src/Acme/HelloBundle/Newsletter/NewsletterManager.php
namespace Sideclick\CoreBundle\Sms;

//use Sideclick\CoreBundle\Sms\Service\Config\SmsServiceInterface;
use Sideclick\CoreBundle\Sms\Service\SmsServiceProvider;

use Sideclick\CoreBundle\Entity\Sms as Message;
use Doctrine\ORM\EntityManager;

//class Scsms extends Service\Bulksms\Bulksms
class Sms
{
    const NUM_ITEMS_TO_PROCESS = 100;
    
    /**
     * Service
     * 
     * @var type 
     */
    private $_service;
    
    /**
     * Debug
     * 
     * @var type 
     */
    private $_debug;
    
    /**
     * Entity manager
     * 
     * @var type 
     */
    private $_em;


    /**
     * Init
     * 
     * @param \Doctrine\ORM\EntityManager $em - required to manage sms related data
     * @param \Sideclick\CoreBundle\Sms\Service\SmsServiceProvider $service - service to send sms with
     * @param bool $debug - debug flag
     * 
     * @return null
     */
    public function __construct(EntityManager $em = null, SmsServiceProvider $service = null, $debug = false) 
    {
        
        if (!is_null($em)) {
            
            //set entity manager
            $this->_em = $em;
        }
        
        if (!is_null($service)) {
            
            //set sms service to use
            $this->_service = $service;
        }
        
        $this->_debug = $debug;
    }
    
    /**
     * 
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function setEntityManager(EntityManager $em)
    {
        $this->_em = $em;
    }

    /**
     * Sms service to use when sending sms.
     * No service is needed when pushing smses to the sms-queue
     * 
     * @param \Sideclick\CoreBundle\Sms\Service\Config\SmsServiceInterface $service
     */
    public function setServiceProvider(SmsServiceProvider $service)
    {
        $this->_service = $service;
    }
    
    /**
     * Get sms service being used
     * 
     * @return type
     */
    public function getServiceProvider()
    {
        if (!is_object($this->_service)) {
            
            throw new \Exception('Invalid Sms Service Provider, action aborted.');
        }
        return $this->_service;
    }

    /**
     * Send SMS
     * 
     * @param \Sideclick\CoreBundle\Entity\Sms $sms
     * 
     * @return \Sideclick\CoreBundle\Entity\Sms
     */
    public function send(Message $sms)
    {
        try {
        
            //send the message
            $result = $this->getServiceProvider()->send($sms->getContent(), $sms->getCellNumber());
            
        } catch(\Exception $e) {
            
            //failed to send the sms
            $result = (object)array(
                'sent' => false,
                'status_message' => $e->getMessage()
            );
        }
        
        //send status
        if ($result->sent) {
            
            $sms->setStatus(Message::STATUS_SENT);
        } else {
            
            $sms->setStatus(Message::STATUS_FAILED);
        }
        
        if (isset($result->status_message)) {
            $sms->setStatusMessage($result->status_message);
        }
        
        //BULK SMS FLAGS
        if (isset($result->bulksms_api_statusCode)) {
            $sms->setBulksmsApiStatusCode($result->bulksms_api_statusCode);
        }
        
        if (isset($result->bulksms_api_batch_id)) {
            $sms->setBulksmsApiBatchId($result->bulksms_api_batch_id);
        }
        
        //PANACEA MOBILE FLAGS
        if (isset($result->panaceamobile_response_message)) {
            $sms->setPanaceaMobileResponseMessage(json_encode($result->panaceamobile_response_message));
        }
        
        if (isset($result->panaceamobile_message_id)) {
            $sms->setPanaceaMobileMessageId($result->panaceamobile_message_id);
        }
        
        if (isset($result->panaceamobile_status_code)) {
            $sms->setPanaceaMobileStatusCode($result->panaceamobile_status_code);
        }
        
        //set the service name used to send this sms
        $sms->setServiceName($this->getServiceProvider()->getName());
        
        $this->_em->persist($sms);
        $this->_em->flush();
        
        return $sms;
    }
    
    /**
     * This function is used to send adhoc SMS messages
     * 
     * @param String $msisdn
     * @param String $message
     * @param Bool   $throwExceptions
     * 
     * @return BulkSMS result
     */
    
    public function sendAdhoc($cellNumber, $message, $throwExceptions = false)
    {
        //push sms to queue
        $sms = $this->push($cellNumber, $message, $throwExceptions);
        
        if ($sms->getStatus() == Message::STATUS_FAILED) {
            
            //sms has invalid cell number or empty content
            return $sms;
        } else if ($this->getServiceProvider()->getCredits() == 0) {
            
            $sms->setStatus(Message::STATUS_FAILED);
            $sms->setStatusMessage("{$this->getServiceProvider()->getName()} does not have enough credits to send this sms.");
            $this->_em->persist($sms);
            $this->_em->flush();
            
            return $sms;
        } 
        
        return $this->send($sms);
    }
    
    /**
     * Push sms to sms-queue
     * 
     * @param type $msisdn
     * @param type $message
     */
    public function push($cellNumber, $message, $throwExceptions = false)
    {
        $sms = new Message();
        
        //validate cell no.
        $validCellNumber = $this->validateCellNumber($cellNumber);
        
        if (!$validCellNumber || empty($message)) {
            
            $errorMessage = !$validCellNumber ? 'Invalid cell number' : 'No content provided';
            
            //if the sms is invalid, either throw an exception or queue the sms as 'failed'
            if ($throwExceptions) {
                
                throw new \Exception($errorMessage);
            } else {
                
                if (!$validCellNumber) {
                    
                    $validCellNumber = $cellNumber;
                }
                
                $sms->setStatus(Message::STATUS_FAILED);
                $sms->setStatusMessage($errorMessage);
            }
        }
        
        //add to sms queue
        $sms->setContent($this->smsTemplate($message));
        $sms->setCellNumber($validCellNumber);
        $this->_em->persist($sms);
        $this->_em->flush();
        
        return $sms;
    }
    
    /**
     * Process sms queue
     * 
     * @throws \Exception
     */
    public function processQueue()
    {
        $messageRepository = $this->_em->getRepository('SideclickCoreBundle:Sms');
        $queryBuilder = $messageRepository->createQueryBuilder('s');
        
        $queryBuilder->andWhere('s.status = :status')
            ->setParameter(':status', Message::STATUS_PENDING)
            ->orderBy('s.created', 'DESC')
            ->setMaxResults(self::NUM_ITEMS_TO_PROCESS);
        
        //comms to send
        $comms = $queryBuilder->getQuery()->execute();
        
        //available credit of current service
        $creditAvailable = $this->getServiceProvider()->getCredits();
        
        //do we have enough credit?
        if ($creditAvailable < count($comms)) {
            
            throw new \Exception(
                "processQueue: sms to be sent exceed available "
                . "sms-credit of [$creditAvailable] with a diff of: "
                . (count($comms) - $creditAvailable) . " - using {$this->getServiceProvider()->getName()} service provider"
            );
        }
        
        foreach ($comms as $sms) {
            
            $this->send($sms);
        }
        
        return count($comms);
    }
    
    /**
     * SMS template
     * 
     * @param string $content content
     * 
     * @return string
     */
    public function smsTemplate($content)
    {
        $content = strlen($content) > 160 ?
            substr($content, 0, 160) : $content;

        $content = strip_tags($content);

        return $content;
    }
    
    /**
     * Validate cell number.  Note that any changes to this function may
     * influence the mobile_number_canonical field on the User entity in
     * SMS Deals
     * 
     * @param type $cellNumber
     * @return string|boolean
     */
    public function validateCellNumber($cellNumber)
    {
        //remove all non numeric chars
        $number = preg_replace('/\D/', '', $cellNumber);

        if (substr($number, 0, 2) == '27') {

            //remove country code if it has one
            $number = '0' . substr($number, 2, 9);

        } else if (substr($number, 0, 1) != '0' && strlen($number) == 9) {

            //gonna assume its missing a leading zero
            $number = '0' . $number;
        }

        //cell number has 10 chars excluding country code
        if (strlen($number) != 10) {

            //and this one doesnt
            return FALSE;
        } else {

            //valid, so return it
            return $number;
        }
    }
}
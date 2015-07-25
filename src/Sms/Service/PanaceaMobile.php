<?php
namespace Sideclick\CoreBundle\Sms\Service;

use Sideclick\CoreBundle\Sms\Service\SmsServiceProvider;
use Sideclick\CoreBundle\Sms\Service\PanaceaMobile\PanaceaMobile as Service;

class PanaceaMobile extends SmsServiceProvider
{
    const SERVICE_NAME = 'panacea-mobile';
    
    /**
     * Panacea mobile api obj
     * 
     * @var type 
     */
    private $_api;
    
    
    /**
     * Get name of service
     * 
     * @return type
     */
    public function getName()
    {
        return self::SERVICE_NAME;
    }
    
    /**
     * Send the sms using panacea mobile
     * 
     * @param type $message
     * @param type $msisdn
     * 
     * @return type
     */
    public function send($message, $msisdn)
    {
        if (is_null($this->_api)) {
            
            //create the api if not already created
            $this->_api = new Service();
            $this->_api->setPassword($this->getPassword());
            $this->_api->setUsername($this->getUsername());
            $this->_api->debug = (bool)$this->_debug;
        }
        
        //make sure we have enough credits
        if ($this->getCredits() > 0) {
        
            //send sms
            $result = $this->_api->message_send($msisdn, $message);
            
            //build custom response
            $response = array(
                'panaceamobile_original_response' => $result
            );
            
            //message successfully sent?
            if ($this->_api->ok($result)) {
                
                $messageId = $result['details'];
                
                /* Now we can check what the status of the message is */
                //$message = $this->_api->message_status($messageId);
                
                $response['sent'] = true;
                $response['panaceamobile_response_message'] = $result;
                $response['panaceamobile_message_id'] = $messageId;
                $response['status_message'] = $result['message'];
                
                //if the sms was sent, decrement available credits
                $credits = $this->getCredits();
                $this->setCredits(--$credits);
                
            } else {
                
                $response['sent'] = false;
                $response['status_message'] = $this->_api->getError();
            }
            
            $response['panaceamobile_status_code'] = $result['status'];
            $response['credits'] = $this->getCredits();
            
            //return reponse as stdClass
            return (object)$response;
        }
        
        throw new \Exception(self::SERVICE_NAME . ' credits exhausted.');
    }
}
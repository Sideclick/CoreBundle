<?php
namespace Sc\CoreBundle\Sms\Service;

use Sc\CoreBundle\Sms\Service\SmsServiceProvider;
use Sc\CoreBundle\Sms\Service\Bulksms\Bulksms as Service;

class BulkSms extends SmsServiceProvider
{
    const SERVICE_NAME = 'bulksms';
    
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
     * Send the sms using bulksms
     * 
     * @param type $message - message
     * @param type $msisdn - cell number
     * 
     * @return type
     */
    public function send($message, $msisdn)
    {
        if ($this->getCredits() > 0) {
        
            //send sms
            $result = Service::send($message, $msisdn, $this->_username, $this->_password);
            
            //build custom response
            $response = array(
                'bulksms_original_response' => $result,
                'bulksms_api_statusCode' => $result['api_statusCode'],
                'bulksms_api_batch_id' => $result['api_batch_id'],
                'status_message' => Service::getApiResponse($result['api_statusCode'])
            );
            
            if ($result['success']) {
                
                //if the sms was sent, decrement available credits
                $credits = $this->getCredits();
                $this->setCredits(--$credits);
                
                $response['sent'] = true;
            } else {
                
                $response['sent'] = false;
            }
            
            $response['credits'] = $this->getCredits();
            $response['message'] = $result['details'];
            
            //return reponse as stdClass
            return (object)$response;
        }
        
        throw new \Exception(self::SERVICE_NAME . ' credits exhausted.');
    }
}
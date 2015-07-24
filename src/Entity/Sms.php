<?php

namespace Sc\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sms
 */
class Sms
{
    const STATUS_PENDING = 'pending';
    const STATUS_SENT = 'sent';
    const STATUS_FAILED = 'failed';
    const STATUS_DELIVERED = 'delivered';
    
    const MAX_CHARACTERS_PER_SMS = 160;
    
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $cell_number;

    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $status;

    /**
     * @var string
     */
    private $status_message;

    /**
     * @var string
     */
    private $service_name;

    /**
     * @var string
     */
    private $bulksms_api_status_code;

    /**
     * @var string
     */
    private $bulksms_api_batch_id;

    /**
     * @var string
     */
    private $uuid;
    
    public function __construct() 
    {
        $this->setUuid(uniqid());
        $this->setStatus(self::STATUS_PENDING);
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set cell_number
     *
     * @param string $cellNumber
     * @return Sms
     */
    public function setCellNumber($cellNumber)
    {
        $this->cell_number = $cellNumber;
    
        return $this;
    }

    /**
     * Get cell_number
     *
     * @return string 
     */
    public function getCellNumber()
    {
        return $this->cell_number;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Sms
     */
    public function setContent($content)
    {
        $this->content = $content;
    
        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Sms
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set status_message
     *
     * @param string $statusMessage
     * @return Sms
     */
    public function setStatusMessage($statusMessage)
    {
        $this->status_message = $statusMessage;
    
        return $this;
    }

    /**
     * Get status_message
     *
     * @return string 
     */
    public function getStatusMessage()
    {
        return $this->status_message;
    }

    /**
     * Set service_name
     *
     * @param string $serviceName
     * @return Sms
     */
    public function setServiceName($serviceName)
    {
        $this->service_name = $serviceName;
    
        return $this;
    }

    /**
     * Get service_name
     *
     * @return string 
     */
    public function getServiceName()
    {
        return $this->service_name;
    }

    /**
     * Set bulksms_api_status_code
     *
     * @param string $bulksmsApiStatusCode
     * @return Sms
     */
    public function setBulksmsApiStatusCode($bulksmsApiStatusCode)
    {
        $this->bulksms_api_status_code = $bulksmsApiStatusCode;
    
        return $this;
    }

    /**
     * Get bulksms_api_status_code
     *
     * @return string 
     */
    public function getBulksmsApiStatusCode()
    {
        return $this->bulksms_api_status_code;
    }

    /**
     * Set bulksms_api_batch_id
     *
     * @param string $bulksmsApiBatchId
     * @return Sms
     */
    public function setBulksmsApiBatchId($bulksmsApiBatchId)
    {
        $this->bulksms_api_batch_id = $bulksmsApiBatchId;
    
        return $this;
    }

    /**
     * Get bulksms_api_batch_id
     *
     * @return string 
     */
    public function getBulksmsApiBatchId()
    {
        return $this->bulksms_api_batch_id;
    }

    /**
     * Set uuid
     *
     * @param string $uuid
     * @return Sms
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
    
        return $this;
    }

    /**
     * Get uuid
     *
     * @return string 
     */
    public function getUuid()
    {
        return $this->uuid;
    }
    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var \DateTime
     */
    private $updated;


    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Sms
     */
    public function setCreated($created)
    {
        $this->created = $created;
    
        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return Sms
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    
        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }
    /**
     * @var string
     */
    private $panacea_mobile_message_id;

    /**
     * @var string
     */
    private $panacea_mobile_response_message;


    /**
     * Set panacea_mobile_message_id
     *
     * @param string $panaceaMobileMessageId
     * @return Sms
     */
    public function setPanaceaMobileMessageId($panaceaMobileMessageId)
    {
        $this->panacea_mobile_message_id = $panaceaMobileMessageId;
    
        return $this;
    }

    /**
     * Get panacea_mobile_message_id
     *
     * @return string 
     */
    public function getPanaceaMobileMessageId()
    {
        return $this->panacea_mobile_message_id;
    }

    /**
     * Set panacea_mobile_response_message
     *
     * @param string $panaceaMobileResponseMessage
     * @return Sms
     */
    public function setPanaceaMobileResponseMessage($panaceaMobileResponseMessage)
    {
        $this->panacea_mobile_response_message = $panaceaMobileResponseMessage;
    
        return $this;
    }

    /**
     * Get panacea_mobile_response_message
     *
     * @return string 
     */
    public function getPanaceaMobileResponseMessage()
    {
        return $this->panacea_mobile_response_message;
    }
    /**
     * @var string
     */
    private $panacea_mobile_status_code;


    /**
     * Set panacea_mobile_status_code
     *
     * @param string $panaceaMobileStatusCode
     * @return Sms
     */
    public function setPanaceaMobileStatusCode($panaceaMobileStatusCode)
    {
        $this->panacea_mobile_status_code = $panaceaMobileStatusCode;
    
        return $this;
    }

    /**
     * Get panacea_mobile_status_code
     *
     * @return string 
     */
    public function getPanaceaMobileStatusCode()
    {
        return $this->panacea_mobile_status_code;
    }
}
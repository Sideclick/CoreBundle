<?php

namespace Sideclick\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IncomingPanaceaSms
 */
class IncomingPanaceaSms
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $to;

    /**
     * @var string
     */
    private $from;

    /**
     * @var string
     */
    private $message;

    /**
     * @var string
     */
    private $charset;

    /**
     * @var string
     */
    private $code;


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
     * Set to
     *
     * @param string $to
     * @return IncomingPanaceaSms
     */
    public function setTo($to)
    {
        $this->to = $to;
    
        return $this;
    }

    /**
     * Get to
     *
     * @return string 
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Set from
     *
     * @param string $from
     * @return IncomingPanaceaSms
     */
    public function setFrom($from)
    {
        $this->from = $from;
    
        return $this;
    }

    /**
     * Get from
     *
     * @return string 
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return IncomingPanaceaSms
     */
    public function setMessage($message)
    {
        $this->message = $message;
    
        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set charset
     *
     * @param string $charset
     * @return IncomingPanaceaSms
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
    
        return $this;
    }

    /**
     * Get charset
     *
     * @return string 
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return IncomingPanaceaSms
     */
    public function setCode($code)
    {
        $this->code = $code;
    
        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }
    /**
     * @var string
     */
    private $queryString;


    /**
     * Set queryString
     *
     * @param string $queryString
     * @return IncomingPanaceaSms
     */
    public function setQueryString($queryString)
    {
        $this->queryString = $queryString;
    
        return $this;
    }

    /**
     * Get queryString
     *
     * @return string 
     */
    public function getQueryString()
    {
        return $this->queryString;
    }
    /**
     * @var \DateTime
     */
    private $created;


    /**
     * Set created
     *
     * @param \DateTime $created
     * @return IncomingPanaceaSms
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
}
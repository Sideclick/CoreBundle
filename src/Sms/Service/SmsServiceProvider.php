<?php
namespace Sc\CoreBundle\Sms\Service;

use Sc\CoreBundle\Entity\AppVariable;
use Doctrine\ORM\EntityManager;

abstract class SmsServiceProvider
{
    /**
     *
     * @var type 
     */
    protected $_em;
    
    /**
     *
     * @var type 
     */
    protected $_username;
    
    /**
     *
     * @var type 
     */
    protected $_password;
    
    /**
     *
     * @var type 
     */
    protected $_debug;
    
    /**
     *
     * @var type 
     */
    protected $_entity;


    /**
     * Init Service
     * 
     * @param type $entityManager - required to manage sms-service related data
     * 
     * @param type $username - service username
     * @param type $password - service password
     * 
     * @return null
     */
    public function __construct(EntityManager $entityManager, $username = null, $password = null, $debug = false) 
    {
        $this->_em = $entityManager;
        
        $this->_debug = $debug;
        
        //get this service's credit app variable
        $this->_entity = $entityManager->getRepository('ScCoreBundle:AppVariable')
            ->findOneByName($this->getName());
        
        //if the app variable doesnt exist, create it
        if (!is_object($this->_entity)) {
            
            $this->_entity = $this->_createAppVariable();
        }
        
        //set the username
        if (!is_null($username)) {
            $this->setUsername($username);
        }
        
        //set the password
        if (!is_null($password)) {
            $this->setPassword($password);
        }
    }
    
    /**
     * Set debug to true or false
     * 
     * @param type $status
     */
    public function setDebug($status) {
        
        $this->_debug = $status;
    }
    
    /**
     * Create a app-variable to store avaible credit for this sms service
     * 
     * @return \Sc\CoreBundle\Entity\AppVariable
     */
    private function _createAppVariable()
    {
        $variable = new AppVariable();
        $variable->setName($this->getName());
        $variable->setValue(0);
        $this->_em->persist($variable);
        $this->_em->flush();
        
        return $variable;
    }

    /**
     * Set the number of available credits
     * 
     * @param int $credits
     * 
     * @return null
     */
    public function setCredits($credits)
    {
        if (is_object($this->_entity)) {
            
            $this->_entity->setValue((int)$credits);
            $this->_em->persist($this->_entity);
            $this->_em->flush();
            
            return true;
        }
        
        throw new \Exception($this->getName() . ' AppVariable not found, you must create the variable in the AppVariable entity.');
    }
    
    /**
     * Get available credits
     * 
     * @return int
     */
    public function getCredits()
    {
        if (is_object($this->_entity)) {
            
            return (int)$this->_entity->getValue();
        }
        
        throw new \Exception($this->getName() . ' AppVariable not found, you must create the variable in the AppVariable entity.');
    }
    
    /**
     * Set username
     * 
     * @param type $username
     */
    public function setUsername($username)
    {
        $this->_username = $username;
    }
    
    /**
     * Set password
     * 
     * @param type $password
     */
    public function setPassword($password)
    {
        $this->_password = $password;
    }
    
    /**
     * Get username
     * 
     * @return type
     */
    public function getUsername()
    {
        return $this->_username;
    }
    
    /**
     * Get password
     * 
     * @return type
     */
    public function getPassword()
    {
        return $this->_password;
    }
    
    /**
     * Get name of service
     * 
     * @return type
     */
    abstract public function getName();

    /**
     * Send an SMS using this service
     * 
     * @param type $message - message
     * @param type $msisdn - cell number
     * 
     * @return type
     */
    abstract public function send($message, $msisdn);
}
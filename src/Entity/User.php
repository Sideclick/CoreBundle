<?php

namespace Sideclick\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\MessageBundle\Model\ParticipantInterface;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * Need all these for the VichUploaderBundle Annotations - for when we provide for a profile pic
 */
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 */
class User extends BaseUser implements ParticipantInterface
{
    /**
     * @var integer
     * We make this protected to be compatible with the FOSUserBundle User
     */
    protected $id;

    /**
     * @var string
     */
    protected $first_name;

    /**
     * @var string
     */
    protected $last_name;


    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // We don't use the 'username' field that comes with FOSUserBundle, we facilitate login using the email
        // address
        // default the username to a unique value because the FOSUserBundle
        // parent class has this as a NOTNULL, UNIQUE field.  This initial
        // setting is purely to have some unique value in here for validation.
        // On prePersist and preUpdate we overrite this value with the email
        // of the User
        $this->username = uniqid();

    }

    /**
     * Returns a textual representation of this user (their full name)
     */
    public function __toString()
    {
        return $this->getFullName();
    }

    /**
     * @ORM\PrePersist
     */
    public function copyEmailToUsername()
    {
        $this->setUsername($this->getEmail());
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return trim($this->getFirstName() . ' ' . $this->getLastName());
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
     * Set first_name
     *
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->first_name = $firstName;
    
        return $this;
    }

    /**
     * Get first_name
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * Set last_name
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->last_name = $lastName;
    
        return $this;
    }

    /**
     * Get last_name
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->last_name;
    }
    /**
     * @var \DateTime
     */
    protected $created;

    /**
     * @var \DateTime
     */
    protected $updated;


    /**
     * Set created
     *
     * @param \DateTime $created
     * @return User
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
     * @return User
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
    private $search_index;


    /**
     * Set search_index
     *
     * @param string $searchIndex
     * @return User
     */
    public function setSearchIndex($searchIndex)
    {
        $this->search_index = $searchIndex;
    
        return $this;
    }

    /**
     * Get search_index
     *
     * @return string 
     */
    public function getSearchIndex()
    {
        return $this->search_index;
    }
    
    /**
     * 
     * Updates the Search Index field with internal data. The Search Index Field
     * provides an easy way to perform a 'like' query for a generalised search.
     * 
     * @ORM\PrePersist
     */
    public function updateSearchIndex()
    {
        $searchIndex =
            $this->getFullName()
            . ' '
            . $this->getEmail();
        
        $this->setSearchIndex($searchIndex);
    }
}
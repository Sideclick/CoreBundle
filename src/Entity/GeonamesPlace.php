<?php

namespace Sideclick\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GeonamesPlace
 */
class GeonamesPlace
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $lat;

    /**
     * @var string
     */
    private $lng;

    /**
     * @var string
     */
    private $latlngtext;

    /**
     * @var string
     */
    private $details;

    /**
     * @var string
     */
    private $country;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var \DateTime
     */
    private $updated;


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
     * Set name
     *
     * @param string $name
     * @return GeonamesPlace
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set lat
     *
     * @param string $lat
     * @return GeonamesPlace
     */
    public function setLat($lat)
    {
        $this->lat = $lat;
    
        return $this;
    }

    /**
     * Get lat
     *
     * @return string 
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Set lng
     *
     * @param string $lng
     * @return GeonamesPlace
     */
    public function setLng($lng)
    {
        $this->lng = $lng;
    
        return $this;
    }

    /**
     * Get lng
     *
     * @return string 
     */
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * Set latlngtext
     *
     * @param string $latlngtext
     * @return GeonamesPlace
     */
    public function setLatlngtext($latlngtext)
    {
        $this->latlngtext = $latlngtext;
    
        return $this;
    }

    /**
     * Get latlngtext
     *
     * @return string 
     */
    public function getLatlngtext()
    {
        return $this->latlngtext;
    }

    /**
     * Set details
     *
     * @param string $details
     * @return GeonamesPlace
     */
    public function setDetails($details)
    {
        $this->details = $details;
        
        // now that we are setting the details, we want to update any other
        // fields that are based on this data
        $this->_setDataFromDetails();
    
        return $this;
    }
    
    /**
     * This method goes through the Details Json array and populates fields
     * on this Entity with data it extracts.
     */
    protected function _setDataFromDetails()
    {
        $data = $this->getDetailsArray();
        
        $this->setName($data['name']);
        $this->setLat($data['lat']);
        $this->setLng($data['lng']);
        $this->setLatlngtext($data['lat'] . $data['lng']);
        $this->setGeonameId($data['geonameId']);
        $this->setCountry($data['countryCode']);
    }

    /**
     * Get details
     *
     * @return string 
     */
    public function getDetails()
    {
        return $this->details;
    }
    
    /**
     * Returns the details of this Google Place as an associative array
     * 
     * @return type
     */
    public function getDetailsArray()
    {
        return json_decode($this->getDetails(), true);
    }

    /**
     * Set country
     *
     * @param string $country
     * @return GeonamesPlace
     */
    public function setCountry($country)
    {
        $this->country = $country;
    
        return $this;
    }

    /**
     * Get country
     *
     * @return string 
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return GeonamesPlace
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    
        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return GeonamesPlace
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
     * @return GeonamesPlace
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
     * @var integer
     */
    private $geonameId;


    /**
     * Set geonameId
     *
     * @param integer $geonameId
     * @return GeonamesPlace
     */
    public function setGeonameId($geonameId)
    {
        $this->geonameId = $geonameId;
    
        return $this;
    }

    /**
     * Get geonameId
     *
     * @return integer 
     */
    public function getGeonameId()
    {
        return $this->geonameId;
    }
}
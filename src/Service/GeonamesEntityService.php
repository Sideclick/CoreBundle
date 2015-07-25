<?php
/**
 * This class is intended to be a SDK for the Geonames API service
 */
namespace Sideclick\CoreBundle\Service;

use Doctrine\ORM\EntityManager;
use Sideclick\CoreBundle\Entity\GeonamesPlace;

class GeonamesEntityService
{
    private $apiService;
    private $em;
    
    /**
     * 
     * @param \Doctrine\ORM\EntityManager $em
     * @param type $container
     */
    public function __construct(EntityManager $em, GeonamesApiService $apiService)
    {
        $this->em = $em;
        $this->apiService = $apiService;
    }
    
    /**
     * Given the latitude and longitude, we query geonames for the closest city
     * we insert it into our database and return the entity
     * 
     * @param type $lat
     * @param type $lng
     * @param int $radius (Max 300 or funct)
     * @return boolean|GeonamesPlace
     */
    public function getClosestCity($lat, $lng)
    {
        // make a call to find some close places that are of time cities1000
        $geonamesData = $this->apiService->findNearbyPlaceName($lat, $lng, 'cities1000', 20);
        
        // if we got some data back
        if ($geonamesData !== false) {
            
            $geonamesArray = json_decode($geonamesData, true);
            
            // we could not find even 1 close city
            if (!isset($geonamesArray['geonames'][0]['geonameId'])) {
                
                return false;
            }
            
            // check if this city is already in our DB
            $matchingPlace = $this->em
                ->getRepository('SideclickCoreBundle:GeonamesPlace')
                ->findOneByGeonameId($geonamesArray['geonames'][0]['geonameId']);
            
            // if it is then return it
            if ($matchingPlace) {
                
                return $matchingPlace;
            }
            
            // otherwise do another query to grab the details of this city
            $geonamesSpecificPlaceData = $this->apiService->get($geonamesArray['geonames'][0]['geonameId']);
            
            // if we managed to get the details
            if ($geonamesSpecificPlaceData !== false) {
                
                // create the place and return it
                $newPlace = new GeonamesPlace();
                $newPlace->setDetails($geonamesSpecificPlaceData);
                $this->em->persist($newPlace);
                $this->em->flush();

                return $newPlace;
            }
            
        }
        
        // if we get here then we could not find a place
        return false;
    }
}
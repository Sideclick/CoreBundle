<?php
/**
 * This class is intended to be a SDK for the Geonames API service
 */
namespace Sideclick\CoreBundle\Service;

class GeonamesApiService
{
    const API_ENDPOINT = 'http://api.geonames.org';
        
    /**
     * 
     * @param \Doctrine\ORM\EntityManager $em
     * @param type $container
     */
    public function __construct()
    {
        $this->username = 'sideclick'; // hard coding the username for now
    }
    
    /**
     * Make a call to the /get function - This returns details about
     * a particular geoname place Will return false on fail or a JSON string
     * of data on success
     * 
     * @param type $geonameId
     * 
     * @return boolean|string
     */
    public function get($geonameId)
    {
        $url = self::API_ENDPOINT . '/getJSON';
        
        $queryParams =
            array(
                'geonameId' => $geonameId
            );
        
        $results = $this->doApiCall($url, $queryParams);
        
        $resultsArray = json_decode($results, true);
        
        if (is_array($resultsArray)
            && isset($resultsArray['geonameId'])
            && $resultsArray['geonameId'] == $geonameId
            ) {
            
            return $results;
        }
        
        return false;
    }
    
    /**
     * Make a call to the findNearbyPlaceName function
     * 
     * @param type $lat
     * @param type $lng
     * @param type $cities
     * @return boolean
     */
    public function findNearbyPlaceName($lat, $lng, $cities, $radius)
    {
        $url = self::API_ENDPOINT . '/findNearbyPlaceNameJSON';
        
        $queryParams =
            array(
                'lat' => $lat,
                'lng' => $lng,
                'cities' => $cities,
                'radius' => $radius
            );
        
        $results = $this->doApiCall($url, $queryParams);
        
        $resultsArray = json_decode($results, true);
        
        if (is_array($resultsArray['geonames'])) {
            
            return $results;
        }
        
        return false;
    }
    
    /**
     * Makes a call to geonames via curl and returns the response. False on fail
     * the string response on success
     * 
     * @param string $url
     * @param array $queryParams
     * 
     * @return boolean|string
     */
    protected function doApiCall($url, $queryParams)
    {
        $queryParams['username'] = $this->username;
        
        $url = $url . '?' . http_build_query($queryParams);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_USERAGENT, 'MyBot/1.0 (http://www.sideclick.co.za/)');

        $result = curl_exec($ch);

        if (!$result) {
          //exit('cURL Error: '.curl_error($ch));
            return false;
        }
        
        return $result;
    }
}
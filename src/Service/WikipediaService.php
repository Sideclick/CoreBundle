<?php

namespace Sc\CoreBundle\Service;

use Doctrine\ORM\EntityManager;
use Sc\CoreBundle\Entity\WikipediaPage;

class WikipediaService
{
    private $em;
    private $templating;
    
    /**
     * 
     * @param \Doctrine\ORM\EntityManager $em
     * @param type $templating
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    
    /**
     * Looks up a Wikipedia page locally that matches the given title. If one
     * cannot be found we do a Wikipedia search and save the resultant page.
     * 
     * @param type $memories
     * @param type $params - data you want sent through to the templates
     * 
     * @return type
     */
    public function retrievePageFromTitle($title)
    {
        // try and find a matching page locally and return
        $possiblePage = $this->em
            ->getRepository('ScCoreBundle:WikipediaPage')
            ->findOneByTitle($title);
        
        if ($possiblePage) {
            return $this->_returnPageIfValid($possiblePage);
        }
        
        // otherwise we will need to make a call to wikipedia
        
        // make a call to wikipedia to get matching pages, thanks to:
        // http://stackoverflow.com/questions/964454/how-to-use-wikipedia-api-if-it-exists
        $url = 'http://en.wikipedia.org/w/api.php?action=query&prop=extracts&titles=' . urlencode($title) . '&format=json&exintro=1';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_USERAGENT, 'MyBot/1.0 (http://www.sideclick.co.za/)');

        $result = curl_exec($ch);

        if (!$result) {
          //exit('cURL Error: '.curl_error($ch));
            return false;
        }
        
        $data = json_decode($result, true);
        
        // if we have a query result with at least one matching page
        if (isset($data['query']) && isset($data['query']['pages']) && count($data['query']['pages']) >= 0) {
            
            // grab the first page data
            $firstPage = array_shift($data['query']['pages']);
            
            // if this page has all the information we need to create it locally
            if (isset($firstPage['pageid']) && isset($firstPage['title']) && isset($firstPage['extract'])
                && !empty($firstPage['pageid']) && !empty($firstPage['title']) && !empty($firstPage['extract'])) {
                
                // create the page
                $newPage = new WikipediaPage();
                $newPage->setPageId($firstPage['pageid']);
                $newPage->setTitle($firstPage['title']);
                $newPage->setExtract($firstPage['extract']);
                
                // save and return it
                $this->em->persist($newPage);
                $this->em->flush();
                return $this->_returnPageIfValid($newPage);
            }
        }
        
        
        // we don't have a locally matching page and we could not find a valid
        // match from wikipedia
        return false;
    }
    
    /**
     * Checks if the Wikipedia page has valid content, returns the page if it
     * does and false if it doesnt
     * 
     * @param \Sc\CoreBundle\Entity\WikipediaPage $page
     * @return boolean
     */
    protected function _returnPageIfValid(WikipediaPage $page)
    {
        // if this has 'redirect' content
        if (stripos($page->getExtract(), 'This page was kept as a redirect')) {
            
            // then it is not valid
            return false;
        }
        
        return $page;
    }

}
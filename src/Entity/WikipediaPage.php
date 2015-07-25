<?php

namespace Sideclick\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WikipediaPage
 */
class WikipediaPage
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $page_id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $content;


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
     * Set page_id
     *
     * @param string $pageId
     * @return WikipediaPage
     */
    public function setPageId($pageId)
    {
        $this->page_id = $pageId;
    
        return $this;
    }

    /**
     * Get page_id
     *
     * @return string 
     */
    public function getPageId()
    {
        return $this->page_id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return WikipediaPage
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return WikipediaPage
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
     * @var string
     */
    private $extract;


    /**
     * Set extract
     *
     * @param string $extract
     * @return WikipediaPage
     */
    public function setExtract($extract)
    {
        $this->extract = $extract;
    
        return $this;
    }

    /**
     * Get extract
     *
     * @return string 
     */
    public function getExtract()
    {
        return $this->extract;
    }
}
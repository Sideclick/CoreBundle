<?php

namespace Sc\CoreBundle\Twig;

class ScExtension extends \Twig_Extension
{
    protected $vichHelper;
    
    public function __construct($vichHelper) 
    {
        $this->vichHelper = $vichHelper;
    }

    public function getFilters()
    {
        return array(
            
            new \Twig_SimpleFilter('sc_localize_image', array($this, 'scLocalizeImage')),
        );
    }

    /**
     * Import remote images and prepare them for use with 
     * the "imagine_filter" filter
     * 
     * @param type $path
     * @param type $entity
     * @param type $field
     * @return string
     */
    public function scLocalizeImage($path, $entity, $field = 'image')
    {
        
        if (empty($path)) {
            
            //palceholder image
            return '/data/downloads/memory-sample-2.jpg';
        }
        
        $url = parse_url($path);
        
        if (isset($url['scheme']) && !in_array($url['scheme'], array('http', 'https'))) {
            
            //local file - should have been upload by given entity
            return $this->vichHelper->asset($entity, $field);
        }
        
        //remote file
        
        //use the image's url as local image name
        $name = preg_replace('/[\W]/', '_', $path);
        $downloadPath = __DIR__ . '/../../../../data/downloads/';
        
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        
        if ($ext == '') {
            
            //google-place images got no extension
            //@todo get google-place image extensions
            $ext = 'jpg';
        }
        
        $name .= ".$ext";
        $destination = $downloadPath . $name;
        
        if (!file_exists($destination)) {
            
            //@todo use a different way to get the remote images coz 
            //this copy() sometimes fails
            copy($path, $destination);
        }
        
        //check if we got a valid image
        if (file_exists($destination) && @imagecreatefromstring(file_get_contents($destination)) !== false) {
            
            $src = '/data/downloads/' . $name;
            return $src;
        } 
        
        //placeholder
        return '/data/downloads/memory-sample-2.jpg';
    }

    public function getName()
    {
        return 'sc_extension';
    }
}
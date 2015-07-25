<?php

namespace Sideclick\CoreBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SideclickUrlizeExtension
 *
 * Registers a custom filter called urlize which replaces URLs in plan text
 * with clickable links
 *
 * @package Sideclick\CoreBundle\Twig
 */
class SideclickUrlizeExtension extends \Twig_Extension
{

    public function getFilters()
    {
        return array(
            
            new \Twig_SimpleFilter('urlize', array($this, 'urlizeFilter')),
        );
    }

    /**
     * a custom filter called urlize which replaces URLs in plan text
     * with clickable links
     *
     * @param $string
     * @return mixed
     */
    public function urlizeFilter($string){
        $content_array = explode(" ", $string);
        $output = '';

        foreach($content_array as $content)
        {
            //starts with http://
            if(substr($content, 0, 7) == "http://")
                $content = '<a target="_blank" href="' . $content . '">' . $content . '</a>';
            
            //starts with https://
            if(substr($content, 0, 8) == "https://")
                $content = '<a target="_blank" href="' . $content . '">' . $content . '</a>';

            //starts with www.
            if(substr($content, 0, 4) == "www.")
                $content = '<a target="_blank" href="http://' . $content . '">' . $content . '</a>';

            $output .= " " . $content;
        }

            $output = trim($output);
        return $output;
    }


    public function getName()
    {
        return 'sc_urlize_extension';
    }
}
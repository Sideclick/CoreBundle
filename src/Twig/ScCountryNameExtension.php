<?php

namespace Sc\CoreBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\Intl\Intl;

/**
 * Class ScUrlizeExtension
 *
 * Registers a custom filter called urlize which replaces URLs in plan text
 * with clickable links
 *
 * @package Sc\CoreBundle\Twig
 */
class ScCountryNameExtension extends \Twig_Extension
{

    public function getFilters()
    {
        return array(
            
            new \Twig_SimpleFilter('countryName', array($this, 'countryNameFilter')),
        );
    }

    /**
     * a custom filter called urlize which replaces URLs in plan text
     * with clickable links
     *
     * @param $string
     * @return mixed
     */
    public function countryNameFilter($countryCode){
        return Intl::getRegionBundle()->getCountryName($countryCode);
    }


    public function getName()
    {
        return 'sc_country_name_extension';
    }
}
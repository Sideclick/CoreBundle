<?php
namespace Sc\CoreBundle\Service;

use \Swift_Events_SendListener;
use \Swift_Events_SendEvent;
use \Swift_Image;
use \Sunra\PhpSimple\HtmlDomParser;

class SwiftMailerImageEmbedService implements Swift_Events_SendListener
{

    /**
     * Embeds all images used in this email
     *  
     * @param Swift_Events_SendEvent $evt
     * 
     * @return null
     */
    public function beforeSendPerformed(Swift_Events_SendEvent $evt)
    {
        // if we cannot open remote URLS
        if (!ini_get('allow_url_fopen')) {

            // then we will get SwiftIO Exceptions for trying to grab images, so we return out of here.
            // @todo we should rather throw an exception here, deal with it somewhere and log it
            return;
        }

        $message = $evt->getMessage();
        $body = $message->getBody();
        
        //get all img tags from the email body
        $dom = HtmlDomParser::str_get_html( $body );
        $images = $dom->find('img');
        
        //go through the images and embed them replacing the 
        //image src val with the generated cid value
        foreach ($images as $image) {
            
            try {

                $cid = $message->embed(
                    Swift_Image::fromPath($image->attr['src'])
                );

                $body = str_replace($image->attr['src'], $cid, $body);
                
            } catch (\Exception $e) {
                // @todo log this?  or just let it be thrown
            }
        }
        
        $message->setBody($body);
    }
    
    /**
     * 
     * @param Swift_Events_SendEvent $evt
     */
    public function sendPerformed(Swift_Events_SendEvent $evt){}
}

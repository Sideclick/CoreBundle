<?php
namespace Sideclick\CoreBundle\Service;

/**
 * Description of Encryption
 *
 * @author rowan
 */
class EncryptionService {
    
    protected $container;
    
    protected $iv;
    
    public function __construct($container)
    {
        $this->container = $container;
        
    }
    
    public function simpleEncrypt($string, $secretKey = null)
    {
        if (is_null($secretKey)) {
            $secretKey = $this->getSecretKey();
        }
        
        return rawurlencode(bin2hex(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $secretKey, $string, MCRYPT_MODE_ECB)));
    }
    
    public function simpleDecrypt($encryptedString, $secretKey = null)
    {
        if (is_null($secretKey)) {
            $secretKey = $this->getSecretKey();
        }
        
        return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $secretKey, hex2bin(rawurldecode($encryptedString)), MCRYPT_MODE_ECB));
        
    }
    
    protected function getSecretKey()
    {
        $secretKey = $this->container->getParameter('secret');
        
        if (empty($secretKey)) {
            
            throw new Exception("Attempting to use 'secret' parameter, but it is empty.  Are you sure your 'secret' parameter is set in your parameters.yml?");
        }
    }
}

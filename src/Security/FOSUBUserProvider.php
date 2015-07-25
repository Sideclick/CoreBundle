<?php
namespace Sideclick\CoreBundle\Security;
 
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;

use HWI\Bundle\OAuthBundle\Security\Core\Exception\AccountNotLinkedException;

class FOSUBUserProvider extends BaseClass
{
 
    /**
     * {@inheritDoc}
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        $property = $this->getProperty($response);
        $username = $response->getUsername();
 
        //on connect - get the access token and the user ID
        $service = $response->getResourceOwner()->getName();
 
        $setter = 'set'.ucfirst($service);
        $setter_id = $setter.'Id';
        $setter_token = $setter.'AccessToken';
 
        //we "disconnect" previously connected users
        if (null !== $previousUser = $this->userManager->findUserBy(array($property => $username))) {
            $previousUser->$setter_id(null);
            $previousUser->$setter_token(null);
            $this->userManager->updateUser($previousUser);
        }
 
        //we connect current user
        $user->$setter_id($username);
        $user->$setter_token($response->getAccessToken());
 
        $this->userManager->updateUser($user);
    }
 
    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        // grab the email address from the user's account
        $email = $response->getEmail();        
        if (empty($email)) {
            // if we dont have one, then we cannot create them or look them up
            // because we use email address as the unique identifier
            throw new AccountNotLinkedException("Unable to access your email address, could not link your account.");
        }
        
        // we used to lookup based on username - but not any more
        //$user = $this->userManager->findUserBy(array($this->getProperty($response) => $username));
        
        $user = $this->userManager->findUserBy(array('email' => $email));
        $username = $response->getUsername();
        
        $service = $response->getResourceOwner()->getName();
        $setter = 'set'.ucfirst($service);
        $setter_id = $setter.'Id';
        $setter_token = $setter.'AccessToken';
            
        //var_dump($response->getUsername());
        //when the user is registrating
        if (null === $user) {
            
            // create new user here
            $user = $this->userManager->createUser();
            $user->$setter_id($username);
            $user->$setter_token($response->getAccessToken());
            //I have set all requested data with the user's username
            //modify here with relevant data
            
            // @todo The below code which brings in the user's name has only
            // been tested for Twitter and Facebook integrations
            
            // grab the real name returned by the response (I.E the first 
            // name(s) and surname of the user.  Split it on space
            $nameArray = explode(' ', $response->getRealName());
            
            // then depending on the number of 'names'
            switch (count($nameArray)) {
                
                // if there are none then we use their username as their name
                // in our system
                case 0:
                    $user->setFirstName($response->getUsername());
                    break;
                
                // if there is one name then we just set it as their first name
                // in our system
                case 1:
                    $user->setFirstName($response->getRealName());
                    break;
                
                // else, awesome, we have at least 2 names so we use the last
                // one as the surname and the first however many as the first
                // names
                default:
                    $user->setLastName(array_pop($nameArray));
                    $user->setFirstName(implode(' ', $nameArray));
                    break;

            }
            // @todo make this set the username and password correctly
            

            //$user->setUsername($username); // we just let the user get a uniqueid
            $user->setEmail($email);
            $user->setPassword($username);
            $user->setEnabled(true);
            $this->userManager->updateUser($user);
            return $user;
        }
 
        $serviceName = $response->getResourceOwner()->getName();
        // should we maybe be setting the setter_id as well?
        $setter = 'set' . ucfirst($serviceName) . 'AccessToken';
 
        //update access token and id (though ID should be the same)
        $user->$setter_id($username);
        $user->$setter_token($response->getAccessToken());
 
        return $user;
    }
 
}
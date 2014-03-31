<?php

namespace PROCERGS\VPR\CoreBundle\Security;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;
use Symfony\Component\Security\Core\User\UserInterface;

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

        $setter = 'set' . ucfirst($service);
        $setter_id = $setter . 'Id';
        $setter_token = $setter . 'AccessToken';
        $setter_username = $setter . 'Username';

        //we "disconnect" previously connected users
        $previousUser = $this->userManager->findUserBy(array($property => $username));
        if (null !== $previousUser && $previousUser->getId() != $user->getId()) {
            $previousUser->$setter_id(null);
            $previousUser->$setter_token(null);
            $previousUser->$setter_username(null);
            $this->userManager->updateUser($previousUser);
        }

        //we connect current user
        $user->$setter_id($username);
        $user->$setter_username($response->getNickname());
        $user->$setter_token($response->getAccessToken());

        $this->userManager->updateUser($user);
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $username = $response->getNickname();
        $serviceId = $response->getUsername();
        $user = $this->userManager->findUserBy(array($this->getProperty($response) => $serviceId));

        if (null === $user) {
            $userData = $response->getResponse();
            $email = $response->getEmail();
            $firstName = $userData['first_name'];
            $surname = $userData['surname'];

            $service = $response->getResourceOwner()->getName();
            $setter = 'set' . ucfirst($service);
            $setter_id = $setter . 'Id';
            $setter_token = $setter . 'AccessToken';
            $setter_username = $setter . 'Username';

            $user = $this->userManager->createUser();
            $user->$setter_id($serviceId);
            $user->$setter_token($response->getAccessToken());
            $user->$setter_username($response->getNickname());

            $user->setUsername($username);
            $user->setFirstName($firstName);
            $user->setSurname($surname);
            $user->setEmail($email);
            $user->setPassword('');
            $user->setEnabled(true);
            $this->userManager->updateUser($user);
        }

        //if user exists - go with the HWIOAuth way
        $user = parent::loadUserByOAuthUserResponse($response);

        $serviceName = $response->getResourceOwner()->getName();
        $setter = 'set' . ucfirst($serviceName) . 'AccessToken';

        //update access token
        $user->$setter($response->getAccessToken());

        return $user;
    }

}

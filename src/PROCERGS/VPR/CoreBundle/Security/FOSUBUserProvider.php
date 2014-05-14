<?php
namespace PROCERGS\VPR\CoreBundle\Security;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;
use Symfony\Component\Security\Core\User\UserInterface;
use PROCERGS\VPR\CoreBundle\Exception\LcException;
use Doctrine\ORM\EntityManager;

class FOSUBUserProvider extends BaseClass
{
    protected $em;

    public function setEntityManager(EntityManager $var)
    {
        $this->em = $var;
    }

    /**
     * {@inheritDoc}
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        $property = $this->getProperty($response);
        $username = $response->getUsername();

        // on connect - get the access token and the user ID
        $service = $response->getResourceOwner()->getName();

        $setter = 'set' . ucfirst($service);
        $setter_id = $setter . 'Id';
        $setter_token = $setter . 'AccessToken';
        $setter_username = $setter . 'Username';

        // we "disconnect" previously connected users
        $previousUser = $this->userManager->findUserBy(array(
            $property => $username
        ));
        if (null !== $previousUser && $previousUser->getId() != $user->getId()) {
            $previousUser->$setter_id(null);
            $previousUser->$setter_token(null);
            $previousUser->$setter_username(null);
            $this->userManager->updateUser($previousUser);
        }

        // we connect current user
        $user->$setter_id($username);
        $user->$setter_username($response->getNickname());
        $user->$setter_token($response->getAccessToken());

        $this->userManager->updateUser($user);
    }

    /**
     *
     * @ERROR!!!
     *
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $username = $response->getNickname();
        $serviceId = $response->getUsername();
        $user = $this->userManager->findUserBy(array(
            $this->getProperty($response) => $serviceId
        ));

        if (null === $user) {
            $user = $this->userManager->createUser();
        }
        $userData = $response->getResponse();
        $email = $response->getEmail();
        if (!isset($userData['first_name'])) {
            throw new LcException('lc.missing.required.field', 'lc.first_name');
        }
        if (!isset($userData['surname'])) {
            throw new LcException('lc.missing.required.field', 'lc.surname');
        }
        $firstName = trim($userData['first_name']);
        $surname = trim($userData['surname']);

        $service = $response->getResourceOwner()->getName();
        $setter = 'set' . ucfirst($service);
        $setter_id = $setter . 'Id';
        $setter_token = $setter . 'AccessToken';
        $setter_username = $setter . 'Username';

        $user->$setter_id($serviceId);
        $user->$setter_token($response->getAccessToken());
        $user->$setter_username($response->getNickname());

        $user->setUsername($username);
        $user->setFirstName($firstName . ' '. $surname);
        $user->setEmail($email);
        $user->setPassword('');
        $user->setEnabled(true);
        if (array_key_exists('city', $userData) && is_numeric($userData['city']['id'])) {
            $cityRepo = $this->em->getRepository('PROCERGSVPRCoreBundle:City');
            $city = $cityRepo->findOneBy(array('ibgeCode' => $userData['city']['id']));
            if ($city) {
                $user->setCity($city);
            }
        }
        $this->userManager->updateUser($user);

        // if user exists - go with the HWIOAuth way
        $user = parent::loadUserByOAuthUserResponse($response);

        $serviceName = $response->getResourceOwner()->getName();
        $setter = 'set' . ucfirst($serviceName) . 'AccessToken';

        // update access token
        $user->$setter($response->getAccessToken());

        return $user;
    }
}

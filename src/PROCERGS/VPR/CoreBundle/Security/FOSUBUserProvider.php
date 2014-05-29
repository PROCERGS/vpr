<?php

namespace PROCERGS\VPR\CoreBundle\Security;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;
use Symfony\Component\Security\Core\User\UserInterface;
use PROCERGS\VPR\CoreBundle\Exception\LcException;
use Doctrine\ORM\EntityManager;
use PROCERGS\VPR\CoreBundle\Entity\TREVoter;
use PROCERGS\VPR\CoreBundle\Event\PersonEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;
use PROCERGS\VPR\CoreBundle\Exception\VoterRegistrationNotFoundException;

class FOSUBUserProvider extends BaseClass
{

    protected $em;
    protected $dispatcher;
    protected $session;
    protected $translator;

    public function setEntityManager(EntityManager $var)
    {
        $this->em = $var;
    }

    public function setDispatcher($var)
    {
        $this->dispatcher = $var;
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
        /* loop do lc
          if (!isset($userData['full_name']) || !strlen(trim($userData['full_name']))) {
          throw new LcException('lc.missing.required.field', 'lc.full_name');
          }
         */
        if (!isset($userData['badges'])) {
            throw new LcException('lc.missing.required.field', 'lc.badges');
        }

        $service = $response->getResourceOwner()->getName();
        $setter = 'set' . ucfirst($service);
        $setter_id = $setter . 'Id';
        $setter_token = $setter . 'AccessToken';
        $setter_refresh = $setter . 'RefreshToken';
        $setter_username = $setter . 'Username';

        $user->$setter_id($serviceId);
        $user->$setter_token($response->getAccessToken());
        $user->$setter_username($response->getNickname());
        $user->$setter_refresh($response->getRefreshToken());

        $user->setUsername($username);
        $user->setFirstName(null);
        if (isset($userData['name']) && strlen(trim($userData['name']))) {
            $user->setFirstName(trim($userData['name']));
        }
        $user->setPassword('');
        $user->setEnabled(true);
        $user->setBadges($userData['badges']);
        $updateDate = new \DateTime($userData['updated_at']);
        if ($updateDate instanceof \DateTime) {
            $user->setLoginCidadaoUpdatedAt($updateDate);
        }
        $user->setTreVoter(null);
        $user->setCity(null);
        if (array_key_exists('city', $userData) && is_numeric($userData['city']['id'])) {
            $cityRepo = $this->em->getRepository('PROCERGSVPRCoreBundle:City');
            $city = $cityRepo->findOneBy(array('ibgeCode' => $userData['city']['id']));
            if ($city) {
                $user->setCity($city);
            }
        }
        $event = new PersonEvent($user, $userData['voter_registration']);
        try {
            $this->dispatcher->dispatch(PersonEvent::VOTER_REGISTRATION_EDIT,
                    $event);
        } catch (VoterRegistrationNotFoundException $e) {
            $message = $this->translator->trans('vote.voter_registration.outsider');
            $this->session->getFlashBag()->add('info', $message);
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

    public function setSession(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

}

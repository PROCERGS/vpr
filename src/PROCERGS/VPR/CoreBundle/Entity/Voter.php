<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;

/**
 * @ORM\Entity
 */
class Voter extends BaseUser implements OAuthAwareUserProviderInterface
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="login_cidadao_id", type="string", length=255, nullable=true)
     */
    protected $login_cidadao_id;

    /**
     * @ORM\Column(name="login_cidadao_access_token", type="string", length=255, nullable=true)
     */
    protected $login_cidadao_access_token;
    
    /**
     * {@inheritDoc}
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        
    }

}

<?php

namespace Donato\OIDCBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * IdentityProvider
 *
 * @ORM\Table(name="identity_provider")
 * @ORM\Entity(repositoryClass="Donato\OIDCBundle\Repository\IdentityProviderRepository")
 */
class IdentityProvider
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\Url(
     *    protocols = {"https","http"},
     *    checkDNS = true
     * )
     * @ORM\Column(name="provider_url", type="string", length=255, unique=true)
     */
    private $providerUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="client_id", type="string", length=255, unique=true)
     */
    private $clientId;

    /**
     * @var string
     *
     * @ORM\Column(name="client_secret", type="string", length=255, unique=true)
     */
    private $clientSecret;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_default", type="boolean", unique=false, nullable=true)
     */
    private $isDefault;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get providerUrl
     *
     * @return string
     */
    public function getProviderUrl()
    {
        return $this->providerUrl;
    }

    /**
     * Set providerUrl
     *
     * @param string $providerUrl
     * @return IdentityProvider
     */
    public function setProviderUrl($providerUrl)
    {
        $this->providerUrl = $providerUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @param string $clientId
     * @return \Donato\OIDCBundle\Entity\IdentityProvider
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;

        return $this;
    }

    /**
     * @return string
     */
    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    /**
     * @param string $clientSecret
     * @return \Donato\OIDCBundle\Entity\IdentityProvider
     */
    public function setClientSecret($clientSecret)
    {
        $this->clientSecret = $clientSecret;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isDefault()
    {
        return $this->isDefault;
    }

    /**
     * @param boolean $isDefault
     * @return IdentityProvider
     */
    public function setIsDefault($isDefault)
    {
        $this->isDefault = $isDefault;

        return $this;
    }


}

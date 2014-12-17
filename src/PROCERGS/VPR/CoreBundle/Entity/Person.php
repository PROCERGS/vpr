<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use JMS\Serializer\Annotation\Groups;

/**
 * Person
 *
 * @ORM\Table(name="person")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\AttributeOverrides({
 *      @ORM\AttributeOverride(name="email",
 *          column=@ORM\Column(
 *              name     = "email",
 *              type     = "string",
 *              length   = 255,
 *              nullable = true,
 *              unique   = false
 *          )
 *      ),
 *      @ORM\AttributeOverride(name="emailCanonical",
 *          column=@ORM\Column(
 *              name     = "email_canonical",
 *              type     = "string",
 *              nullable = true,
 *              unique   = false,
 *              length   = 255
 *          )
 *      ),
 *      @ORM\AttributeOverride(name="password",
 *          column=@ORM\Column(
 *              nullable = true
 *          )
 *      )
 * })
 */
class Person extends BaseUser implements OAuthAwareUserProviderInterface
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255, nullable=true)
     */
    protected $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="login_cidadao_id", type="string", length=255, nullable=true)
     */
    protected $loginCidadaoId;

    /**
     * @var string
     *
     * @ORM\Column(name="login_cidadao_access_token", type="string", length=255, nullable=true)
     */
    protected $loginCidadaoAccessToken;

    /**
     * @var string
     *
     * @ORM\Column(name="login_cidadao_username", type="string", length=255, nullable=true)
     */
    protected $loginCidadaoUsername;

    /**
     * @var string
     *
     * @ORM\Column(name="login_cidadao_refresh_token", type="string", length=255, nullable=true)
     */
    protected $loginCidadaoRefreshToken;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="City")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id", nullable=true)
     * @Groups({"vote"})
     */
    protected $city;

    /**
     * @ORM\ManyToOne(targetEntity="TREVoter")
     * @ORM\JoinColumn(name="tre_voter_id", referencedColumnName="id", nullable=true, unique=false)
     * @Groups({"vote"})
     */
    protected $treVoter;

    /**
     * @ORM\Column(name="mobile", type="string", nullable=true)
     */
    protected $mobile;

    /**
     * @ORM\Column(name="badges",type="array", nullable=true)
     */
    protected $badges;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="login_cidadao_updated_at", type="datetime", nullable=true)
     */
    protected $loginCidadaoUpdatedAt;

    /**
     * @var boolean
     * @ORM\Column(name="login_cidadao_accept_registration", type="boolean", nullable=true)
     */
    protected $loginCidadaoAcceptRegistration;

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
     * Set firstName
     *
     * @param string $firstName
     * @return Person
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set loginCidadaoId
     *
     * @param string $loginCidadaoId
     * @return Person
     */
    public function setLoginCidadaoId($loginCidadaoId)
    {
        $this->loginCidadaoId = $loginCidadaoId;

        return $this;
    }

    /**
     * Get loginCidadaoId
     *
     * @return string
     */
    public function getLoginCidadaoId()
    {
        return $this->loginCidadaoId;
    }

    /**
     * Set loginCidadaoAccessToken
     *
     * @param string $loginCidadaoAccessToken
     * @return Person
     */
    public function setLoginCidadaoAccessToken($loginCidadaoAccessToken)
    {
        $this->loginCidadaoAccessToken = $loginCidadaoAccessToken;

        return $this;
    }

    /**
     * Get loginCidadaoAccessToken
     *
     * @return string
     */
    public function getLoginCidadaoAccessToken()
    {
        return $this->loginCidadaoAccessToken;
    }

    /**
     * Set loginCidadaoUsername
     *
     * @param string $loginCidadaoUsername
     * @return Person
     */
    public function setLoginCidadaoUsername($loginCidadaoUsername)
    {
        $this->loginCidadaoUsername = $loginCidadaoUsername;

        return $this;
    }

    /**
     * Get loginCidadaoUsername
     *
     * @return string
     */
    public function getLoginCidadaoUsername()
    {
        return $this->loginCidadaoUsername;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Person
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {

    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        if (!($this->getCreatedAt() instanceof \DateTime)) {
            $this->createdAt = new \DateTime();
        }
    }

    public function getCityOrTreCity()
    {
        $city = $this->getCity();
        $treVoter = $this->getTreVoter();
        if (is_null($city) && $treVoter instanceof TREVoter) {
            $city = $treVoter->getCity();
        }
        return $city;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function setCity(City $city = null)
    {
        $this->city = $city;

        return $this;
    }

    public function getTreVoter()
    {
        return $this->treVoter;
    }

    public function setTreVoter(TREVoter $var = null)
    {
        $this->treVoter = $var;

        return $this;
    }

    public function getMobile()
    {
        return $this->mobile;
    }

    public function setMobile($mobile)
    {
        $mobile = preg_replace('/[^0-9]/', '', $mobile);
        $this->mobile = $mobile;
    }

    public function getBadges()
    {
        return $this->badges;
    }

    public function setBadges($var)
    {
        $this->badges = $var;

        return $this;
    }

    public static function checkNamesEqual($name1, $name2)
    {
        $name1 = explode(' ', $name1);
        $name2 = explode(' ', $name2);
        $firstName1 = self::filter(reset($name1));
        $firstName2 = self::filter(reset($name2));

        return (mb_strtolower(trim($firstName1)) === mb_strtolower(trim($firstName2)));
    }

    private static function filter($value)
    {
        $map = array(
            'á' => 'a',
            'à' => 'a',
            'ã' => 'a',
            'â' => 'a',
            'é' => 'e',
            'ê' => 'e',
            'í' => 'i',
            'ó' => 'o',
            'ô' => 'o',
            'õ' => 'o',
            'ú' => 'u',
            'ü' => 'u',
            'ç' => 'c',
            'Á' => 'A',
            'À' => 'A',
            'Ã' => 'A',
            'Â' => 'A',
            'É' => 'E',
            'Ê' => 'E',
            'Í' => 'I',
            'Ó' => 'O',
            'Ô' => 'O',
            'Õ' => 'O',
            'Ú' => 'U',
            'Ü' => 'U',
            'Ç' => 'C',
            '-' => ''
        );

        return strtr($value, $map);
    }

    public function setLoginCidadaoUpdatedAt($var)
    {
        $this->loginCidadaoUpdatedAt = $var;
        return $this;
    }

    public function getLoginCidadaoUpdatedAt()
    {
        return $this->loginCidadaoUpdatedAt;
    }

    public function setLoginCidadaoRefreshToken($var)
    {
        $this->loginCidadaoRefreshToken = $var;
        return $this;
    }

    public function getLoginCidadaoRefreshToken()
    {
        return $this->loginCidadaoRefreshToken;
    }

    public function setLoginCidadaoAcceptRegistration($loginCidadaoAcceptRegistration)
    {
        $this->loginCidadaoAcceptRegistration = $loginCidadaoAcceptRegistration;
        return $this;
    }

    public function getLoginCidadaoAcceptRegistration()
    {
        return $this->loginCidadaoAcceptRegistration;
    }    
    
    public function getCheckList()
    {
        $badges = $this->getBadges();
        $return['item']['full_name'] = strlen($this->getFirstName()) > 0;
        $return['item']['email'] = isset($badges['login-cidadao.valid_email']) && $badges['login-cidadao.valid_email'];
        $return['item']['nfg_access_lvl'] = isset($badges['login-cidadao.nfg_access_lvl']) && $badges['login-cidadao.nfg_access_lvl'] >= 2;
        $return['item']['voter_registration'] = isset($badges['login-cidadao.voter_registration']) && $badges['login-cidadao.voter_registration'];
        if (!is_null($this->getLoginCidadaoUpdatedAt())) {
            $return['updated_at'] = $this->getLoginCidadaoUpdatedAt()->format('Y-m-d H:i:s');
        } else {
            $return['updated_at'] = null;
        }
        $return['code'] = ($return['item']['full_name'] && $return['item']['email'] && $return['item']['nfg_access_lvl'] && $return['item']['voter_registration']) ? 0 : 1;
        $return['msg'] = null;
        return $return;
    }

}

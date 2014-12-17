<?php

namespace PROCERGS\VPR\CountingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CuvPessoa
 *
 * @ORM\Table(name="legacy_cuv_pessoa", indexes={@ORM\Index(name="fk_pessoa_cuv_cidade", columns={"cuv_id_cidade"}), @ORM\Index(name="fk_pessoa_cuv_municipio", columns={"cuv_id_municipio"}), @ORM\Index(name="fk_cuv_pessoa_municipio", columns={"id_municipio"}), @ORM\Index(name="fk_cuv_pessoa_data_source", columns={"id_data_source"}), @ORM\Index(name="index_cuv_pessoa_name", columns={"name"}), @ORM\Index(name="index_cuv_pessoa_email", columns={"email"})})
 * @ORM\Entity
 */
class LegacyCuvPessoa
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=100, nullable=true)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=45, nullable=true)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=100, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="cep", type="string", length=8, nullable=true)
     */
    private $cep;

    /**
     * @var integer
     *
     * @ORM\Column(name="cuv_id_cidade", type="integer", nullable=true)
     */
    private $cuvIdCidade;

    /**
     * @var integer
     *
     * @ORM\Column(name="cuv_id_municipio", type="integer", nullable=true)
     */
    private $cuvIdMunicipio;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_municipio", type="integer", nullable=true)
     */
    private $idMunicipio;

    /**
     * @var integer
     *
     * @ORM\Column(name="cuv_id_estado", type="integer", nullable=true)
     */
    private $cuvIdEstado;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_data_source", type="integer", nullable=true)
     */
    private $idDataSource;



    /**
     * Set name
     *
     * @param string $name
     * @return CuvPessoa
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return CuvPessoa
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return CuvPessoa
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return CuvPessoa
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set cep
     *
     * @param string $cep
     * @return CuvPessoa
     */
    public function setCep($cep)
    {
        $this->cep = $cep;

        return $this;
    }

    /**
     * Get cep
     *
     * @return string 
     */
    public function getCep()
    {
        return $this->cep;
    }

    /**
     * Set cuvIdCidade
     *
     * @param integer $cuvIdCidade
     * @return CuvPessoa
     */
    public function setCuvIdCidade($cuvIdCidade)
    {
        $this->cuvIdCidade = $cuvIdCidade;

        return $this;
    }

    /**
     * Get cuvIdCidade
     *
     * @return integer 
     */
    public function getCuvIdCidade()
    {
        return $this->cuvIdCidade;
    }

    /**
     * Set cuvIdMunicipio
     *
     * @param integer $cuvIdMunicipio
     * @return CuvPessoa
     */
    public function setCuvIdMunicipio($cuvIdMunicipio)
    {
        $this->cuvIdMunicipio = $cuvIdMunicipio;

        return $this;
    }

    /**
     * Get cuvIdMunicipio
     *
     * @return integer 
     */
    public function getCuvIdMunicipio()
    {
        return $this->cuvIdMunicipio;
    }

    /**
     * Set idMunicipio
     *
     * @param integer $idMunicipio
     * @return CuvPessoa
     */
    public function setIdMunicipio($idMunicipio)
    {
        $this->idMunicipio = $idMunicipio;

        return $this;
    }

    /**
     * Get idMunicipio
     *
     * @return integer 
     */
    public function getIdMunicipio()
    {
        return $this->idMunicipio;
    }

    /**
     * Set cuvIdEstado
     *
     * @param integer $cuvIdEstado
     * @return CuvPessoa
     */
    public function setCuvIdEstado($cuvIdEstado)
    {
        $this->cuvIdEstado = $cuvIdEstado;

        return $this;
    }

    /**
     * Get cuvIdEstado
     *
     * @return integer 
     */
    public function getCuvIdEstado()
    {
        return $this->cuvIdEstado;
    }

    /**
     * Set idDataSource
     *
     * @param integer $idDataSource
     * @return CuvPessoa
     */
    public function setIdDataSource($idDataSource)
    {
        $this->idDataSource = $idDataSource;

        return $this;
    }

    /**
     * Get idDataSource
     *
     * @return integer 
     */
    public function getIdDataSource()
    {
        return $this->idDataSource;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}

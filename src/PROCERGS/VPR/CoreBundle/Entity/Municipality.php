<?php
namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="municipio")
 */
class Municipality
{
  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
  */
  public $codibge;
  /** @ORM\Column(type="string", length=75, nullable=true) */
  public $municipio;
  /** @ORM\Column(type="string", length=75, nullable=true) */
  public $mesoreg;
  /** @ORM\Column(type="string", length=75, nullable=true) */
  public $microreg;
  /** @ORM\Column(type="integer", nullable=true) */
  public $codcorede;
  /** @ORM\Column(type="string", length=75, nullable=true) */
  public $corede;
  /** @ORM\Column(type="decimal", precision=10, scale=7, nullable=true) */
  public $latitude;
  /** @ORM\Column(type="decimal", precision=10, scale=7, nullable=true) */
  public $longitude;
  /** @ORM\Column(type="string", nullable=true) */
  public $populacao;
  /** @ORM\Column(type="decimal", precision=10, scale=2, nullable=true) */
  public $codmunicipio;
  /** @ORM\Column(type="decimal", precision=10, scale=2, nullable=true) */
  public $pibPerCapita;
}
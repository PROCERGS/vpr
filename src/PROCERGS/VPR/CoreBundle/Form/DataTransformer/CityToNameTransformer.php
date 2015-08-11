<?php

namespace PROCERGS\VPR\CoreBundle\Form\DataTransformer;

use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\DataTransformerInterface;
use PROCERGS\VPR\CoreBundle\Entity\City;
use Doctrine\ORM\EntityManager;

class CityToNameTransformer implements DataTransformerInterface
{
    /** @var EntityManager */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     *
     * @param City $city
     * @return string
     */
    public function transform($city)
    {
        if (!($city instanceof City)) {
            return '';
        }

        return $city->getName();
    }

    public function reverseTransform($cityName)
    {
        if (!$cityName) {
            return;
        }

        $city = $this->em->getRepository('PROCERGSVPRCoreBundle:City')
            ->findOneBy(array('name' => $cityName));

        if ($city === null) {
            throw new TransformationFailedException("City not found!");
        }

        return $city;
    }
}

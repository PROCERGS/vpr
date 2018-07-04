<?php
namespace PROCERGS\VPR\CoreBundle\Twig;

use PROCERGS\VPR\CoreBundle\Entity\Poll;
use Doctrine\ORM\EntityManager;

class VPRExtension extends \Twig_Extension
{

    protected $em;

    public function __construct(EntityManager $var1)
    {
        $this->em = $var1;
    }

    public function getFunctions()
    {
        return array(
            'getActiveOrLastPoll' => new \Twig_Function_Method($this, 'getActiveOrLastPoll', array(
                'is_safe' => array(
                    'html'
                )
            ))
        );
    }
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('numberFormatPrecision', array($this, 'numberFormatPrecision')),
        );
    }
    public function numberFormatPrecision($number, $precision = 2, $separator = '.')
    {
        $numberParts = explode($separator, $number);
        $response = $numberParts[0];
        if(count($numberParts)>1){
            $response .= $separator;
            $response .= substr($numberParts[1], 0, $precision);
        }
        return $response;
    }
	
    private static $_poll = null;
    public function getActiveOrLastPoll()
    {
    	if (null === self::$_poll) {
    		$poll = $this->em->getRepository('PROCERGSVPRCoreBundle:Poll')->findActivePoll();
    		if (!$poll) {
    			$poll = $this->em->getRepository('PROCERGSVPRCoreBundle:Poll')->findLastPoll();
    		}
    		if (!$poll) {
    			$poll = new Poll();
    		}
    		self::$_poll = $poll;
    	}
    	return self::$_poll;
    }

    public function getName()
    {
        return 'vpr_twig_extension';
    }

}

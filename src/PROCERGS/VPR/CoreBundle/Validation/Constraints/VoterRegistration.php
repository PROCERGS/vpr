<?php

namespace PROCERGS\VPR\CoreBundle\Validation\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class VoterRegistration extends Constraint
{

    public $message = 'voter_registration.invalid';
}

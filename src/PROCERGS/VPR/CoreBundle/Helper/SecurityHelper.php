<?php

namespace PROCERGS\VPR\CoreBundle\Helper;

use PROCERGS\VPR\CoreBundle\Entity\Person;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class SecurityHelper
{
    /** @var AuthorizationCheckerInterface */
    private $security;

    public function __construct(AuthorizationCheckerInterface $security)
    {
        $this->security = $security;
    }

    public function getLoggedInUserLevel()
    {
        $level = 0;
        foreach ($this->getRoleMapping() as $role => $lvl) {
            if ($this->security->isGranted($role)) {
                $level = $lvl;
                break;
            }
        }

        return $level;
    }

    public function getTargetPersonLevel(Person $person)
    {
        $roles = $person->getRoles();
        $level = 0;
        foreach ($this->getRoleMapping() as $role => $lvl) {
            if (in_array($role, $roles)) {
                $level = $lvl;
                break;
            }
        }

        return $level;
    }

    public function getRoleLevel($role)
    {
        $map = $this->getRoleMapping();
        return $map[$role];
    }

    private function getRoleMapping()
    {
        $map = array(
            'ROLE_SUPER_ADMIN' => 8,
            'ROLE_ADMIN' => 7,
            'ROLE_MANAGE_USER' => 6,
            'ROLE_CRUD_BALLOTBOX' => 5,
            'ROLE_CRUD_POLL' => 4,
            'ROLE_STATS' => 3,
            'ROLE_COREDE' => 2,
            'ROLE_COORDINATOR' => 1,
            'ROLE_USER' => 0,
        );
        arsort($map);
        return $map;
    }
}

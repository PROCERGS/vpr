<?php

namespace PROCERGS\VPR\CoreBundle\Helper;

use PROCERGS\VPR\CoreBundle\Entity\Corede;
use PROCERGS\VPR\CoreBundle\Entity\PollOption;
use PROCERGS\VPR\CoreBundle\Entity\PollOptionRepository;

class PollOptionHelper
{
    /** @var PollOptionRepository */
    protected $pollOptionRepository;

    /**
     * PollOptionHelper constructor.
     * @param PollOptionRepository $pollOptionRepository
     */
    public function __construct(PollOptionRepository $pollOptionRepository)
    {
        $this->pollOptionRepository = $pollOptionRepository;
    }

    /**
     * @param mixed $ballotSeq
     * @param Corede $corede
     * @return array
     */
    public function ballotSeqToIds($ballotSeq, Corede $corede)
    {
        if (!is_array($ballotSeq)) {
            $ballotSeq = [$ballotSeq];
        }

        /** @var PollOption[] $pollOption */
        $pollOptions = $this->pollOptionRepository->findBy(
            ['categorySorting' => $ballotSeq, 'corede' => $corede]
        );

        $result = [];
        foreach ($pollOptions as $pollOption) {
            $result[] = $pollOption->getId();
        }

        return $result;
    }
}

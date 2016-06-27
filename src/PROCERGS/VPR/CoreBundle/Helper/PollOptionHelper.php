<?php

namespace PROCERGS\VPR\CoreBundle\Helper;

use PROCERGS\VPR\CoreBundle\Entity\Corede;
use PROCERGS\VPR\CoreBundle\Entity\Poll;
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
    public function ballotSeqToIds($ballotSeq, Poll $poll, Corede $corede)
    {
        /** @var PollOption[] $pollOption */
        $pollOptions = $this->pollOptionRepository->findByCategorySorting($ballotSeq, $poll, $corede);

        $result = [];
        foreach ($pollOptions as $pollOption) {
            $result[] = $pollOption->getId();
        }

        return $result;
    }
}

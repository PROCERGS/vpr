<?php

namespace PROCERGS\VPR\CoreBundle\Helper;

use PROCERGS\VPR\CoreBundle\Entity\Corede;
use PROCERGS\VPR\CoreBundle\Entity\Poll;
use PROCERGS\VPR\CoreBundle\Entity\PollOption;
use PROCERGS\VPR\CoreBundle\Entity\PollOptionRepository;
use PROCERGS\VPR\CoreBundle\Entity\Step;
use PROCERGS\VPR\CoreBundle\Exception\InvalidPollOptionsException;

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

        $steps = [];
        $result = [];
        foreach ($pollOptions as $pollOption) {
            $step = $pollOption->getStep();
            $steps[$step->getId()] = $step;
            $result[$step->getId()][] = $pollOption->getId();
        }

        $valid = [];
        foreach ($result as $stepId => $options) {
            /** @var Step $step */
            $step = $steps[$stepId];

            if ($this->pollOptionRepository->checkStepOptions($step, $options)) {
                $valid = array_merge($valid, $options);
            } else {
                throw new InvalidPollOptionsException("Selecionadas opções em excesso ou de uma etapa inválida.");
            }
        }

        return $valid;
    }
}

<?php

namespace PROCERGS\VPR\CoreBundle\Tests;

use Doctrine\ORM\EntityManager;
use PROCERGS\VPR\CoreBundle\Entity\BallotBox;
use PROCERGS\VPR\CoreBundle\Entity\Category;
use PROCERGS\VPR\CoreBundle\Entity\City;
use PROCERGS\VPR\CoreBundle\Entity\Corede;
use PROCERGS\VPR\CoreBundle\Entity\Poll;
use PROCERGS\VPR\CoreBundle\Entity\PollOption;
use PROCERGS\VPR\CoreBundle\Entity\Step;
use PROCERGS\VPR\CoreBundle\Entity\TREVoter;

class DatabasePopulator
{
    /** @var EntityManager */
    protected $em;

    /**
     * DatabasePopulator constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }


    public function populate()
    {
        $response = [];

        $poll = new Poll();
        $poll->setName('Test Poll')
            ->setOpeningTime(new \DateTime("-1 hour"))
            ->setClosingTime(new \DateTime("+1 hour"))
            ->setApurationTime(new \DateTime("+2 hours"))
            ->setDescription('Test Poll')
            ->generatePrivateAndPublicKeys();
        $response['pollPassphrase'] = $poll->getSecret();
        $this->em->persist($poll);
        $this->em->flush($poll);

        $ballotBox = new BallotBox();
        $response['ballotBoxPassphrase'] = $ballotBox->generatePassphrase();
        $ballotBox->setName('SMS BallotBox')
            ->setPoll($poll)
            ->setSecret($response['ballotBoxPassphrase'])
            ->setPin(1234)
            ->setIsOnline(false)
            ->setIsSms(true)
            ->setKeys();
        $this->em->persist($ballotBox);
        $this->em->flush($ballotBox);

        $corede = new Corede();
        $corede->setName('Metropolitano');
        $this->em->persist($corede);
        $this->em->flush($corede);

        $city = new City();
        $city->setName('Porto Alegre')
            ->setId(88013)
            ->setIsCapital(true)
            ->setCodSefa(96)
            ->setIbgeCode(4314902)
            ->setCorede($corede);
        $this->em->persist($city);
        $this->em->flush($city);
        $response['city'] = $city;

        $step = new Step();
        $step
            ->setName('Step 1')
            ->setPoll($poll)
            ->setMaxSelection(5)
            ->setSorting(1);
        $this->em->persist($step);
        $this->em->flush($step);

        $category = new Category();
        $category
            ->setName('Category 1')
            ->setTitleBg('')
            ->setIconBg('')
            ->setIconNum('')
            ->setOptionBg('')
            ->setSorting(1);
        $this->em->persist($category);
        $this->em->flush($category);

        $pollOptionsMapping = [
            10 => 'Opção 10',
            9 => 'Opção 9',
            8 => 'Opção 8',
            7 => 'Opção 7',
            6 => 'Opção 6',
            5 => 'Opção 5',
            4 => 'Opção 4',
            3 => 'Opção 3',
            2 => 'Opção 2',
            1 => 'Opção 1',
        ];

        $pollOptions = [];
        foreach ($pollOptionsMapping as $sortOrder => $title) {
            $pollOption = new PollOption();
            $pollOption
                ->setCategory($category)
                ->setCorede($corede)
                ->setStep($step)
                ->setTitle($title)
                ->setCategorySorting($sortOrder);
            $this->em->persist($pollOption);
            $this->em->flush($pollOption);
            $pollOptions[] = $pollOption;
        }
        $response['pollOptions'] = $pollOptions;

        return $response;
    }

    public function addVoter($voterRegistration, City $city)
    {
        $treVoter = new TREVoter($voterRegistration);
        $treVoter->setId($voterRegistration)
            ->setVotingZone(1)
            ->setName('Fulano de Tal')
            ->setCity($city)
            ->setCorede($city->getCorede())
            ->setCityName($city->getName())
            ->setCityCode($city->getId());
        $this->em->persist($treVoter);
        $this->em->flush($treVoter);
    }
}
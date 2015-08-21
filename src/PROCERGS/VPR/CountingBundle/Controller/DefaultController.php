<?php

namespace PROCERGS\VPR\CountingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PROCERGS\VPR\CoreBundle\Entity\Poll;
use PROCERGS\VPR\CoreBundle\Entity\Vote;
use JMS\Serializer\SerializationContext;

class DefaultController extends Controller
{

    /**
     * @Route("/count/{pollId}")
     * @Template()
     */
    public function indexAction($pollId)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(0);
        $logger = $this->get('logger');
        $em     = $this->getDoctrine()->getManager();
        $poll   = $em->getRepository('PROCERGSVPRCoreBundle:Poll')->find($pollId);

        $voteRepo = $em->getRepository('PROCERGSVPRCoreBundle:Vote');
        $votes    = $voteRepo->findBy(array('corede' => 19));
        //$votes = $voteRepo->findByPoll($poll);

        $privateKeyFile = $this->container->getParameter('privateKeyFile');
        $privatePollKey = openssl_pkey_get_private($privateKeyFile, 'test');
        $serializer     = $this->container->get('jms_serializer');
        $openVotes      = array();
        $votedOptions   = array();
        $optionsCount   = array();
        foreach ($votes as $vote) {
            $openVote    = $vote->openVote($privatePollKey);
            $openVotes[] = $openVote;
            $openOptions = $serializer->deserialize($openVote->getPlainOptions(),
                'ArrayCollection<PROCERGS\VPR\CoreBundle\Entity\PollOption>',
                'json');
            $openVote->setPollOption($openOptions);

            foreach ($openOptions as $option) {
                $id = 'option'.$option->getId();
                if (array_key_exists($id, $optionsCount)) {
                    $optionsCount[$id] += 1;
                } else {
                    $optionsCount[$id] = 1;
                }
                $votedOptions[$id] = $option;
                $logger->info("1 vote for $id");
            }
        }

        return compact('openVotes', 'votedOptions', 'optionsCount');
    }

    /**
     * @Route("/count/{pollId}/offline", name="vpr_count_load_offline")
     * @Template
     */
    public function loadOfflineAction(Request $request, $pollId)
    {
        $em = $this->getDoctrine()->getManager();

        $serializer = $this->get('jms_serializer');

        $builder = $this->createFormBuilder()
            ->add('votes', 'file');

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $data      = $form->getData();
            $votesFile = $this->parseOfflineVotes($data['votes']);

            $voterRepo     = $em->getRepository('PROCERGSVPRCoreBundle:TREVoter');
            $ballotBoxRepo = $em->getRepository('PROCERGSVPRCoreBundle:BallotBox');
            $coredeRepo    = $em->getRepository('PROCERGSVPRCoreBundle:Corede');
            $cityRepo      = $em->getRepository('PROCERGSVPRCoreBundle:City');
            $optionsRepo   = $em->getRepository('PROCERGSVPRCoreBundle:PollOption');

            $voters         = $this->findIndexed($voterRepo, 'id',
                $votesFile['voter_registrations']);
            $ballotBoxesId  = $this->findIndexed($ballotBoxRepo, 'id',
                $votesFile['ballot_boxes']);
            $ballotBoxesPin = $this->findIndexed($ballotBoxRepo, 'pin',
                $votesFile['pins']);
            $coredes        = $this->findIndexed($coredeRepo, 'id',
                $votesFile['coredes']);
            $cities         = $this->findIndexed($cityRepo, 'id',
                $votesFile['cities']);
            $options        = $this->findIndexed($optionsRepo, 'id',
                $votesFile['options']);

            $valid  = array();
            $errors = array();
            foreach ($votesFile['votes'] as $vote) {
                $isValid = true;

                $voterRegistration = $vote['voterRegistration'];
                if (!array_key_exists($voterRegistration, $voters)) {
                    $isValid  = false;
                    $errors[] = sprintf("[%s] Invalid Voter (%s)", $vote['id'],
                        $voterRegistration);
                    continue;
                }

                $ballotBoxId  = $ballotBoxesId[$vote['ballotBoxId']];
                $ballotBoxPin = $ballotBoxesPin[$vote['pin']];
                $corede       = $coredes[$vote['coredeId']];
                $city         = $cities[$vote['cityId']];
                $voter        = $voters[$voterRegistration];

                if ($city->getCorede()->getId() !== $corede->getId()) {
                    $isValid  = false;
                    $errors[] = sprintf("[%s] City (%s) doesn't belong to Corede (%s)",
                        $vote['id'], $city->getName(), $corede->getName());
                }
                if ($ballotBoxId->getCity()->getId() !== $city->getId()) {
                    $isValid  = false;
                    $errors[] = sprintf("[%s] Ballot Box (ID: %s) shouldn't be on this city (%s)",
                        $vote['id'], $ballotBoxId->getId(), $city->getName());
                }
                if ($voter->getCorede()->getId() !== $corede->getId()) {
                    $isValid  = false;
                    $errors[] = sprintf("[%s]Voter (ID: %s) shouldn't be on this Corede (%s)",
                        $vote['id'], $voter->getId(), $corede->getName());
                }
                if ($ballotBoxPin->getId() !== $ballotBoxId->getId()) {
                    $isValid  = false;
                    $errors[] = sprintf("[%s]Ballot Box and PIN don't match! (IDs: %s and %s) (PINs: %s and )",
                        $vote['id'], $ballotBoxId->getId(),
                        $ballotBoxPin->getId(), $ballotBoxId->getPin(),
                        $ballotBoxPin->getPin());
                }

                if ($isValid) {
                    $csv = sprintf('%s;%s;%s;%s;%s;%s;%s;%s;%s', $vote['id'],
                        $vote['createdAt'], $vote['closedAt'],
                        $vote['authType'], $vote['ballotBoxId'],
                        $vote['coredeId'], $vote['cityId'], $vote['options'],
                        $vote['voterRegistration']);

                    $valid[$vote['coredeId']][] = $csv;
                }
            }

            var_dump($errors);
            echo "<pre>";
            foreach ($valid as $corede) {
                echo implode(PHP_EOL, $corede);
                echo PHP_EOL.PHP_EOL.PHP_EOL.PHP_EOL;
            }
            die();

            return array();
        }

        return array('form' => $form->createView());
    }

    /**
     * @Route("/count/{pollId}/offline/encrypt", name="vpr_count_encrypt_offline")
     * @Template
     */
    public function encryptPreFilteredVotesAction(Request $request, $pollId)
    {
        $em = $this->getDoctrine()->getManager();

        $serializer = $this->get('jms_serializer');

        $builder = $this->createFormBuilder()
            ->add('votes', 'file');

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $data      = $form->getData();
            $votesFile = $this->parseOfflineVotes($data['votes']);

            $poll       = $em->getRepository('PROCERGSVPRCoreBundle:Poll')->find($pollId);
            $offlineIds = $em->getRepository('PROCERGSVPRCoreBundle:Vote')->getOfflineIds($poll);

            $ballotBoxRepo = $em->getRepository('PROCERGSVPRCoreBundle:BallotBox');
            $coredeRepo    = $em->getRepository('PROCERGSVPRCoreBundle:Corede');
            $cityRepo      = $em->getRepository('PROCERGSVPRCoreBundle:City');
            $optionsRepo   = $em->getRepository('PROCERGSVPRCoreBundle:PollOption');

            $ballotBoxesId = $this->findIndexed($ballotBoxRepo, 'id',
                $votesFile['ballot_boxes']);
            $coredes       = $this->findIndexed($coredeRepo, 'id',
                $votesFile['coredes']);
            $cities        = $this->findIndexed($cityRepo, 'id',
                $votesFile['cities']);
            $options       = $this->findIndexed($optionsRepo, 'id',
                $votesFile['options']);

            foreach ($votesFile['votes'] as $vote) {
                if (array_search($vote['id'], $offlineIds) !== false) {
                    continue;
                }
                $ballotBox = $ballotBoxesId[$vote['ballotBoxId']];
                $corede    = $coredes[$vote['coredeId']];
                $city      = $cities[$vote['cityId']];

                $context = SerializationContext::create()
                    ->setSerializeNull(true)
                    ->setGroups(array('vote'));

                $optionIds = $vote['options'];
                if ($optionIds !== "") {
                    $options = $optionsRepo->findById(explode(',', $optionIds));
                }
                $serializedOptions = $serializer->serialize($options, 'json',
                    $context);

                $voteObj = new Vote();
                $closed  = $voteObj->populateOfflineVote($vote, $ballotBox,
                    $corede, $city, $serializedOptions);

                $em->persist($closed);
                $em->flush();
                //die();
            }
        }

        return array('form' => $form->createView());
    }

    /**
     * Parse offline votes combined in a single file in the format:
     *     0         1                 2                 3            4         5         6       7           8
     * vote_id;data_hora_voto;data_hora_encerramento;auth_type;ballot_box_id;corede_id;city_id;options;voter_registration
     *
     * @param UploadedFile $file
     * @return type
     */
    private function parseOfflineVotes(UploadedFile $file)
    {
        $handle = $file->openFile();

        $votes = array(
            'voter_registrations' => array(),
            'ballot_boxes' => array(),
            'coredes' => array(),
            'cities' => array(),
            'pins' => array(),
            'options' => array()
        );
        while (($data  = $handle->fgetcsv(';')) !== FALSE) {
            if (count($data) < 8) {
                break;
            }


            $id                = $data[0];
            $voterRegistration = str_pad($data[8], 12, 0, STR_PAD_LEFT);
            $ballotBoxId       = $data[4];
            $coredeId          = $data[5];
            $cityId            = $data[6];

            if (preg_match('/^ballot_box\.([0-9]+)\./', $id, $m) === 1) {
                $pin = $m[1];
            } else {
                $pin = null;
            }

            $votes['voter_registrations'][$voterRegistration] = $voterRegistration;
            $votes['ballot_boxes'][$ballotBoxId]              = $ballotBoxId;
            $votes['coredes'][$coredeId]                      = $coredeId;
            $votes['cities'][$cityId]                         = $cityId;
            $votes['pins'][$pin]                              = $pin;
            $this->populateOptionsArray($data[7], $votes);

            $votes['votes'][] = array(
                'id' => $id,
                'pin' => $pin,
                'createdAt' => $data[1],
                'closedAt' => $data[2],
                'authType' => $data[3],
                'ballotBoxId' => $ballotBoxId,
                'coredeId' => $coredeId,
                'cityId' => $cityId,
                'options' => $data[7],
                'voterRegistration' => $voterRegistration
            );
        }

        return $votes;
    }

    private function findIndexed(\Doctrine\ORM\EntityRepository $repo, $index,
                                 $value)
    {
        $entities = $repo->findBy(array($index => $value));

        $result = array();
        foreach ($entities as $entity) {
            $method = 'get'.ucfirst($index);

            $result[$entity->$method()] = $entity;
        }

        return $result;
    }

    private function populateOptionsArray($options, &$votes)
    {
        foreach (explode(',', $options) as $option) {
            if (!is_numeric($option)) {
                continue;
            }
            $votes['options'][$option] = $option;
        }
    }

    private function validateOfflineVote(&$erros)
    {
        if ($city->getCorede()->getId() !== $corede->getId()) {
            $isValid  = false;
            $errors[] = sprintf("[%s] City (%s) doesn't belong to Corede (%s)",
                $vote['id'], $city->getName(), $corede->getName());
        }
        if ($ballotBoxId->getCity()->getId() !== $city->getId()) {
            $isValid  = false;
            $errors[] = sprintf("[%s] Ballot Box (ID: %s) shouldn't be on this city (%s)",
                $vote['id'], $ballotBoxId->getId(), $city->getName());
        }
        if ($voter->getCorede()->getId() !== $corede->getId()) {
            $isValid  = false;
            $errors[] = sprintf("[%s]Voter (ID: %s) shouldn't be on this Corede (%s)",
                $vote['id'], $voter->getId(), $corede->getName());
        }
        if ($ballotBoxPin->getId() !== $ballotBoxId->getId()) {
            $isValid  = false;
            $errors[] = sprintf("[%s]Ballot Box and PIN don't match! (IDs: %s and %s) (PINs: %s and )",
                $vote['id'], $ballotBoxId->getId(), $ballotBoxPin->getId(),
                $ballotBoxId->getPin(), $ballotBoxPin->getPin());
        }
    }
}

<?php

namespace PROCERGS\VPR\CoreBundle\Entity\Sms;

class TPDSmsVoteFactory
{
    /**
     * @param $sms
     * @return SmsVote
     */
    public static function createSmsVote($sms)
    {
        $date = \DateTime::createFromFormat('Y-m-d\TH:i:s.000-03:00', $sms->dataHoraRecebimento);
        $to = BrazilianPhoneNumberFactory::createFromE164("+{$sms->de}");

        $smsVote = new SmsVote();
        $smsVote
            ->setSmsId($sms->id)
            ->setSender($to->toE164())
            ->setMessage($sms->mensagem)
            ->setReceivedAt($date);

        return $smsVote;
    }
}

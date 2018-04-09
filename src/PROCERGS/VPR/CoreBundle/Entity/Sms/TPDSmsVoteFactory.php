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
        if (!$sms->date) {
            throw new \Exception("Nao foi enviado data hora recebimento ");
        }
        $date = \DateTime::createFromFormat('Y-m-d\TH:i:s', substr($sms->date, 0, 19) );
        if (!$date) {
            throw new \Exception("Formato de hora invalida: " . $sms->date);
        }
        $to = BrazilianPhoneNumberFactory::createFromE164("+{$sms->from}");

        $smsVote = new SmsVote();
        $smsVote
            ->setSmsId($sms->id)
            ->setSender($to->toE164())
            ->setMessage($sms->text)
            ->setReceivedAt($date);

        return $smsVote;
    }
}

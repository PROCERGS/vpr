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
        if (!$sms->dataHoraRecebimento) {
            throw new \Exception("Nao foi enviado data hora recebimento ");
        }
        $date = \DateTime::createFromFormat('Y-m-d\TH:i:s', substr($sms->dataHoraRecebimento, 0, 19) );
        if (!$date) {
            throw new \Exception("Formato de hora invalida: " . $sms->dataHoraRecebimento);
        }
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

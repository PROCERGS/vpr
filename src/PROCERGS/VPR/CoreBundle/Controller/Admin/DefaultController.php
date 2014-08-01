<?php

namespace PROCERGS\VPR\CoreBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Default controller.
 *
 * @Route("/")
 */
class DefaultController extends Controller
{

    /**
     * Lists all Person entities.
     *
     * @Route("/", name="admin")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        
        return array();
    }
    /**
    * @Route("/sendNotification", name="admin1")
    * @Method("GET")
    * @Template()
    */
    public function sendNotificationAction()
    {
        $user = $this->getUser();
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_HEADER, 1);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
        //curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->ch, CURLOPT_ENCODING, '');
        $url = $this->container->getParameter('login_cidadao_base_url').'/oauth/v2/token';
        $data = http_build_query(array(
            'grant_type' => 'client_credentials',
            'client_id' => $this->container->getParameter('login_cidadao_id'),
            'client_secret' => $this->container->getParameter('login_cidadao_secret'),
        ));
        $url .= '?' . $data;
        curl_setopt($this->ch, CURLOPT_URL, $url);
        $result = curl_exec($this->ch);
        $result = explode("\r\n\r\n", $result);
        $json = json_decode($result[1], true);
        $user->setLoginCidadaoAccessToken($json['access_token']);
        $user->setLoginCidadaoRefreshToken($json['refresh_token']);
        $this->getDoctrine()->getManager()->flush($user);
        
        $url = $this->container->getParameter('login_cidadao_base_url').'/api/v1/person/sendnotification';
        $url .= '?'. http_build_query(array(
            'access_token' => $json['access_token']
        ));
        for ($i =0; $i<500; $i++)
        $data1[] = array(
            'text' => str_repeat("text ", 100),
            'shorttext' => str_repeat("shorttext ", 10),
            'title' => str_repeat("title ", 10),
            'config_id' => 5,
            'person_id' => 46
        );
        curl_setopt($this->ch, CURLOPT_POST, 1);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, json_encode($data1));
        curl_setopt($this->ch, CURLOPT_URL, $url);
            $result = curl_exec($this->ch);
        die($result);
        $result = explode("\r\n\r\n", $result);
        $json = json_decode($result[1], true);
        curl_close($this->ch);
        
    }

}

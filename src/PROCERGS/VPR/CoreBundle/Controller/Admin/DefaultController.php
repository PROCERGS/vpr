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
            'grant_type' => 'refresh_token',
            'client_id' => $this->container->getParameter('login_cidadao_id'),
            'client_secret' => $this->container->getParameter('login_cidadao_secret'),
            'refresh_token' => $user->getLoginCidadaoRefreshToken()
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
        $data = http_build_query(array(
            'access_token' => $json['access_token']
        ));
        $url .= '?' . $data;
        $data = http_build_query(array(
            'text' => str_repeat("text ", 10),
            'shorttext' => str_repeat("shorttext ", 10),
            'title' => str_repeat("title ", 10),
            'id_config' => 5
        ));
        curl_setopt($this->ch, CURLOPT_POST, 1);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($this->ch, CURLOPT_URL, $url);
        $result = curl_exec($this->ch);
        die($result);
        $result = explode("\r\n\r\n", $result);
        $json = json_decode($result[1], true);
        curl_close($this->ch);
        
    }
    
    /**
     * @Route("/sendNotification2", name="admin2")
     * @Method("GET")
     * @Template()
     */
    public function sendNotification2Action()
    {
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
    
        $url = $this->container->getParameter('login_cidadao_base_url').'/api/v1/person/sendnotification';
        $data = http_build_query(array(
            'access_token' => $json['access_token']
        ));
        $url .= '?' . $data;
        $data = http_build_query(array(
            'text' => str_repeat("text ", 10),
            'shorttext' => str_repeat("shorttext ", 10),
            'title' => str_repeat("title ", 10),
            'id_config' => 5
        ));
        curl_setopt($this->ch, CURLOPT_POST, 1);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($this->ch, CURLOPT_URL, $url);
        $result = curl_exec($this->ch);
        die($result);
        $result = explode("\r\n\r\n", $result);
        $json = json_decode($result[1], true);
        curl_close($this->ch);
    
    }

}

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
        
        $url = $this->container->getParameter('login_cidadao_base_url').'/api/v1/person/notification.json';
        //Authorization: Bearer 
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, array(
        'Authorization: Bearer ' .$json['access_token']
            ));
            $data1 = array(
                'shortText' => str_repeat("shorttext ", 10),
                'title' => str_repeat("title ", 10),
                'category' => 1,
                'person' => 46,
                'sender' => 1,
                'icon' => 'oi',
                'placeholders' => array(
                    'linkclick' => 'venga cabron',
                    'linktitle' => 'senta o dedo aqui'
                ),
                'callbackUrl' => $this->generateUrl('procergsvpr_core_return_notification_login_cidadao', array(), true)
            );
            $bot = http_build_query($data1);
        curl_setopt($this->ch, CURLOPT_POST, 1);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $bot);
        curl_setopt($this->ch, CURLOPT_URL, $url);
            $result = curl_exec($this->ch);
        die($result);
        $result = explode("\r\n\r\n", $result);
        $json = json_decode($result[1], true);
        curl_close($this->ch);
        
    }
    
    /**
     * @Route("/queryCountry", name="admin2")
     * @Method("GET")
     * @Template()
     */
    public function queryCountryAction()
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
    
        $url = $this->container->getParameter('login_cidadao_base_url').'/api/v1/public/country';
        $url .= '?'. http_build_query(array(
            'access_token' => $json['access_token']
        ));
        curl_setopt($this->ch, CURLOPT_URL, $url);
        $result = curl_exec($this->ch);
        die($result);
        $result = explode("\r\n\r\n", $result);
        $json = json_decode($result[1], true);
        curl_close($this->ch);
    }
    
    /**
     * @Route("/queryUf", name="admin3")
     * @Method("GET")
     * @Template()
     */
    public function queryUfAction()
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
    
        $url = $this->container->getParameter('login_cidadao_base_url').'/api/v1/public/uf';
        $url .= '?'. http_build_query(array(
            'access_token' => $json['access_token']
        ));
        curl_setopt($this->ch, CURLOPT_URL, $url);
        $result = curl_exec($this->ch);
        die($result);
        $result = explode("\r\n\r\n", $result);
        $json = json_decode($result[1], true);
        curl_close($this->ch);
    }
    
    /**
     * @Route("/queryCity", name="admin4")
     * @Method("GET")
     * @Template()
     */
    public function queryCityAction()
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
    
        $url = $this->container->getParameter('login_cidadao_base_url').'/api/v1/public/city';
        $url .= '?'. http_build_query(array(
            'access_token' => $json['access_token']
        ));
        curl_setopt($this->ch, CURLOPT_URL, $url);
        $result = curl_exec($this->ch);
        die($result);
        $result = explode("\r\n\r\n", $result);
        $json = json_decode($result[1], true);
        curl_close($this->ch);
    }

}

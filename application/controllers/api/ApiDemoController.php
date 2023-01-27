<?php

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'libraries/RestController.php';
use chriskacerguis\RestServer\RestController;


class ApiDemoController extends RestController {
    public function __construct()
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin,Authorization, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if($method == "OPTIONS") {
            die();
        }
        parent::__construct();
        $this->load->database();
        $this->load->helper('security');
    }
    
    public function testAPICi_get()
    {
        $mdp = do_hash('123admin');
        $response = [
            'etat' => 'success',
             'status' => 'ok',
             'message' => 'Connexion ci3  succés !',
             'mdp' => $mdp,
         ];
         
         $this->response($response);
    }

}
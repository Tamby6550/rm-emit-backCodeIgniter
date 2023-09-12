<?php

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'libraries/RestController.php';
use chriskacerguis\RestServer\RestController;

class TestApi extends RestController
{
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
    }

    public function testapi(){
        $sql="select classe_nbre_etud as count from classe_tamby_app where classe_annee_univ='2021-2022' and classe_grade='2' and classe_mention='Informatique' and classe_niveau='M1' ";
        $query = $this->db->query($sql);

        $reponse = [
            'data' => $query
        ];

      
        $this->response($reponse, RestController::HTTP_OK);
    }
}
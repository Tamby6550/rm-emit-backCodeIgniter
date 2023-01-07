<?php

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'libraries/RestController.php';
use chriskacerguis\RestServer\RestController;

class Mention extends RestController
{
    public function __construct()
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if($method == "OPTIONS") {
            die();
        }
        parent::__construct();
        $this->load->database();
    

    }

    public function getMention_get()//Select distinct Mention
    {
        $this->db->distinct();
        $this->db->select('nom_mention');
        $this->db->select('libmention');
        $query = $this->db->get("mentions");
        $element_const = $query->result();

        // if ($element_const) {
            $this->response($element_const, RestController::HTTP_OK);
        // } else {
        //     $this->response([
        //         'status' => false,
        //         'message' => 'Aucun enregistrement trouvé',
        //     ]);
        // }
    }

    //Ajout Mention
    public function ajoutMention_post()
    {

        //Maka Json
        $data = json_decode(file_get_contents('php://input'), true);


        // /*Maka Max ny id_niveau */
        $this->db->select_max('id_mention');
        $query = $this->db->get('mentions');
        $result = $query->row_array();
        $max = $result['id_mention'];
        $maxIdClasse = $max + 1;

        $data['id_mention']=$maxIdClasse;
        
        //Enregistrena
        $this->db->insert('mentions', $data);

        $response = [
           
            'etat' => 'success',
            'status' => $data,
            'message' => 'Enregistrement  succés !',
        ];
        $this->response($response);
    }

    public function getMentionParcours_get()
    {
        $query = $this->db->get("mention_parcours");
        $element_const = $query->result();

        // if ($element_const) {
            $this->response($element_const, RestController::HTTP_OK);
        // } else {
        //     $this->response([
        //         'status' => false,
        //         'message' => 'Aucun enregistrement trouvé',
        //     ]);
        // }
    }

   
     //Ajout Class
     public function ajoutMentionParcours_post()
     {
         //Maka Json
         $data = json_decode(file_get_contents('php://input'), true);
 
         $dataParcours=array();
         
 
         //Element dans table classe
         $dataParcours['parcours']=$data['parcours'];
         $dataParcours['libparcours']=$data['libparcours'];

         //Cles etrangère
         $dataParcours['id_mention']=$data['id_mention'];         
 
         // /*Maka Max ny id_niveau */
         $this->db->select_max('id_parcours');
         $query = $this->db->get('parcours');
         $result = $query->row_array();
         $max = $result['id_parcours'];
         $maxIdClasse = $max + 1;
  
         $dataParcours['id_parcours']=$maxIdClasse;
         
         //Enregistrena
         $this->db->insert('parcours', $dataParcours);
 
         $response = [
             'status' => $data,
             'message' => 'Enregistrement  succés !',
         ];
         $this->response($response);
     }
}
<?php

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'libraries/RestController.php';
use chriskacerguis\RestServer\RestController;

class Classe extends RestController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }


    public function getClasse_get()
    {
        $query = $this->db->get("classe");
        $element_const = $query->result();

        if ($element_const) {
            $this->response($element_const, RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Aucun enregistrement trouvé',
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }

     //Ajout Class
     public function ajoutClass_post()
     {
         //Maka Json
         $data = json_decode(file_get_contents('php://input'), true);

         $dataClasse=array();
 
         //Element dans table classe
         $dataClasse['libelle_classe']=$data['libelle_classe'];
         $dataClasse['nbre_etud']=$data['nbre_etud'];
         $dataClasse['anne_scolaire']=$data['anne_scolaire'];
         
         //id_niveau (cles etrangère)
         $dataClasse['id_mention']=$data['id_mention'];
 
 
         // /*Maka Max ny id_elemnt */
         $this->db->select_max('id_classe');
         $query = $this->db->get('classe');
         $result = $query->row_array();
         $max = $result['id_classe'];
         $maxIdClasse = $max + 1;
 
         $dataClasse['id_classe']=$maxIdClasse;
         
         //Enregistrena
         $this->db->insert('classe', $dataClasse);
 
         $response = [
             'status' => $data,
             'message' => 'Enregistrement  succés !',
         ];
         $this->response($response);
     }

}
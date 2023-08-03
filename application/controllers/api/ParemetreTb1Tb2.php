<?php
defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'libraries/RestController.php';
use chriskacerguis\RestServer\RestController;

class ParemetreTb1Tb2 extends RestController
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
        $this->load->library('Authorization_Token');	
    }
   

    //Ajout engagement_tamby
    public function ajoutTableau1_post()
    {
       $headers = $this->input->request_headers(); 
       if (isset($headers['Authorization'])) {
           $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
           if ($decodedToken['status'])
           {
               //Maka Json
               $data = json_decode(file_get_contents('php://input'), true);

               $dataTableauA=array();

               //Element dans le details
               $dataTableauA['anne_univ']=$data['anne_univ'];
               $dataTableauA['et_tableau_a']=$data['et_tableau_a'];
               $dataTableauA['ed_tableau_a']=$data['ed_tableau_a'];
               $dataTableauA['ep_tableau_a']=$data['ep_tableau_a'];
               $dataTableauA['seme_id']=$data['seme_id'];
               $dataTableauA['mention_nom']=$data['mention_nom'];
               $dataTableauA['niv_id']=$data['niv_id'];
               $dataTableauA['parcours_nom']=$data['parcours_nom'];

               try {
                   $this->db->where('niv_id', $dataTableauA['niv_id']);
                   $this->db->where('anne_univ', $dataTableauA['anne_univ']);
                   $this->db->where('seme_id', $dataTableauA['seme_id']);
                   $this->db->where('mention_nom', $dataTableauA['mention_nom']);
                   $verf=$this->db->get('tambytableau_a_default');
                   $res_verf = $verf->result();

                   //raha efa misy de atao mis a jour
                   if ($res_verf) {
                       $this->db->set('et_tableau_a', $dataTableauA['et_tableau_a']);
                       $this->db->set('ed_tableau_a', $dataTableauA['ed_tableau_a']);
                       $this->db->set('ep_tableau_a', $dataTableauA['ep_tableau_a']);
                       $this->db->where('niv_id', $dataTableauA['niv_id']);
                       $this->db->where('anne_univ', $dataTableauA['anne_univ']);
                       $this->db->where('seme_id', $dataTableauA['seme_id']);
                       $this->db->where('mention_nom', $dataTableauA['mention_nom']);
                       $this->db->update('tambytableau_a_default');

                       $response = [
                            'etat' => 'success',
                            'situation' => 'Mis à jour ',
                            'message' => 'Mis à jour succés !',
                            'sql' => $res_verf,
                            ];
                        $this->response($response);
                   }
                    //Insertion dans la table detailstamby
                    $this->db->insert('tambytableau_a_default', $dataEngagement);
               } catch (\Throwable $th) {
                  //Insertion dans la table detailstamby
               //    $this->db->insert('detailstamby', $dataEngagement);

               }
               
               $response = [
                   'etat' => 'success',
                   'situation' => 'Enregistrement Engagement',
                   'message' => 'Mis à jour succé !',
                   'sql' => $res_verf,
                   
               ];
               $this->response($response);
           }
           else {
               $this->response($decodedToken);
           }
       }
       else {
           $this->response(['Authentication failed'], RestController::HTTP_OK);
       }
    }
}
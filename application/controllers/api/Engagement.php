<?php
defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'libraries/RestController.php';
use chriskacerguis\RestServer\RestController;

class Engagement extends RestController
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
    public function ajoutEngagement_post()
    {
       $headers = $this->input->request_headers(); 
       if (isset($headers['Authorization'])) {
           $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
           if ($decodedToken['status'])
           {
               //Maka Json
               $data = json_decode(file_get_contents('php://input'), true);

               $dataEngagement=array();
               $datafaire=array();
               
               //Element dans le details
               $dataEngagement['id_enga']=$data['prof_id'].''.$data['grad_id'].''.$data['nom_enga'].''.$data['mention'].''.$data['annee_univ'];
               $dataEngagement['nom_enga']=$data['nom_enga'];
               $dataEngagement['nbre_etu']=$data['nbre_etu'];
               $dataEngagement['valeur']=$data['valeur'];
               $dataEngagement['date_engamnt1']=$data['date_engamnt1'];
               $dataEngagement['date_engamnt2']=$data['date_engamnt2'];
               $dataEngagement['grad_id']=$data['grad_id'];
               $dataEngagement['mention']=$data['mention'];

               $datafaire['prof_id']=$data['prof_id'];
               $datafaire['annee_univ']=$data['annee_univ'];
               $datafaire['id_enga']=$data['prof_id'].''.$data['grad_id'].''.$data['nom_enga'].''.$data['mention'].''.$data['annee_univ'];

               try {
                   $this->db->where('id_enga', $datafaire['id_enga']);
                   $this->db->where('annee_univ', $datafaire['annee_univ']);
                   $verf=$this->db->get('faire_engag_tamby');
                   $res_verf = $verf->result();

                   //raha efa misy de atao mis a jour
                   if ($res_verf) {
                       $this->db->set('nom_enga', $dataEngagement['nom_enga']);
                       $this->db->set('nbre_etu', $dataEngagement['nbre_etu']);
                       $this->db->set('valeur', $dataEngagement['valeur']);
                       $this->db->set('date_engamnt1', $dataEngagement['date_engamnt1']);
                       $this->db->set('date_engamnt2', $dataEngagement['date_engamnt2']);
                       $this->db->where('id_enga', $dataEngagement['id_enga']);
                       $this->db->update('engagement_tamby');
                   }
                   //Sinon inserena
                   else {
                       //Insertion dans la table detailstamby
                       $this->db->insert('engagement_tamby', $dataEngagement);
                       
                       //Insertion table faire
                       $this->db->insert('faire_engag_tamby', $datafaire);
                   }
                   
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

    public function getEngagement_get($prof_id,$grad_id,$mention)
     {
        $headers = $this->input->request_headers(); 
		if (isset($headers['Authorization'])) {
			$decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
            if ($decodedToken['status'])
            {
                $sql="select eng.nom_enga,eng.nbre_etu,eng.valeur,eng.date_engamnt1,eng.date_engamnt2 ,faire.annee_univ
                from engagement_tamby eng, faire_engag_tamby faire 
                where faire.id_enga=eng.id_enga 
                and faire.prof_id='".$prof_id."' and eng.grad_id='".$grad_id."' and eng.mention='".$mention."' ";
                $query3 = $this->db->query($sql);
                $res = $query3->result();

                $this->response($res, RestController::HTTP_OK);
            }
            else {
                $this->response($decodedToken);
            }
		}
		else {
			$this->response(['Authentication failed'], RestController::HTTP_OK);
		}
      
     }
    public function deleteEngagement_delete()
     {
        $headers = $this->input->request_headers(); 
		if (isset($headers['Authorization'])) { 
			$decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
            if ($decodedToken['status'])
            {
                $data = json_decode(file_get_contents('php://input'), true);

                $this->db->where('id_enga', $data['id_enga']);
                $this->db->delete('faire_engag_tamby');
                $this->db->where('id_enga', $data['id_enga']);
                $this->db->delete('engagement_tamby');
                // $this->db->delete('engagement_tamby');
                $response = [
                    'etat' => 'info',
                    'status' => 'Suppression reuissie',
                    'message' => 'L\'enregistrement bien supprimer ',
                ];
                $this->response($response, RestController::HTTP_OK);
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
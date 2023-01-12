<?php

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'libraries/RestController.php';
use chriskacerguis\RestServer\RestController;

class Matiere extends RestController
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

     //Affiche tous les proff
     public function getMatiereProfRm_get($rm_id,$mention_nom,$grad_id,$prof_id)
     {
        $sql="select prof_id,nom_prof,prof_contact,matiere,unite_ens,semestre ,abbr_niveau from mat_niv_parcours_prof_ue_semestre_associer_respmention";
        $condition=" Where nom_mention='".$mention_nom."' and grad_id='".$grad_id."' and rm_id='".$rm_id."' and prof_id='".$prof_id."' ";

        $headers = $this->input->request_headers(); 
		if (isset($headers['Authorization'])) {
			$decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
            if ($decodedToken['status'])
            {
                $sql=$sql . $condition;
                $query = $this->db->query($sql);
                $res = $query->result();
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
     //Affiche 
     public function getMatiereRm_get($rm_id,$mention_nom,$grad_id)
     {
        $sql="select prof_id,nom_prof,prof_contact,matiere,unite_ens,semestre,seme_code ,abbr_niveau from mat_niv_parcours_prof_ue_semestre_associer_respmention";
        $condition=" Where nom_mention='".$mention_nom."' and grad_id='".$grad_id."' and rm_id='".$rm_id."' order by abbr_niveau,seme_code ASC ";

        $headers = $this->input->request_headers(); 
		if (isset($headers['Authorization'])) {
			$decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
            if ($decodedToken['status'])
            {
                $sql=$sql . $condition;
                $query = $this->db->query($sql);
                $res = $query->result();
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

     public function ajouteDetailsMatiere_post($rm_id,$mention_nom,$grad_id)
     {
        $headers = $this->input->request_headers(); 
		if (isset($headers['Authorization'])) {
			$decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
            if ($decodedToken['status'])
            {
                //Convertie date aujourd'hui yyyy-mm-dd en yyyymmdd
                $today = date('Y-m-d');
                $dateConv = $today;
                $dateConv = str_replace('-', '', $dateConv);

                //Maka Json
                $data = json_decode(file_get_contents('php://input'), true);

                $dataDetails=array();
                $dataMatiere=array();
                $id_prof="";
        
                //Element dans le Matiere
                $dataMatiere['vheure']=$data['vheure'];
                $dataMatiere['credit']=$data['credit'];
                $dataMatiere['mati_id']=$data['mati_id'];
                
                //Element dans le details
                $dataDetails['base_et']=$data['base_et'];
                $dataDetails['group_et']=$data['group_et'];
                $dataDetails['base_ed']=$data['base_ed'];
                $dataDetails['group_ed']=$data['group_ed'];
                $dataDetails['base_ep']=$data['base_ep'];
                $dataDetails['group_ep']=$data['group_ep'];
                
                //Maka id_matiere 
                $id_prof=$data['mat_id'];
        
                //ny id_details ovaina ho date efa convertie + max id_matiere
                $dataDetails['id_details']= $dateConv."".$data['mat_id'];
                
                //Insertion dans la table detailstamby
                $this->db->insert('detailstamby', $dataDetails);
        

                $this->db->set('vheure', $dataMatiere['vheure']);
                $this->db->set('credit', $dataMatiere['credit']);
                $this->db->set('id_details', $dataDetails['id_details']);
                $this->db->where('mati_id', $dataMatiere['mati_id']);
                $this->db->update('matiere');
        
                
                $response = [
                    'etat' => 'success',
                    'situation' => 'Ajout Details',
                    'message' => 'Enregistrement  succés !',
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
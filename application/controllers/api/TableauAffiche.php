<?php

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'libraries/RestController.php';
use chriskacerguis\RestServer\RestController;

class TableauAffiche extends RestController
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
     public function getTableauAffiche($rm_id,$mention_nom,$grad_id,$prof_id)
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
}
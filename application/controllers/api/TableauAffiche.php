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


     //Affiche tous les infos tableau
     public function getTableauAffiche_get($anne_univ,$mention_nom,$niveau_id)
     {
        $sql="select info.mati_id,info.mat_libelle,info.semestre,info.nom_prof , det.* from mat_niv_parcours_prof_ue_semestre_associer_respmention info,
        anne_univ_tamby_rm anne,detailstamby det 
        where anne.mati_id=info.mati_id and anne.anne_lib='".$anne_univ."'  and anne.id_details=det.id_details and  niv_id='".$niveau_id."' and nom_mention='".$mention_nom."'";

        $sqlSemestre="select distinct semestre,seme_id  from mat_niv_parcours_prof_ue_semestre_associer_respmention info
        where info.anne_univ='".$anne_univ."'  and  info.niv_id='".$niveau_id."' and info.nom_mention='".$mention_nom."'";
        $headers = $this->input->request_headers(); 
		if (isset($headers['Authorization'])) {
			$decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
            if ($decodedToken['status'])
            {
                $query1 = $this->db->query($sqlSemestre);
                $res = $query1->result();

                for ($i=0; $i < count($res); $i++) { 
                    $sql3="SELECT info.mati_id,info.mat_libelle,info.semestre,info.nom_prof ,info.vheure,info.credit, det.* from mat_niv_parcours_prof_ue_semestre_associer_respmention info,
                    anne_univ_tamby_rm anne,detailstamby det 
                    where anne.mati_id=info.mati_id and anne.anne_lib='".$anne_univ."' AND info.seme_id='".$res[$i]->seme_id."'
                    AND anne.id_details=det.id_details and  niv_id='".$niveau_id."' and nom_mention='".$mention_nom."'";
                    $query3 = $this->db->query($sql3);
                    $req3 = $query3->result();
                    $res[$i]->details=$req3;
                }
                
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
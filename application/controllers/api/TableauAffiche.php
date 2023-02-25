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
     public function getTableauAffiche_get($anne_univ,$mention_nom,$niveau_id,$rm_id)
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
                    $sql3="SELECT  info.mati_id,info.mat_libelle,info.semestre,info.nom_prof ,info.vheure,info.credit, det.* 
                    from mat_niv_parcours_prof_ue_semestre_associer_respmention info,
                    anne_univ_tamby_rm anne,detailstamby det 
                    where anne.mati_id=info.mati_id and anne.anne_lib='".$anne_univ."' AND info.seme_id='".$res[$i]->seme_id."'
                    AND anne.id_details=det.id_details and  niv_id='".$niveau_id."'
                    AND nom_mention='".$mention_nom."' and info.rm_id='".$rm_id."'";
                    $query3 = $this->db->query($sql3);
                    $req3 = $query3->result();
                    $res[$i]->details=$req3;
                    $res[$i]->nombreM=count($req3);

                    $sql4="SELECT 
                    SUM(CAST(info.vheure AS DECIMAL)) AS tvheure , 
                    SUM(CAST(info.credit AS DECIMAL)) AS tcredit , 
                    SUM(CAST(det.base_et AS DECIMAL)) AS tbase_et,
                    SUM(CAST(det.total_et AS DECIMAL)) AS ttotal_et,
                    SUM(CAST(det.total_ed AS DECIMAL)) AS ttotal_ed,
                    SUM(CAST(det.total_ep AS DECIMAL)) AS ttotal_ep
                    from mat_niv_parcours_prof_ue_semestre_associer_respmention info,
                    anne_univ_tamby_rm anne,detailstamby det 
                    where anne.mati_id=info.mati_id and anne.anne_lib='".$anne_univ."' AND info.seme_id='".$res[$i]->seme_id."'
                    AND anne.id_details=det.id_details and  niv_id='".$niveau_id."' and nom_mention='".$mention_nom."' and info.rm_id='".$rm_id."'";
                    $query4 = $this->db->query($sql4);
                    $req4 = $query4->row_array();
                    $res[$i]->total=$req4;
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
     //Affiche classe et le nombre d'étudiant
     public function getTitreTableau_get($rm_id,$mention_nom,$niveau_id,$grad_id,$anne_univ)
     {
        $niveau='';
        if ($niveau_id=='1' || $niveau_id=='2' || $niveau_id=='3') {
            $niveau='L'.$niveau_id;
        }else if ($niveau_id=='4') {
            $niveau='M1';
        }else{
            $niveau='M2';

        }
       
        $headers = $this->input->request_headers(); 
		if (isset($headers['Authorization'])) {
			$decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
            if ($decodedToken['status'])
            {
                $sql="SELECT count(*) 
                FROM  public.affiche_etu_niv_parc_grad_rm where   niv_id='".$niveau_id."' and mention_nom='".$mention_nom."' and grad_id='".$grad_id."' and rm_id='".$rm_id."' and annee_univ='".$anne_univ."'
                group by niv_libelle ";
                $query = $this->db->query($sql);
                $res = $query->row_array();

                $sql1="Select * from groupe_tamby where anne_univ='".$anne_univ."' and niveau='".$niveau."' and mention='".$mention_nom."'";
                $query3 = $this->db->query($sql1);
                $group_tamby = $query3->row_array();

                $sql2="SELECT distinct nom_mention,parc_libelle,abbr_niveau from mat_niv_parcours_prof_ue_semestre_associer_respmention where
                anne_univ='".$anne_univ."' and niv_id='".$niveau_id."' and nom_mention='".$mention_nom."'
                and rm_id='".$rm_id."'";
                $query4 = $this->db->query($sql2);
                $info = $query4->row_array();

                $reponse = [
                    'nbreClasse' => $res,
                    'group_tamby' =>$group_tamby,
                    'info' =>$info,
                ];

              
                $this->response($reponse, RestController::HTTP_OK);
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
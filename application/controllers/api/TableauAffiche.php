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
     public function getTableauAffiche_get($parcours,$anne_univ,$mention_nom,$niveau_id,$rm_id)
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
                    AND nom_mention='".$mention_nom."' and info.nom_parcours='".$parcours."' and info.rm_id='".$rm_id."'";
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
                    AND anne.id_details=det.id_details and  niv_id='".$niveau_id."' and nom_mention='".$mention_nom."' and info.nom_parcours='".$parcours."' and info.rm_id='".$rm_id."'";
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
     public function getTableauAfficheSommeEtEdEp_get($anne_univ,$mention_nom,$niveau_id,$rm_id)
     {
        $headers = $this->input->request_headers(); 
		if (isset($headers['Authorization'])) {
			$decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
            if ($decodedToken['status'])
            {
                $sql4="SELECT 
                SUM(CAST(det.total_et AS DECIMAL)) AS ttotal_et,
                SUM(CAST(det.total_ed AS DECIMAL)) AS ttotal_ed,
                SUM(CAST(det.total_ep AS DECIMAL)) AS ttotal_ep
                from mat_niv_parcours_prof_ue_semestre_associer_respmention info,
                anne_univ_tamby_rm anne,detailstamby det 
                where anne.mati_id=info.mati_id and anne.anne_lib='".$anne_univ."' 
                AND anne.id_details=det.id_details and  niv_id='".$niveau_id."' and nom_mention='".$mention_nom."'  and info.rm_id='".$rm_id."'";
                $query4 = $this->db->query($sql4);
                $req4 = $query4->row_array();
                $this->response($req4, RestController::HTTP_OK);
            }
            else {
                $this->response($decodedToken);
            }
		}
		else {
			$this->response(['Authentication failed'], RestController::HTTP_OK);
		}
     }
     public function getTableauAfficheSommeEtEdEpParcours_get($parcours,$anne_univ,$mention_nom,$niveau_id,$rm_id)
     {
        $headers = $this->input->request_headers(); 
		if (isset($headers['Authorization'])) {
			$decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
            if ($decodedToken['status'])
            {
                $sql4="SELECT 
                SUM(CAST(det.total_et AS DECIMAL)) AS ttotal_et,
                SUM(CAST(det.total_ed AS DECIMAL)) AS ttotal_ed,
                SUM(CAST(det.total_ep AS DECIMAL)) AS ttotal_ep
                from mat_niv_parcours_prof_ue_semestre_associer_respmention info,
                anne_univ_tamby_rm anne,detailstamby det 
                where anne.mati_id=info.mati_id and anne.anne_lib='".$anne_univ."' 
                AND anne.id_details=det.id_details and  niv_id='".$niveau_id."' and nom_mention='".$mention_nom."' and info.nom_parcours='".$parcours."' and info.rm_id='".$rm_id."'";
                $query4 = $this->db->query($sql4);
                $req4 = $query4->row_array();
                $this->response($req4, RestController::HTTP_OK);
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
     public function getTitreTableau_get($parcours,$rm_id,$mention_nom,$niveau_id,$grad_id,$anne_univ)
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
                // $sql="SELECT count(*) 
                // FROM  public.affiche_etu_niv_parc_grad_rm where   niv_id='".$niveau_id."' and mention_nom='".$mention_nom."' and grad_id='".$grad_id."' and rm_id='".$rm_id."' and annee_univ='".$anne_univ."'
                // group by niv_libelle ";
                $sql="select classe_nbre_etud as count from classe_tamby_app where classe_annee_univ='".$anne_univ."' and classe_grade='".$grad_id."' and classe_mention='".$mention_nom."' and classe_niveau='".$niveau."' ";
                $query = $this->db->query($sql);
                $res = $query->row_array();

                $sql1="Select * from groupe_tamby where anne_univ='".$anne_univ."' and grade='".$grad_id."' and mention='".$mention_nom."'";
                $query3 = $this->db->query($sql1);
                $group_tamby = $query3->row_array();

                $sql2="SELECT distinct nom_mention,parc_libelle,abbr_niveau from mat_niv_parcours_prof_ue_semestre_associer_respmention where
                anne_univ='".$anne_univ."' and niv_id='".$niveau_id."' and nom_mention='".$mention_nom."'
                and nom_parcours='".$parcours."' and rm_id='".$rm_id."'";
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
     public function getTableauAfficheTableauA_get($parcours,$rm_id,$anne_univ,$mention_nom,$prof_id,$grad_id)
     {

        $headers = $this->input->request_headers(); 
		if (isset($headers['Authorization'])) {
			$decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
            if ($decodedToken['status'])
            {
                    $total=array();
                    $res=array();
                    $sql4="SELECT 
                    info.parc_libelle,
                    prof.prof_type,
                    prof.prof_titre,
                    prof.prof_grade,
                    SUM(CAST(info.vheure AS DECIMAL)) AS tvheure , 
                    SUM(CAST(det.base_et AS DECIMAL)) AS tbase_et,
                    SUM(CAST(det.base_ed AS DECIMAL)) AS tbase_ed,
                    SUM(CAST(det.base_ep AS DECIMAL)) AS tbase_ep,
                    SUM(CAST(det.total_et AS DECIMAL)) AS ttotal_et,
                    SUM(CAST(det.total_ed AS DECIMAL)) AS ttotal_ed,
                    SUM(CAST(det.total_ep AS DECIMAL)) AS ttotal_ep,
                        
                    (SUM(CAST(det.total_et AS DECIMAL))+SUM(CAST(det.total_ed AS DECIMAL))+SUM(CAST(det.total_ep AS DECIMAL))) as heureDeclare
                    from mat_niv_parcours_prof_ue_semestre_associer_respmention info,
                    anne_univ_tamby_rm anne,detailstamby det ,professeur prof
                    where info.prof_id=prof.prof_id and anne.mati_id=info.mati_id and anne.anne_lib='".$anne_univ."' 
                    AND anne.id_details=det.id_details AND nom_mention='".$mention_nom."' and anne.prof_id='".$prof_id."' and info.grad_id='".$grad_id."'  and info.nom_parcours='".$parcours."'  and info.rm_id='".$rm_id."'
                    group by info.parc_libelle,prof.prof_type,prof.prof_titre,prof.prof_grade";
                    $query4 = $this->db->query($sql4);
                    $total = $query4->row_array();

                    $sql3="SELECT distinct info.niv_id,info.abbr_niveau,info.mati_id,info.mat_libelle,info.nom_prof ,info.vheure, det.* 
                    from mat_niv_parcours_prof_ue_semestre_associer_respmention info,
                    anne_univ_tamby_rm anne,detailstamby det 
                    where anne.mati_id=info.mati_id and anne.anne_lib='".$anne_univ."' 
                    AND anne.id_details=det.id_details AND nom_mention='".$mention_nom."' and anne.prof_id='".$prof_id."' and info.nom_parcours='".$parcours."' and info.grad_id='".$grad_id."' ";
                    $query3 = $this->db->query($sql3);
                    $res= $query3->result();

                    $sql="Select * from groupe_tamby where anne_univ='".$anne_univ."' and grade='".$grad_id."' and mention='".$mention_nom."'";
                    $query5 = $this->db->query($sql);
                    $group_tamby = $query5->row_array();

                    
                    for ($i=0; $i < count($res); $i++) { 
                        $niveau='';
                        if ($res[$i]->niv_id=='1' || $res[$i]->niv_id=='2' || $res[$i]->niv_id=='3') {
                            $niveau='L'.$res[$i]->niv_id;
                        }else if ($res[$i]->niv_id=='4') {
                            $niveau='M1';
                        }else{
                            $niveau='M2';
                        }
                        // $sql="SELECT count(*) 
                        // FROM  public.affiche_etu_niv_parc_grad_rm where   niv_id='".$res[$i]->niv_id."' and mention_nom='".$mention_nom."' and grad_id='".$grad_id."' and rm_id='".$rm_id."' and annee_univ='".$anne_univ."'
                        // group by niv_libelle ";
                        $sql="select classe_nbre_etud as count from classe_tamby_app where classe_annee_univ='".$anne_univ."' and classe_grade='".$grad_id."' and classe_mention='".$mention_nom."' and classe_niveau='".$niveau."'";
                        $query = $this->db->query($sql);
                        $nbre_classe = $query->row_array();
                        $res[$i]->nbgroup=$nbre_classe;
                    }
                    $reponse = [
                        'total' => $total,
                        'detail' =>$res,
                        'group_tamby' =>$group_tamby,
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
     public function getTableauAfficheTableauB_get($parcours,$rm_id,$anne_univ,$mention_nom,$prof_id,$grad_id)
     {
        $headers = $this->input->request_headers(); 
		if (isset($headers['Authorization'])) {
			$decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
            if ($decodedToken['status'])
            {
                    $total=array();
                    $res=array();
                    $sql4="SELECT 
                    info.parc_libelle,
                    prof.prof_type,
                    prof.prof_titre,
                    prof.prof_grade,
                    SUM(CAST(det.total_et AS DECIMAL)) AS ttotal_et,
                    SUM(CAST(det.total_ed AS DECIMAL)) AS ttotal_ed,
                    SUM(CAST(det.total_ep AS DECIMAL)) AS ttotal_ep,
                    
                    (select SUM(CAST(eng.valeur AS DECIMAL)) from engagement_tamby eng,faire_engag_tamby faire 
                     where faire.id_enga=eng.id_enga and faire.prof_id='".$prof_id."' and faire.annee_univ='".$anne_univ."' and eng.grad_id='".$grad_id."') as total_enga,
                                
                    (SUM(CAST(det.total_ed AS DECIMAL))+(select SUM(CAST(eng.valeur AS DECIMAL)) from engagement_tamby eng,faire_engag_tamby faire 
                     where faire.id_enga=eng.id_enga and faire.prof_id='".$prof_id."' and faire.annee_univ='".$anne_univ."' and eng.grad_id='".$grad_id."')) as total_ed_enga,
                    
                    (SUM(CAST(det.total_et AS DECIMAL))+SUM(CAST(det.total_ed AS DECIMAL))+SUM(CAST(det.total_ep AS DECIMAL))+
                    (select SUM(CAST(eng.valeur AS DECIMAL)) from engagement_tamby eng,faire_engag_tamby faire 
                     where faire.id_enga=eng.id_enga and faire.prof_id='".$prof_id."' and faire.annee_univ='".$anne_univ."' and eng.grad_id='".$grad_id."')
                     ) as heureDeclare
                     
                    from mat_niv_parcours_prof_ue_semestre_associer_respmention info,
                    anne_univ_tamby_rm anne,detailstamby det ,professeur prof
                    where info.prof_id=prof.prof_id and anne.mati_id=info.mati_id and anne.anne_lib='".$anne_univ."' 
                    AND anne.id_details=det.id_details AND nom_mention='".$mention_nom."' and anne.prof_id='".$prof_id."' and info.grad_id='".$grad_id."' and info.nom_parcours='".$parcours."' and info.rm_id='".$rm_id."'
                    group by info.parc_libelle,prof.prof_type,prof.prof_titre,prof.prof_grade";
                    $query4 = $this->db->query($sql4);
                    $total = $query4->row_array();

                    $sql3="select * from engagement_tamby eng,faire_engag_tamby faire where faire.id_enga=eng.id_enga and faire.prof_id='".$prof_id."'
                    and faire.annee_univ='".$anne_univ."' and eng.grad_id='".$grad_id."' ";
                    $query3 = $this->db->query($sql3);
                    $res= $query3->result();


                    $reponse = [
                        'total' => $total,
                        'detail' =>$res,
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
     public function getTableauAfficheTableauFinale_get($rm_id,$anne_univ,$mention_nom,$grad_id)
     {
        $headers = $this->input->request_headers(); 
		if (isset($headers['Authorization'])) {
			$decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
            if ($decodedToken['status'])
            {
                    $total=array();
                    $res=array();
                    $sql4="SELECT 
                    info.prof_id,
                    info.nom_prof,
                    info.prof_grade,
                    info.attribution,
                    SUM(CAST(det.total_et AS DECIMAL)) AS ttotal_et,
                    SUM(CAST(det.total_ed AS DECIMAL)) AS ttotal_ed,
                    SUM(CAST(det.total_ep AS DECIMAL)) AS ttotal_ep,
                             
                    (select SUM(CAST(eng.valeur AS DECIMAL)) 
                    from engagement_tamby eng,faire_engag_tamby faire where faire.id_enga=eng.id_enga and faire.prof_id=info.prof_id
                    and faire.annee_univ='".$anne_univ."' and eng.grad_id='".$grad_id."' and eng.mention='".$mention_nom."' and eng.nom_enga like '%Encadrement%') as encadrement,
                                
                    (select SUM(CAST(eng.valeur AS DECIMAL))  
                    from engagement_tamby eng,faire_engag_tamby faire where faire.id_enga=eng.id_enga and faire.prof_id=info.prof_id
                    and faire.annee_univ='".$anne_univ."' and eng.grad_id='".$grad_id."' and eng.mention='".$mention_nom."' and eng.nom_enga like '%Soutenance%') as soutenance,
                    
                    (select SUM(CAST(eng.valeur AS DECIMAL)) 
                    from engagement_tamby eng,faire_engag_tamby faire where faire.id_enga=eng.id_enga and faire.prof_id=info.prof_id
                    and faire.annee_univ='".$anne_univ."' and eng.grad_id='".$grad_id."' and eng.mention='".$mention_nom."' and eng.nom_enga like '%Voyages%') as voyages
                    
                    from mat_niv_parcours_prof_ue_semestre_associer_respmention info,
                    anne_univ_tamby_rm anne,detailstamby det ,professeur prof
                    where info.prof_id=prof.prof_id and anne.mati_id=info.mati_id and anne.anne_lib='".$anne_univ."'
                    AND anne.id_details=det.id_details AND nom_mention='".$mention_nom."'  and info.grad_id='".$grad_id."' and  info.rm_id='".$rm_id."'
                    group by info.nom_prof,info.prof_id,info.prof_grade,info.attribution ";
                    $query4 = $this->db->query($sql4);
                    $res = $query4->result();

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
     public function getParcoursDansMention_get($mention_nom,$grad_id)
     {
        $headers = $this->input->request_headers(); 
		if (isset($headers['Authorization'])) {
			$decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
            if ($decodedToken['status'])
            {

                    $sql4="SELECT distinct parc_id, mention_nom, parc_nom, parc_libelle, grad_id
                    FROM public.parcours where grad_id='".$grad_id."' and mention_nom='".$mention_nom."' ";

                    $query4 = $this->db->query($sql4);
                    $res = $query4->result();

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
     public function gettableau1_get($anne_univ,$mention_nom,$nom_parcours)
     {
        $headers = $this->input->request_headers(); 
		if (isset($headers['Authorization'])) {
			$decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
            if ($decodedToken['status'])
            {

                $sqlNiveau="SELECT distinct niv_id,abbr_niveau from mat_niv_parcours_prof_ue_semestre_associer_respmention 
                where anne_univ=? and nom_mention=? and nom_parcours=? ";

                $sqlDetails="SELECT 
                    trim(sem.seme_code) as nomsemestre ,
                    tb1.et_tableau_a as et,
                    tb1.ed_tableau_a as ed,
                    tb1.ep_tableau_a as ep
                    from tambytableau_a_default tb1 inner join semestre sem on sem.seme_id=tb1.seme_id 
                    where anne_univ=? and tb1.mention_nom=? and tb1.parcours_nom=? AND tb1.niv_id=? ";

                $query1 = $this->db->query($sqlNiveau,[$anne_univ,$mention_nom,$nom_parcours]);
                $resultat = $query1->result();
                    
                for ($i=0; $i < count($resultat); $i++) {
                    $query2 = $this->db->query($sqlDetails,[$anne_univ,$mention_nom,$nom_parcours,$resultat[$i]->niv_id]);
                    $resdetails = $query2->result();
                    $resultat[$i]->details=$resdetails;
                }

                $this->response($resultat, RestController::HTTP_OK);
            }
            else {
                $this->response($decodedToken);
            }
		}
		else {
			$this->response(['Authentication failed'], RestController::HTTP_OK);
		}
     }
     public function gettableau2_get($anne_univ,$mention_nom,$nom_parcours)
     {
        $headers = $this->input->request_headers(); 
		if (isset($headers['Authorization'])) {
			$decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
            if ($decodedToken['status'])
            {

                $sqlNiveau="SELECT distinct niv_id,abbr_niveau from mat_niv_parcours_prof_ue_semestre_associer_respmention 
                where anne_univ=? and nom_mention=? and nom_parcours=? ";

                $sqlDetails="SELECT 
                    tb2.travaux as travail,
                    tb2.et_tableau_b as et,
                    tb2.ed_tableau_b as ed,
                    tb2.ep_tableau_b as ep
                    from tambytableau_b_default tb2 
                    where anne_univ=? and tb2.mention_nom=? and tb2.parcours_nom=? AND tb2.niv_id=?";

                $query1 = $this->db->query($sqlNiveau,[$anne_univ,$mention_nom,$nom_parcours]);
                $resultat = $query1->result();
                    
                for ($i=0; $i < count($resultat); $i++) {
                    $query2 = $this->db->query($sqlDetails,[$anne_univ,$mention_nom,$nom_parcours,$resultat[$i]->niv_id]);
                    $resdetails = $query2->result();
                    $resultat[$i]->details=$resdetails;
                }

                $this->response($resultat, RestController::HTTP_OK);
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
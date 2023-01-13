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
     public function getMatiereRm_get($rm_id,$mention_nom,$grad_id,$anne_univ,$niv_id,$etat)
     {
         
         $sql="select prof_id,nom_prof,prof_contact,matiere,mati_id,etat as etat_mat,ue_code,unite_ens,semestre,seme_code ,abbr_niveau from mat_niv_parcours_prof_ue_semestre_associer_respmention";
         $headers = $this->input->request_headers(); 
         if (isset($headers['Authorization'])) {
             $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
             if ($decodedToken['status'])
             {
                 $reponse=array();
                 if ($etat=='5') {//rehefa tsy manao recherche 
                    $condition=" Where nom_mention='".$mention_nom."' and grad_id='".$grad_id."' and rm_id='".$rm_id."' and anne_univ='".$anne_univ."' and niv_id='".$niv_id."' order by ue_code,matiere ASC ";
                }else{
                    $condition=" Where nom_mention='".$mention_nom."' and grad_id='".$grad_id."' and rm_id='".$rm_id."' and anne_univ='".$anne_univ."' and niv_id='".$niv_id."' and etat='".$etat."' order by ue_code,matiere ASC ";
                }
                $sql=$sql . $condition;
                $query = $this->db->query($sql);
                $res = $query->result();
                
                $this->db->select("annee_univ  as label");
                $this->db->select('annee_univ as value');
                $queryanne_univ = $this->db->get("annee_univ");
                $anne_univ = $queryanne_univ->result();


                $sqlniveau ="select distinct niv_id as value ,abbr_niveau as label from mat_niv_parcours_prof_ue_semestre_associer_respmention Where nom_mention='".$mention_nom."' and grad_id='".$grad_id."' and rm_id='".$rm_id."'"; 
                $queryniveau =  $this->db->query($sqlniveau);
                $niveau = $queryniveau->result();

                $reponse = [
                    'matiere' => $res,
                    'anne_univ' =>$anne_univ,
                    'niveau' =>$niveau,
                    'sql' =>$sql
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
     public function updateEtatMatiere_put()
     {

        $headers = $this->input->request_headers(); 
		if (isset($headers['Authorization'])) {
			$decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
            if ($decodedToken['status'])
            {
               // Récupérer les données de la requête
                $data = json_decode(file_get_contents('php://input'), true);
                $etat_re='';
                // Mettre à jour l'enregistrement dans la base de données
                $this->db->set('etat', $data['etat']);
                $this->db->where('mati_id', $data['mati_id']);
                $this->db->where('anne_lib', $data['anne_univ']);
                $this->db->update('anne_univ_tamby_rm');
                if ($data['etat']=='0'||$data['etat']==null) {
                   $etat_re='Pas en encore demaré !';
                }
                if ($data['etat']=='1') {
                   $etat_re='En cours !';
                }
                if ($data['etat']=='2') {
                   $etat_re='Términé !';
                }
                $response = [
                    'etat' => 'success',
                    'situation' => 'Modification etat de matière',
                    'message' => $data['nom_mat'].' : '.$etat_re,
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
<?php

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'libraries/RestController.php';
use chriskacerguis\RestServer\RestController;

class Proff_api extends RestController
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


    // $headers = $this->input->request_headers(); 
    // if (isset($headers['Authorization'])) {
    //     $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
    //     if ($decodedToken['status'])
    //     {
    //        Code ok
    //     }
    //     else {
    //         $this->response($decodedToken);
    //     }
    // }
    // else {
    //     $this->response(['Authentication failed'], RestController::HTTP_OK);
    // }
    //Affiche tous les proff
    public function afficheProff_get($rm_id,$mention_nom,$grad_id)
    {
        $headers = $this->input->request_headers(); 
        if (isset($headers['Authorization'])) {
            $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
            if ($decodedToken['status'])
            {
                
                $this->db->select("prof_id")->distinct();
                $this->db->select("nom_prof");
                $this->db->select("prof_contact");
                $this->db->select("prof_adresse");
                $this->db->where('nom_mention', $mention_nom);
                $this->db->where('grad_id', $grad_id);
                $this->db->where('rm_id', $rm_id);
                $query = $this->db->get("mat_niv_parcours_prof_ue_semestre_associer_respmention");
                $proff = $query->result();
        
                $this->response($proff, RestController::HTTP_OK);
            }
            else {
                $this->response($decodedToken);
            }
        }
        else {
            $this->response(['Authentication failed'], RestController::HTTP_OK);
        }
       
    }

    public function ajoutProff_post()
    {
        // Récupérer les données de la requête
        $data = json_decode(file_get_contents('php://input'), true);

        /*Maka Max ny id_Prof */
        $this->db->select_max('id_prof');
        $query = $this->db->get('proff');
        $result = $query->row_array();
        $max = $result['id_prof'];

        //Nampina 1 ny  max
        $data['id_prof'] = $max + 1;

        //Enregistrena
        $this->db->insert('proff', $data);

        $response = [
            'etat' => 'success',
            'status' => 'success',
            'message' => 'L\'enregistrement succès',
        ];
        $this->response($response);
    }

    // Fonction pour mettre à jour un enregistrement dans la table Proff
    public function updateProff_put()
    {
        // Récupérer les données de la requête
        $data = json_decode(file_get_contents('php://input'), true);

        // Mettre à jour l'enregistrement dans la base de données
        $this->db->set('nom_prof', $data['nom_prof']);
        $this->db->set('cat_prof', $data['cat_prof']);
        $this->db->where('id_prof', $data['id_prof']);
        $this->db->update('proff');
        $response = [
            'etat' => 'success',
            'status' => 'success',
            'message' => 'L\'enregistrement a été mis à jour avec succès',
        ];
        $this->response($response);
    }

    //Supprimer un Prof
    public function supprimerProff_delete($id_prof)
    {
        $this->db->where('id_prof', $id_prof);
        $this->db->delete('proff');
        $response = [
            'etat' => 'info',
            'status' => 'Suppression',
            'message' => 'L\'enregistrement bien supprimer ',
        ];
        $this->response($response);
    }

    //Recherche
    public function rechercheProf_post()
    {
        //Maka Json
        $data = json_decode(file_get_contents('php://input'), true);
        $sql="Select * from proff where 1=1 ";

        if (trim($data['nom_prof'])!="") {$sql=$sql. " AND upper(nom_prof) like upper('%".$data['nom_prof']."%')";}
        if (trim($data['cat_prof'])!="") {$sql=$sql. " AND upper(cat_prof) like upper('%".$data['cat_prof']."%')";}
      
        $query = $this->db->query($sql);
        $res = $query->result();

        $this->response($res);
    }

}

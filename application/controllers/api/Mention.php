<?php

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'libraries/RestController.php';
use chriskacerguis\RestServer\RestController;

class Mention extends RestController
{
    public function __construct()
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if($method == "OPTIONS") {
            die();
        }
        parent::__construct();
        $this->load->database();
    

    }

    public function getMention_get()
    {
        $query = $this->db->get("mentions");
        $resultat = $query->result();

        $this->response($resultat, RestController::HTTP_OK);
      
    }

    public function getIdMention_get()
    {
        // $this->db->distinct();
        $this->db->select("trim(nom_mention||' ('||libmention||')')  as label");
        $this->db->select('id_mention as value');
        $query = $this->db->get("mentions");
        $resultat = $query->result();

        $this->response($resultat, RestController::HTTP_OK);
      
    }

    //Ajout Mention
    public function ajoutMention_post()
    {

        //Maka Json
        $data = json_decode(file_get_contents('php://input'), true);

        $dataMention=array();
         

        //Element dans table mention
        $dataMention['nom_mention']=$data['nom_mention'];
        $dataMention['libmention']=$data['libmention'];


        // /*Maka Max ny id_niveau */
        $this->db->select_max('id_mention');
        $query = $this->db->get('mentions');
        $result = $query->row_array();
        $max = $result['id_mention'];
        $maxIdClasse = $max + 1;

        $dataMention['id_mention']=$maxIdClasse;
        
        //Enregistrena
        $this->db->insert('mentions', $dataMention);

        $response = [
            'etat' => 'success',
            'status' => $data,
            'message' => 'Enregistrement  succés !',
        ];
        $this->response($response);
    }

    public function getMentionParcours_get()
    {
        $query = $this->db->get("mention_parcours");
        $resultat = $query->result();

        $this->response($resultat, RestController::HTTP_OK);
    }

   
     //Ajout Class
     public function ajoutMentionParcours_post()
     {
         //Maka Json
         $data = json_decode(file_get_contents('php://input'), true);
 
         $dataParcours=array();
         

         //Element dans table parcours
         $dataParcours['parcours']=$data['parcours'];
         $dataParcours['libparcours']=$data['libparcours'];

         //Cles etrangère
         $dataParcours['id_mention']=$data['id_mention'];         
 
         // /*Maka Max ny id_niveau */
         $this->db->select_max('id_parcours');
         $query = $this->db->get('parcours');
         $result = $query->row_array();
         $max = $result['id_parcours'];
         $maxIdClasse = $max + 1;
  
         $dataParcours['id_parcours']=$maxIdClasse;
         
         //Enregistrena
         $this->db->insert('parcours', $dataParcours);
 
        $response = [
             'etat' => 'success',
             'status' => 'success',
             'message' => 'L\'enregistrement succès',
         ];
         $this->response($response);
     }

    //Supprimer mention
    public function supprimerMention_delete($id_mention)
    {
         //Verifier  sode efa misy donne ao @vue classe_mention_parcours
         $this->db->where('id_mention', $id_mention);
         $query = $this->db->get("classe_mention_parcours");
         $resverf = $query->result();
 
         //Raha mbola tsisy
         if ($resverf) {
             $response = [
                 'etat' => 'warn',
                 'status' => 'Suppression non reuissie',
                 'message' => 'Un problème est survenu avec la foreign key de votre table de base de données ',
             ];
         } else {
            $this->db->where('id_mention', $id_mention);
            $this->db->delete('mentions');
             $response = [
                 'etat' => 'info',
                 'status' => 'Suppression reuissie',
                 'message' => 'L\'enregistrement bien supprimer ',
             ];
         }


        $this->response($response);
    }

    // Fonction pour mettre à jour un enregistrement dans la table Proff
    public function updateMention_put()
    {
        // Récupérer les données de la requête
        $data = json_decode(file_get_contents('php://input'), true);
    
         // Mettre à jour l'enregistrement dans la base de données
         $this->db->set('nom_mention', $data['nom_mention']);
         $this->db->set('libmention', $data['libmention']);
         $this->db->where('id_mention', $data['id_mention']);
         $this->db->update('mentions');
         $response = [
             'etat' => 'success',
             'status' => 'success',
             'message' => 'L\'enregistrement a été mis à jour avec succès',
         ];
         $this->response($response);
    }
    // Fonction pour mettre à jour un enregistrement dans la table Parcours
    public function updateParcours_put()
    {
        // Récupérer les données de la requête
        $data = json_decode(file_get_contents('php://input'), true);
    
         // Mettre à jour l'enregistrement dans la base de données
         $this->db->set('id_mention', $data['id_mention']);
         $this->db->set('parcours', $data['parcours']);
         $this->db->set('libparcours', $data['libparcours']);
         $this->db->where('id_parcours', $data['id_parcours']);
         $this->db->update('parcours');
         $response = [
             'etat' => 'success',
             'status' => $data,
             'message' => 'L\'enregistrement a été mis à jour avec succès',
         ];
         $this->response($response);
    }

    //Supprimer 
    public function supprimerParcours_delete($id_parcours)
    {
        
        //Verifier  sode efa misy donne ao @vue classe_mention_parcours
        $this->db->where('id_parcours', $id_parcours);
        $query = $this->db->get("classe_mention_parcours");
        $resverf = $query->result();

        //Raha mbola tsisy
        if ($resverf) {
           
            $response = [
                'etat' => 'warn',
                'status' => 'Suppression non reuissie',
                'message' => 'Un problème est survenu avec la foreign key de votre table de base de données ',
            ];
        } else {
            $this->db->where('id_parcours', $id_parcours);
            $this->db->delete('parcours');
            $response = [
                'etat' => 'info',
                'status' => 'Suppression reuissie',
                'message' => 'L\'enregistrement bien supprimer ',
            ];
        }
  
        $this->response($response);
    }


    //Recherche
    public function recherche_post()
    {
        //Maka Json
        $data = json_decode(file_get_contents('php://input'), true);
        $sql="Select * from mention_parcours where id_mention='".$data['id_mention']."'";

        if (trim($data['parcours'])!="") {$sql=$sql. " AND upper(parcours) like upper('%".$data['parcours']."%')";}
        if (trim($data['libparcours'])!="") {$sql=$sql. " AND upper(libparcours) like upper('%".$data['libparcours']."%')";}
      
        $query = $this->db->query($sql);
        $mention_parcourss = $query->result();

        $this->response($mention_parcourss);
    }

    //Recherhce (@ajout classe)
    public function rechercheMention_get($id_mention)
    {

        $this->db->select("trim(nom_parcours||' ('||abbrparcours||')')  as label");
        $this->db->select('id_parcours as value');
        $this->db->where('id_mention', $id_mention);
        $query = $this->db->get("mention_parcours");
        $resultat = $query->result();

        $this->response($resultat);
    }


    
}
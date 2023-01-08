<?php

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'libraries/RestController.php';
use chriskacerguis\RestServer\RestController;

class Classe extends RestController
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


    public function getClasse_get()
    {
        $query = $this->db->get("classe");
        $element_const = $query->result();

        if ($element_const) {
            $this->response($element_const, RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Aucun enregistrement trouvé',
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function getClasseMentionParcours_get()
    {
        $this->db->order_by("id_classe", "DESC");
        $query = $this->db->get("classe_mention_parcours");
        $resultat = $query->result();

        $this->response($resultat, RestController::HTTP_OK);
    }

     //Ajout Class
     public function ajoutClass_post()
     {
         //Maka Json
         $data = json_decode(file_get_contents('php://input'), true);

         $dataClasse=array();
 
         //Element dans table classe
         $dataClasse['libelle_classe']=$data['libelle_classe'];
         $dataClasse['nbre_etud']=$data['nbre_etud'];
         $dataClasse['anne_scolaire']=$data['anne_scolaire'];
         
         //id_niveau (cles etrangère)
         $dataClasse['id_parcours']=$data['id_parcours'];
 
 
         // /*Maka Max ny id_elemnt */
         $this->db->select_max('id_classe');
         $query = $this->db->get('classe');
         $result = $query->row_array();
         $max = $result['id_classe'];
         $maxIdClasse = $max + 1;
 
         $dataClasse['id_classe']=$maxIdClasse;
         
         //Enregistrena
         $this->db->insert('classe', $dataClasse);
 
         $response = [
            'etat' => 'success',
             'status' => $data,
             'message' => 'Enregistrement  succés !',
         ];
         $this->response($response);
     }
     //modification classe
     public function updateClasse_put()
    {
        // Récupérer les données de la requête
        $data = json_decode(file_get_contents('php://input'), true);

        // Mettre à jour l'enregistrement dans la base de données
        $this->db->set('libelle_classe', $data['libelle_classe']);
        $this->db->set('nbre_etud', $data['nbre_etud']);
        $this->db->set('anne_scolaire', $data['anne_scolaire']);
        $this->db->set('id_parcours', $data['id_parcours']);
        $this->db->where('id_classe', $data['id_classe']);
        $this->db->update('classe');
        $response = [
            'etat' => 'success',
            'status' => 'success',
            'message' => 'L\'enregistrement a été mis à jour avec succès',
        ];
        $this->response($response);
    }
    //supprimer classe
    public function supprimerClasse_delete($id_classe)
    {

        //  //Verifier  sode efa misy donne ao @vue classe_mention_parcours
        //  $this->db->where('id_mention', $id_mention);
        //  $query = $this->db->get("mention_parcours");
        //  $resverf = $query->result();
 
        //  //Raha mbola tsisy
        //  if ($resverf) {
        //      $response = [
        //          'etat' => 'warn',
        //          'status' => 'Suppression non reuissie',
        //          'message' => 'Un problème est survenu avec la foreign key de votre table de base de données ',
        //      ];
        //  } else {
           
        //      $response = [
        //          'etat' => 'info',
        //          'status' => 'Suppression reuissie',
        //          'message' => 'L\'enregistrement bien supprimer ',
        //      ];
        //  }


        $this->db->where('id_classe', $id_classe);
        $this->db->delete('classe');
        $response = [
            'etat' => 'info',
            'status' => 'Suppression reuissie',
            'message' => 'L\'enregistrement bien supprimer ',
        ];
        $this->response($response);
    }

    //Recherche
    public function rechercheClasseParcoursMention_post()
    {
        //Maka Json
        $data = json_decode(file_get_contents('php://input'), true);
        $sql="Select * from classe_mention_parcours where id_mention='".$data['id_mention']."'";

        if (trim($data['id_parcours'])!="") {$sql=$sql." AND id_parcours='".$data['id_parcours']."'";}
        if (trim($data['anne_scolaire'])!="") {$sql=$sql." AND anne_scolaire='".$data['anne_scolaire']."'";}
      
        $sql=$sql ." order by id_classe DESC";
        $query = $this->db->query($sql);
        $resRecherche = $query->result();

        $this->response($resRecherche);
    }


}
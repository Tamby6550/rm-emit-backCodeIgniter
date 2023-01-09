<?php
defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'libraries/RestController.php';
use chriskacerguis\RestServer\RestController;

class Engagement extends RestController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    //Get engagement
    public function getEngagement_get()
    {
        $query = $this->db->get("engagement");
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
    //Ajout engagement
    public function ajoutEngagement_post()
    {
    //Maka Json
        $data = json_decode(file_get_contents('php://input'), true);

        $dataEngagement=array();

        //Element dans table engagement
        $dataEngagement['nom_engagement']=$data['nom_engagement'];
        $dataEngagement['valeur']=$data['valeur'];

        // /*Maka Max ny id_elemnt */
        $this->db->select_max('id_enga');
        $query = $this->db->get('engagement');
        $result = $query->row_array();
        $max = $result['id_enga'];
        $maxIdEnga = $max + 1;

        $dataEngagement['id_enga']=$maxIdEnga;
    
        //Enregistrena
        $this->db->insert('engagement', $dataEngagement);

        $response = [
        'status' => $data,
        'message' => 'Enregistrement  succés !',
        ];
        $this->response($response);
    }
    //update engagement
    public function updateEngagement_put()
    {
        // Récupérer les données de la requête
        $data = json_decode(file_get_contents('php://input'), true);

        // Mettre à jour l'enregistrement dans la base de données
        $this->db->set('nom_engagement', $data['nom_engagement']);
        $this->db->set('valeur', $data['valeur']);
        $this->db->where('id_enga', $data['id_enga']);
        $this->db->update('engagement');
        $response = [
            'etat' => 'success',
            'status' => 'success',
            'message' => 'L\'enregistrement a été mis à jour avec succès',
        ];
        $this->response($response);
    }
    //delete engagement
    public function supprimerEngagement_delete($id_engagement)
    {
        $this->db->where('id_enga', $id_engagement);
        // $this->db->where('nom_prof', 'John');
        // $this->db->where('cat_prof', 'Mathématiques');
        $this->db->delete('engagement');
        $response = [
            'etat' => 'success',
            'status' => 'success',
            'message' => 'L\'enregistrement bien supprimer ',
        ];
        $this->response($response);
    }

}
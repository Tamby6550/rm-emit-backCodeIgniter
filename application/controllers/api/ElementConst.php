<?php

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'libraries/RestController.php';
use chriskacerguis\RestServer\RestController;

class ElementConst extends RestController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function elementconst_get()
    {
        $query = $this->db->get("element_const");
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

    public function assigneElementconst_post()
    {
        //Convertie date aujourd'hui yyyy-mm-dd en yyyymmdd
        $today = date('Y-m-d');
        $dateConv = $today;
        $dateConv = str_replace('-', '', $dateConv);

        //Maka Json
        $data = json_decode(file_get_contents('php://input'), true);

        $dataDetails=array();
        $dataElementConst=array();
        $id_prof="";

        //Element dans le details
        $dataDetails['base_et']=$data['base_et'];
        $dataDetails['group_et']=$data['group_et'];
        $dataDetails['base_ed']=$data['base_ed'];
        $dataDetails['group_ed']=$data['group_ed'];
        $dataDetails['base_ep']=$data['base_ep'];
        $dataDetails['group_ep']=$data['group_ep'];
        
        //Maka id_Prof 
        $id_prof=$data['id_prof'];

        //ny id_details ovaina ho date efa convertie + max id_elementconst
        $dataDetails['id_details']= $dateConv."".$$data['id_elemnt'];
        
        $this->db->insert('details', $dataDetails);

        //Modifier table element Constitutif
        $this->db->set('id_prof', $data['id_prof']);
        $this->db->set('id_details', $dataDetails['id_details']);
        $this->db->where('id_elemnt', $data['id_elemnt']);
        $this->db->update('element_const');

        $response = [
            'status' => $data,
            'message' => 'Enregistrement de modification succés !',
        ];
        $this->response($response);
    }

    //Ajout element constitutif
    public function ajoutElementconst_post()
    {
        //Maka Json
        $data = json_decode(file_get_contents('php://input'), true);

        $dataElementConst=array();
        

        //Element dans table element constitutif
        $dataElementConst['nom_el']=$data['nom_el'];
        $dataElementConst['credit']=$data['credit'];
        $dataElementConst['vh']=$data['vh'];
        $dataElementConst['module_elemt']=$data['module_elemt'];
        $dataElementConst['etat']=$data['etat'];
        $dataElementConst['semetre']=$data['semetre'];

        //IdClasse (cles etrangère)
        $dataElementConst['id_classe']=$data['id_classe'];


        // /*Maka Max ny id_elemnt */
        $this->db->select_max('id_elemnt');
        $query = $this->db->get('element_const');
        $result = $query->row_array();
        $max = $result['id_elemnt'];
        $maxIdElementConst = $max + 1;

        $dataElementConst['id_elemnt']=$maxIdElementConst;
        
        //Enregistrena
        $this->db->insert('element_const', $dataElementConst);

        $response = [
            'status' => $data,
            'message' => 'Enregistrement  succés !',
        ];
        $this->response($response);
    }

    public function updateElementconst_put()
    {
        // Récupérer les données de la requête
        $data = $this->input->input_stream();
        // Vérifier si toutes les données obligatoires sont présentes
        if (!isset($data['id_elemnt']) || !isset($data['nom_el']) || !isset($data['credit']) || !isset($data['VH']) || !isset($data['module_elemt']) || !isset($data['etat']) || !isset($data['id_prof'])) {
            // Renvoyer une erreur si les données obligatoires sont manquantes
            $this->output->set_status_header(400, 'Bad Request');
            $response = [
                'status' => 'error',
                'message' => 'Les données obligatoires sont manquantes',
                'data' => $data,
            ];
            $this->response($response);
            return;
        }
        // Mettre à jour l'enregistrement dans la base de données
        $this->db->set('nom_el', $data['nom_el']);
        $this->db->set('credit', $data['credit']);
        $this->db->set('VH', $data['VH']);
        $this->db->set('module_elemt', $data['module_elemt']);
        $this->db->set('etat', $data['etat']);
        $this->db->set('id_prof', $data['id_prof']);
        $this->db->where('id_elemnt', $data['id_elemnt']);
        $this->db->update('element_const');
        $response = [
            'status' => 'success',
            'message' => 'L\'enregistrement a été mis à jour avec succès',
        ];
        $this->response($response);
    }

    //ajout Details
    public function ajoutDetails_post()
    {
        // Récupérer les données de la requête
        $data = $this->input->input_stream();

        /*Maka Max ny id_Details */
        $this->db->select_max('id_details');
        $query = $this->db->get('details');
        $result = $query->row_array();
        $max = $result['id_details'];

        //Nampina 1 ny  max
        $data['id_details'] = $max + 1;

        //Enregistrena
        $this->db->insert('details', $data);

        $response = [
            'status' => $data,
            'message' => 'Détails enregistrés avec succès !',
        ];
        $this->response($response);
    }
}

<?php

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'libraries/RestController.php';

use chriskacerguis\RestServer\RestController;

class CompteRm extends RestController
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
        $this->load->helper('security');
        $this->load->library('Authorization_Token');

       

    }

   

     //Ajout responsable associer , Responsable Mention
     public function ajoutRmAssocier_post()
     {
        //Verifie raha misy @
        function verify_at_symbol($word) {
            if (substr($word, -1) === "@") {
                return true;
            } else {
                return false;
            }
        }
        //Mamafa @
        function remove_at_symbol($string) {
            return rtrim($string, "@");
        }

         //Maka Json
         $data = json_decode(file_get_contents('php://input'), true);

         $dataRM=array();
         $dataAss=array();
 
         //Element dans table responsable
         $dataRM['rm_nom']=$data['rm_nom'];
         $mdp=$data['rm_mdp'];
         
         //Cryptena le mdp
         $mdp = do_hash($mdp);

        //Affectena ao
         $dataRM['rm_mdp']=$mdp;

         //Element dans table Assoccier
         $dataAss['mention_nom']=$data['mention_nom'];
         $dataAss['grad_id']=$data['grad_id'];

         //Cas exceptionnel (Admin ou autre)
        if (verify_at_symbol($dataRM['rm_nom'])) {
            // /*Maka Max ny rm_id */
            $dataRM['rm_nom']=remove_at_symbol($dataRM['rm_nom']);
            $this->db->select_max('rm_id');
            $query = $this->db->get('respmention');
            $result = $query->row_array();
            $max = $result['rm_id'];
            $maxIdClasse = $max + 1;
    
            $dataRM['rm_id']=$maxIdClasse;
            $dataAss['rm_id']=$maxIdClasse;

            //Enregistrena table responsable mention
            $this->db->insert('respmention', $dataRM);

            //Enregistrena table responsable associer
            $this->db->insert('associer', $dataAss);
    
            $response = [
                'etat' => 'success',
                'situation' => 'Creation compte',
                'message' => 'Enregistrement  succés !',
            ];
        }

        //Rehefa Normale
        else{
            $this->db->where('mention', $dataAss['mention_nom']);
            $this->db->where('grad_id',  $dataAss['grad_id']);
            $query = $this->db->get("info_compte_rm");
            $verfCompte = $query->row_array();
            //verifiena so efa ao le compte
            if ($verfCompte) {
               $response = [
                   'etat' => 'warn',
                    'situa' => 'Creation compte',
                    'message' => 'Cette mention et ce niveau ont déjà un RM !',
                ];
            }
            //Raha mbola tsisy ilay compte 
            else{         
            // /*Maka Max ny rm_id */
            $this->db->select_max('rm_id');
            $query = $this->db->get('respmention');
            $result = $query->row_array();
            $max = $result['rm_id'];
            $maxIdClasse = $max + 1;
    
            $dataRM['rm_id']=$maxIdClasse;
            $dataAss['rm_id']=$maxIdClasse;
    
            //Enregistrena table responsable mention
            $this->db->insert('respmention', $dataRM);
    
            //Enregistrena table responsable associer
            $this->db->insert('associer', $dataAss);
    
            $response = [
               'etat' => 'success',
                'situation' => 'Creation compte',
                'message' => 'Enregistrement  succés !',
            ];
           }
        }

         $this->response($response);
     }

    // Get login
    public function getLogin_post()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        //Element dans table responsable

        // $dataRM['nom']=$data['rm_nom'];
        $dataRM['mention']=$data['mention'];
        $dataRM['grad_id']=$data['grad_id'];
        $mdp=$data['motpasse'];
        $mdp = do_hash($mdp);
        $this->db->select("rm_id");
        $this->db->where('motpasse', $mdp);
        $this->db->where('mention', $dataRM['mention']);
        $this->db->where('grad_id',  $dataRM['grad_id']);
        $query = $this->db->get("info_compte_rm");
        $resultat = $query->row_array();

         if ($resultat) {
            // $mdp = do_hash($mdp);
            $this->db->where('rm_id',  $resultat['rm_id']);
            $this->db->where('motpasse', $mdp);
            $query1 = $this->db->get("info_compte_rm");
            $resmdp = $query1->row_array();
            if ($resmdp) {
                $payload = array(
                    'rm_id' => $resmdp['rm_id'],
                    'nom' => $resmdp['nom'],
                    'mention' => $resmdp['mention']
                );
               
                $tokenData = $this->authorization_token->generateToken($payload);
                
                $response = [
                    'etat' => 'success',
                    'situation' => 'Login',
                    'message' => 'Bienvenue !',
                    'info' =>$resmdp,
                    'token' =>$tokenData
                    // 'decodetoken' =>$decodedToken
                ];
            }else{
                $response = [
                    'etat' => 'warn',
                    'situation' => 'Login',
                    'message' => 'Mot de passe incorrect !'
                    // 'status' => $resultat['rm_id']
                ];
            }
        } else {
             $response = [
                'etat' => 'warn',
                'situation'=> 'Login',
                'message' => 'Votre compte n\'existe pas ! ',
             ];
        }

        $this->response($response);
     }
  //Affiche tous les proff
  public function getGradeMention_get()
  {
    
    $query = $this->db->get("grade");
    $grade = $query->result();
    $query = $this->db->get("mention");
    $mention = $query->result();

    $response = [
        'grade' => $grade,
        'mention'=> $mention
     ];
    $this->response($response, RestController::HTTP_OK);
       
  }
}
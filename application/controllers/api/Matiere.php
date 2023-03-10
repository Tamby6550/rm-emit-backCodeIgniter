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
         
         $sql="select distinct prof_id,nom_prof,vheure,credit,prof_contact,matiere,mati_id,etat as etat_mat,dat_deb_etat,date_fin_etat,date_session_n,date_session_r,ue_code,unite_ens,semestre,seme_code ,abbr_niveau from mat_niv_parcours_prof_ue_semestre_associer_respmention";
         $headers = $this->input->request_headers(); 
         if (isset($headers['Authorization'])) {
             $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
             if ($decodedToken['status'])
             {
                 $reponse=array();
                
                //rhefa admin
                if ($rm_id=="admin") {
                    
                    if ($etat=='5') {//rehefa tsy manao recherche 
                        $condition=" Where nom_mention='".$mention_nom."' and grad_id='".$grad_id."'  and anne_univ='".$anne_univ."' and niv_id='".$niv_id."' order by ue_code,matiere ASC ";
                    }else{
                        $condition=" Where nom_mention='".$mention_nom."' and grad_id='".$grad_id."'  and anne_univ='".$anne_univ."' and niv_id='".$niv_id."' and etat='".$etat."' order by ue_code,matiere ASC ";
                    }
                }else{
                    if ($etat=='5') {//rehefa tsy manao recherche 
                        $condition=" Where nom_mention='".$mention_nom."' and grad_id='".$grad_id."' and rm_id='".$rm_id."' and anne_univ='".$anne_univ."' and niv_id='".$niv_id."' order by ue_code,matiere ASC ";
                    }else{
                        $condition=" Where nom_mention='".$mention_nom."' and grad_id='".$grad_id."' and rm_id='".$rm_id."' and anne_univ='".$anne_univ."' and niv_id='".$niv_id."' and etat='".$etat."' order by ue_code,matiere ASC ";
                    }
                }
                $sql=$sql . $condition;
                $query = $this->db->query($sql);
                $res = $query->result();
                
                $niveau="";
                $this->db->select("annee_univ  as label");
                $this->db->select('annee_univ as value');
                $queryanne_univ = $this->db->get("annee_univ");
                $anne_univ = $queryanne_univ->result();

                //Rehefa admin de tsy micompte ny rm_id
                if ($rm_id=="admin") {
                    $sqlniveau ="select distinct niv_id as value ,abbr_niveau as label from mat_niv_parcours_prof_ue_semestre_associer_respmention Where nom_mention='".$mention_nom."' and grad_id='".$grad_id."' "; 
                    $queryniveau =  $this->db->query($sqlniveau);
                    $niveau = $queryniveau->result();
                }else{
                    $sqlniveau ="select distinct niv_id as value ,abbr_niveau as label from mat_niv_parcours_prof_ue_semestre_associer_respmention Where nom_mention='".$mention_nom."' and grad_id='".$grad_id."' and rm_id='".$rm_id."'"; 
                    $queryniveau =  $this->db->query($sqlniveau);
                    $niveau = $queryniveau->result();
                }


                    
                    $this->db->select("grad_nom  as label");
                    $this->db->select('grad_id as value');
                    $this->db->where('grad_nom<>',  'admin');
                    $query = $this->db->get("grade");
                    $grade = $query->result();
                    
                    $this->db->select("mention_nom  as label");
                    $this->db->select('mention_nom as value');
                    $this->db->where('mention_nom<>',  'Admin');
                    $query = $this->db->get("mention");
                    $mention = $query->result();
                    $reponse = [
                        'matiere' => $res,
                        'anne_univ' =>$anne_univ,
                        'niveau' =>$niveau,
                        'sql' =>$sql,
                        'grade' => $grade,
                        'mention'=> $mention
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

     public function ajouteDetailsMatiere_post()
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
                
                $dataann['anne_univ']=$data['anne_univ'];
                
                
                $dataDetails['id_details']= $dateConv."".$data['mati_id'];
                
                try {
                    $this->db->where('id_details', $dataDetails['id_details']);
                    $verf=$this->db->get('detailstamby');
                    $res_verf = $verf->result();
                    //raha efa misy de atao mis a jour
                    if ($res_verf) {
                        $this->db->set('base_et', $dataDetails['base_et']);
                        $this->db->set('group_et', $dataDetails['group_et']);
                        $this->db->set('base_ed', $dataDetails['base_ed']);
                        $this->db->set('group_ed', $dataDetails['group_ed']);
                        $this->db->set('base_ep', $dataDetails['base_ep']);
                        $this->db->set('group_ep', $dataDetails['group_ep']);
                        $this->db->where('id_details', $dataDetails['id_details']);
                        $this->db->update('detailstamby');
                    }
                    //Sinon inserena
                    else {
                        //Insertion dans la table detailstamby
                        $this->db->insert('detailstamby', $dataDetails);
                    }
                    
                   
                } catch (\Throwable $th) {
                   //Insertion dans la table detailstamby
                //    $this->db->insert('detailstamby', $dataDetails);

                }

               
        

                $this->db->set('vheure', $dataMatiere['vheure']);
                $this->db->set('credit', $dataMatiere['credit']);
                $this->db->where('mati_id', $dataMatiere['mati_id']);
                $this->db->update('matiere');


                $this->db->set('id_details', $dataDetails['id_details']);
                $this->db->where('mati_id', $dataMatiere['mati_id']);
                $this->db->where('anne_lib', $dataann['anne_univ']);
                $this->db->update('anne_univ_tamby_rm');
        
                
                $response = [
                    'etat' => 'success',
                    'situation' => 'Enregistrement Details',
                    'message' => 'Mis ?? jour succ?? !',
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

     public function getDetailsMatiere_get($anne_univ,$mati_id)
     {
        $headers = $this->input->request_headers(); 
		if (isset($headers['Authorization'])) {
			$decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
            if ($decodedToken['status'])
            {
                $sql="select det.* from detailstamby det,anne_univ_tamby_rm ann where det.id_details=ann.id_details and ann.anne_lib='".$anne_univ."' and ann.mati_id='".$mati_id."'";
                $query3 = $this->db->query($sql);
                $details_tamby = $query3->row_array();

                $this->response($details_tamby, RestController::HTTP_OK);
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
               // R??cup??rer les donn??es de la requ??te
                $data = json_decode(file_get_contents('php://input'), true);
                $etat_re='';
                // Mettre ?? jour l'enregistrement dans la base de donn??es
                if ($data['etat']=='0') {
                    $this->db->set('etat', $data['etat']);
                    $this->db->where('mati_id', $data['mati_id']);
                    $this->db->where('anne_lib', $data['anne_univ']);
                    $this->db->update('anne_univ_tamby_rm');
                }

                if ($data['etat']=='1') {
                    $this->db->set('date_debut', $data['valuedt']);
                    $this->db->set('etat', $data['etat']);
                    $this->db->where('mati_id', $data['mati_id']);
                    $this->db->where('anne_lib', $data['anne_univ']);
                    $this->db->update('anne_univ_tamby_rm');
                }

                if ($data['etat']=='2') {
                    $this->db->set('date_fin',  $data['valuedt']);
                    $this->db->set('etat', $data['etat']);
                    $this->db->where('mati_id', $data['mati_id']);
                    $this->db->where('anne_lib', $data['anne_univ']);
                    $this->db->update('anne_univ_tamby_rm');
                }

                if ($data['etat']=='3') {
                    $this->db->set('date_sn',  $data['valuedt']);
                    $this->db->set('etat', $data['etat']);
                    $this->db->where('mati_id', $data['mati_id']);
                    $this->db->where('anne_lib', $data['anne_univ']);
                    $this->db->update('anne_univ_tamby_rm');
                }

                if ($data['etat']=='4') {
                    $this->db->set('date_sr',  $data['valuedt']);
                    $this->db->set('etat', $data['etat']);
                    $this->db->where('mati_id', $data['mati_id']);
                    $this->db->where('anne_lib', $data['anne_univ']);
                    $this->db->update('anne_univ_tamby_rm');
                }

               
                if ($data['etat']=='0'||$data['etat']==null) {
                   $etat_re='Pas en encore demar?? !';
                }
                if ($data['etat']=='1') {
                   $etat_re='En cours !';
                }
                if ($data['etat']=='2') {
                   $etat_re='T??rmin?? !';
                }
                if ($data['etat']=='3') {
                   $etat_re='T??rmin?? examen session normale !';
                }
                if ($data['etat']=='4') {
                   $etat_re='T??rmin?? examen session rattrapage !';
                }
                $response = [
                    'etat' => 'success',
                    'situation' => 'Modification etat de mati??re',
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

     public function getChartRm_get($rm_id,$mention_nom,$grad_id,$anne_univ,$niv_id,$filtre)
     {
         
         $sql="select now()";
         $headers = $this->input->request_headers(); 
         $conditionNon="";
         $conditionEn="";
         $conditionTermine="";
         if (isset($headers['Authorization'])) {
             $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
             if ($decodedToken['status'])
             {
                 $reponse=array();
                  //Rehefa  admin ts mikoty ny rm_id
                 if ($rm_id=="admin") {
                    if ($niv_id=='0') {
                        $sql="select   count(etat) as termine , 
    
                        (select   count(etat) from mat_niv_parcours_prof_ue_semestre_associer_respmention
                        where  nom_mention='".$mention_nom."' and grad_id='".$grad_id."'
                         and anne_univ='".$anne_univ."'  and etat='1' ) as  encours,
    
                        (select   count(etat) from mat_niv_parcours_prof_ue_semestre_associer_respmention
                        where  nom_mention='".$mention_nom."' and grad_id='".$grad_id."' 
                         and anne_univ='".$anne_univ."'  and etat='0' ) as  pas_encore
                        
                        
                        from mat_niv_parcours_prof_ue_semestre_associer_respmention
                        where  nom_mention='".$mention_nom."' and grad_id='".$grad_id."' 
                       and anne_univ='".$anne_univ."'  and (etat='2' or etat='3' or etat='4')";
                    }else{
    
                        if ($filtre=='c') {
                            $sql="select  count(etat) as termine , 
    
                            (select   count(etat) from mat_niv_parcours_prof_ue_semestre_associer_respmention
                            where  nom_mention='".$mention_nom."' and grad_id='".$grad_id."'
                            and anne_univ='".$anne_univ."'  and etat='1' and niv_id='".$niv_id."' ) as  encours,
        
                            (select   count(etat) from mat_niv_parcours_prof_ue_semestre_associer_respmention
                            where  nom_mention='".$mention_nom."' and grad_id='".$grad_id."' 
                             and anne_univ='".$anne_univ."'  and etat='0'  and niv_id='".$niv_id."') as  pas_encore
                            
                            from mat_niv_parcours_prof_ue_semestre_associer_respmention
                            where  nom_mention='".$mention_nom."' and grad_id='".$grad_id."' 
                          and anne_univ='".$anne_univ."'  and (etat='2' or etat='3' or etat='4')  and niv_id='".$niv_id."'";
                        }else if ($filtre=='e') {
                            $sql="select  count(etat) as termine_sr , 
    
                            (select   count(etat) from mat_niv_parcours_prof_ue_semestre_associer_respmention
                            where  nom_mention='".$mention_nom."' and grad_id='".$grad_id."'
                           and anne_univ='".$anne_univ."'  and etat='3' and niv_id='".$niv_id."' ) as  termine_sn
                            
                            from mat_niv_parcours_prof_ue_semestre_associer_respmention
                            where  nom_mention='".$mention_nom."' and grad_id='".$grad_id."' 
                         and anne_univ='".$anne_univ."'  and etat='4'  and niv_id='".$niv_id."'";
                        }
    
                    }
                }
                
                //Rehefa tsy admin fa RM tsotra
                else{
                    //rehefa tsy manao recherche 
                 if ($niv_id=='0') {
                    $sql="select count(etat) as termine , 

                    (select  count(etat) from mat_niv_parcours_prof_ue_semestre_associer_respmention
                    where  nom_mention='".$mention_nom."' and grad_id='".$grad_id."'
                    and rm_id='".$rm_id."' and anne_univ='".$anne_univ."'  and etat='1' ) as  encours,

                    (select  count(etat) from mat_niv_parcours_prof_ue_semestre_associer_respmention
                    where  nom_mention='".$mention_nom."' and grad_id='".$grad_id."' 
                    and rm_id='".$rm_id."' and anne_univ='".$anne_univ."'  and etat='0' ) as  pas_encore
                    
                    
                    from mat_niv_parcours_prof_ue_semestre_associer_respmention
                    where  nom_mention='".$mention_nom."' and grad_id='".$grad_id."' 
                    and rm_id='".$rm_id."' and anne_univ='".$anne_univ."'  and (etat='2' or etat='3' or etat='4')";
                 }else{

                    if ($filtre=='c') {
                        $sql="select count(etat) as termine , 

                        (select  count(etat) from mat_niv_parcours_prof_ue_semestre_associer_respmention
                        where  nom_mention='".$mention_nom."' and grad_id='".$grad_id."'
                        and rm_id='".$rm_id."' and anne_univ='".$anne_univ."'  and etat='1' and niv_id='".$niv_id."' ) as  encours,
    
                        (select  count(etat) from mat_niv_parcours_prof_ue_semestre_associer_respmention
                        where  nom_mention='".$mention_nom."' and grad_id='".$grad_id."' 
                        and rm_id='".$rm_id."' and anne_univ='".$anne_univ."'  and etat='0'  and niv_id='".$niv_id."') as  pas_encore
                        
                        from mat_niv_parcours_prof_ue_semestre_associer_respmention
                        where  nom_mention='".$mention_nom."' and grad_id='".$grad_id."' 
                        and rm_id='".$rm_id."' and anne_univ='".$anne_univ."'  and (etat='2' or etat='3' or etat='4')  and niv_id='".$niv_id."'";
                    }else if ($filtre=='e') {
                        $sql="select count(etat) as termine_sr , 

                        (select  count(etat) from mat_niv_parcours_prof_ue_semestre_associer_respmention
                        where  nom_mention='".$mention_nom."' and grad_id='".$grad_id."'
                        and rm_id='".$rm_id."' and anne_univ='".$anne_univ."'  and etat='3' and niv_id='".$niv_id."' ) as  termine_sn
                        
                        from mat_niv_parcours_prof_ue_semestre_associer_respmention
                        where  nom_mention='".$mention_nom."' and grad_id='".$grad_id."' 
                        and rm_id='".$rm_id."' and anne_univ='".$anne_univ."'  and etat='4'  and niv_id='".$niv_id."'";
                    }

                }
            }
                
                $query = $this->db->query($sql);
                $res = $query->row_array();


                $querycmpt = $this->db->query("SELECT count(rm_id) as nbrecmpt FROM public.info_compte_rm where mention='".$mention_nom."' and grad_id='".$grad_id."'");
                $resnbrcmpt = $querycmpt->row_array();
                
                $dtres=array();

                if ($rm_id=="admin") {

                    if ($niv_id=='0') {
                        $dtres=[$res['pas_encore']/$resnbrcmpt['nbrecmpt'],$res['encours']/$resnbrcmpt['nbrecmpt'],$res['termine']/$resnbrcmpt['nbrecmpt']];
                    }else{
                        if ($filtre=='c') {
                            $dtres=[$res['pas_encore']/$resnbrcmpt['nbrecmpt'],$res['encours']/$resnbrcmpt['nbrecmpt'],$res['termine']/$resnbrcmpt['nbrecmpt']];
                        }else  if ($filtre=='e'){
                            $dtres=[$res['termine_sn']/$resnbrcmpt['nbrecmpt'],$res['termine_sr']/$resnbrcmpt['nbrecmpt']];
                        }else{
                            $dtres=['0','0','0'];
    
                        }
                    }
                }else{
                    if ($niv_id=='0') {
                        $dtres=[$res['pas_encore'],$res['encours'],$res['termine']];
    
                    }else{
                        if ($filtre=='c') {
                            $dtres=[$res['pas_encore'],$res['encours'],$res['termine']];
                        }else  if ($filtre=='e'){
                            $dtres=[$res['termine_sn'],$res['termine_sr']];
                        }else{
                            $dtres=['0','0','0'];
    
                        }
                    }
                }
              
                  
                $this->db->select("annee_univ  as label");
                $this->db->select('annee_univ as value');
                $queryanne_univ = $this->db->get("annee_univ");
                $anne_univ = $queryanne_univ->result();


                 //Rehefa admin de tsy micompte ny rm_id
                if ($rm_id=="admin") {
                    $sqlniveau ="select distinct niv_id as value ,abbr_niveau as label from mat_niv_parcours_prof_ue_semestre_associer_respmention Where nom_mention='".$mention_nom."' and grad_id='".$grad_id."' "; 
                    $queryniveau =  $this->db->query($sqlniveau);
                    $niveau = $queryniveau->result();
                }else{
                    $sqlniveau ="select distinct niv_id as value ,abbr_niveau as label from mat_niv_parcours_prof_ue_semestre_associer_respmention Where nom_mention='".$mention_nom."' and grad_id='".$grad_id."' and rm_id='".$rm_id."'"; 
                    $queryniveau =  $this->db->query($sqlniveau);
                    $niveau = $queryniveau->result();
                }

                //Admin no mila anazy
                $this->db->select("grad_nom  as label");
                $this->db->select('grad_id as value');
                $this->db->where('grad_nom<>',  'admin');
                $query = $this->db->get("grade");
                $grade = $query->result();
                    
                $this->db->select("mention_nom  as label");
                $this->db->select('mention_nom as value');
                $this->db->where('mention_nom<>',  'Admin');
                $query = $this->db->get("mention");
                $mention = $query->result();
                //Admin no mila anazy//


                $reponse = [
                    'etat' => $dtres,
                    'anne_univ' =>$anne_univ,
                    'niveau' =>$niveau,
                    'sql' =>$sql,
                    'grade' => $grade,
                    'mention'=> $mention
                    
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
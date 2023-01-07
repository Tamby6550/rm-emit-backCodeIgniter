<?php

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'libraries/RestController.php';
use chriskacerguis\RestServer\RestController;


class ApiDemoController extends RestController {
    public function index_get()
    {
        $this->load->database();
       
        $query = $this->db->query('SELECT * FROM ma_table');
        foreach ($query->result() as $row) {
        echo $row->nom;
        echo $row->prenom;
        }
    }

}
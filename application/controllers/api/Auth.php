<?php

defined('BASEPATH') or exit('No direct script access allowed');


class Auth 
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('jwt');
    }
    public function generate_token($rm_id) {

            // Créez un tableau de données à encoder dans le token JWT
            $payload = array(
                'sub' => $rm_id,
                'iat' => time(),
                'exp' => time() + (60 * 60 * 24) // expire dans 24 heures
            );
            // Encodez le tableau de données en utilisant la méthode "encode" de la bibliothèque JWT
            $token = $this->jwt->encode($payload, 'yoursecretkey');
            // Renvoyez le token
            return $token;
    }
    public function validate_token($token) {
        try {
            // Décodez le token en utilisant la méthode "decode" de la bibliothèque JWT
            $payload = $this->jwt->decode($token, 'yoursecretkey');
            // Vérifiez la date d'expiration
            if (time() > $payload->exp) {
                return false;
            } else {
                return true;
            }
        } catch (Exception $e) {
            // Si le token est illisible ou non valide, une exception est levée
            return false;
        }
    }
    
}
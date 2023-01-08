<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['api/demo'] = 'api/ApiDemoController/index';

//Controlleur Proff_api
/*Affichage Proff */
$route['api/getProff'] = 'api/Proff_api/proff';
/*Mettre a jour Proff*/
$route['api/update'] = 'api/Proff_api/update';
//Ajout Proff 
$route['api/ajoutProff'] = 'api/Proff_api/ajoutProff';
//Delete Proff
$route['api/supprimerProff/(:any)'] = 'api/Proff_api/supprimerProff/$1';



//Controlleur Element constitutif
/* Affichage Element constitutif */
$route['api/elementconst'] = 'api/ElementConst/elementconst';
/*Mettre a jour Element constitutif*/
$route['api/updateElementconst'] = 'api/ElementConst/updateElementconst';
//Ajout Prof 
$route['api/ajoutElementconst'] = 'api/ElementConst/ajoutElementconst';
//Ampifandraisina @prof sy details 
$route['api/assigneElementconst'] = 'api/ElementConst/assigneElementconst';




//Controlleur Mention
/*Get All Mention */
$route['api/getMention'] = 'api/Mention/getMention';

/*Get Id Mention*/
$route['api/getIdMention'] = 'api/Mention/getIdMention';

/*Get All Mention et Parcours*/
$route['api/getMentionParcours'] = 'api/Mention/getMentionParcours';

//Ajout Mention 
$route['api/ajoutMention'] = 'api/Mention/ajoutMention';

//Update Mention
$route['api/updateMention'] = 'api/Mention/updateMention';

//Update Parcours
$route['api/updateParcours'] = 'api/Mention/updateParcours';

//Ajout Mention et Parcours
$route['api/ajoutMentionParcours'] = 'api/Mention/ajoutMentionParcours';

//Supprimer mention
$route['api/supprimerMention/(:any)'] = 'api/Mention/supprimerMention/$1';

//Supprimer Parcours
$route['api/supprimerParcours/(:any)'] = 'api/Mention/supprimerParcours/$1';

//Recherche Pacrours
$route['api/recherche'] = 'api/Mention/recherche';





//Controlleur Classe
/*Get All classe */
$route['api/getClasse'] = 'api/Classe/getClasse';

//Ajout Classe 
$route['api/ajoutClass'] = 'api/Classe/ajoutClass';
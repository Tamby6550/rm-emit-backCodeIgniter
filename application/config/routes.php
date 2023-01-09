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
$route['api/updateProff'] = 'api/Proff_api/updateProff';
//Ajout Proff 
$route['api/ajoutProff'] = 'api/Proff_api/ajoutProff';
//Delete Proff
$route['api/supprimerProff/(:any)'] = 'api/Proff_api/supprimerProff/$1';
//Recherche Proff
$route['api/rechercheProf'] = 'api/Proff_api/rechercheProf';



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

//Supprimer mention
$route['api/rechercheMention/(:any)'] = 'api/Mention/rechercheMention/$1';



//Controlleur Classe
/*Get All classe */
$route['api/getClasse'] = 'api/Classe/getClasse';

/*Get All classe Parcous Mention */
$route['api/getClasseMentionParcours'] = 'api/Classe/getClasseMentionParcours';
//Ajout Classe 
$route['api/ajoutClass'] = 'api/Classe/ajoutClass';
//modification classe
$route['api/updateClasse'] = 'api/Classe/updateClasse';
//suppression classe
$route['api/supprimerClasse/(:any)'] = 'api/Classe/supprimerClasse/$1';
/*Get Recherche classe Parcous Mention */
$route['api/rechercheClasseParcoursMention'] = 'api/Classe/rechercheClasseParcoursMention';




//controlleur Engagement
//Get Engagement
$route['api/getEngagement'] = 'api/Engagement/getEngagement';
//Ajout engagement
$route['api/ajoutEngagement'] = 'api/Engagement/ajoutEngagement';
//update engagement
$route['api/updateEngagement'] = 'api/Engagement/updateEngagement';
//delete engagement
$route['api/supprimerEngagement/(:any)'] = 'api/Engagement/supprimerEngagement/$1';

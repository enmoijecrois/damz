<?php

// Imports
require_once 'config/config.php';
require_once 'models/functions.php';
require_once 'controllers/authController.php';


$action = isset($_GET['action']) ? $_GET['action'] : 'home';

// Etapes et traitements
switch ($action) {

	// Pages publiques.
	case 'home':
		$sTitre = 'Impression de documents à Caen';
		require('views/home.php');
		break;
	case 'accueil': // modifier pour 'imprint' ?
		$sTitre = 'Imprimez votre document';
		require('views/dossier.php');
		break;
	case 'orderOverview':
		$sTitre = 'Résumé de ma commande';
		require('views/orderOverview.php');
		break;
	case 'contact':
		$sTitre = 'Contactez-nous';
		require('views/contact.php');
		break;
	case 'legals':
		$sTitre = 'Mentions légales';
		require('views/legals.php');
		break;

	// Connexion.
	case 'login':
		$email = isset($_GET['email']) ? $_GET['email'] : '';
		$sTitre = 'Connexion';
		require('views/login.php');
		break;
	case 'signup':
		$sTitre = 'Créez votre compte';
		require('views/signup.php');
		break;
	case 'forgotPassword':
		$sTitre = 'Mot de passe oublié';
		require('views/forgotPassword.php');
		break;
	case 'resetPassword':
		$sTitre = 'Réinitialisation de mon mot de passe';
		require('views/resetPassword.php');
		break;
	case 'logout':
		$sTitre = 'Déconnexion';
		require('views/login.php');
		break;
	case 'verifyUser':
		$email = $_GET['email'];
		require('controllers/verifyEmail.php');
		break;
	case 'resendConfirmationMail':
		require('controllers/resendConfirmationMail.php');
		break;

	// Pages user connecté.
	case 'account':
		$sTitre = 'Mon compte';
		require('views/account.php');
		break;
	case 'accountAddresses':
		$sTitre = 'Mes adresses';
		require('views/accountAddresses.php');
		break;

	// ADMIN
	case 'admin':
		$sTitre = 'Administration';
		require('views/admin.php');
		break;
	case 'adminUsers':
		$sTitre = 'Administration';
		require('views/adminUsers.php');
		break;
	case 'adminOrders':
		$sTitre = 'Administration';
		require('views/adminOrders.php');
		break;
	case 'adminOrdersPast':
		$sTitre = 'Administration';
		require('views/adminOrdersPast.php');
		break;
	case 'adminGetInvoice':
		require('controllers/adminGetInvoiceController.php');
		break;
	case 'adminCouleurs':
		$sTitre = 'Administration';
		require('views/adminCouleurs.php');
		break;
	case 'adminPaliersNB':
		$sTitre = 'Administration';
		$_page = 'NB';
		require('views/adminPaliers.php');
		break;
	case 'adminPaliersCouleur':
		$sTitre = 'Administration';
		$_page = 'Couleur';
		require('views/adminPaliers.php');
		break;
	case 'adminPaliersSpiplast':
		$sTitre = 'Administration';
		$_page = 'spiplast';
		require('views/adminPaliers.php');
		break;
	case 'adminPaliersSpimetal':
		$sTitre = 'Administration';
		$_page = 'spimetal';
		require('views/adminPaliers.php');
		break;
	case 'adminPaliersThermo':
		$sTitre = 'Administration';
		$_page = 'thermo';
		require('views/adminPaliers.php');
		break;

	// Gesetion des erreurs
	case 'error':
		$sTitre = 'Erreur';
		require('views/error.php');
		break;

	default:
		$_GET['e'] = 404;
		$sTitre = 'Erreur';
		require('views/error.php');

}

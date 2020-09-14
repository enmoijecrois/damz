<?php

use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;

require_once 'models/AuthMgr.class.php';
require_once 'controllers/sendEmails.php';

$email = '';
$pseudo = '';
$password = '';
$passwordConf = '';
$errors = [];

// --------------- SIGN UP USER ---------------
if (isset($_POST['signup-btn'])) {
	$email = trim($_POST['email']);
	$pseudo = trim($_POST['pseudo']);
	$password = trim($_POST['password']);
	$passwordConf = trim($_POST['passwordConf']);
    $token = bin2hex(random_bytes(50));
	
	$validator = new EmailValidator();

	$pwLength = mb_strlen($password) >= 8;
	$pwLowercase = preg_match('/[a-z]/', $password);
	$pwUppercase = preg_match('/[A-Z]/', $password);
	$pwNumber = preg_match('/[0-9]/', $password);
	$pwSpecialchar = preg_match('/[' . preg_quote('-_"%\'*;<>?^`{|}~/\\#=&', '/') . ']/', $password);
	
    if (empty($email)) {
        $errors[] = 'E-mail requis';
    }
	elseif (!$validator->isValid($email, new RFCValidation())) {
		$errors[] = 'E-mail invalide';
	}
	elseif (AuthMgr::emailExists($email)) {
		$errors[] = 'Un compte avec cette adresse e-mail existe déjà';
	}
    if (empty($pseudo)) {
        $errors[] = 'Pseudo requis';
    }
	elseif (!preg_match('/^[a-z0-9!#$%&\'*+\/=?_-]{1,50}$/i', $pseudo)) {
        $errors[] = 'Votre pseudo contient des caractères invalides';
    }
	elseif (AuthMgr::pseudoExists($pseudo)) {
		$errors[] = 'Un compte avec ce pseudo existe déjà';
	}
    if (empty($password)) {
        $errors[] = 'Mot de passe requis';
    }
    elseif (!$pwLength || !$pwLowercase || !$pwUppercase || !$pwNumber || !$pwSpecialchar) {
        $errors[] = 'Le mot de passe ne satisfait pas les conditions (8 caractères et AU MOINS 1 minuscule, 1 majuscule, 1 chiffre, 1 caractère spécial)';
    }
    elseif (empty($passwordConf)) {
        $errors[] = 'Confirmation du mot de passe requise';
    }
    elseif (strcmp($password, $passwordConf) !== 0) {
        $errors[] = 'Les mots de passe ne correspondent pas';
    }

    // Insert user into DB
    if (empty($errors)) {
        if (!AuthMgr::setUser($email, $pseudo, $password, $token)) {
            $_SESSION['message_error'][] = 'L\'inscription a échoué, veuillez réessayer ultérieurement';
        }
		else {
            // Send confirmation email to user.
			$emailSent = sendMail('signup.html', [
				'{link_confirm}' => $settings['site_url'] . '/email-verification?token=' . $token,
			], 'Inscription sur ' . $settings['site_name'], $email);
			
            if (!$emailSent) {
                $_SESSION['message_status'][] = 'Votre inscription est prise en compte';
                $_SESSION['message_error'][] = 'L\'envoi de l\'e-mail de confirmation a échoué, <a href="/email-verification?token=' . $token . '">renvoyer l\'e-mail</a>';
				
                header('location: /connexion');
				exit;
            }
			else {
				$_SESSION['message_status'][] = 'Un lien de confirmation vous a été adressé à <em>' . $email . '</em> pour finaliser votre inscription';

                header('location: /connexion');
				exit;
            }
        }
    }
}

// --------------- LOGIN ---------------
if (isset($_POST['login-btn'])) {
    if (empty($_POST['pseudo'])) {
        $errors[] = 'Pseudo/e-mail requis';
    }
    if (empty($_POST['password'])) {
        $errors[] = 'Mot de passe requis';
    }
// TODO ajouter check user deleted
    if (empty($errors)) {
		$checkLogin = AuthMgr::checkLogin($_POST['pseudo'], $_POST['password']);
		
        switch ($checkLogin['status']) {
			case 'error':
				$errors[] = 'Mauvais pseudo ou mot de passe';
				break;
				
			case 'not_confirmed':
				$errors[] = 'Veuillez confirmer votre compte, si vous n\'avez pas reçu d\'e-mail de confirmation <a href="/email-confirmation?token=' . $checkLogin['user']['secure_key'] . '&amp;email=' . $checkLogin['user']['email'] . '">cliquez ici</a>';
				break;
				
			case 'ok':
				switch ($checkLogin['user']['user_type']) {
					case 'admin':
					case 'admprinter':
						$redirect = '/index.php?action=admin';
						break;
						
					case 'user':
					default:
						$redirect = '/mon-compte';
						// Redirection si la connexion se fait durant le tunnel de paiement.
						if (!empty($_SESSION['tunnel'])) {
							$redirect = $_SESSION['tunnel'];
						}
						break;
				}
				
                // storing the user's data in the session generates his connection
				$_SESSION['user'] = $checkLogin['user'];
				
				header('location: ' . $redirect);
				exit;
				break;
        }
    }
}

// --------------- FORGOT PASSWORD ---------------
if (isset($_POST['forgot-password-btn'])) {
	$email = trim($_POST['email']);
	
	$validator = new EmailValidator();
	
    if (empty($email)) {
        $errors[] = 'E-mail requis';
    }
	elseif (!$validator->isValid($email, new RFCValidation())) {
		$errors[] = 'E-mail invalide';
	}

    if (empty($errors)) {
		$checkAuth = AuthMgr::getUserByEmail($email);
		
        if (!$checkAuth) {
			$_SESSION['message_error'][] = 'Adresse e-mail introuvable';
		}
		else {
			$emailSent = sendMail('forgot-password.html', [
				'{link_initialize}' => $settings['site_url'] . '/reinitialiser-mot-de-passe?token=' . $checkAuth['secure_key'] . '&amp;email=' . $email,
			], 'Récupération de mot de passe sur ' . $settings['site_name'], $email);
			
            if (!$emailSent) {
                $_SESSION['message_error'][] = 'L\'envoi de l\'e-mail de récupération de mot de passe a échoué, veuillez réessayer ultérieurement';
            }
			else {
				$_SESSION['message_status'][] = 'Un lien de récupération de mot de passe vous a été envoyé. Cliquez dessus pour réinitialiser votre mot de passe.';
            }
			header('location: /mot-de-passe-oublie');
			exit;
        }
    }
}

// --------------- RESET PASSWORD ---------------
if (isset($_POST['reset-password-btn'])) {
	$password = trim($_POST['password']);
	$passwordConf = trim($_POST['passwordConf']);
	
	$pwLength = mb_strlen($password) >= 8;
	$pwLowercase = preg_match('/[a-z]/', $password);
	$pwUppercase = preg_match('/[A-Z]/', $password);
	$pwNumber = preg_match('/[0-9]/', $password);
	$pwSpecialchar = preg_match('/[' . preg_quote('-_"%\'*;<>?^`{|}~/\\#=&', '/') . ']/', $password);
	
    if (empty($password)) {
        $errors[] = 'Mot de passe requis';
    }
    elseif (!$pwLength || !$pwLowercase || !$pwUppercase || !$pwNumber || !$pwSpecialchar) {
        $errors[] = 'Le mot de passe ne satisfait pas les conditions (8 caractères et AU MOINS 1 minuscule, 1 majuscule, 1 chiffre, 1 caractère spécial)';
    }
    elseif (empty($passwordConf)) {
        $errors[] = 'Confirmation du mot de passe requise';
    }
    elseif (strcmp($password, $passwordConf) !== 0) {
        $errors[] = 'Les mots de passe ne correspondent pas';
    }

    if (empty($errors)) {
		$checkAuth = AuthMgr::resetPassword($password, $_GET['token'], $_GET['email']);
		// if coming from "admin : ajouter un utilisateur", auto validate subscription (no need to verify email)
		// TODO varirabliser $_GET['sc']
		if (isset($_GET['sc']) && $_GET['sc'] == 'Tl-BfTxzHhr1n4.Q') {
			AuthMgr::verifyEmail($_GET['token']);
		}

		switch ($checkAuth) {
			case 'db_connection_failed':
				$errors[] = 'La connexion a échoué, veuillez réessayer ultérieurement';
				break;
				
			case 'user_not_found':
				$errors[] = 'L\'utilisateur est introuvable, veuillez vérifier le lien contenu dans l\'e-mail de récupération de mot de passe';
				break;
				
			case 'password_updated':
				$_SESSION['message_status'][] = 'Votre mot de passe a été modifié, vous pouvez vous connecter';
				$_SESSION['user']['email_value'] = $_GET['email'];

				header('location: /connexion');
				exit;
				break;
		}
    }
}

// --------------- LOGOUT ---------------
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    AuthMgr::disconnectUser();
    header('location: /connexion');
    exit;
}

// TODO vider les enregistrements (token) non confirmés après 15 minutes (cronjob).

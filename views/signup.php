<?php

require_once 'controllers/authController.php';

unset($_SESSION['user']);

$css = '<link rel="stylesheet" href="/public/css/password.css">';

require_once 'views/head.php';
?>

<div class="row">
	<div class="col-12 form-wrapper auth">
		<h1>Créez votre compte</h1>
		<?php echo displayMessage($errors); ?>
		<form id="signup-form" action="" method="post">
			<div class="form-group">
				<label for="signup-email">Adresse e-mail</label>
				<div id="message_email_doublon"></div>
				<input type="email" id="signup-email" name="email" class="form-control" value="<?php echo htmlentities($email, ENT_QUOTES); ?>" required="required" pattern="[a-zA-Z0-9](\w\.?)*[a-zA-Z0-9]@[a-zA-Z0-9]+\.[a-zA-Z]{2,6}">
			</div>
			<div class="form-group">
				<label for="signup-pseudo">Pseudo (pour la connexion)</label>
				<div id="message_pseudo_doublon"></div>
				<input type="text" id="signup-pseudo" name="pseudo" class="form-control" value="<?php echo htmlentities($pseudo, ENT_QUOTES); ?>" required="required">
			</div>
			<div class="form-group">
				<label for="signup-password">Mot de passe</label>
				<input type="password" id="signup-password" name="password" class="form-control" required="required">
			</div>
			<div class="form-group">
				<label for="signup-passwordC">Confirmation du mot de passe</label>
				<input type="password" id="signup-passwordC" name="passwordConf" class="form-control" required="required">
			</div>
			<div id="message">
				<p><b>Le mot de passe doit contenir&nbsp;</b></p>
				<p id="letter" class="invalid">Une lettre <b>minuscule</b></p>
				<p id="capital" class="invalid">Une lettre <b>majuscule</b></p>
				<p id="number" class="invalid">Un <b>nombre</b></p>
				<p id="specialchar" class="invalid">Un <b>caractère spécial</b></p>
				<p id="length" class="invalid">Au moins <b>8 caractères</b></p>
			</div>
			<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
			<button type="submit" id="signup-btn" name="signup-btn" class="btn btn-primary">Inscription</button>
		</form>
		<p class="mt-5">Vous avez déjà un compte&nbsp;? <a href="/connexion">Connectez-vous</a></p>
	</div>
</div>

<?php

// TODO validation jQuery
$javascript = '';

require_once 'views/footer.php';

<?php

if (empty($_SESSION['user']['id_user'])) {
    header('location: /connexion');
	exit;
}

if (!empty($_SESSION['user']['user_type']) && 'admin' != $_SESSION['user']['user_type']) {
    header('location: /');
	exit;
}

require_once 'controllers/adminUsersController.php';

$css = '
<link rel="stylesheet" href="/public/css/admin.css">
<link rel="stylesheet" href="/public/css/jquery-ui.css">
<link rel="stylesheet" href="/public/css/password.css">
';

require_once 'views/head.php';
?>

<div class="row">
	<div class="col-12">
		<h1>Utilisateurs</h1>
		<?php echo displayMessage($errors); ?>
	</div>
</div>

<!-- TODO Tabs with 'deleted users' -->

<div class="row">
	<div class="col-12">
		<?php if (isset($_GET['edit']) && empty($_GET['edit'])) : // add 
		?>
			<p><a href="?action=adminUsers"><i class="fas fa-long-arrow-alt-left"></i> Annuler</a></p>
			<form action="" method="post" class="mb-4">
				<div class="form-group">
					<label for="signup-user_type">Type de compte</label>
					<select id="signup-user_type" name="user_type" class="form-control" required>
						<option value="">-- Sélectionner --</option>
						<?php foreach ($settings['accounts'] as $accountType => $accountName) : ?>
							<option value="<?php echo $accountType; ?>" <?php echo ($accountType == $user_user_type ? ' selected' : ''); ?>><?php echo $accountName; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="form-group">
					<label for="signup-email">Adresse e-mail</label>
					<div id="message_email_doublon"></div>
					<input type="email" id="signup-email" name="email" class="form-control" value="<?php echo htmlentities($user_email, ENT_QUOTES); ?>" required="required" pattern="[a-zA-Z0-9](\w\.?)*[a-zA-Z0-9]@[a-zA-Z0-9]+\.[a-zA-Z]{2,6}">
				</div>
				<div class="form-group">
					<label for="signup-pseudo">Pseudo (pour la connexion)</label>
					<div id="message_pseudo_doublon"></div>
					<input type="text" id="signup-pseudo" name="pseudo" class="form-control" value="<?php echo htmlentities($user_pseudo, ENT_QUOTES); ?>" required="required">
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
				<p class="text-danger">L'utilisateur sera notifié par e-mail.</p>
				<button type="submit" name="add-user-btn" class="btn btn-primary">Ajouter</button>
			</form>

		<?php elseif (!empty($_GET['edit'])) : // upd 
		?>
			<p><a href="?action=adminUsers"><i class="fas fa-long-arrow-alt-left"></i> Annuler</a></p>
			<form action="" method="post" class="mb-4">
				<div class="form-group">
					<label for="signup-email">Adresse e-mail</label>
					<input type="email" id="signup-email" name="email" class="form-control" value="<?php echo htmlentities($user_email, ENT_QUOTES); ?>" disabled>
				</div>
				<div class="form-group">
					<label for="signup-user_type">Type de compte</label>
					<select id="signup-user_type" name="user_type" class="form-control">
						<option value="">-- Sélectionner --</option>
						<?php foreach ($settings['accounts'] as $accountType => $accountName) : ?>
							<option value="<?php echo $accountType; ?>" <?php echo ($accountType == $user_user_type ? ' selected' : ''); ?>><?php echo $accountName; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="form-group">
					<label for="signup-first_name">Prénom</label>
					<input type="text" id="signup-first_name" name="first_name" class="form-control" value="<?php echo htmlentities($user_first_name, ENT_QUOTES); ?>">
				</div>
				<div class="form-group">
					<label for="signup-last_name">Nom</label>
					<input type="text" id="signup-last_name" name="last_name" class="form-control" value="<?php echo htmlentities($user_last_name, ENT_QUOTES); ?>">
				</div>
				<div class="form-group">
					<label for="signup-phone">Téléphone</label>
					<input type="text" id="signup-phone" name="phone" class="form-control" value="<?php echo htmlentities($user_phone, ENT_QUOTES); ?>">
				</div>
				<p class="text-danger">L'utilisateur sera notifié par e-mail.</p>
				<input type="hidden" name="id_user" value="<?php echo (int) $id; ?>">
				<button type="submit" name="upd-user-btn" class="btn btn-primary">Modifier</button>
			</form>
		<?php else : ?>
			<p><a href="?action=adminUsers&amp;edit"><i class="fas fa-plus-circle"></i> Ajouter un utilisateur</a></p>
		<?php endif; ?>
		<form action="" method="get">
			<input type="hidden" name="action" value="adminUsers">
			<p class="float-left"><?php echo $numUsers . ' ' . ($numUsers > 1 ? 'résultats' : 'résultat'); ?></p>
			<p class="float-right"><?php echo $pagination->getPageResultDisplay(); ?></p>
			<div class="clear"></div>
			<table class="table table-striped table-bordered table-hover table-sm table-responsive mt-3">
				<thead class="thead-light">
					<tr>
						<th class="align-top">Date <?php echo getOrderArrows(1, $sort_order, $sort_way); ?></th>
						<th class="align-top">Prénom <?php echo getOrderArrows(2, $sort_order, $sort_way); ?><br>
							nom <?php echo getOrderArrows(3, $sort_order, $sort_way); ?></th>
						<th class="align-top">Adresse e-mail <?php echo getOrderArrows(4, $sort_order, $sort_way); ?><br>
							Téléphone <?php echo getOrderArrows(5, $sort_order, $sort_way); ?></th>
						<th class="align-top">Confirmé <?php echo getOrderArrows(6, $sort_order, $sort_way); ?></th>
						<th class="align-top">Type <?php echo getOrderArrows(7, $sort_order, $sort_way); ?></th>
						<th class="align-top">Nb de comm. <?php echo getOrderArrows(8, $sort_order, $sort_way); ?></th>
						<th class="align-top">Actions</th>
					</tr>
					<tr>
						<th class="align-top">
							<input type="text" name="date_from" id="date_from" value="<?php echo htmlentities($date_from_fr, ENT_QUOTES); ?>" placeholder="De" title="De">
							<input type="text" name="date_to" id="date_to" value="<?php echo htmlentities($date_to_fr, ENT_QUOTES); ?>" placeholder="À" title="À">
						</th>
						<th class="align-top">
							<input type="text" name="first_name" value="<?php echo htmlentities($firstname, ENT_QUOTES); ?>" placeholder="Prénom" title="Prénom">
							<input type="text" name="last_name" value="<?php echo htmlentities($lastname, ENT_QUOTES); ?>" placeholder="Nom" title="Nom">
						</th>
						<th class="align-top">
							<input type="text" name="email" value="<?php echo htmlentities($email, ENT_QUOTES); ?>" placeholder="E-mail" title="E-mail">
							<input type="text" name="phone" value="<?php echo htmlentities($phone, ENT_QUOTES); ?>" placeholder="Téléphone" title="Téléphone">
						</th>
						<th class="align-top">
							<select name="confirmed">
								<option value="">-- Sélectionner --</option>
								<option value="1" <?php echo ($confirmed === '1' ? ' selected' : ''); ?>>Confirmé</option>
								<option value="0" <?php echo ($confirmed === '0' ? ' selected' : ''); ?>>Non confirmé</option>
							</select>
						</th>
						<th class="align-top">
							<select name="user_type[]" multiple style="min-width:5em;height:4em;">
								<?php foreach ($settings['accounts'] as $accountType => $accountName) : ?>
									<option value="<?php echo $accountType; ?>" <?php echo (in_array($accountType, $userType) ? ' selected' : ''); ?>><?php echo $accountName; ?></option>
								<?php endforeach; ?>
							</select>
						</th>
						<th class="align-top">
							<input type="text" name="num_orders" value="<?php echo htmlentities($numOrders, ENT_QUOTES); ?>">
						</th>
						<th class="align-top">
							<input type="hidden" name="sort_order" value="<?php echo $sort_order; ?>">
							<input type="hidden" name="sort_way" value="<?php echo $sort_way; ?>">
							<button class="btn btn-primary btn-sm" name="filter">Filtrer</button><br>
							<a class="btn btn-secondary btn-sm" href="/index.php?action=adminUsers" title="Supprimer les filtres">×</a>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php if (empty($users)) : ?>
						<tr>
							<td colspan="7">Aucun utilisateur</td>
						</tr>
					<?php else : ?>
						<?php foreach ($users as $user) : ?>
							<tr>
								<td><?php echo date('d-m-Y H:i', strtotime($user['date_add'])); ?></td>
								<td>
									<?php echo $user['first_name']; ?><br>
									<?php echo $user['last_name']; ?><br>
								</td>
								<td>
									<?php echo $user['email']; ?><br>
									<?php echo $user['phone']; ?><br>
								</td>
								<td>
									<?php echo $user['subscr_confirmed'] ? '✔️' : '❌'; ?><br>
								</td>
								<td>
									<?php echo $settings['accounts'][$user['user_type']]; ?><br>
								</td>
								<td>
									<?php echo $user['num_orders']; ?><br>
								</td>
								<td>
									<a href="/index.php?action=adminUsers&amp;edit=<?php echo $user['id_user']; ?>" title="Modifier"><i class="fas fa-pen"></i></a>
									<a href="?action=adminUsers&amp;del=<?php echo $user['id_user']; ?>" onclick="return confirm('Voulez-vous vraiment supprimer cet utilisateur ?')" title="Supprimer"><i class="fas fa-trash"></i></a>
									<?php if (empty($user['subscr_confirmed'])) : ?>
										<br><a href="/index.php?action=adminUsers&amp;resend-confirmation-link=<?php echo $user['id_user']; ?>" onclick="return confirm('Voulez-vous vraiment renvoyer le lien de confirmation à cet utilisateur ?')" title="Renvoyer le lien de confirmation"><i class="fas fa-check-square"></i></a>
									<?php endif; ?>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</form>
		<?php echo $pagination->render(); ?>
	</div>
</div>

<?php

$javascript = '
<script src="/public/js/jquery-ui.min.js"></script>
<script>
$(function() {
	$("#date_from").datepicker({
		dateFormat: "dd-mm-yy",
		dayNames: ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"],
		dayNamesMin: ["Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa"],
		monthNames: ["Janvier","Février","Mars","Avril","Mai","Juin","Juillet","Août","Septembre","Octobre","Novembre","Décembre"],
		monthNamesShort: ["Jan","Fev","Mar","Avr","Mai","Jun","Jul","Aou","Sep","Oct","Nov","Dec"],
		firstDay: 1,
		maxDate: "+0d",
		minDate: "-10y",
		changeMonth: true,
		changeYear: true,
		//showOn: "both",
		//buttonText: "Choisir",
		nextText: "Suivant",
		prevText: "Précédent",
		onSelect: function(selected){
			$("#date_to").datepicker("option", "minDate", selected);
		}
	});
	$("#date_to").datepicker({
		dateFormat: "dd-mm-yy",
		dayNames: ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"],
		dayNamesMin: ["Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa"],
		monthNames: ["Janvier","Février","Mars","Avril","Mai","Juin","Juillet","Août","Septembre","Octobre","Novembre","Décembre"],
		monthNamesShort: ["Jan","Fev","Mar","Avr","Mai","Jun","Jul","Aou","Sep","Oct","Nov","Dec"],
		firstDay: 1,
		maxDate: "+0d",
		minDate: "-10y",
		changeMonth: true,
		changeYear: true,
		//showOn: "both",
		//buttonText: "Choisir",
		nextText: "Suivant",
		prevText: "Précédent",
		onSelect: function(selected){
			$("#date_from").datepicker("option", "maxDate", selected);
		}
	});
});
</script>';
require_once 'views/footer.php';

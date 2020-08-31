<?php

if (empty($_SESSION['user']['id_user'])) {
    header('location: index.php?action=login');
	exit;
}

// protège accès direct à http://localhost/views/admin.php (views devra etre interdit avec htaccess)
if (!empty($_SESSION['user']['user_type']) && 'admin' != $_SESSION['user']['user_type']) {
    header('location: /index.php?action=logout');
	exit;
}

require_once 'controllers/adminUsersController.php';

$css = '
<link rel="stylesheet" href="/public/css/admin.css">
<link rel="stylesheet" href="/public/css/jquery-ui.css">
';

require_once 'views/head.php';
?>

<div class="row">
	<div class="col-12">
		<h1>Utilisateurs</h1>
		<?php echo displayMessage(); ?>
	</div>
</div>

<div class="row">
	<div class="col-12">
<?php if (isset($_GET['edit'])): // add/upd ?>
		<p><a href="?action=adminUsers"><i class="fas fa-long-arrow-alt-left"></i> Annuler</a></p>
		<form action="" method="post" class="mb-4">
			<div class="form-group">
				<label for="signup-user_type">Type de compte</label>
				<select id="signup-user_type" name="user_type" class="form-control">
					<option value="">-- Sélectionner --</option>
					<option value="admin">Administrateur</option>
					<option value="admprinter">Imprimeur</option>
					<option value="user">Utilisateur</option>
				</select>
			</div>
			<div class="form-group">
				<label for="signup-email">Adresse e-mail</label>
				<input type="email" id="signup-email" name="email" class="form-control" value="<?php echo htmlentities($email, ENT_QUOTES); ?>" required="required" pattern="[a-zA-Z0-9](\w\.?)*[a-zA-Z0-9]@[a-zA-Z0-9]+\.[a-zA-Z]{2,6}">
			</div>
			<div class="form-group">
				<label for="signup-pseudo">Pseudo (pour la connexion)</label>
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
			<div class="form-check">
				<input type="checkbox" id="signup-subsc_confirmed" name="subsc_confirmed" class="form-check-input" value="1"<?php echo ($user_subscr_confirmed == 1 ? ' checked' : ''); ?>>
				<label class="form-check-label" for="signup-subsc_confirmed">Utilisateur confirmé</label>
			</div>
			<button type="submit" id="signup-btn" name="signup-btn" class="btn btn-primary">Valider</button>
		</form>
<?php else: ?>
		<p><a href="?action=adminUsers&amp;edit"><i class="fas fa-plus-circle"></i> Ajouter un utilisateur</a></p>
<?php endif; ?>
		<form action="" method="get">
			<input type="hidden" name="action" value="adminUsers">
			<p class="float-left"><?php echo $numUsers . ' ' . ($numUsers > 1 ? 'résultats' : 'résultat'); ?></p>
			<p class="float-right"><?php echo ($limitFrom + 1 == $limitTo ? $limitTo : ($limitFrom + 1) . ' → ' . $limitTo); ?>&nbsp;/ <?php echo $numUsers; ?></p>
			<div class="clear"></div>
			<table class="table table-striped table-bordered table-hover table-sm table-responsive mt-3">
				<thead class="thead-light">
					<tr>
						<th class="align-top">Date <span class="order"><a<?php echo ($sort_order == 1 && $sort_way == 1 ? ' class="active"' : ''); ?> href="<?php echo getUrl(array('sort_order'=>'1', 'sort_way'=>'1')); ?>">▲</a><a<?php echo ($sort_order == 1 && $sort_way == 2 ? ' class="active"' : ''); ?> href="<?php echo getUrl(array('sort_order'=>'1', 'sort_way'=>'2')); ?>">▼</a></span></th>
						<th class="align-top">Prénom <span class="order"><a<?php echo ($sort_order == 2 && $sort_way == 1 ? ' class="active"' : ''); ?> href="<?php echo getUrl(array('sort_order'=>'2', 'sort_way'=>'1')); ?>">▲</a><a<?php echo ($sort_order == 2 && $sort_way == 2 ? ' class="active"' : ''); ?> href="<?php echo getUrl(array('sort_order'=>'2', 'sort_way'=>'2')); ?>">▼</a></span><br>
						nom <span class="order"><a<?php echo ($sort_order == 3 && $sort_way == 1 ? ' class="active"' : ''); ?> href="<?php echo getUrl(array('sort_order'=>'3', 'sort_way'=>'1')); ?>">▲</a><a<?php echo ($sort_order == 3 && $sort_way == 2 ? ' class="active"' : ''); ?> href="<?php echo getUrl(array('sort_order'=>'3', 'sort_way'=>'2')); ?>">▼</a></span></th>
						<th class="align-top">Adresse e-mail <span class="order"><a<?php echo ($sort_order == 4 && $sort_way == 1 ? ' class="active"' : ''); ?> href="<?php echo getUrl(array('sort_order'=>'4', 'sort_way'=>'1')); ?>">▲</a><a<?php echo ($sort_order == 4 && $sort_way == 2 ? ' class="active"' : ''); ?> href="<?php echo getUrl(array('sort_order'=>'4', 'sort_way'=>'2')); ?>">▼</a></span><br>
						Téléphone <span class="order"><a<?php echo ($sort_order == 5 && $sort_way == 1 ? ' class="active"' : ''); ?> href="<?php echo getUrl(array('sort_order'=>'5', 'sort_way'=>'1')); ?>">▲</a><a<?php echo ($sort_order == 5 && $sort_way == 2 ? ' class="active"' : ''); ?> href="<?php echo getUrl(array('sort_order'=>'5', 'sort_way'=>'2')); ?>">▼</a></span></th>
						<th class="align-top">Confirmé <span class="order"><a<?php echo ($sort_order == 6 && $sort_way == 1 ? ' class="active"' : ''); ?> href="<?php echo getUrl(array('sort_order'=>'6', 'sort_way'=>'1')); ?>">▲</a><a<?php echo ($sort_order == 6 && $sort_way == 2 ? ' class="active"' : ''); ?> href="<?php echo getUrl(array('sort_order'=>'6', 'sort_way'=>'2')); ?>">▼</a></span></th>
						<th class="align-top">Type <span class="order"><a<?php echo ($sort_order == 7 && $sort_way == 1 ? ' class="active"' : ''); ?> href="<?php echo getUrl(array('sort_order'=>'7', 'sort_way'=>'1')); ?>">▲</a><a<?php echo ($sort_order == 7 && $sort_way == 2 ? ' class="active"' : ''); ?> href="<?php echo getUrl(array('sort_order'=>'7', 'sort_way'=>'2')); ?>">▼</a></span></th>
						<th class="align-top">Nb de comm. <span class="order"><a<?php echo ($sort_order == 8 && $sort_way == 1 ? ' class="active"' : ''); ?> href="<?php echo getUrl(array('sort_order'=>'8', 'sort_way'=>'1')); ?>">▲</a><a<?php echo ($sort_order == 8 && $sort_way == 2 ? ' class="active"' : ''); ?> href="<?php echo getUrl(array('sort_order'=>'8', 'sort_way'=>'2')); ?>">▼</a></span></th>
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
								<option value="1"<?php echo ($confirmed === '1' ? ' selected' : ''); ?>>Confirmé</option>
								<option value="0"<?php echo ($confirmed === '0' ? ' selected' : ''); ?>>Non confirmé</option>
							</select>
						</th>
						<th class="align-top">
							<select name="user_type[]" multiple style="min-width:5em;height:4em;">
								<option value="admin"<?php echo (in_array('admin', $userType) ? ' selected' : ''); ?>>Admin</option>
								<option value="admprinter"<?php echo (in_array('admprinter', $userType) ? ' selected' : ''); ?>>Printer</option>
								<option value="user"<?php echo (in_array('user', $userType) ? ' selected' : ''); ?>>User</option>
							</select>
						</th>
						<th class="align-top">
							<input type="text" name="num_orders" value="<?php echo htmlentities($numOrders, ENT_QUOTES); ?>">
						</th>
						<th class="align-top">
							<button class="btn btn-primary btn-sm" name="filter">Filtrer</button><br>
							<a class="btn btn-secondary btn-sm" href="/index.php?action=adminUsers" title="Supprimer les filtres">×</a>
						</th>
					</tr>
				</thead>
				<tbody>
<?php if (empty($users)): ?>
					<tr><td colspan="7">Aucun utilisateur</td></tr>
<?php else: ?>
	<?php foreach ($users as $user): ?>
					<tr>
						<td><?php echo date('d-m-Y H:i', strtotime($user['date_add'])); ?></td>
						<td>
							<?php echo $user['first_name']; ?><br>
							<?php echo $user['last_name']; ?><br>
						</td>
						<td>
			<?php if (!empty($user['num_orders'])): ?>
							<a href="/index.php?action=adminOrders&amp;email=<?php echo $user['email']; ?>&amp;filter" title="Voir ses commandes"><?php echo $user['email']; ?></a><br>
			<?php else: ?>
							<?php echo $user['email']; ?><br>
							<?php echo $user['phone']; ?><br>
			<?php endif; ?>
						</td>
						<td>
							<?php echo $user['subscr_confirmed']; ?><br>
						</td>
						<td>
							<?php echo $user['user_type']; ?><br>
						</td>
						<td>
							<?php echo $user['num_orders']; ?><br>
						</td>
						<td>
							<a href="/index.php?action=adminUsers&amp;edit=<?php echo $user['id_user']; ?>" title="Modifier"><i class="fas fa-pen"></i></a>
							<a href="?action=adminUsers&amp;del=<?php echo $user['id_user']; ?>" onclick="return confirm('Voulez-vous vraiment supprimer cet utilisateur ?')" title="Supprimer"><i class="fas fa-trash"></i></a>
							<!--a href="/index.php?action=adminGetInvoice&amp;id=<?php echo $user['id_user']; ?>" title="PDF"><i class="fas fa-file-pdf"></i></a>
							<a href="/index.php?action=adminGetInvoice&amp;id=<?php echo $user['id_user']; ?>" title="Étiquette"><i class="fas fa-receipt"></i></a>
							<a href="/index.php?action=adminUsers&amp;archive=<?php echo $user['id_user']; ?>" onclick="return confirm('Voulez-vous vraiment archiver cette commande ?')" title="Archiver"><i class="fas fa-archive"></i></a-->
						</td>
					</tr>
	<?php endforeach; ?>
<?php endif; ?>
				</tbody>
			</table>
		</form>
		<?php echo $pagination->render($paginationPages); ?>
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

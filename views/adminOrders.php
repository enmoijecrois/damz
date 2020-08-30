<?php

if (empty($_SESSION['user']['id_user'])) {
    header('location: index.php?action=login');
	exit;
}

// protège accès direct à http://localhost/views/admin.php (views devra etre interdit avec htaccess)
if (
	!empty($_SESSION['user']['user_type']) &&
	!in_array($_SESSION['user']['user_type'], ['admin', 'admprinter'])
) {
    header('location: /index.php?action=logout');
	exit;
}

require_once 'controllers/adminOrdersController.php';

$css = '
<link rel="stylesheet" href="/public/css/admin.css">
<link rel="stylesheet" href="/public/css/jquery-ui.css">
';

require_once 'views/head.php';
?>

<div class="row">
	<div class="col-12">
		<h1>Commandes</h1>
		<?php echo displayMessage(); ?>
	</div>
</div>

<ul class="nav nav-tabs">
	<li class="nav-item"><a class="nav-link active" href="/index.php?action=adminOrders">Commandes</a></li>
	<li class="nav-item"><a class="nav-link" href="/index.php?action=adminOrdersPast">Historique</a></li>
</ul>

<div class="row">
	<div class="col-12">
		<form action="/index.php?action=adminOrders" method="get">
			<input type="hidden" name="action" value="adminOrders">
			<p class="mt-3 float-left"><?php echo $numOrders . ' ' . ($numOrders > 1 ? 'résultats' : 'résultat'); ?></p>
			<p class="mt-3 float-right"><?php echo ($limitFrom + 1); ?> → <?php echo $limitTo; ?>&nbsp;/ <?php echo $numOrders; ?></p>
			<div class="clear"></div>
			<table class="table table-striped table-bordered table-hover table-sm table-responsive mt-3">
				<thead class="thead-light">
					<tr>
						<th class="align-top">Date <span class="order"><a<?php echo ($sort_order == 1 && $sort_way == 1 ? ' class="active"' : ''); ?> href="<?php echo getUrl(array('sort_order'=>'1', 'sort_way'=>'1')); ?>">▲</a><a<?php echo ($sort_order == 1 && $sort_way == 2 ? ' class="active"' : ''); ?> href="<?php echo getUrl(array('sort_order'=>'1', 'sort_way'=>'2')); ?>">▼</a></span></th>
						<th class="align-top">Prénom <span class="order"><a<?php echo ($sort_order == 2 && $sort_way == 1 ? ' class="active"' : ''); ?> href="<?php echo getUrl(array('sort_order'=>'2', 'sort_way'=>'1')); ?>">▲</a><a<?php echo ($sort_order == 2 && $sort_way == 2 ? ' class="active"' : ''); ?> href="<?php echo getUrl(array('sort_order'=>'2', 'sort_way'=>'2')); ?>">▼</a></span><br>
						nom <span class="order"><a<?php echo ($sort_order == 3 && $sort_way == 1 ? ' class="active"' : ''); ?> href="<?php echo getUrl(array('sort_order'=>'3', 'sort_way'=>'1')); ?>">▲</a><a<?php echo ($sort_order == 3 && $sort_way == 2 ? ' class="active"' : ''); ?> href="<?php echo getUrl(array('sort_order'=>'3', 'sort_way'=>'2')); ?>">▼</a></span></th>
						<th class="align-top">Adresse e-mail <span class="order"><a<?php echo ($sort_order == 4 && $sort_way == 1 ? ' class="active"' : ''); ?> href="<?php echo getUrl(array('sort_order'=>'4', 'sort_way'=>'1')); ?>">▲</a><a<?php echo ($sort_order == 4 && $sort_way == 2 ? ' class="active"' : ''); ?> href="<?php echo getUrl(array('sort_order'=>'4', 'sort_way'=>'2')); ?>">▼</a></span><br>
						Téléphone <span class="order"><a<?php echo ($sort_order == 5 && $sort_way == 1 ? ' class="active"' : ''); ?> href="<?php echo getUrl(array('sort_order'=>'5', 'sort_way'=>'1')); ?>">▲</a><a<?php echo ($sort_order == 5 && $sort_way == 2 ? ' class="active"' : ''); ?> href="<?php echo getUrl(array('sort_order'=>'5', 'sort_way'=>'2')); ?>">▼</a></span></th>
						<th class="align-top">Adresse <span class="order"><a<?php echo ($sort_order == 6 && $sort_way == 1 ? ' class="active"' : ''); ?> href="<?php echo getUrl(array('sort_order'=>'6', 'sort_way'=>'1')); ?>">▲</a><a<?php echo ($sort_order == 6 && $sort_way == 2 ? ' class="active"' : ''); ?> href="<?php echo getUrl(array('sort_order'=>'6', 'sort_way'=>'2')); ?>">▼</a></span></th>
						<th class="align-top">Commande <span class="order"><a<?php echo ($sort_order == 7 && $sort_way == 1 ? ' class="active"' : ''); ?> href="<?php echo getUrl(array('sort_order'=>'7', 'sort_way'=>'1')); ?>">▲</a><a<?php echo ($sort_order == 7 && $sort_way == 2 ? ' class="active"' : ''); ?> href="<?php echo getUrl(array('sort_order'=>'7', 'sort_way'=>'2')); ?>">▼</a></span></th>
						<th class="align-top">Actions</th>
					</tr>
					<tr>
						<th>
							<input type="text" name="date_from" id="date_from" value="<?php echo htmlentities($date_from_fr, ENT_QUOTES); ?>" placeholder="De" title="De">
							<input type="text" name="date_to" id="date_to" value="<?php echo htmlentities($date_to_fr, ENT_QUOTES); ?>" placeholder="À" title="À">
						</th>
						<th>
							<input type="text" name="first_name" value="<?php echo htmlentities($firstname, ENT_QUOTES); ?>" placeholder="Prénom" title="Prénom">
							<input type="text" name="last_name" value="<?php echo htmlentities($lastname, ENT_QUOTES); ?>" placeholder="Nom" title="Nom">
						</th>
						<th>
							<input type="text" name="email" value="<?php echo htmlentities($email, ENT_QUOTES); ?>" placeholder="E-mail" title="E-mail">
							<input type="text" name="phone" value="<?php echo htmlentities($phone, ENT_QUOTES); ?>" placeholder="Téléphone" title="Téléphone">
						</th>
						<th>
							<input type="text" name="zip_code" value="<?php echo htmlentities($zipcode, ENT_QUOTES); ?>" placeholder="Code postal" title="Code postal">
							<input type="text" name="city" value="<?php echo htmlentities($city, ENT_QUOTES); ?>" placeholder="Ville" title="Ville">
						</th>
						<th class="align-top">
							<select name="doc_type">
								<option value="">Type de document</option>
								<option value="dossier"<?php echo ($docType === 'dossier' ? ' selected' : ''); ?>>Dossier</option>
								<option value="memoire"<?php echo ($docType === 'memoire' ? ' selected' : ''); ?>>Mémoire</option>
								<option value="these"<?php echo ($docType === 'these' ? ' selected' : ''); ?>>Thèse</option>
								<option value="perso"<?php echo ($docType === 'perso' ? ' selected' : ''); ?>>Personnalisé</option>
							</select>
						</th>
						<th>
							<button class="btn btn-primary btn-sm" name="filter">Filtrer</button><br>
							<a class="btn btn-secondary btn-sm" href="/index.php?action=adminOrders" title="Supprimer les filtres">×</a>
						</th>
					</tr>
				</thead>
				<tbody>
<?php if (empty($orders)): ?>
					<tr><td colspan="6">Aucune commande</td></tr>
<?php else: ?>
	<?php foreach ($orders as $order): ?>
					<tr>
						<td><?php echo date('d-m-Y H:i', strtotime($order['date_add'])); ?></td>
						<td>
							<?php echo $order['first_name']; ?><br>
							<?php echo $order['last_name']; ?><br>
						</td>
						<td>
							<?php echo $order['email']; ?><br>
							<?php echo $order['phone']; ?><br>
						</td>
						<td>
							<?php echo $order['address']; ?><br>
							<?php echo (!empty($order['address2']) ? $order['address2'] . '<br>' : ''); ?>
							<?php echo $order['zip_code'] . ' ' . $order['city']; ?><br>
							<?php echo $order['country_name']; ?><br>
						</td>
						<td>
							<?php echo $settings['mapping'][$order['doc_type']]; ?><br>
							<?php echo $order['nb_page']; ?> pages<br>
							<?php echo $order['quantity'], ' ', ($order['quantity'] > 1 ? 'exemplaires' : 'exemplaire'); ?><br>
							<?php echo ($order['rectoverso'] ? 'recto-verso' : 'recto'); ?><br>
						</td>
						<td>
							<a href="/index.php?action=adminGetInvoice&amp;id=<?php echo $order['id_orders']; ?>" title="Facture"><i class="fas fa-file-invoice-dollar"></i></a>
							<a href="/index.php?action=adminGetInvoice&amp;id=<?php echo $order['id_orders']; ?>" title="PDF"><i class="fas fa-file-pdf"></i></a>
							<a href="/index.php?action=adminGetInvoice&amp;id=<?php echo $order['id_orders']; ?>" title="Étiquette"><i class="fas fa-receipt"></i></a>
							<a href="/index.php?action=adminOrders&amp;archive=<?php echo $order['id_orders']; ?>" onclick="return confirm('Voulez-vous vraiment archiver cette commande ?')" title="Archiver"><i class="fas fa-archive"></i></a>
						</td>
					</tr>
	<?php endforeach; ?>
<?php endif; ?>
				</tbody>
			</table>
		</form>
		<?php echo $pagination->render($paginationPages, 'top'); ?>
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

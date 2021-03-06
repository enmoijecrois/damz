<?php

require_once _ROOT_DIR_ . '/vendor/autoload.php';
require_once _ROOT_DIR_ . '/models/AdminGestionMgr.class.php';

use Spipu\Html2Pdf\Html2Pdf;

// TODO if (empty($_GET['id']) || isset($_GET['archive'])) {
if (empty($_GET['id'])) {
	exit;
}
$id = (int) $_GET['id'];
$archive = $_GET['archive'];
$where = '';
$params = [];

$where .= ' AND o.`id_orders` = :id_orders';
$params[':id_orders'] = $id;
$order = 'id_orders';
$way = 'ASC';

$orders = AdminGestionMgr::getOrders($params, $archive, $where, $order, $way);

$id_orders = $orders[0]["id_orders"];
$date_add = $orders[0]["date_add"];
$id_user = $orders[0]["id_user"];
$id_address = $orders[0]["id_address"];
$nom_fichier = $orders[0]["nom_fichier"];
$nb_page = $orders[0]["nb_page"];
$nb_page_nb = $orders[0]["nb_page_nb"];
$nb_page_c = $orders[0]["nb_page_c"];
$doc_type_ori = $orders[0]["doc_type"];
$doc_type = $settings['mapping'][$orders[0]["doc_type"]];
$couv_ft = $orders[0]["couv_ft"];
$couv_fc = $orders[0]["couv_fc"];
$couv_fc_type = $orders[0]["couv_fc_type"];
$id_couv_color = $orders[0]["couv_fc_color"];
if ($id_couv_color) {
	$result = AdminGestionMgr::getColorsByID($id_couv_color);
	$couv_fc_color = $result['text'];
}
$dos_ft = $orders[0]["dos_ft"];
$dos_fc = $orders[0]["dos_fc"];
$dos_fc_type = $orders[0]["dos_fc_type"];
$id_dos_color = $orders[0]["dos_fc_color"];
if ($id_dos_color) {
	$result = AdminGestionMgr::getColorsByID($id_dos_color);
	$dos_fc_color = $result['text'];
}
$reliure_type = $settings['mapping'][$orders[0]["reliure_type"]];
$reliure_color = $orders[0]["reliure_color"];
$quantity = $orders[0]["quantity"];
$rectoverso = $orders[0]["rectoverso"];
$tva = number_format($orders[0]["tva"], 2, ',', ' ');
$total = number_format($orders[0]["total"], 2, ',', ' ');
$total_num = $orders[0]["total"];
// $archive = $orders[0]["archive"];
$first_name = $orders[0]["first_name"];
$last_name = $orders[0]["last_name"];
$email = $orders[0]["email"];
$phone = $orders[0]["phone"];
// TODO à modifier : doit matcher les nouvelles colonnes dans la table "orders" qui vont enregistrer les infos du user à l'instant T (adresse de livraison, etc) pour que les factures restent identiques en cas de modif des infos du user.
$addr_name = $orders[0]["addr_name"];
$address = $orders[0]["address"];
$address2 = $orders[0]["address2"];
$zip_code = $orders[0]["zip_code"];
$city = $orders[0]["city"];
$country_name = $orders[0]["country_name"];

// Download PDF.
ob_start();

?>

<style type="text/css">
	page {
		line-height: 1.2;
	}
</style>
<page style="font-size:16px;">
	<page_footer>
		<table style="width:100%;">
			<tr>
				<td style="width:50%;text-align:left;">COPIFAC<br> 116 et 106 rue de Geôle<br> 14000 Caen</td>
				<td style="width:50%;text-align:right;">Tél : 02 31 38 98 66<br> E-mail : contact@copifac.fr</td>
			</tr>
		</table>
	</page_footer>
	<table style="width:100%">
		<tr>
			<td style="width:30%;"><img src="<?php echo $settings['site_url']; ?>/public/img/logo_copyfac.png" alt="copyfac" style="width:50mm;background-color:#6690a1;"></td>
			<td style="width:70%;text-align:right;font-size:24px;">Facture N° <?php echo $id_orders; ?></td>
		</tr>
	</table>
	<hr style="color:#ccc;">
	<nobreak>
		<h2 style="font-size:24px;letter-spacing:-1px;">Résumé de la commande</h2>
		<table style="width:100%">
			<tr>
				<td style="width:50%;background:#eee;height:6mm;padding-left:2mm;">
					Nom du fichier
				</td>
				<td style="width:50%;background:#eee;height:6mm;padding-left:2mm;">
					<?php echo (str_replace(' ', '_', $id_orders . '_' . $doc_type_ori) . '_' . date("Y-m-d_H-i", strtotime($date_add)) . '.pdf'); ?>
				</td>
			</tr>
			<tr>
				<td style="width:50%;height:1mm;"> </td>
				<td style="width:50%;height:1mm;"> </td>
			</tr>
			<tr>
				<td style="width:50%;background:#eee;height:6mm;padding-left:2mm;">
					Nombre de pages total
				</td>
				<td style="width:50%;background:#eee;height:6mm;padding-left:2mm;">
					<?php echo $nb_page; ?>
				</td>
			</tr>
			<tr>
				<td style="width:50%;background:#eee;height:6mm;padding-left:2mm;">
					Nombre de pages noir et blanc
				</td>
				<td style="width:50%;background:#eee;height:6mm;padding-left:2mm;">
					<?php echo $nb_page_nb; ?>
				</td>
			</tr>
			<tr>
				<td style="width:50%;background:#eee;height:6mm;padding-left:2mm;">
					Nombre de pages couleur
				</td>
				<td style="width:50%;background:#eee;height:6mm;padding-left:2mm;">
					<?php echo $nb_page_c; ?>
				</td>
			</tr>
			<tr>
				<td style="width:50%;height:1mm;"> </td>
				<td style="width:50%;height:1mm;"> </td>
			</tr>
			<tr>
				<td style="width:50%;background:#eee;height:6mm;padding-left:2mm;">
					Type de document
				</td>
				<td style="width:50%;background:#eee;height:6mm;padding-left:2mm;">
					<?php echo $doc_type; ?>
				</td>
			</tr>
			<tr>
				<td style="width:50%;height:1mm;"> </td>
				<td style="width:50%;height:1mm;"> </td>
			</tr>
			<tr>
				<td style="width:50%;background:#eee;height:6mm;padding-left:2mm;">
					Couverture : feuillet transparent
				</td>
				<td style="width:50%;background:#eee;height:6mm;padding-left:2mm;">
					<?php echo ($couv_ft ? 'Oui' : 'Non'); ?>
				</td>
			</tr>
			<tr>
				<td style="width:50%;background:#eee;height:6mm;padding-left:2mm;">
					Couverture : feuille cartonnée
				</td>
				<td style="width:50%;background:#eee;height:6mm;padding-left:2mm;">
					<?php echo ($couv_fc ? 'Oui : ' . mb_strtolower($couv_fc_color) : 'Non'); ?>
				</td>
			</tr>
			<tr>
				<td style="width:50%;height:1mm;"> </td>
				<td style="width:50%;height:1mm;"> </td>
			</tr>
			<tr>
				<td style="width:50%;background:#eee;height:6mm;padding-left:2mm;">
					Dos : feuillet transparent
				</td>
				<td style="width:50%;background:#eee;height:6mm;padding-left:2mm;">
					<?php echo ($dos_ft ? 'Oui' : 'Non'); ?>
				</td>
			</tr>
			<tr>
				<td style="width:50%;background:#eee;height:6mm;padding-left:2mm;">
					Dos : feuille cartonnée
				</td>
				<td style="width:50%;background:#eee;height:6mm;padding-left:2mm;">
					<?php echo ($dos_fc ? 'Oui : ' . mb_strtolower($dos_fc_color) : 'Non'); ?>
				</td>
			</tr>
			<tr>
				<td style="width:50%;height:1mm;"> </td>
				<td style="width:50%;height:1mm;"> </td>
			</tr>
			<tr>
				<td style="width:50%;background:#eee;height:6mm;padding-left:2mm;">
					Reliure : type
				</td>
				<td style="width:50%;background:#eee;height:6mm;padding-left:2mm;">
					<?php echo ucfirst($reliure_type); ?>
				</td>
			</tr>
			<tr>
				<td style="width:50%;background:#eee;height:6mm;padding-left:2mm;">
					Reliure : couleur
				</td>
				<td style="width:50%;background:#eee;height:6mm;padding-left:2mm;">
					<?php echo $reliure_color; ?>
				</td>
			</tr>
			<tr>
				<td style="width:50%;height:1mm;"> </td>
				<td style="width:50%;height:1mm;"> </td>
			</tr>
			<tr>
				<td style="width:50%;background:#eee;height:6mm;padding-left:2mm;">
					Nombre d'exemplaires
				</td>
				<td style="width:50%;background:#eee;height:6mm;padding-left:2mm;">
					<?php echo $quantity; ?>
				</td>
			</tr>
			<tr>
				<td style="width:50%;height:1mm;"> </td>
				<td style="width:50%;height:1mm;"> </td>
			</tr>
			<tr>
				<td style="width:50%;background:#eee;height:6mm;padding-left:2mm;">
					Recto-verso
				</td>
				<td style="width:50%;background:#eee;height:6mm;padding-left:2mm;">
					<?php echo ($rectoverso ? 'Oui' : 'Non'); ?>
				</td>
			</tr>
			<tr>
				<td style="width:50%;height:1mm;"> </td>
				<td style="width:50%;height:1mm;"> </td>
			</tr>
			<tr>
				<td style="width:50%;height:1mm;"> </td>
				<td style="width:50%;height:1mm;"> </td>
			</tr>
			<tr>
				<td style="width:50%;background:#eee;height:6mm;padding-left:2mm;">
					TOTAL
				</td>
				<td style="width:50%;background:#eee;height:6mm;padding-left:2mm;">
					<?php echo $total . ' €  (dont TVA : ' . $tva . ' €)'; ?>
				</td>
			</tr>
		</table>

		<p></p>
		<p></p>

		<table style="width:100%;background:#fff;border-collapse:collapse;">
			<tr>
				<td style="width:40%;background:#fff;border:1px solid;padding:0 5mm;" valign="top">
					<br>
					<b>Adresse de livraison</b><br>
					<?php echo $addr_name; ?><br>
					<?php echo $address; ?><br>
					<?php echo ($address2 ? $address2 . '<br>' : ''); ?>
					<?php echo $zip_code . ' ' . $city; ?><br><br>
					<b>Transporteur</b><br>
					TNT<br>
				</td>
				<td style="width:60%;background:#fff;border:1px solid;padding:0 5mm;" valign="top">
					<br>
					<b>Date de commande : <?php echo date('d-m-Y H\hi', strtotime($date_add)); ?></b><br><br>
					Montant des travaux : <?php echo $total . ' €  (dont TVA : ' . $tva . ' €)'; ?><br>
					Montant livraison : 3,50 €<br>
					<b>Montant total : <?php echo number_format($total_num + 3.5, 2, ',', ' ') . ' €'; ?></b> <br><br>
					<b>Mode de paiement</b><br>
					<img src="<?php echo $settings['site_url']; ?>/public/img/paypal.png" alt="paypal" style="width:20mm;"><br>
					<img src="<?php echo $settings['site_url']; ?>/public/img/stripe.png" alt="visa" style="width:20mm;"><br>
					<br>
				</td>
			</tr>
		</table>
	</nobreak>
</page>

<?php

$sPdf = ob_get_contents();
ob_end_clean();

$html2pdf = new Html2Pdf('P', 'A4', 'fr', true, 'UTF-8', array(5, 5, 5, 10));

//$html2pdf->addFont('leaguegothic', '', 'leaguegothic.php');
//$html2pdf->addFont('sourcesanspro', '', 'sourcesanspro.php');
//$html2pdf->pdf->SetFont('leaguegothic','',32);
//$html2pdf->pdf->SetFont('sourcesanspro','',32);

//$html2pdf->setModeDebug();
$html2pdf->pdf->SetAuthor('CopyFac');
$html2pdf->pdf->SetTitle('Dossier-rapide');
$html2pdf->pdf->SetSubject('Dossier-rapide');
$html2pdf->pdf->SetDisplayMode('real', 'SinglePage', 'UseThumbs');
$html2pdf->setDefaultFont('arial');
$html2pdf->writeHTML($sPdf);
$html2pdf->output($id_orders . '_facture' . '.pdf', 'D');

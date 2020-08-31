<?php

require_once _ROOT_DIR_ . '/models/AdminGestionMgr.class.php';
require_once _ROOT_DIR_ . '/models/Pagination.class.php';

$archive = 'adminOrdersPast' == $_GET['action'] ? 1 : 0;

// Récupération des commandes.
$tab_order = [
	1 => 'o.`date_add`',
	2 => 'u.`first_name`',
	3 => 'u.`last_name`',
	4 => 'u.`email`',
	5 => 'u.`phone`',
	6 => 'a.`city`',
	7 => 'o.`doc_type`',
];
$tab_way = [
	1 => 'ASC',
	2 => 'DESC',
];

$sort_order = !empty($_GET['sort_order']) ? $_GET['sort_order'] : 1;
$sort_way = !empty($_GET['sort_way']) ? $_GET['sort_way'] : 1;

$order = ($sort_order && array_key_exists($sort_order, $tab_order) ? $tab_order[$sort_order] : $tab_order[1]);
$way = ($sort_way && array_key_exists($sort_way, $tab_way) ? $tab_way[$sort_way] : $tab_way[1]);

$date_from = '';
$date_from_fr = '';
$date_to = '';
$date_to_fr = '';
$firstname = '';
$lastname = '';
$email = '';
$phone = '';
$zipcode = '';
$city = '';
$docType = '';

$params = [];
$where = '';

if (isset($_GET['filter'])) {
	$date_from_fr = !empty($_GET['date_from']) ? $_GET['date_from'] : '';
	$date_from = !empty($date_from_fr) ? date('Y-m-d', strtotime($date_from_fr)) : '';
	$date_to_fr = !empty($_GET['date_to']) ? $_GET['date_to'] : '';
	$date_to = !empty($date_to_fr) ? date('Y-m-d', strtotime($date_to_fr)) : '';
	$firstname = !empty($_GET['first_name']) ? $_GET['first_name'] : '';
	$lastname = !empty($_GET['last_name']) ? $_GET['last_name'] : '';
	$email = !empty($_GET['email']) ? $_GET['email'] : '';
	$phone = !empty($_GET['phone']) ? $_GET['phone'] : '';
	$zipcode = !empty($_GET['zip_code']) ? $_GET['zip_code'] : '';
	$city = !empty($_GET['city']) ? $_GET['city'] : '';
	$docType = !empty($_GET['doc_type']) ? $_GET['doc_type'] : '';

	// TODO Add controls.
	
	if (!empty($date_from)) {
		$where .= ' AND o.`date_add` > :date_from';
		$params[':date_from'] = $date_from;
	}
	if (!empty($date_to)) {
		$where .= ' AND o.`date_add` < :date_to';
		$params[':date_to'] = $date_to . ' 23:59:59';
	}
	if (!empty($firstname)) {
		$where .= ' AND u.`first_name` LIKE :firstname';
		$params[':firstname'] = '%' . $firstname . '%';
	}
	if (!empty($lastname)) {
		$where .= ' AND u.`last_name` LIKE :lastname';
		$params[':lastname'] = '%' . $lastname . '%';
	}
	if (!empty($email)) {
		$where .= ' AND u.`email` LIKE :email';
		$params[':email'] = '%' . $email . '%';
	}
	if (!empty($phone)) {
		$where .= ' AND u.`phone` LIKE :phone';
		$params[':phone'] = '%' . $phone . '%';
	}
	if (!empty($zipcode)) {
		$where .= ' AND a.`zip_code` LIKE :zipcode';
		$params[':zipcode'] = '%' . $zipcode . '%';
	}
	if (!empty($city)) {
		$where .= ' AND a.`city` LIKE :city';
		$params[':city'] = '%' . $city . '%';
	}
	if (!empty($docType)) {
		$where .= ' AND o.`doc_type` = :doc_type';
		$params[':doc_type'] = $docType;
	}
}

$orders = AdminGestionMgr::getOrders($params, $archive, $where, $order, $way);
$numOrders = count($orders);

// Pagination.
define('NUM_PER_PAGE', 10);
$pagination = new Pagination('page');
// Redéfinition des attributs.
$pagination
	->setGoFirst('«')
	->setGoPrevious('‹')
	->setGoNext('›')
	->setGoLast('»')
	//->setPaginationWrapper('<nav><ul class="pagination">%s</ul></nav>')
	//->setAvoidDuplicateContent(FALSE)
	->setItemsPerPage(NUM_PER_PAGE)
	->setTotalRows($numOrders);
$paginationPages = $pagination->process();
$limitFrom = $pagination->limitFrom();
$limitTo = $limitFrom + NUM_PER_PAGE;
if ($limitTo > $numOrders) {
	$limitTo = $numOrders;
}

$orders = AdminGestionMgr::getOrders($params, $archive, $where, $order, $way, $limitFrom, NUM_PER_PAGE);

// Archive an order.
if (!empty($_GET['archive'])) {
	AdminGestionMgr::archiveOrder($_GET['archive']);
	$_SESSION['message_status'][] = 'Commande archivée';
	header('location: index.php?action=adminOrders');
	exit;
}

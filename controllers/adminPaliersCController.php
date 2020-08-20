<?php

require_once _ROOT_DIR_ . '/models/dao/DbConnection.class.php';

// Récupération des paliers couleur.
function getPaliersC() {
	$query = 'SELECT * FROM paliers_couleur ORDER BY position DESC';
	$sth = DbConnection::getConnection('administrateur')->query($query);
	$sth->execute();
	return $sth->fetchAll(PDO::FETCH_ASSOC);
}

$id = '';
$palier = '';
$prix = '';
$errors = [];

$addUpd = 'add';
if (!empty($_GET['edit']) && is_numeric($_GET['edit'])) {
	$addUpd = 'upd';
	$query = 'SELECT * FROM paliers_couleur WHERE id = :id ORDER BY position DESC';
	$stmt = DbConnection::getConnection('administrateur')->prepare('SELECT * FROM paliers_couleur WHERE id = :id');
	$stmt->bindParam(':id', $_GET['edit']);
	$stmt->execute();
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	$id = $result['id'];
	$palier = $result['palier'];
	$prix = $result['prix'];
	$stmt->closeCursor();
	DbConnection::disconnect();
}

if (isset($_POST['edit-btn'])) {
	$palier = trim($_POST['palier']);
	$prix = str_replace(',', '.', trim($_POST['prix']));

    if (empty($palier)) {
        $errors[] = 'Palier requis';
    }
	elseif (!is_numeric($palier)) {
        $errors[] = 'Le palier doit être un nombre entier';
	}
    if (empty($prix)) {
        $errors[] = 'Prix requis';
    }
	elseif (!is_numeric($prix)) {
        $errors[] = 'Le prix doit être un nombre décimal';
	}
	
	if (empty($errors)) {
		$dbh = DbConnection::getConnection('administrateur');
		if ('add' == $addUpd) {
			$stmt = $dbh->query('SELECT MAX(position) + 1 AS pos FROM paliers_couleur');
			$stmt->execute();
			$max = $stmt->fetch(PDO::FETCH_ASSOC);
			
			$stmt = $dbh->prepare('INSERT INTO paliers_couleur (palier, prix, position) VALUES (:palier, :prix, :position)');
			$stmt->bindParam(':palier', $palier, PDO::PARAM_INT);
			$stmt->bindParam(':prix', $prix, PDO::PARAM_STR);
			$stmt->bindParam(':position', $max['pos'], PDO::PARAM_INT);
		}
		else {
			$stmt = $dbh->prepare('UPDATE paliers_couleur SET palier = :palier, prix = :prix WHERE id = :id');
			$stmt->bindParam(':palier', $palier, PDO::PARAM_INT);
			$stmt->bindParam(':prix', $prix, PDO::PARAM_STR);
			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		}
		$result = $stmt->execute();
		$stmt->closeCursor();
		DbConnection::disconnect();
		if ($result) {
			$_SESSION['message_status'][] = 'add' == $addUpd ? 'Palier ajouté' : 'Palier modifié';
		}
		header('location: index.php?action=adminPaliersC');
		exit;
	}
}

if (!empty($_GET['del']) && is_numeric($_GET['del'])) {
	$dbh = DbConnection::getConnection('administrateur');
	$stmt = $dbh->prepare('DELETE FROM paliers_couleur WHERE id = :id');
	$stmt->bindParam(':id', $_GET['del'], PDO::PARAM_INT);
	$stmt->execute();
	$stmt->closeCursor();
	DbConnection::disconnect();
	$_SESSION['message_status'][] = 'Palier supprimé';
	header('location: index.php?action=adminPaliersC');
	exit;
}
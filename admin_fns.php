<?php

function db(): PDO{
	$user = 'u47507';
    $pass = '2613634';
    $db = new PDO('mysql:host=localhost;dbname=u47507', $user, $pass, array(PDO::ATTR_PERSISTENT => true));

    return $db;
}

function getUsers(): array{
	$users = db()->query("
        SELECT *
		FROM users LEFT JOIN powers
		ON powers.power_id = users.id 
		WHERE true
	");

	return $users->fetchALL();
}

function deleteUser(int $id): void{
	$stmt = db()->prepare("
        DELETE users, powers
        FROM users INNER JOIN powers 
		WHERE users.id = powers.power_id 
		AND users.id = ?
	");
    $stmt->execute([$id]);
	$stmt->execute();
}

function editUser(int $id): void{
	$user = db()->prepare("
		SELECT * 
		FROM user_pass 
		WHERE id = ?
	");
    $user->execute([$id]);
	$res = $user->fetchALL()[0];

	header('Location: login.php?act=auth&login=' . $res['login'] . '&password=' . $res['password']);
}

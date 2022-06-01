<?php 

require_once 'auth-admin.php';
require_once 'admin_fns.php';


is_auth(true);

if(isset($_POST['act'], $_POST['id'])){

	if($_POST['act'] == 'delete')
		deleteUser($_POST['id']);

	if($_POST['act'] == 'edit')
		editUser($_POST['id']);
}

require 'admin_table.php';
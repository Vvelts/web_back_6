<?php
	if(!is_auth()){ header('Location admin.php'); exit();}

?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Задание_6_admin</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css?вs1ы">
</head>

<body class="text-dark">
    <div class="container-fluid">
        
        <div class="items d-flex flex-column">

        <?php

            if (!empty($messages)) {
        ?>
             <div class="row d-flex flex-row justify-content-center mt-3 order-sm-3">
                <div class="col-sm-9 content">
                    
                </div>
            </div>
        <?php
            }
        ?>

            <div class="row d-flex flex-row justify-content-center mt-3 order-sm-3">
                <div class="col-sm-9 content admin">
	                <table>
					 	<thead>
					  		<tr>
					    		<th>ID</th>
					    		<th>Имя</th>
					     		<th>E-mail</th>
					     		<th>Пол</th>
					     		<th>Конеч.</th>
					     		<th>Биография</th>
					     		<th>Суперспособности</th>
					     		<th></th>
					     		<th></th>
					  		</tr>
					 	</thead>

						<tbody>
					  		<?php
					  			genirateTBody();
					  		?>
						</tbody>  
					
					<!-- 	<tfoot>
					  		<tr>
					    		<td colspan="5" style="text-align:right">ИТОГО:</td><td>1168,80</td>
					  		</tr>
						</tfoot> -->

					</table>
                </div>
            </div>


        </div>
        
    </div>


    <div class="footer">
        <a href="index.php">На главную</a>
    </div>
</body>

</html>


<?php 

function genirateTBody(): string {

	$users = getUsers();

	$res = '';
	$spw = [0, 0, 0];

	foreach ($users as $user){

		foreach (explode(", ", $user['powers']) as $power) {
			if($power == 'inf')
				$spw[0]++;
			if($power == 'levitation')
				$spw[1]++;
			if($power == 'through')
				$spw[2]++;
		}


		$res .= "<tr>";
		$res .= 	"<form action=\"admin.php\" method=\"post\">";
		$res .= 		"<td>" . $user['id'] . "</td>";
		$res .= 		"<td>" . $user['name'] . "</td>";
		$res .= 		"<td>" . $user['email'] . "</td>";
		$res .= 		"<td>" . ($user['gender'] == 'f' ? 'Ж' : 'М' ). "</td>";
		$res .= 		"<td>" . $user['limbs'] . "</td>";
		$res .= 		"<td>" . $user['biography'] . "</td>";
		$res .= 		"<td>" . str_replace(",", "<br>", $user['powers']) . "</td>";
		$res .= 		"<td>";
		$res .= 		"<form action=\"admin.php\" method=\"post\">";
		$res .=				"<input name=\"id\" value=\"" . $user['id'] . "\" type=\"hidden\" />";
		$res .=				"<input name=\"act\" value=\"delete\" type=\"hidden\" />";
		$res .=				"<input type=\"submit\" value=\"УДАЛИТЬ\" />";
        $res .= 		"</form>";
		$res .= 	"</td>";
		$res .= 		"<td>";
		$res .= 		"<form action=\"admin.php\" method=\"post\">";
		$res .=				"<input name=\"id\" value=\"" . $user['id'] . "\" type=\"hidden\" />";
		$res .=				"<input name=\"act\" value=\"edit\" type=\"hidden\" />";
		$res .=				"<input type=\"submit\" value=\"ИЗМЕНИТЬ\" />";
        $res .= 		"</form>";
		$res .= 	"</td>";
		$res .= "</tr>";

	}

	$res .= "<tfoot>";
	$res .= "	<tr>";
	$res .= "		<td colspan=\"9\" style=\"text-align:right\">";
	$res .= "		Бессмертие×{$spw[0]} Левитация×{$spw[1]} Прохождение сквозь стены×{$spw[2]}";
	$res .= "		</td>";
	$res .= "	</tr>";
	$res .= "</tfoot>";

	return print($res);
}



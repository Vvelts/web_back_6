<?php
header('Content-Type: text/html; charset=UTF-8');

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $messages = array();

	if (isset($_COOKIE['save'])) {

    	$messages[] = 'Данные сохранены';
        if(isset($_COOKIE['pass'])){
            $messages[] = "Вы можете <a href=\"login.php\">войти</a> с логином <strong>" . $_COOKIE['login'] . "</strong> и паролем <strong>" . $_COOKIE['pass'] . "</strong> для изменения данных.";
            
            setcookie('pass', '', time() - 1);
        }

        setcookie('save', '', time() - 1);
  	}

    $errors = array();
    $errors['token'] = !empty($_COOKIE['token']);
    $errors['name'] = !empty($_COOKIE['name_error']);
    $errors['email'] = !empty($_COOKIE['email_error']);
    $errors['date'] = !empty($_COOKIE['date_error']);
    $errors['radio-group-1'] = !empty($_COOKIE['radio-group-1_error']);
    $errors['radio-group-2'] = !empty($_COOKIE['radio-group-2_error']);
    $errors['superpowers'] = !empty($_COOKIE['superpowers_error']);
    $errors['biography'] = !empty($_COOKIE['biography_error']);
    $errors['check-1'] = !empty($_COOKIE['check-1_error']);
    $errors['1'] = !empty($_COOKIE['1_error']);
    $errors['2'] = !empty($_COOKIE['2_error']);
    $errors['3'] = !empty($_COOKIE['3_error']);


    if ($errors['name']) {
        setcookie('name_error', '', 100000);
        $messages[] = '<div class="error">Введите имя.</div>';
    }elseif ($errors['1']){
        setcookie('1_error', '', 100000);
        $messages[] = '<div class="error">Введите имя латинскими буквами</div>';
    } 

    if ($errors['email']) {
        setcookie('email_error', '', 100000);
        $messages[] = '<div class="error">Введите email.</div>';
    }elseif ($errors['2']){
        setcookie('2_error', '', 100000);
        $messages[] = '<div class="error">email имеет вид: test@example.com</div>';
    } 

    if ($errors['date']) {
        setcookie('date_error', '', 100000);
        $messages[] = '<div class="error">Введите дату рождения.</div>';
    }elseif ($errors['3']){
        setcookie('3_error', '', 100000);
        $messages[] = '<div class="error">Формат даты 09.02.2001</div>';
    }

    if ($errors['radio-group-1']) {
        setcookie('radio-group-1_error', '', 100000);
        $messages[] = '<div class="error">Укажите пол.</div>';
    }

    if ($errors['radio-group-2']) {
        setcookie('radio-group-2_error', '', 100000);
        $messages[] = '<div class="error">Укажите кол-во конечностей.</div>';
    }

    if ($errors['superpowers']) {
        setcookie('superpowers_error', '', 100000);
        $messages[] = '<div class="error">Укажите суперспособность.</div>';
    }

    if ($errors['biography']) {
        setcookie('biography_error', '', 100000);
        $messages[] = '<div class="error">Напишите биографию.</div>';
    }

    if ($errors['check-1']) {
        setcookie('check-1_error', '', 100000);
        $messages[] = '<div class="error">Примите условия.</div>';
    }
    if ($errors['token']) {
        setcookie('token', '', 100000);
        $messages[] = '<div class="error">STOP REQUEST. CSRF-Protection</div>';
    }


  
    $values = array();

    $values['name'] = empty($_COOKIE['name_value']) ? '' : $_COOKIE['name_value'];
    $values['email'] = empty($_COOKIE['email_value']) ? '' : $_COOKIE['email_value'];
    $values['date'] = empty($_COOKIE['date_value']) ? '' : $_COOKIE['date_value'];
    $values['radio-group-1'] = empty($_COOKIE['radio-group-1_value']) ? '' : $_COOKIE['radio-group-1_value'];
    $values['radio-group-2'] = empty($_COOKIE['radio-group-2_value']) ? '' : $_COOKIE['radio-group-2_value'];
    $values['superpowers'] = empty($_COOKIE['superpowers_value']) ? [] : explode(", ", $_COOKIE['superpowers_value']);
    $values['biography'] = empty($_COOKIE['biography_value']) ? '' : $_COOKIE['biography_value'];
    $values['check-1'] = empty($_COOKIE['check-1_value']) ? '' : $_COOKIE['check-1_value'];
  
    $flag = FALSE;
    
    foreach($errors as $er){

        if(!empty($er)){
            $flag = TRUE;
            break;
        }

        print($er);
    }
            

    $id = '';

    if (isset($_SESSION['login'], $_SESSION['pass']) && $_SESSION['login'] != 'none') { 

        $login = $_SESSION['login'];

        $data = db()->prepare("
            SELECT * 
            FROM user_pass 
            WHERE login = ?
                AND password = ?
        ");
        $data->execute([
            $login,
            $_SESSION['pass']
        ]);

        $data = $data->fetchALL();
        $id = (isset($data[0]['id']) ? $data[0]['id'] : '');

        if($id != ''){
            try {
                $data = db()->prepare("
                    SELECT * 
                    FROM users 
                    WHERE id = ?
                ");
                $data->execute([$id]);
                $data = $data->fetchALL();

                foreach ($data as $row) {
                    $values['name'] = $row['name'];
                    $values['email'] = $row['email'];
                    $values['date'] = $row['date'];
                    $values['radio-group-1'] = $row['gender'];
                    $values['radio-group-2'] = $row['limbs'];
                    $values['biography'] = $row['biography'];
                    $values['check-1'] = $row['policy'];
                }

                $data = db()->prepare("
                    SELECT powers
                    FROM powers 
                    WHERE power_id = ?
                ");
                $data->execute([$id]);
                $data = $data->fetchALL();

                $values['superpowers'] = explode(", ", $data[0]['powers']);

                require_once 'auth-admin.php';
                if(is_auth(false) && isset($_SESSION['edit']) && $_SESSION['edit']){

                    $messages[] = "Изменение профиля " . $login;
                    $messages[] = "<form action=\"?\" method=\"post\">
                                        <input type=\"hidden\" name=\"act\" value=\"logout\" />
                                        <input type=\"hidden\" name=\"loc\" value=\"admin.php\" />
                                    <input type=\"submit\" value=\"Назад\" />
                                 </form>";
                }else{
                    $messages[] = "Вход с логином " . $login;
                    $messages[] = "<form action=\"?\" method=\"post\">
                                        <input type=\"hidden\" name=\"act\" value=\"logout\" />
                                        <input type=\"hidden\" name=\"loc\" value=\"?\" />
                                    <input type=\"submit\" value=\"Выйти\" />
                                 </form>";
                }


            }catch(PDOException $e){

                echo 'Ошибка: ' . $e->getMessage();
            }
        }else
            session_destroy();
    }

    include('form.php');

}elseif(isset($_POST['act']) && $_POST['act'] = 'logout'){
    session_destroy();

    header('Location: ' . @ $_POST['loc']);

    exit();

}else{

      $errors = FALSE;

    if (!preg_match("/^[-a-zA-Z]+$/",$_POST['name'])){
        setcookie('1_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    } 

    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
        setcookie('2_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    } 
      /*if (!preg_match("/^(\d{1,2})\.(\d{1,2})(?:\.(\d{4}))?$/",$_POST['date'])){
        setcookie('3_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
      }*/

    if (empty($_POST['name'])) {
        setcookie('name_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }else 
        setcookie('name_value', $_POST['name'], time() + 365 * 24 * 60 * 60);
    
    if (empty($_POST['email'])) {
        setcookie('email_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    } else 
        setcookie('email_value', $_POST['email'], time() + 365 * 24 * 60 * 60);
    
    if (empty($_POST['date'])) {
        setcookie('date_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }else 
        setcookie('date_value', $_POST['date'], time() + 365 * 24 * 60 * 60);
    
    if (empty($_POST['radio-group-1'])) {
        setcookie('radio-group-1_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }else 
        setcookie('radio-group-1_value', $_POST['radio-group-1'], time() + 365 * 24 * 60 * 60);
      
    if (empty($_POST['radio-group-2'])) {
        setcookie('radio-group-2_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }else 
        setcookie('radio-group-2_value', $_POST['radio-group-2'], time() + 365 * 24 * 60 * 60);
    
    if (empty($_POST['superpowers'])) {
        setcookie('superpowers_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }else 
        setcookie('superpowers_value', implode(", ", $_POST['superpowers']), time() + 365 * 24 * 60 * 60);
      
    if (empty($_POST['biography'])) {
        setcookie('biography_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }else 
        setcookie('biography_value', $_POST['biography'], time() + 365 * 24 * 60 * 60);
      
    if (empty($_POST['check-1'])) {
        setcookie('check-1_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }else 
        setcookie('check-1_value', $_POST['check-1'], time() + 365 * 24 * 60 * 60);

    if(checkToken($_POST['auth-token']) == false){
        setcookie('token', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }

      

    if ($errors) {
        header('Location: index.php');
        exit();
    
    }else {
        setcookie('name_error', '', 100000);
        setcookie('1_error', '', 100000);
        setcookie('email_error', '', 100000);
        setcookie('2_error', '', 100000);
        setcookie('date_error', '', 100000);
        setcookie('3_error', '', 100000);
        setcookie('radio-group-1_error', '', 100000);
        setcookie('radio-group-2_error', '', 100000);
        setcookie('superpowers_error', '', 100000);
        setcookie('biography_error', '', 100000);
        setcookie('check-1_error', '', 100000);
    }


    $id = '';

    if (isset($_SESSION['login'], $_SESSION['pass'])) { 

        $login = $_SESSION['login'];

        $data = db()->prepare("
            SELECT * 
            FROM user_pass 
            WHERE login = ?
                AND password = ?
        ");
        $data->execute([
            $login,
            $_SESSION['pass']
        ]);

        $data = $data->fetchALL();

        $id = (isset($data[0]['id']) ? $data[0]['id'] : '');

        if($id != ''){
           
            $name = htmlspecialchars($_POST['name']);
            $email = htmlspecialchars($_POST['email']);
            $date = htmlspecialchars($_POST['date']);
            $gender = htmlspecialchars($_POST['radio-group-1']);
            $limbs = htmlspecialchars($_POST['radio-group-2']);
            $powers = implode(", ", $_POST['superpowers']);
            $policy = htmlspecialchars($_POST['check-1']);
            $biography = htmlspecialchars($_POST['biography']);
          
            try { 
                $stmt = db()->prepare("
                    UPDATE users 
                    SET 
                        `name` = ?,
                        `email` = ?,
                        `date` = ?,
                        gender = ?,
                        limbs = ?,
                        biography = ?,
                        policy = ?

                    WHERE id  =  '" . $_POST['edit_proc'] . "' 
                ");
                $stmt->execute([
                    $name,
                    $email,
                    $date,
                    $gender,
                    $limbs,
                    $biography,
                    $policy
                ]);
                
                $stmt = db()->prepare("
                    UPDATE powers 
                    SET powers = ?
                    WHERE power_id  = ?
                ");
                $stmt->execute([
                    $powers,
                    $_POST['edit_proc']
                ]);

            }catch (PDOException $e) {

                print('Error : ' . $e->getMessage());
                exit();
            }
        }else{
            session_destroy();
            header('Location index.php');
            exit();
        }

    }else{

        $name = htmlspecialchars($_POST['name']);
        $email = htmlspecialchars($_POST['email']);
        $date = htmlspecialchars($_POST['date']);
        $gender = htmlspecialchars($_POST['radio-group-1']);
        $limbs = htmlspecialchars($_POST['radio-group-2']);
        $powers = implode(", ", $_POST['superpowers']);
        $policy = htmlspecialchars($_POST['check-1']);
        $biography = htmlspecialchars($_POST['biography']);

        $login = genirate_login($name);
        $pass = password_genirate();
        $pass2 = md5($pass);

        setcookie('login', $login);
        setcookie('pass', $pass);

        try {
            $db = db();
            $stmt = $db->prepare("INSERT INTO users SET name = ?, email = ?, date = ?, gender = ?, limbs = ?, policy = ?, biography = ?");
            $stmt -> execute(array($name, $email, $date, $gender, $limbs, $policy, $biography));
            $id = $db->lastInsertId();
      
            $superpowers = $db->prepare("INSERT INTO powers SET power_id = ?, powers = ?");
            $superpowers -> execute(array($id, $powers));
        
            $user_pass = $db->prepare("INSERT INTO user_pass SET id = ?, login = ?, password = ?");
            $user_pass->execute(array($id, $login, $pass2));
        
        }catch(PDOException $e){

            print('Error : ' . $e->getMessage());
            exit();
        }
    }

    setcookie('save', '1');
    header('Location: index.php');
}





function password_genirate(int $length = 6): string{               
    $chars = 'qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP1234567890'; 
    $size = strlen($chars) - 1; 
    $password = ''; 

    while($length--) {
        $password .= $chars[random_int(0, $size)]; 
    }

    return $password;
}

function genirate_login(string $name): string{
  
    return $name . mt_rand(100, 999);
}

function db(): PDO{
    $user = 'u47507';
    $pass = '2613634';
    $db = new PDO('mysql:host=localhost;dbname=u47507', $user, $pass, array(PDO::ATTR_PERSISTENT => true));

    return $db;
}


function checkToken(string $check): bool{
    if(isset($_SESSION['token']))
        if(password_verify($check, $_SESSION['token']))
            return true;

    return false;
}

function createToten(): string{
    $uid = uniqid();
    $_SESSION['token'] = password_hash($uid, PASSWORD_BCRYPT);
    return $uid;
}
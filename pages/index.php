<?
session_start();

include 'connect.php';
include 'functions.php';

if(isset($_POST['submit']))
{
	$query = mysqli_query($link,"SELECT id, password FROM users WHERE login='".mysqli_real_escape_string($link,$_POST['login'])."'");
	$data = mysqli_fetch_assoc($query);
	
	// Сравнение паролей
	if(password_verify($_POST['password'], $data['password']))
	{
		$_SESSION['id'] = $data['id'];
		$_SESSION['login'] = $_POST['login'];
		header("location: tasklist.php");
	}

	else
	{
		$err = [];
	
		// Проверка вводных данных
		if(!preg_match("/^[a-zA-Z0-9]+$/",$_POST['login']))
		{
			$err[] = "Логин может состоять только из букв английского алфавита и цифр";
		}
	
		if(strlen($_POST['login']) < 3 or strlen($_POST['login']) > 30)
		{
			$err[] = "Логин должен быть не меньше 3-х символов и не больше 30";
		}

		if(mysqli_num_rows($query) > 0)
		{
			$err[] = "Пользователь с таким логином уже существует в базе данных";
		}

		if(count($err) == 0)
		{
			$login = $_POST['login'];

			// Для хеширования паролей используем функцию
			$password = password_hash($_POST['password'], PASSWORD_BCRYPT);
			mysqli_query($link,"INSERT INTO users SET login='".$login."', password='".$password."'");
			user_session();
		}
		else
		{
			print "<b>При регистрации произошли следующие ошибки:</b><br>";
			foreach($err AS $error)
			{
				print $error."<br>";
			}
		}
	}
}
?>

<? include 'template.php'; ?>

<!-- Форма регистрации/авторизации -->
<form method="POST">
	<div class="index_container">
		Логин:<input name="login" type="text" required><br>
		Пароль:<input name="password" type="password" required><br>
		<input name="submit" type="submit" value="Зарегистрироваться/Войти">
	</div>
</form>


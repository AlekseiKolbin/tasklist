<?
session_start();

include 'connect.php';
include_once 'redirect_session.php';

if(isset($_POST['task_add']))
{
	$err = [];

	// Проверка задачи
	if(!preg_match("/^[а-яА-ЯёЁa-zA-Z0-9\s,.]+$/u",htmlspecialchars($_POST['task_name'], ENT_QUOTES, 'UTF-8')))
	{
		$err[] = 'Задача может состоять только из букв английского, русского алфавита, цифр и знаков("," и ".").';
	}

	if(strlen($_POST['task_name']) < 10)
	{
		$err[] = "Задача должна быть не меньше 10-ти символов";
	}

	if(count($err) == 0)
	{
		$task_name = $_POST['task_name'];
		mysqli_query($link,"INSERT INTO tasks SET user_id ='".$_SESSION["id"]."', description='".$task_name."'");
	}

	else
	{
		print "<b>При добавлении задачи произошли следующие ошибки:</b><br>";
		foreach($err AS $error)
		{
			print $error."<br>";
		}
	}
}

// Кнопки управления пользователя
if ($_POST['task_all_clean'])
{
	mysqli_query($link,"DELETE FROM tasks WHERE user_id ='".$_SESSION["id"]."'");
}

if ($_POST['out'])
{
	header("location: index.php");
	session_destroy();
}

if ($_POST['task_clean'] and $_POST['checked'] !=0)
{
	mysqli_query($link,"DELETE FROM tasks WHERE id IN ( " . implode( ', ', $_POST['checked'] ) . " )");
	header("location: tasklist.php");
}

if ($_POST['task_done'] and $_POST['checked'] !=0)
{
	mysqli_query($link,"UPDATE tasks SET status = '1' WHERE id IN ( " . implode( ', ', $_POST['checked'] ) . " )");
	header("location: tasklist.php");
}

// Выводим задачи пользователя из БД
$result = mysqli_query($link,"SELECT * FROM tasks WHERE user_id ='".$_SESSION["id"]."'");
for ($data = []; $row = mysqli_fetch_assoc($result) ; $data[] = $row);
?>

<? include 'template.php'; ?>

<div class="index_container">
	<h2><? echo htmlspecialchars($_SESSION['login']); ?></h2>
	<form method="POST">
		<div class="menu_container">
			Задайте себе задание:<input type="text" name="task_name"><br>
			<input type="submit" name="task_add" value="Добавить">
			<input type="submit" name="task_all_clean" value="Очистить">
			<input type="submit" name="task_clean" value="Убрать Задания">
			<input type="submit" name="task_done" value="Отм. как выполненно">
			<input type="submit" name="out" value="Выйти">
		</div>
		<div class="list_container">
			<table>
				<? foreach ($data as $task) { ?>
					<tr>
						<td style="color:<?if(htmlspecialchars($task['status']) == 1):?>green<?php endif;?>;"><?= htmlspecialchars($task['description'])?></td>
						<td><input type="checkbox" name="checked[]" value="<?=htmlspecialchars($task['id']) ?>" /></td>
					</tr>
				<? } ?>
			</table>
		</div>
	</form>
</div>
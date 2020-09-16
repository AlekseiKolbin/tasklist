<?
	function user_session(){
		include 'connect.php';
		$query = mysqli_query($link,"SELECT id, password FROM users WHERE login='".mysqli_real_escape_string($link,$_POST['login'])."'");
		$data = mysqli_fetch_assoc($query);
		$_SESSION['id'] = $data['id'];
		$_SESSION['login'] = $_POST['login'];
		header("location: tasklist.php");
	}
?>
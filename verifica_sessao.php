<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<?php
	session_start();
	if(!isset($_SESSION['usuario']))	{
		
		echo "<script>alert('ATENÇÃO! Você precisa efetuar o login !');</script>";
		echo "<meta http-equiv=REFRESH Content='0;URL=index.php'>";
		
	}

?>
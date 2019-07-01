<?php

function registrarAtividade($atividade)
{	
	/*Registra atividade do usuário	*/
	$query = "insert into auditoria(dt_atividade,usuario_idt,atividade)
	values (now(),'".$_SESSION['idt']."',upper('".$atividade."'))";
	$resultado = mysql_query($query);
	if($resultado)
	{
		return(1);
	}
	else
	{ 
		return(0);
	}
}

?>


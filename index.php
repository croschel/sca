<?php
	session_start();

/*Verifica a data atual para consulta abastecimento*/
  $d_fim=date('d');
  $data_fim=date('Y-m-').$d_fim; 
  if (date('m')==1) {
  	$data_ini=(date('Y')-1).'-12-'.date('d');
  } 
  else {
  	$data_ini=date('Y').'-'.(date('m')).'-'.date('d');
  }
	include "configuracao.php";
	include "conexao.php";

	/*Verifica se ha novas mensagems*/
	$query2="select count(*) from destinatario
				where destinatario_idt='".$_SESSION['idt']."' 
				and leitura is null";

   $result2 = mysql_query($query2);
	$row2 = mysql_fetch_row($result2);



?>
<html xmlns="http://www.w3.org/1999/xhtml"
    xml:lang="pt-BR"
    lang="pt-BR"
    dir="ltr">
<head>
<?php
   echo "<title>".$sigla_oc_geral." - Sistema de Controle de Abastecimentos</title>";
?>
	<link rel='icon' href='imagens/favicon.ico' type='image/x-icon' />
	<META http-equiv='Content-Type' content='text/html; charset= UTF-8' />

	<!--META http-equiv="Content-Type" content="text/html; charset=UTF-8" /!-->
 	<script type="text/javascript" src="jquery/jquery-1.11.3.min.js"></script>
   <style type="text/css">@import "jquery//jquery-ui-1.11.4/jquery-ui.min.css";</style> 
   <script type="text/javascript" src="jquery/jquery-ui-1.11.4/jquery-ui.min.js"></script>
 	 <link rel="stylesheet" type="text/css" href="css/estilos.css">
 	 
   <script type="text/javascript" src="jquery/jquery_numeric/jquery.numeric.js"></script>
	
   <script type="text/javascript">
   $(function() {
   	
		function date (id)
		{
	
		   $("#"+id).datepicker(
		   {
			dateFormat: 'dd/mm/yy',
			dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
			dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
			dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
			monthNames: ['Janeiro','Fevereiro','Mar\u00e7o','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
			monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
			nextText: 'Próximo',
			prevText: 'Anterior',
			changeMonth: true,
			changeYear: true,
			
			
		  });
	
		}   	
   	
   	date('dt_ini');
   	date('dt_fim');
   	
  });
  
  $("#idt").numeric();
  
   </script>
   
</head>

<style type="text/css">
	t1 {font-weight: bold; font-size:28; font-family:verdana, sans-serif,helvetica;}
	t2 {font-style: italic; font-size:16; font-family:verdana, sans-serif,helvetica;}
	t3 {font-style: italic; font-size:14; font-family:verdana, sans-serif,helvetica;}
	coluna {font-style: italic; font-weight: bold; font-size:16; font-family:verdana, sans-serif,helvetica; color: #000000;}
	lin {font-size:12; font-family: arial, helvetica, serif; color: #000000;}
	cred {font-size:12; font-family: arial, helvetica, serif; color: #FFFFCC;}
   marcar {font-style: italic; font-weight: bold; font-size:14; font-family:verdana, sans-serif,helvetica; background-color: yellow;}
</style>


<?php 
echo "<body link= '$link' alink= '$link' vlink= '$link'>";
echo "<table border='1' width='100%' height='12%' cellspacing='0' cellpadding='0' >";
echo "<tr bgcolor='$cor_cabecalho'>
	<td width='16%' height='10%' align='center'>
		<a href='index.php?nomeArquivo=principal.php'><img src='./imagens/exercito.gif' width='50' height='70' border='0'></a>
	</td>
	<td width='70%' height='10%'>
		<table border='0' width='100%'>
		<tr>
		<td colspan='3'>	
			<font color='#ffff00'><font size='5'><center><b><i>$oc_geral</b><br>
			Sistema para Controle de Abastecimentos</i></center></font>
		</td>
		</tr>
		<tr>
		<td width='40%'>
			<br>&nbsp;&nbsp;
			<font color='$cor_menu' size='2'><I>Usuário Logado:&nbsp;<font color='$cor_submenu' size='2'>".substr($_SESSION['post_grad'],1)." ".$_SESSION['nome_guerra']."</I>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<font color='$cor_menu' size='2'><I>OM:&nbsp;<font color='$cor_submenu' size='2'>".$_SESSION['unidade']."</I>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<font color='$cor_menu' size='2'><I>Função:&nbsp;<font color='$cor_submenu' size='2'>".$_SESSION['perfil']."</I>
		</td>
		</tr>
		</table>
	<td width='15%' height='10%' align='left'>
		<font color='#CDCD00' size='2'><i><b>&nbsp;Desenvolvimento:</b> <br></font><cred>&nbsp;&nbsp;&nbsp;&nbsp;ST Cícero - 4º B Log</I><br><br>
		<font color='#CDCD00' size='2'><i>&nbsp;<b>Versão: </b><font color='#FFFFCC'>$versao</I>
	</td>
</tr>";
echo "</table>";
echo "<table border='1' width='100%' height='88%' cellspacing='0' cellpadding='0' >";
$menu = array();
echo "<tr>";
	echo "<td width='16%' bgcolor='$cor_fundo_menu' valign='top'>";

		echo "<b><font color='$cor_menu' size='2'>&nbsp;CADASTROS:</font></b><br>";
		foreach(array_keys($menu_cadastro) as $i){
			echo "&nbsp;&nbsp;&nbsp;&nbsp<a href='index.php?nomeArquivo=$menu_cadastro[$i].php' style='text-decoration:none'><font color='$cor_submenu' size='2'>$i</font></a><br>";	
			array_push($menu, $menu_cadastro[$i]);
			$i++;	
		}
		if($_SESSION['perfil']!='') {
			if($row2[0]<>0) {
				echo "&nbsp;&nbsp;&nbsp;&nbsp;<b><a href='index.php?nomeArquivo=cons_msg.php' style='text-decoration:none' title='Você possui $row2[0] mensagem(ns) nova(s)'><font color='$cor_menu' size='2'>Nova(s) Mensagem(ns)</font></a></b> <br>";
			} else {
				echo "&nbsp;&nbsp;&nbsp;&nbsp;<a href='index.php?nomeArquivo=cons_msg.php' style='text-decoration:none'><font color='$cor_submenu' size='2'>Mensagens</font></a><br>";	
			}
		}	
		echo "</font>";
		echo"<hr><b><font color='$cor_menu' size='2'>&nbsp; COMBUSTíVEIS:</b></font><br>";
		foreach(array_keys($menu_combustiveis) as $i){
			echo "&nbsp;&nbsp;&nbsp;&nbsp<a href='index.php?nomeArquivo=$menu_combustiveis[$i].php' style='text-decoration:none'><font color='$cor_submenu' size='2'>$i</font></a><br>";	
			array_push($menu, $menu_combustiveis[$i]);
			$i++;	
		}

		echo"<hr><b><font color='$cor_menu' size='2'>&nbsp;ABASTECIMENTOS:</b></font><br>";
		foreach(array_keys($menu_abastecimento) as $i){
			echo "&nbsp;&nbsp;&nbsp;&nbsp<a href='index.php?nomeArquivo=$menu_abastecimento[$i].php' style='text-decoration:none'><font color='$cor_submenu' size='2'>$i</font></a><br>";	
			array_push($menu, $menu_abastecimento[$i]);
			$i++;	
		}
		if (($_SESSION['perfil']=='ADMINISTRADOR *') or ($_SESSION['perfil']=='ADMINISTRADOR') or ($_SESSION['perfil']=='FISCAL DA OM') or ($_SESSION['perfil']=='CMT DA OM')) {
			echo "&nbsp;&nbsp;&nbsp;&nbsp;<a href='./vale.pdf' style='text-decoration:none' target='_blank' ><font color='$cor_submenu' size='2'>Autz Escrita</font></a><br>";
		}

		echo"<hr><b><font color='$cor_menu' size='2'>&nbsp; CONSULTAS:</b><br> </font>";
		foreach(array_keys($menu_consultas) as $i){
			echo "&nbsp;&nbsp;&nbsp;&nbsp<a href='index.php?nomeArquivo=$menu_consultas[$i].php' style='text-decoration:none'><font color='$cor_submenu' size='2'>$i</font></a><br>";	
			array_push($menu, $menu_consultas[$i]);
			$i++;	
		}

		echo"<hr><b><font color='$cor_menu' size='2'>&nbsp; GRÁFICOS:</b><br> </font>";
		foreach(array_keys($menu_graficos) as $i){
			echo "&nbsp;&nbsp;&nbsp;&nbsp<a href='index.php?nomeArquivo=$menu_graficos[$i].php' style='text-decoration:none'><font color='$cor_submenu' size='2'>$i</font></a><br>";	
			array_push($menu, $menu_graficos[$i]);
			$i++;	
		}

		echo"<hr><b><font color='$cor_menu' size='2'>&nbsp; ADMINISTRAÇÃO:</b><br> </font>";
		foreach(array_keys($menu_adm) as $i){
			echo "&nbsp;&nbsp;&nbsp;&nbsp<a href='index.php?nomeArquivo=$menu_adm[$i].php' style='text-decoration:none'><font color='$cor_submenu' size='2'>$i</font></a><br>";	
			array_push($menu, $menu_adm[$i]);
			$i++;	
		}

		array_push($menu, 'login', 'sair', 'sobre', 'index', 'principal', 'cons_msg');

  		echo "<hr><b><font color='$cor_menu' size='2'>&nbsp; AJUDA:</b><br> </font>";
			echo "&nbsp;&nbsp;&nbsp;&nbsp;<a href='./Apresentacao.pdf' style='text-decoration:none' target='_blank' ><font color='$cor_submenu' size='2'>Apresentação</font></a><br>";
			echo "&nbsp;&nbsp;&nbsp;&nbsp;<a href='./Manual.pdf' style='text-decoration:none' target='_blank' ><font color='$cor_submenu' size='2'>Manual</font></a><br>";
			if($_SESSION['usuario'])	{
				echo "&nbsp;&nbsp;&nbsp;&nbsp;<a href='index.php?nomeArquivo=downloads.php' style='text-decoration:none'><font color='$cor_submenu' size='2'>Downloads</font></a><br>";
			}
			echo "&nbsp;&nbsp;&nbsp;&nbsp;<a href='index.php?nomeArquivo=sobre.php' style='text-decoration:none'><font color='$cor_submenu' size='2'>Sobre</font></a>";

		echo"<hr>
       	<div align='center'><a href='index.php?nomeArquivo=sair.php' style='text-decoration:none'><IMG SRC='./imagens/sair.gif' width='30' height='30' border=0></a>
        	</div>";
	echo "</td>";
	echo "<td width='84%' bgcolor='$cor_fundo' valign='top' align='center'>";
				if((stripos($_SERVER ['REQUEST_URI'], 'nomeArquivo=/') !== false ) or (stripos($_SERVER ['REQUEST_URI'], 'nomeArquivo=.') !== false ) or (stripos($_GET['nomeArquivo'], '../') !== false )){
					$_GET['nomeArquivo'] = "reg_acesso.php";
				}

  			   if(!isset($_GET['nomeArquivo'])) {
					$_GET['nomeArquivo'] = "login.php";
			   }

/*				if(stripos($_GET['nomeArquivo'],'.php') === false ) {
					$_GET['nomeArquivo'] = "reg_acesso.php";
				} else {
					$pagina = str_replace(".php","",$_GET['nomeArquivo']);
					if ( in_array($pagina, $menu) ) { 
						$_GET['nomeArquivo'] = $_GET['nomeArquivo']; 
					} else {
						$_GET['nomeArquivo'] = "reg_acesso.php"; 
					}
				}*/

			   include $_GET['nomeArquivo'];

	echo "</td>";	
echo "</tr>";
echo "</table>";	
	
?>
</body>
</html>


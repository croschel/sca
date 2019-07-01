<?php
  if(!isset($_SESSION['usuario']))
    include "verifica_sessao.php";
?>

<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>

<?php    
	echo "<br><t1>Relação de Usuários Cadastrados no Sistema</b></t1> <p>";

	echo "<hr><form method='GET' action='index.php?nomeArquivo=cons_usuario.php' name='viatura'>";
	echo "<input type='hidden' name='nomeArquivo' value='cons_usuario.php'>";
	echo "<coluna>Localizar um Usuário: <input type='text' size='40' maxlength='50' name='id' value='".$_GET['id']."' title='Para localizar um usuário digite a identidade ou o nome do usuário e pressione <<Executar>>.'>";
	echo "<input type='hidden' name='pesq' value='1'>";
	echo "<input type='submit' value= ' Executar '><hr>";
	echo "</form>";

	if(($_GET['pesq']) and ($_GET['id']!='')){
		$qry="select om.sigla, u.idt, u.nome_guerra, u.post_grad, om.codom
				from om, usuario u
				where om.codom=u.om_codom
						and (u.idt like '%".$_GET['id']."%' or u.nome like '%".$_GET['id']."%' or u.nome_guerra like '%".$_GET['id']."%' )
				order by 1";
		$result1 = mysql_query($qry);
		echo "<table border='0' width='100%' align='left' valign='top'>";
		echo "<tr align='center'>
				<td>&nbsp;</td>
				<td width='50' bgcolor='$cor1'><coluna>OM</td>
				<td width='200' bgcolor='$cor1'><coluna>Idt</td>
				<td width='250' bgcolor='$cor1'><coluna>Usuário</td>
				<td>&nbsp;</td>
				</tr>";
		if(mysql_num_rows($result1)<1){
			echo "<tr align='center'>";
				echo "<td><lin>&nbsp;</td>";
				echo "<td bgcolor='$cor3' colspan='3'><lin><marcar>Não foi localizada nenhum usuário com esta sequência de caracteres</lin></td>";
			echo "</tr>";
		}	
		 while ($user = mysql_fetch_array($result1, MYSQL_NUM)){
		 	if($user[0] != $ant){
		 		echo "<tr><td></td><td colspan='3' bgcolor='$cor2'><coluna1>&nbsp;&nbsp;&nbsp;$user[0]</td></tr>";
		 		$ant = $user[0];
		 	}
			echo "<tr align='center'>";
				echo "<td><lin>&nbsp;</td>";
				echo "<td><lin>&nbsp;</td>";
				echo "<td bgcolor='$cor3'><lin><a href='index.php?nomeArquivo=cons_usuario.php&listar=$user[4]&id=$user[1]#$user[4]' style='text-decoration:none' title='".substr($user[6],1)." $user[7]'>$user[1]</a></lin></td>";
				echo "<td bgcolor='$cor3'><lin><a href='index.php?nomeArquivo=cons_usuario.php&listar=$user[4]&id=$user[1]#$user[4]' style='text-decoration:none' title='".substr($user[2],1)." $user[7]'>".substr($user[3],1)." $user[2] </a></lin></td>";
			echo "</tr>";
		}
		echo "</table>";
	}
	
	echo "<table border='0' width='100%' align='center' valign='top' >";
	if (($_SESSION['perfil']!='CONSULTOR') and ($_SESSION['perfil']!='CONSULTOR *')){
		echo "<div align='left'><t2><a href='index.php?nomeArquivo=cad_usuario.php&om=".$_GET['listar']."' style='text-decoration:none'>&nbsp;&nbsp;Cadastrar Novo Usuário<IMG SRC='./imagens/novo.png' width='25' height='25' border=0></a><br>";
	}
  	$query="SELECT distinct om1.codom, om1.sigla
				FROM om om1, om om2
				where 
					om1.codom=om2.subordinacao 
					AND om1.subordinacao is not null
					ORDER BY abs(substring(om1.sigla,6)), om1.sigla";

   $result = mysql_query($query);
   while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
			echo "<tr bgcolor='$cor1'><td colspan='3'><coluna>Comando: <font color='$cor_submenu'>$row[1]</td><td bgcolor='$cor_fundo'>&nbsp;</td></tr>";
			$query1="select o.codom, o.sigla, count(u.idt), o.subordinacao from om o, usuario u where o.codom=u.om_codom and (o.subordinacao='$row[0]' or o.codom='$row[0]') group by u.om_codom order by o.sigla";

	$result1 = mysql_query($query1);
	echo "<tr align='center' bgcolor='$cor2'><td width='2%' bgcolor='$cor_fundo'></td><td width='40%'><coluna>Organização Militar</td><td width='8%'><coluna>Nº Usuários</td><td width='50%' bgcolor='$cor_fundo'>&nbsp;</td></tr>";

	while ($row1 = mysql_fetch_array($result1, MYSQL_NUM)) {
		/*/Seleciona a relação de viaturas em carga*/
		$result2 = mysql_query("select idt, post_grad, nome_guerra, perfil, situacao 
					from usuario where om_codom='$row1[0]' 
					order by situacao desc, post_grad");

		if ($_GET['listar']==$row1[0]){
			echo "<tr><td width='2%' bgcolor='$cor_fundo'></td><td colspan=3>";
			echo "<table border=0 width='100%'>";					
			echo "<tr><a name='$row1[0]'></a><td colspan='8' bgcolor='$cor1'>&nbsp;<coluna>OM: <font color='$cor_submenu'>	$row1[1]</coluna></td></tr>";

			echo "<tr align='center' bgcolor='$cor2'><td width='2%' rowspan='$nr_linhas' bgcolor='$cor_fundo'></td><td width='10%'><coluna>Identidade</td><td width='10%'><coluna>Post/Grad</td><td width='20%'><coluna>Nome de Guerra</td><td width='10%'><coluna>Perfil</td><td width='15%'><coluna>Situação</td><td width='8%'><coluna>Alterar</td><td width='8%'><coluna>Remover</td></tr>";
			while ($row2 = mysql_fetch_array($result2, MYSQL_NUM)) {
				echo"<tr bgcolor='$cor3' class='marcar_linha'><td bgcolor='$cor_fundo'></td><td align='center'><lin>"; if (($_GET[idt]==$row2[0]) or ($_GET[id]==$row2[0])) {echo "<marcar>";} echo " $row2[0]</td>
					<td><lin>";  if (($_GET[idt]==$row2[0]) or ($_GET[id]==$row2[0])) {echo "<marcar>";} echo substr($row2[1],1)."</td>
					<td align='center'><lin>"; if (($_GET[idt]==$row2[0]) or ($_GET[id]==$row2[0])) {echo "<marcar>";} echo " $row2[2]</td>
					<td align='center'><lin>$row2[3]</td><td align='center'><lin>$row2[4]</td> ";
				if ($_SESSION['perfil']=='ADMINISTRADOR *') {
						echo "<td align='center'><lin><a href='index.php?nomeArquivo=alterar_usuario.php&&idt=$row2[0]'>Alterar</td><td align='center'><lin><a href='index.php?nomeArquivo=remover_registro.php&valor=$row2[0]&tab=usuario&ch=idt&registro=o usuário ".substr($row2[1],1)." $row2[2]&arquivo=remover_usuario.php&ancora=$row1[0]''>Remover</a></td></tr>";
				}elseif ($_SESSION['perfil']=='ADMINISTRADOR'){
					if(($_SESSION['codom']==$row1[0]) or ($_SESSION['codom']==$row1[3])) {
						echo "<td align='center'><lin><a href='index.php?nomeArquivo=alterar_usuario.php&&idt=$row2[0]'>Alterar</td><td align='center'><lin><a href='index.php?nomeArquivo=remover_registro.php&valor=$row2[0]&tab=usuario&ch=idt&registro=o usuário ".substr($row2[1],1)." $row2[2]&arquivo=remover_usuario.php&ancora=$row1[0]''>Remover</a></td></tr>";
					} else {
						echo"<td align='center'><lin>-</td><td align='center'><lin>-</td></tr>";
					}
				}elseif (($_SESSION['perfil']=='FISCAL DA OM') or ($_SESSION['perfil']=='CMT DA OM')){
					if($_SESSION['codom']==$row1[0]) {
						echo "<td align='center'><lin><a href='index.php?nomeArquivo=alterar_usuario.php&&idt=$row2[0]'>Alterar</td><td align='center'><lin><a href='index.php?nomeArquivo=remover_registro.php&valor=$row2[0]&tab=usuario&ch=idt&registro=o usuário ".substr($row2[1],1)." $row2[2]&arquivo=remover_usuario.php&ancora=$row1[0]''>Remover</a></td></tr>";
					} else {
						echo"<td align='center'><lin>-</td><td align='center'><lin>-</td></tr>";
					}
				}
    		}
			
    		echo "<tr><td bgcolor='$cor_fundo'></td><td colspan='6' align='center'>&nbsp;&nbsp;&nbsp;<lin><a href='index.php?nomeArquivo=cons_usuario.php' style='text-decoration:none'>Fechar&nbsp;</td></tr>";	
    		echo "</table></td></tr>";	
    		echo "<tr><td></td><td colspan='7' bgcolor='$cor_fundo'> &nbsp;</td></tr>";	
		} else {
			echo"<tr bgcolor='$cor3' class='marcar_linha'><td width='2%' bgcolor='$cor_fundo'></td><td><lin>&nbsp;$row1[1] &nbsp;&nbsp;&nbsp;<a href='index.php?nomeArquivo=cons_usuario.php"; if ($_GET['listar']==$row1[0]){ echo "' style='text-decoration:none'>Fechar";} else { echo "&listar=$row1[0]#$row1[0]' style='text-decoration:none'>Listar";} echo "</td><td align='center'><lin>$row1[2]</td><td bgcolor='$cor_fundo'>&nbsp;</td></tr>";
		}

	}
	echo "<tr><td>&nbsp;</td></tr>";
}

echo "</table>";
?>

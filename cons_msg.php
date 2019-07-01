<?php
  if(empty($_SESSION['usuario']))
    include "verifica_sessao.php";
?>
<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>

<?php
echo "<table border='0' width='100%' align='center' valign='top'>";
	echo "<tr align='center' >
				<td colspan=4>";
				if($_GET['arq']=='1') {
					echo "<br><t1>Visualização de Mensagens Arquivadas</t1> <br><br>";
				} elseif($_GET['arq']=='2') {
					echo "<br><t1>Visualização de Mensagens Enviadas</t1> <br><br>";
				} else {
					echo "<br><t1>Visualização de Mensagens Recebidas</t1> <br><br>";
				}	
		echo "</td>
			</tr>";
	echo "<tr align='left' bgcolor='$cor1'>
				<td colspan=4 valign=button>
					&nbsp;<a href='index.php?nomeArquivo=cad_msg.php' style='text-decoration:none' title='Escrever Nova Mensagem'><IMG SRC='./imagens/escrever.jpg'  border=0></a>
					&nbsp;<a href='index.php?nomeArquivo=cons_msg.php' style='text-decoration:none' title='Visualizar Mensagens Recebidas'><IMG SRC='./imagens/recebidas.jpg'  border=0></a>
					&nbsp;<a href='index.php?nomeArquivo=cons_msg.php&arq=2' style='text-decoration:none' title='Visualizar Mensagens Enviadas'><IMG SRC='./imagens/enviadas.jpg'  border=0></a>
					&nbsp;<a href='index.php?nomeArquivo=cons_msg.php&arq=1' style='text-decoration:none' title='Visualizar Mensagens Arquivadas'><IMG SRC='./imagens/arquivadas.jpg'  border=0></a>
				</td>
			</tr>";
	echo "<tr align='center' bgcolor='$cor2'>";
				if ($_GET['arq']=='2') {
					echo "<td width='30%'><coluna><b>Destnatário(s)</td>";
				} else {
					echo "<td width='30%'><coluna><b>Remetente</td>";
				}		
				echo "<td width='55%'><coluna><b>Assunto</td>
				<td width='13%'><coluna><b>Data</td>
			</tr>";


	if($_GET['arq']=='1') {
		$query="select m.data_hora, m.assunto, d.remetente_idt, d.leitura, d.destinatario_idt 
					from mensagem m, destinatario d 
					where m.remetente_idt=d.remetente_idt
							and m.data_hora=d.data_hora 
							and d.destinatario_idt='".$_SESSION['idt']."' 
							and d.arquivada is not null 
					order by m.data_hora desc";
	} elseif($_GET['arq']=='2') {
		$query="select distinct m.data_hora, m.assunto, m.remetente_idt from mensagem m, destinatario d 
					where m.remetente_idt=d.remetente_idt
							and m.data_hora=d.data_hora 
							and m.remetente_idt='".$_SESSION['idt']."' 
					order by data_hora desc";
	} else {
		$query="select m.data_hora, m.assunto, d.remetente_idt, d.leitura, d.destinatario_idt 
					from mensagem m, destinatario d 
					where m.remetente_idt=d.remetente_idt
							and m.data_hora=d.data_hora 
							and d.destinatario_idt='".$_SESSION['idt']."' 
							and d.arquivada is null 
					order by m.data_hora desc";
	}	
 
   $result = mysql_query($query);
	$controle=0;
   while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		echo "<tr bgcolor='$cor3' class='marcar_linha'>";
		if ($_GET['arq']=='2') { //Se for mensagens enviadas


				echo "<td>&nbsp;<lin>";
				$qry="select substr(u.post_grad,2), u.nome_guerra, om.sigla
						from om, usuario u, destinatario d
						where om.codom=u.om_codom
							and u.idt=d.destinatario_idt
							and d.remetente_idt='$row[2]'
							and d.data_hora='$row[0]'
							";				
   				$res = mysql_query($qry);

				$link='';
   			while ($dest = mysql_fetch_array($res, MYSQL_NUM)) {
					$link= $link."<a href='index.php?nomeArquivo=ler_msg.php&dt_hr=$row[0]&idt=$row[2]&lida=1&arq=".$_GET['arq']."' style='text-decoration:none' title='$dest[2]'><font color='#1C1C1C'>$dest[0] $dest[1]</a>, ";
				}
				echo substr($link,0,-2)."</td>";
				echo "<td>&nbsp;<lin><a href='index.php?nomeArquivo=ler_msg.php&dt_hr=$row[0]&idt=$row[2]&lida=1&arq=".$_GET['arq']."' style='text-decoration:none'><font color='#1C1C1C'>$row[1]</a></td>";
				echo "<td align=center><lin>".substr($row[0],8,2)."/".substr($row[0],5,2)."/".substr($row[0],0,4)." ".substr($row[0],10,6)."</td>";

	} else {
		$query1="select substr(u.post_grad,2), u.nome_guerra, om.sigla 
				from om, usuario u, destinatario d
				where om.codom=u.om_codom
					and u.idt=d.remetente_idt 
					and u.idt='$row[2]' ";

		$result1 = mysql_query($query1);
		$row1 = mysql_fetch_row($result1);
		if ($row[3] == null) { /*/Se for mensagens recebidas e não lidas */
				echo "<td>&nbsp;<lin><b><a href='index.php?nomeArquivo=ler_msg.php&dt_hr=$row[0]&idt=$row[2]&arq=".$_GET['arq']."' style='text-decoration:none' title='Integrante do $row1[2]'><font color='#1C1C1C'>$row1[0] $row1[1]</a></td>";
				echo "<td>&nbsp;<lin><b><a href='index.php?nomeArquivo=ler_msg.php&dt_hr=$row[0]&idt=$row[2]&arq=".$_GET['arq']."' style='text-decoration:none'><font color='#1C1C1C'>$row[1]</a></td>";
				echo "<td align=center><lin>".substr($row[0],8,2)."/".substr($row[0],5,2)."/".substr($row[0],0,4)." ".substr($row[0],10,6)."</td>";
		} else { /*/se for mensagens recebidas e lidas*/
				echo "<td>&nbsp;<lin><a href='index.php?nomeArquivo=ler_msg.php&dt_hr=$row[0]&idt=$row[2]&lida=1&arq=".$_GET['arq']."' style='text-decoration:none' title='Integrante $row1[2]'><font color='#1C1C1C'>$row1[0] $row1[1]</a></td>";
				echo "<td>&nbsp;<lin><a href='index.php?nomeArquivo=ler_msg.php&dt_hr=$row[0]&idt=$row[2]&lida=1&arq=".$_GET['arq']."' style='text-decoration:none'><font color='#1C1C1C'>$row[1]</a></td>";
				echo "<td align=center><lin>".substr($row[0],8,2)."/".substr($row[0],5,2)."/".substr($row[0],0,4)." ".substr($row[0],10,6)."</td>";
		}
	}
		echo "</tr>";
  	}
  	$num_rows = mysql_num_rows($result);
  	mysql_free_result($result);
echo "</table>";
?>




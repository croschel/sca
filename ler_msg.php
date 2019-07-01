<?php
  if(empty($_SESSION['usuario']))
    include "verifica_sessao.php";
?>
<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>

<br><t1>Leitura de Mensagem</t1> <p>
<?php
	/*/Seeleciona a Mensagem Escolhida*/
	$query="select * from mensagem where data_hora='".$_GET['dt_hr']."' and remetente_idt='".$_GET['idt']."'";
	$result = mysql_query($query);
	$row = mysql_fetch_row($result);

	/*/Se não foi lida registra a leitura da mensagem*/
	if($_GET['lida']!='1') {
		$query2 = "update destinatario set leitura=now()
				where data_hora='".$_GET['dt_hr']."' and destinatario_idt='".$_SESSION['idt']."' ";

		$rc = mysql_query($query2);
	}

	/*/Seeleciona o Remetente da Mensagem*/
	$query1="select substr(u.post_grad,2), u.nome_guerra, om.sigla, u.idt 
				from om, usuario u 
				where om.codom=u.om_codom
					and u.idt='".$_GET['idt']."'";
	$result1 = mysql_query($query1);
	$row1 = mysql_fetch_row($result1);

	/*/Verifica a Mensagem Anterior*/
	if($_GET['arq']=='2') {
		$query3="select data_hora, remetente_idt from mensagem where data_hora < '".$_GET['dt_hr']."' and remetente_idt='".$_SESSION['idt']."' order by data_hora desc";
	} elseif($_GET['arq']=='1') {
		$query3="select data_hora, remetente_idt from destinatario where data_hora < '".$_GET['dt_hr']."' and destinatario_idt='".$_SESSION['idt']."' and arquivada is not null order by data_hora desc";
	} else {
		$query3="select data_hora, remetente_idt from destinatario where data_hora < '".$_GET['dt_hr']."' and destinatario_idt='".$_SESSION['idt']."' and arquivada is null order by data_hora desc";
	}	
	$result3 = mysql_query($query3);
	$row3 = mysql_fetch_row($result3);

	//Verifica a Proxima Mensagem

	if($_GET['arq']=='2') {
		$query4="select data_hora, remetente_idt from mensagem where data_hora > '".$_GET['dt_hr']."' and remetente_idt='".$_SESSION['idt']."' order by data_hora ";
	} elseif($_GET['arq']=='1') {
		$query4="select data_hora, remetente_idt from destinatario where data_hora > '".$_GET['dt_hr']."' and destinatario_idt='".$_SESSION['idt']."' and arquivada is not null order by data_hora";
	} else {
		$query4="select data_hora, remetente_idt from destinatario where data_hora > '".$_GET['dt_hr']."' and destinatario_idt='".$_SESSION['idt']."' and arquivada is null order by data_hora";
	}	

	$result4 = mysql_query($query4);
	$row4 = mysql_fetch_row($result4);

	echo "<table border='0' width='100%' align='center' valign='top'>";
	echo "<tr bgcolor='$cor2' >";
		echo "<td colspan=2 valign='middle'>";
			if($row3[0]<>'') {
				echo "&nbsp;<a href='index.php?nomeArquivo=ler_msg.php&dt_hr=$row3[0]&idt=$row3[1]&lida=".$_GET['lida']."&arq=".$_GET['arq']."' style='text-decoration:none'title='Mensagem Anterior'><IMG SRC='./imagens/anterior.jpg'  border=0></a>";
			} else {
				echo "&nbsp;<IMG SRC='./imagens/anterior.jpg'  border=0></a>";
			}
			if($row4[0]<>'') {
				echo "&nbsp;<a href='index.php?nomeArquivo=ler_msg.php&dt_hr=$row4[0]&idt=$row4[1]&lida=".$_GET['lida']."&arq=".$_GET['arq']."' style='text-decoration:none' title='Próxima Mensagem'><IMG SRC='./imagens/proxima.jpg'  border=0></a>";
			}else {
				echo "&nbsp;<IMG SRC='./imagens/proxima.jpg'  border=0 </a>";
			}
			if($_GET['arq']!='2') {			
				echo "&nbsp;<a href=\"index.php?nomeArquivo=resp_msg.php&destino=$row1[0] $row1[1], &ri=$row1[3],&dt_hr=".$_GET['dt_hr']."\" style='text-decoration:none' title='Responder esta Mensagem'><IMG SRC='./imagens/responder.jpg'  border=0></a>";
			}			
			echo "&nbsp;<a href='index.php?nomeArquivo=cad_msg.php&dest=1&enc=$row1[3]&ri=$row1[3],&dt_hr=".$_GET['dt_hr']."' style='text-decoration:none' title='Encaminhar esta Mensagem'><IMG SRC='./imagens/encaminhar.jpg'  border=0></a>";

			if($_GET['arq']=='1') {
				echo "&nbsp;<a href='index.php?nomeArquivo=arq_msg.php&dt_hr=".$_GET['dt_hr']."&idt=".$_GET['idt']."&arq=0' style='text-decoration:none' title='Desarquivar esta Mensagem'><IMG SRC='./imagens/desarquivar.jpg'  border=0></a>";
			} elseif($_GET['arq']=='2') {
				echo "";
			} else {
				echo "&nbsp;<a href='index.php?nomeArquivo=arq_msg.php&dt_hr=".$_GET['dt_hr']."&idt=".$_GET['idt']."&arq=1' style='text-decoration:none' title='Arquivar esta Mensagem'><IMG SRC='./imagens/arquivar.jpg'  border=0></a>";
			}
			echo "&nbsp;<a href='index.php?nomeArquivo=cons_msg.php&arq=".$_GET['arq']."' style='text-decoration:none' title='Voltar a Lista de Mensagens'><IMG SRC='./imagens/retornar.jpg'  border=0></a>";
			echo "&nbsp;<a href='impr_msg.php?dt_hr=".$_GET['dt_hr']."&idt=".$_GET['idt']."' style='text-decoration:none' title='Imprimir a Mensagem em Tela'><IMG SRC='./imagens/imprimir.jpg'  border=0></a>";
		echo "</td>";
	echo "</tr>";
	echo "<tr><td colspan=2><hr></td></tr>";
	echo "<tr><td align=right width='10%'><coluna>Assunto:&nbsp;</td><td width='90%'>$row[2]</td></tr>";
	echo "<tr><td align=right><coluna>Remetente:&nbsp;</td><td><lin><b>$row1[0] $row1[1]</b> do(a) $row1[2]</td></tr>";
	echo "<tr><td colspan=2><hr></td></tr>";
	echo "<tr><td align='right' valign='top'><coluna>Texto:&nbsp;</td><td><lin>".nl2br($row[3])."</td></tr>";
	echo "<tr><td colspan=2><br><br><br></td></tr>";
	echo "<tr bgcolor='$cor2'><td colspan=2>&nbsp;</td></tr>";
	echo "<tr><td align='right' valign='top'><coluna>Leitura:&nbsp;</td><td ><lin>";
	/*/mostra as informações sobre a leitura das mensagens*/	
	$qry="select substr(u.post_grad,2), u.nome_guerra, om.sigla, d.leitura
			from om, usuario u, destinatario d 
			where om.codom=u.om_codom
			and u.idt=d.destinatario_idt
			and d.data_hora='".$_GET['dt_hr']."' 
			and d.remetente_idt='".$_GET['idt']."'";				
   $res = mysql_query($qry);

   while ($dest = mysql_fetch_array($res, MYSQL_NUM)) {
   	if ($dest[3]!='') {
			echo "<label title='O destinatário leu em ".substr($dest[3],8,2)."/".substr($dest[3],5,2)."/".substr($dest[3],0,4)." às ".substr($dest[3],11,5)." hs.'><IMG SRC='./imagens/executada.jpg' width='15' height='15' border='0' >&nbsp;$dest[0] $dest[1] <i>($dest[2])</i> </label><br>";
		} else {
			echo "<label title='O destinatário ainda não leu.'> <IMG SRC='./imagens/naolida.jpg' width='15' height='15' border='0' >&nbsp;$dest[0] $dest[1] <i>($dest[2])</i> </label><br>";
		}	
	}
	echo "</td></tr>";
	echo "<tr><td colspan=2><br></td></tr>";
	echo "<tr bgcolor='$cor2'><td colspan=3>&nbsp;</td></tr>";
	echo "</form>";
	echo "</table>";
?>




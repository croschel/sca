<?php
  if(empty($_SESSION['usuario']))
    include "verifica_sessao.php";
?>
<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>

<script type="text/javascript" >
function insere(dest,valor,idt){

	if (valor==true){
		if (document.msg.destino.value==''){
			document.msg.destino.value = dest + ", ";
			document.msg.idts.value = idt + ",";
		} else {
			document.msg.destino.value = document.msg.destino.value + dest + ", ";
			document.msg.idts.value = document.msg.idts.value + idt + ",";
		}
	} else {
		while (document.msg.destino.value.indexOf(dest) != -1) {
 			document.msg.destino.value = document.msg.destino.value.replace(dest+", ", '');
 			document.msg.idts.value = document.msg.idts.value.replace(idt+",", '');
		}
	}
}

function getOM(cod_om) {
      var destino=document.msg.destino.value;
      var ri=document.msg.idts.value;
      var ass=document.msg.assunto.value;
      var tex=document.msg.texto.value;
      var dt_hr=document.msg.dt_hr.value;    
      var enc=document.msg.enc.value;
      document.location=('index.php?nomeArquivo=cad_msg.php&dest=1&codom=' + cod_om + '&destino=' + destino + "&enc=" + enc +"&dt_hr=" + dt_hr +"&ri=" + ri + '&assunto=' + ass + "&texto=" + tex);
}

</script>

<?php
	echo "<table border='0' width='100%' align='center' valign='top'>";
		echo "<tr><td colspan='3' align='center'><t1><br>Registro de Mensagem<p></t1></td></tr>";
	if($_GET['enc']!='') { 
		echo "<tr><td colspan='3' bgcolor='$cor1'><coluna>&nbsp;&nbsp;&nbsp;Encaminhar Mensagem</td></tr>";
	} else { 
		echo "<tr><td colspan='3' bgcolor='$cor1'><coluna>&nbsp;&nbsp;&nbsp;Nova Mensagem</td></tr>";
	}
	echo "<form name='msg' method='POST' action='index.php?nomeArquivo=gravar_msg.php' >";

				echo "<tr><td width='10%' valign='top' align='right' rowspan='2'><coluna>Destinatário(s):&nbsp;</td></td><td width='90%'><t2>OM:&nbsp; <select name='om' onChange='getOM(this.value);'>";
				echo "<option value=''></option>";
				/*/Seleciona a relaçao de OM */
				$result1 = mysql_query("select distinct o.codom, o.sigla
								from om o, usuario u
								where o.codom=u.om_codom 
								order by 1");
				while ($row1 = mysql_fetch_array($result1, MYSQL_NUM)) {
					echo "<option value=$row1[0] "; if ($_GET['codom']==$row1[0]){echo "selected";} echo ">$row1[1]</option>";
				}
				echo "</select><br><lin>";
				/*/Seleciona a relaçao de destinatários*/
				$result2 = mysql_query("select u.idt, substr(u.post_grad,2), u.nome_guerra, u.perfil
								from om o, usuario u
								where o.codom=u.om_codom 
									and o.codom='".$_GET['codom']."'
									and u.situacao='PRONTO NA OM'
								order by 2");
				$cont=1;
				while ($row2 = mysql_fetch_array($result2, MYSQL_NUM)) {
					$pos = strpos($_GET['destino'], $row2[2]);
					if ($pos === false) {
						echo "<input type='checkbox' name='$row2[1] $row2[2]' value='$row2[0]' onclick='insere(this.name,this.checked,this.value)'/> <b>$row2[1] $row2[2]</b> - $row2[3]<br>";
					} else {
						echo "<input type='checkbox' name='$row2[1] $row2[2]' value='$row2[0]' checked onclick='insere(this.name,this.checked,this.value)'/> <b>$row2[1] $row2[2]</b> - <lin>$row2[3]<br>";
					}
					$conty++;
				}
				echo "</td></tr>";
	echo "<tr><td><textarea name='destino' rows='2' cols='80' readonly>".substr($_GET['destino'],0,-1)." </textarea></td></tr>";


	if($_GET['enc']!='') { 	
		/*/Seleciona o assunto e o texto*/
		$query3 = "select substr(u.post_grad,2), u.nome_guerra, m.assunto, m.texto
								from usuario u, mensagem m
								where u.idt=m.remetente_idt
									and m.data_hora='".$_GET['dt_hr']."'
									and m.remetente_idt='".$_GET['enc']."'
								";
		$result3 = mysql_query($query3);
		$row3 = mysql_fetch_row($result3); 
		$assunto = "Enc: ".$row3[2];
		$pos = strripos($_GET['texto'], "===========================================");
/*/		$row3[3]=substr($_GET['texto'],0,$pos);*/
		$texto="&#13;&#13;&#13;===========================================&#13;$row3[0] $row3[1] Escreveu em: ".substr($_GET['dt_hr'],8,2)."/".substr($_GET['dt_hr'],5,2)."/".substr($_GET['dt_hr'],0,4)." as ".substr($_GET['dt_hr'],11,5)." hs &#13;&#13;".$row3[3];
/*/	echo 	"<input type='hidden' name='enc' value='".$_GET['enc']."'>";*/
	} else {
		$assunto=$_GET['assunto'];
		$texto=$_GET['texto'];
	}
	echo 	"<input type='hidden' name='enc' value='".$_GET['enc']."'>";
	echo "<tr><td align=right><coluna>Assunto:&nbsp;</td><td><input type='text' size='44' maxlength='50' name='assunto' value='$assunto' title='Informe o assunto da mensagem.'></td></tr>";
	echo "<tr><td align='right' valign='top'><coluna>Texto:&nbsp;</td><td><textarea name='texto' rows='10' cols='80'>".$texto."</textarea></td></tr>";
	echo "<tr><td align='right'></td><td><input type='submit' value= '  Enviar  '>&nbsp;&nbsp;&nbsp;<input type='reset' value= '  Limpar  '>&nbsp;&nbsp;&nbsp;<input type='button' value= '  Cancelar  ' onclick='history.go(-1)'></td></tr>";
	echo 	"<input type='hidden' name='idts' value='".$_GET['ri']."'>";
	echo 	"<input type='hidden' name='dt_hr' value='".$_GET['dt_hr']."'>";
	echo "</form>";
	echo "</table>";
?>




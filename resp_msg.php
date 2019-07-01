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
      document.location=('index.php?nomeArquivo=cad_msg.php&dest=1&codom=' + cod_om + '&destino=' + destino + "&ri=" + ri + '&assunto=' + ass + "&texto=" + tex);
}

</script>

<br><t1>Resposta de Mensagem</t1> <p>
<?php
	echo "<table border='0' width='100%' align='center' valign='top'>";
	echo "<tr><td colspan='3' bgcolor='#4A708B'><coluna>&nbsp;&nbsp;&nbsp;Resposta da Mensagem</td></tr>";
	echo "<tr><td align='right' valign='top' width='10%'><coluna>Destinatário:&nbsp;";
	echo "<form name='msg' method='POST' action='index.php?nomeArquivo=gravar_msg.php' >";
				echo "<td width='90%'><textarea name='destino' rows='2' cols='80' readonly>".substr($_GET['destino'],0,-1)." </textarea></td>
			</tr>";
			if($_GET['dest']=='1') {
				echo "<tr><td align='right'></td><td ><t2>Organizações Militares:&nbsp; <select name='om' onChange='getOM(this.value);'>";
				echo "<option value=''></option>";
				
				/*/Seleciona a relaçao de usuários motoristas*/
				$result1 = mysql_query("select distinct o.codom, o.sigla
								from om o, usuario u
								where o.codom=u.om_codom 
								order by 1");
				while ($row1 = mysql_fetch_array($result1, MYSQL_NUM)) {
					echo "<option value=$row1[0] "; if ($_GET['codom']==$row1[0]){echo "selected";} echo ">$row1[1]</option>";
				}
				echo "</select></td></tr>";
				
				/*/Seleciona a relaçao de usuários motoristas*/
				$result2 = mysql_query("select u.idt, u.post_grad, u.nome_guerra, f.funcao
								from om o, usuario u
								where o.codom=u.om_codom 
									and o.codom='".$_GET['codom']."'
								order by 1");
				$cont=1;
				while ($row2 = mysql_fetch_array($result2, MYSQL_NUM)) {
					$pos = strpos($_GET['destino'], $row2[2]);
					if ($pos === false) {
						echo "<tr><td></td><td><lin><input type='checkbox' name=\"$row2[1] $row2[2]\" value='$row2[0]' onclick='insere(this.name,this.checked,this.value)'/> <b>".substr($row2[1],1)." $row2[2]</b> - $row2[3]</td></tr>";
					} else {
						echo "<tr><td></td><td><lin><input type='checkbox' name=\"$row2[1] $row2[2]\" value='$row2[0]' checked onclick='insere(this.name,this.checked,this.value)'/> <b>".substr($row2[1],1)." $row2[2]</b> - <lin>$row2[3]</td></tr>";
					}
					$conty++;
				}

			}
		
	/*/Seleciona o assunto e o texto*/
/*	$query3 = "select assunto, texto
								from mensagem
								where data_hora='".$_GET['dt_hr']."'
									and remetente_idt='".substr($_GET['ri'],0,-1)."'
								";
	$result3 = mysql_query($query3);
	$row3 = mysql_fetch_row($result3); 
	$row3[1]="&#13;&#13;&#13;&#13;&#13;Em ".substr($_GET['dt_hr'],8,2)."/".substr($_GET['dt_hr'],5,2)."/".substr($_GET['dt_hr'],0,4).", ".substr($_GET['destino'],0,-2)." Escreveu:&#13;=====================================================&#13;".$row3[1];
*/
	$query3 = "select substr(u.post_grad,2), u.nome_guerra, m.assunto, m.texto
								from usuario u, mensagem m
								where u.idt=m.remetente_idt
									and m.data_hora='".$_GET['dt_hr']."'
									and remetente_idt='".substr($_GET['ri'],0,-1)."'
								";
		$result3 = mysql_query($query3);
		$row3 = mysql_fetch_row($result3); 
		$row3[2] = "Re: ".$row3[2];
		$pos = strripos($_GET['texto'], "===========================================");
		$row3[3]="&#13;&#13;&#13;===========================================&#13;$row3[0] $row3[1] Escreveu em: ".substr($_GET['dt_hr'],8,2)."/".substr($_GET['dt_hr'],5,2)."/".substr($_GET['dt_hr'],0,4)." as ".substr($_GET['dt_hr'],11,5)." hs &#13;&#13;".$row3[3];


	echo "<tr><td align=right><coluna>Assunto:&nbsp;</td><td><input type='text' size='44' maxlength='50' name='assunto' value='$row3[2]' title='Informe o assunto da mensagem.'></td></tr>";
	echo "<tr><td align='right' valign='top'><coluna>Texto:&nbsp;</td><td><textarea name='texto' rows='10' cols='80'>$row3[3]</textarea></td></tr>";
	echo "<tr><td align='right'></td><td><input type='submit' value= '  Enviar  '>&nbsp;&nbsp;&nbsp;<input type='button' value= ' Cancelar ' onclick='history.go(-1)'></td></tr>";
	echo 	"<input type='hidden' name='idts' value='".$_GET['ri']."'>";
	echo "</form>";
	echo "</table>";
?>




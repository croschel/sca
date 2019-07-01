<?php
	include "verifica_sessao.php";
	/*Conexão ao Banco de Dados*/
	include "conexao.php";
	/*Seeleciona a OM*/
	$query="select * from om where codom='".$_GET['codom']."'";
	$result = mysql_query($query,$conexao);
	$row = mysql_fetch_row($result);

?>
<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<Script Language="JavaScript">

function habilitar(that){
	if (that.value=='Não Integrante'){
		document.om.subordinacao.disabled=1;
		document.om.oc.disabled=1;
	} else {
		document.om.subordinacao.disabled=0;
		document.om.oc.disabled=0;
	}
}

</script>

<?php
	echo "<center><t1><br>Alteração de Organização Militar </t1> </center>";
	echo "<form method='POST' action='index.php?nomeArquivo=gravar_om.php' name='om'>";
   echo "<input type='hidden' name='codom_ant' value='$row[0]'><br>";
	echo "<table>";
	echo "<tr><td align='right'>Codom da OM:* </td><td><input type='text' size='20' name='codom' value='$row[0]' required ></td></tr>";
	echo "<tr><td align='right'>Sigla da OM:* </td><td><input type='text' size='30' maxlength='30' name='sigla' value='$row[1]' required ></td></tr>";
	echo "<tr><td align='right'>Nome da OM:* </td><td><input type='text' size='60' maxlength='50' name='nome_om' value='$row[2]' required ></td></tr>";

	if (($_SESSION['perfil']=='FISCAL DA OM') or ($_SESSION['perfil']=='ABASTECEDOR')){
		echo "<tr><td align='right'>Tipo de OM:* </td><td><input type='text' size='20' readonly name='tipo' value='$row[3]'></td></tr>";
		echo "<input type='hidden' name='oc' value='$row[4]'>";
		echo "<input type='hidden' name='subordinacao' value='$row[5]'>";
	} elseif ($_SESSION['perfil']=='ADMINISTRADOR') {
		echo "<tr><td align='right'>Tipo de OM:* </td><td><select name='tipo' size='1' onchange='habilitar(this)'>";
             						echo "<option value='OC'"; if ($row[3]=="OC"){ echo "selected";} echo ">órgão Controlador</option>";
            						echo "<option value='Integrante'"; if ($row[3]=="Integrante"){ echo "selected";} echo ">Integrante do Sistema</option>";
            						echo "<option value='Não Integrante'"; if ($row[3]=="Não Integrante"){ echo "selected";} echo ">Não Integrante</option>";
         					echo "</select></td></tr>";
		echo "<tr><td align='right'>Subordinado a(o):* </td><td><select name='subordinacao' size='1'"; if ($row[3]=="Não Integrante"){ echo "disabled";} echo ">
					<option></option>";
    					/*/Seleciona a relação de OM */ 
    					$result = mysql_query("select * from om where tipo in('OC', 'Integrante') and sigla like 'CMDO%' order by sigla");
  						while ($om = mysql_fetch_array($result, MYSQL_NUM)) {
							echo "<option value='$om[0]' "; if ($row[5]==$om[0]){ echo "selected";} echo ">".$om[1];
						}
					echo "</select></td></tr>";
		echo "<tr><td align='right'>Orgão Controlador:* </td><td><select name='oc' size='1'"; if ($row[3]=="Não Integrante"){ echo "disabled";} echo ">
					<option></option>";
    					/*/Seleciona a relação de OM */ 
    					$result = mysql_query("select * from om where tipo = 'OC' order by sigla");
  						while ($om = mysql_fetch_array($result, MYSQL_NUM)) {
							echo "<option value='$om[0]' "; if ($row[4]==$om[0]){ echo "selected";} echo ">".$om[1];
						}
					echo "</select></td></tr>";
   } else {
			echo "<tr><td align='right'>Tipo de OM:* </td><td><select name='tipo' size='1' onchange='habilitar(this)'>";
            						echo "<option value='OC'"; if ($row[3]=="OC"){ echo "selected";} echo ">órgão Controlador</option>";
            						echo "<option value='Integrante'"; if ($row[3]=="Integrante"){ echo "selected";} echo ">Integrante do Sistema</option>";
            						echo "<option value='Não Integrante'"; if ($row[3]=="Não Integrante"){ echo "selected";} echo ">Não Integrante</option>";
         					echo "</select></td></tr>";
		echo "<tr><td align='right'>Subordinado a(o):* </td><td><select name='subordinacao' size='1'"; if ($row[3]=="Não Integrante"){ echo "disabled";} echo ">
					<option></option>";
    					/*/Seleciona a relação de OM */ 
    					$result = mysql_query("SELECT distinct om1.codom, om1.sigla
    													FROM om om1, om om2
    													where	om1.codom=om2.subordinacao
    														AND om1.subordinacao is not null
    														or om1.codom='".$_GET['codom']."'
    													ORDER BY abs(om1.sigla), om1.sigla");
  						while ($om = mysql_fetch_array($result, MYSQL_NUM)) {
							echo "<option value='$om[0]' "; if ($row[5]==$om[0]){ echo "selected";} echo ">".$om[1];
						}
					echo "</select></td></tr>";
		echo "<tr><td align='right'>Orgão Controlador:* </td><td><select name='oc' size='1'"; if ($row[3]=="Não Integrante"){ echo "disabled";} echo ">
					<option></option>";
    					/*/Seleciona a relação de OM */ 
    					$result = mysql_query("select * from om where tipo='OC' order by sigla");
  						while ($om = mysql_fetch_array($result, MYSQL_NUM)) {
							echo "<option value=\"".$om[0]."\" "; if ($row[4]==$om[0]){ echo "selected";} echo ">".$om[1];
						}
					echo "</select></td></tr>";
   }
	echo "<input type='hidden' name='operacao' value='2'>";
	echo "<tr><td>&nbsp;</td><td></td></tr>";
	echo "<tr><td align='right'><input type='submit' value= 'Gravar'></td><td><input type='reset' value= '  Limpar  '>&nbsp;<input type='button' value= 'Retornar' onclick='history.go(-1)'></td></tr>";
	echo "</table>";
	echo "</form>";

?>



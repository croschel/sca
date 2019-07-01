<?php
     include "verifica_sessao.php";
?>

<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>

<script language='JavaScript'>
function SomenteNumero(e){
    var tecla=(window.event)?event.keyCode:e.which;   
    if((tecla>47 && tecla<58)) return true;
    else{
    	if (tecla==8 || tecla==0) return true;
	else  return false;
    }
}

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

<form name="om" method="POST" action="index.php?nomeArquivo=gravar_om.php" >
   <br><t1>Cadastro de Organizações Militares </t1> <p> </center>
<table>
	<tr><td align="right">Codom da OM:* </td><td><input type="text" size="10" maxlength="6" name="codom" onkeypress='return SomenteNumero(event)' required ></td></tr>
	<tr><td align="right">Sigla da OM:* </td><td><input type="text" size="30" maxlength="30" name="sigla" required ></td></tr>
	<tr><td align="right">Nome da OM:* </td><td><input type="text" size="60" maxlength="50" name="nome_om" required ></td></tr>
	<?php	
	if (($_SESSION['perfil']=='FISCAL DA OM') or ($_SESSION['perfil']=='ABASTECEDOR')){
		echo "<tr><td align='right'>Tipo de OM:* </td><td><select name='tipo' size='1'>
            						<option value='Não Integrante'>Não Integrante</option>
         					</select></td></tr>";
	} elseif ($_SESSION['perfil']=='ADMINISTRADOR') {
		echo "<tr><td align='right'>Tipo de OM:* </td><td><select name='tipo' size='1' onchange='habilitar(this)'>
            						<option value='Integrante' selected>Integrante do Sistema</option> 
            						<option value='Não Integrante'>Não Integrante</option>
         					</select></td></tr>";
		echo "<tr><td align='right'>Subordinada a(o):* </td><td><select name='subordinacao' size='1'>
					<option></option>";
    					/*/Seleciona a relaãão de OM*/
    					$result = mysql_query("select * from om where tipo in('OC', 'Integrante') and nome like 'COMANDO%' order by sigla");
  						while ($om = mysql_fetch_array($result, MYSQL_NUM)) {
							echo "<option value=\"".$om[0]."\" >".$om[1];
						}
					echo "</select></td></tr>";
		echo "<tr><td align='right'>Orgão Controlador:* </td><td><select name='oc' size='1'>";
    					/*/Seleciona a relaãão de OM */
    					$result = mysql_query("select * from om where codom='".$_SESSION['codom']."' order by sigla");
  						while ($om = mysql_fetch_array($result, MYSQL_NUM)) {
							echo "<option value=\"".$om[0]."\" >".$om[1];
						}
					echo "</select></td></tr>";
   } else {
		echo "<tr><td align='right'>Tipo de OM:* </td><td><select name='tipo' size='1' onchange='habilitar(this)'>
            						<option value='OC' >órgão Controlador</option>
            						<option value='Integrante' selected>Integrante do Sistema</option> 
            						<option value='Não Integrante'>Não Integrante</option>
         					</select></td></tr>";
		echo "<tr><td align='right'>Subordinada a(o):* </td><td><select name='subordinacao' size='1'>
					<option></option>";
    					/*/Seleciona a relaãão de OM*/
    					$result = mysql_query("SELECT distinct om1.codom, om1.sigla
    													FROM om om1, om om2
    													where	om1.codom=om2.subordinacao
    														AND om1.subordinacao is not null
    													ORDER BY abs(substring(om1.sigla,6)), om1.sigla");
  						while ($om = mysql_fetch_array($result, MYSQL_NUM)) {
							echo "<option value=\"".$om[0]."\" >".$om[1];
						}
					echo "</select></td></tr>";
		echo "<tr><td align='right'>Orgão Controlador:* </td><td><select name='oc' size='1'>
					<option></option>";
    					/*/Seleciona a relaãão de OM*/
    					$result = mysql_query("select * from om where tipo='OC' order by sigla");
  						while ($om = mysql_fetch_array($result, MYSQL_NUM)) {
							echo "<option value=\"".$om[0]."\" >".$om[1];
						}
					echo "</select></td></tr>";
   }
   ?>
	
	<input type="hidden" name="operacao" value="1">
	<tr><td></td><td></td></tr>
	<tr><td align="right"><input type="submit" value= "Cadastrar"></td><td><input type="reset" value= "  Limpar  ">&nbsp;<input type="button" value= "Retornar" onclick="history.go(-1)"></td></tr>
   </table>
</form>


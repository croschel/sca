<?php
     include "verifica_sessao.php";
?>

<script language='JavaScript'>
function SomenteNumero(e){
    var tecla=(window.event)?event.keyCode:e.which;   
    if((tecla>47 && tecla<58)) return true;
    else{
    	if (tecla==8 || tecla==0) return true;
	else  return false;
    }
}

function habilita_gerente(perfil) {
	if (perfil=='CMT DA OM') {
		document.usuario.gerente.disabled = 0;
	} else if (perfil=='FISCAL DA OM') {
		document.usuario.gerente.disabled = 0;
	} else {
		document.usuario.gerente.disabled = 1;	
	}	
}

function visualiza_gerente(sinal) {

	if (sinal.substr(0,1)=='-') {
		document.usuario.gerente.style='display:none';
		document.usuario.gerente1.style='display:none';
	} else {
//	alert(sinal);
		document.usuario.gerente.style='display:block';
		document.usuario.gerente1.style='display:block';
		$("input[name='gerente1']").css({background:'transparent', border:'none',})
	}
}

</script>


<?php	
	echo "<form name='usuario' method='POST' action='index.php?nomeArquivo=gravar_usuario.php' >";
	echo "<center><t1><br>Cadastro de Usuário <p></t1>  </center>";
	echo "<table border='0'>";
	echo "<tr><td align='right'>Identidade do Usuário:* </td><td><input type='text' size='20' maxlength='12' name='idt' onkeypress='return SomenteNumero(event)' required ></td></tr>";
	echo "<tr><td align='right'>Posto/Graduação:* </td><td><select name='post_grad' size='1' required>
            						<option value='' selected disabled>:-: SELECIONE:-:</option>
            						<option value='aGEN'>GEN</option>
            						<option value='bCEL'>CEL</option>
            						<option value='cTEN CEL'>TEN CEL</option>
            						<option value='dMAJ'>MAJ</option>
            						<option value='eCAP'>CAP</option>
            						<option value='f1º TEN'>1º TEN</option>
            						<option value='g2º TEN'>2º TEN</option>
            						<option value='hASP OF'>ASP OF</option>
            						<option value='iST'>ST</option>
            						<option value='j1º SGT'>1º SGT</option>
            						<option value='k2º SGT'>2º SGT</option>
            						<option value='l3º SGT'>3º SGT</option>
            						<option value='mCB'>CB</option>
            						<option value='nSD'>SD</option>
            						<option value='oSC'>SC</option>
          					</select></td></tr>";
	echo "<tr><td align='right'>Nome do Usuário:* </td><td><input type='text' size='60' maxlength='70' name='nome_usuario' required></td></tr>";
	echo "<tr><td align='right'>Nome de Guerra:* </td><td><input type='text' size='30' maxlength='30' name='nome_guerra' required></tr>";

	$qry = "select om.codom, om.sigla, cmdo.codom, om.oc, cmdo.sigla, ifnull(r.om_codom,'-') 
			from om cmdo, om LEFT JOIN (select DISTINCT om_codom from reservatorio) r on(om.codom=r.om_codom)
			where om.subordinacao=cmdo.codom ";
									
			/*/Seleciona a relação de Unidades */
			if ($_SESSION['perfil']=='FISCAL DA OM'){
				$qry .= "and om.codom='".$_SESSION['codom']."' ";
			} elseif ($_SESSION['perfil']=='ADMINISTRADOR'){
				$qry .= "and om.tipo<>'Não Integrante' and (cmdo.codom='".$_SESSION['codom']."' or om.codom='".$_SESSION['codom']."') ";
			} else {
				$qry .= "and om.tipo <> 'Não Integrante' ";
			}
			$qry .= "order by abs(cmdo.sigla), cmdo.sigla, abs(om.sigla) ";
			$result = mysql_query($qry);

	echo "<tr><td align='right'>OM do Usuário:* </td><td><select name='codom' onChange='visualiza_gerente(this.value)' required >";
						echo "<option value='' selected disabled>:-: ESCOLHA A OM :-:</option>";
						while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
							if($cmdo!=$row[4]) {
								echo "<optgroup label=''>";
  								echo "<optgroup label=':. $row[4] .:'>";
  								$cmdo=$row[4];
  							}
							echo "<option value=\"".substr($row[5],0,1).$row[0]."\" "; if ($_GET['om']==$row[0]){	echo 'selected'; } echo " >".$row[1]."</option>";
						}
						echo "</select></td></tr>";

	echo "<tr><td align='right'>Perfil do Usuário:* </td><td><select name='perfil' size='1' onChange='habilita_gerente(this.value)' required >";
								echo "<option value='' selected disabled>:-: ESCOLHA O PERFIL :-:</option>";
								if ($_SESSION['perfil']=='ADMINISTRADOR *') {
									echo "<option value='ADMINISTRADOR *'>ADMINISTRADOR *</option><option value='ADMINISTRADOR'>ADMINISTRADOR</option>
            							<option value='CONSULTOR *'>CONSULTOR *</option>
            							<option value='ADMINISTRADOR'>ADMINISTRADOR</option>
            							<option value='CONSULTOR'>CONSULTOR</option>";
   							} elseif ($_SESSION['perfil']=='ADMINISTRADOR') {
            					echo "<option value='ADMINISTRADOR'>ADMINISTRADOR</option>
            							<option value='CONSULTOR'>CONSULTOR</option>";
							   } 
            				echo "<option value='CMT DA OM'>CMT DA OM</option>
            						<option value='FISCAL DA OM'>FISCAL DE COMBUSTÍVEL</option>
            						<option value='ABASTECEDOR'>ABASTECEDOR</option>";
   							echo "</select></td></tr>";
  
  echo "<tr id='linha' ><td align='right'><input type='checkbox' name='gerente' value='SIM' disabled style='display:none' ></td><td> <input type='text' name='gerente1' value='Gerente de Combustível' style='display:none' readonly></td</tr>";
   
	echo "<tr><td align='right'>Situação do Usuário:* </td><td><select name='situacao' size='1'>
            						<option value='PRONTO NA OM'>PRONTO NA OM</option>
            						<option value='DESLIGADO DA OM'>DESLIGADO DA OM</option>
          					</select></td></tr>";

	echo "<tr><td align='right'>Senha do Usuário:* </td><td><input type='password' size='20' maxlength='15' name='senha'></td></tr>";
	echo "<input type='hidden' name='operacao' value='1'>";
	echo "<tr><td>&nbsp;</td><td></td></tr>";
	echo "<tr><td align='right'><input type='submit' value= 'Cadastrar' ></td><td><input type='reset' value= '  Limpar  '>&nbsp;<input type='button' value= 'Retornar' onclick='history.go(-1)'></td></tr>";
	echo "</table>";
	echo "</form>";

?>
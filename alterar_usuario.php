<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8" />
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

function habilita_gerente(perfil) {
//	alert(perfil);
	if (perfil=='CMT DA OM') {
		document.usuario.gerente.disabled = 0;
	} else if (perfil=='FISCAL DA OM') {
		document.usuario.gerente.disabled = 0;
	} else {
		document.usuario.gerente.disabled = 1;	
	}	
//	$("input[name='gerente1']").css({background:'transparent', border:'none',})

}

function visualiza_gerente(sinal) {
//	alert(sinal);
	if (sinal.substr(0,1)=='-') {
		document.usuario.gerente.style='display:none';
		document.usuario.gerente1.style='display:none';
	} else {
		document.usuario.gerente.style='display:block';
		document.usuario.gerente1.style='display:block';
		$("input[name='gerente1']").css({background:'transparent', border:'none',})
	}
}

</script>

<?php
     include "verifica_sessao.php";
      /*ConexÃão ao Banco de Dados*/
      include "conexao.php";
      /*Seeleciona todos os alunos*/
      $query="select u.*, ifnull(r.om_codom,'-') from usuario u LEFT JOIN reservatorio r on(u.om_codom=r.om_codom) where u.idt='".$_GET['idt']."'";
      $result = mysql_query($query);
      $row = mysql_fetch_row($result);

	echo "<br><center><t1>Alteração de Usuário</t1> <b> </center>";
	echo "<form method='POST' action='index.php?nomeArquivo=gravar_usuario.php' name='usuario'>";
	echo "<table border='0'>";
	echo "<input type='hidden' name='idt_ant' value='$row[0]'><br>";
	echo "<tr><td align='right'>Identidade do Usuário:* </td><td><input type='text' size='20' maxlength='12' name='idt' value='$row[0]' required onkeypress='return SomenteNumero(event)'></td></tr>";
	echo "<tr><td align='right'>Posto/Graduação:* </td><td><select name='post_grad' size='1'>
            						<option value='aGEN' "; if ($row[1]=='aGEN'){ echo 'selected';} echo " >GEN</option>
            						<option value='bCEL' "; if ($row[1]=='bCEL'){ echo 'selected';} echo " >CEL</option>
            						<option value='cTEN CEL' "; if ($row[1]=='cTEN CEL'){ echo 'selected';} echo " >TEN CEL</option>
            						<option value='dMAJ' "; if ($row[1]=='dMAJ'){ echo 'selected';} echo " >MAJ</option>
            						<option value='eCAP' "; if ($row[1]=='eCAP'){ echo 'selected';} echo " >CAP</option>
            						<option value='f1º TEN' "; if ($row[1]=='f1º TEN'){ echo 'selected';} echo " >1º TEN</option>
            						<option value='g2º TEN' "; if ($row[1]=='g2º TEN'){ echo 'selected';} echo " >2º TEN</option>
            						<option value='hAsp Of' "; if ($row[1]=='hAsp Of'){ echo 'selected';} echo " >Asp Of</option>
            						<option value='iST' "; if ($row[1]=='iST'){ echo 'selected';} echo " >ST</option>
            						<option value='j1º SGT' "; if ($row[1]=='j1º SGT'){ echo 'selected';} echo " >1º SGT</option>
            						<option value='k2º SGT' "; if ($row[1]=='k2º SGT'){ echo 'selected';} echo " >2º SGT</option>
            						<option value='l3º SGT' "; if ($row[1]=='l3º SGT'){ echo 'selected';} echo " >3º SGT</option>
            						<option value='mCB' "; if ($row[1]=='mCB'){ echo 'selected';} echo " >CB</option>
            						<option value='nSD' "; if ($row[1]=='nSD'){ echo 'selected';} echo " >SD</option>
            						<option value='oSC' "; if ($row[1]=='oSC'){ echo 'selected';} echo " >SC</option>";
          					echo "</select></td></tr>";
	echo "<tr><td align='right'>Nome do Usuário:* </td><td><input type='text' size='60' maxlength='70' name='nome_usuario' value='$row[2]' required></td></tr>";
	echo "<tr><td align='right'>Nome de Guerra:* </td><td><input type='text' size='30' maxlength='30' name='nome_guerra' value='$row[3]' required></td></tr>";

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
 						while ($om = mysql_fetch_array($result, MYSQL_NUM)) {
							if($cmdo!=$om[4]) {
  								echo "<optgroup label=''>";
  								echo "<optgroup label=':. $om[4] .:'>";
  								$cmdo=$om[4];
  							}
  							echo "<option value='".substr($om[5],0,1).$om[0]."' "; if ($om[0]==$row[4]){	echo 'selected'; } echo ">$om[1]</option>";
						}
					echo "</select></td></tr>";

	echo "<tr><td align='right'>Perfil do Usuário:* </td><td><select name='perfil' size='1' onChange='habilita_gerente(this.value)'>";
										if ($_SESSION['perfil']=='ADMINISTRADOR *') {        						
            							echo "<option value='ADMINISTRADOR *' "; if ($row[5]=='ADMINISTRADOR *'){ echo 'selected';} echo ">ADMINISTRADOR *</option>";
            							echo "<option value='CONSULTOR *'"; if ($row[5]=='CONSULTOR *'){ echo 'selected';} echo '>CONSULTOR *</option>';
	            						echo "<option value='ADMINISTRADOR' "; if ($row[5]=='ADMINISTRADOR'){ echo 'selected';} echo ">ADMINISTRADOR</option>";
	            						echo "<option value='CONSULTOR' "; if ($row[5]=='CONSULTOR'){ echo 'selected';} echo ">CONSULTOR</option> "; 
										}           						
            						
										if ($_SESSION['perfil']=='ADMINISTRADOR') {        						
	            						echo "<option value='ADMINISTRADOR' "; if ($row[5]=='ADMINISTRADOR'){ echo 'selected';} echo ">ADMINISTRADOR</option>";
	            						echo "<option value='CONSULTOR' "; if ($row[5]=='CONSULTOR'){ echo 'selected';} echo ">CONSULTOR</option> "; 
										}           						
            						echo "<option value='CMT DA OM' "; if ($row[5]=='CMT DA OM'){ echo 'selected';} echo " >CMT DA OM</option>";
            						echo "<option value='FISCAL DA OM' "; if ($row[5]=='FISCAL DA OM'){ echo 'selected';} echo " >FISCAL DA OM</option>";
            						echo "<option value='ABASTECEDOR' "; if ($row[5]=='ABASTECEDOR'){ echo 'selected';} echo " >ABASTECEDOR</option>";
          					echo "</select></td></tr> ";
 	echo "<tr id='linha' ><td align='right'><input type='checkbox' name='gerente' value='SIM' "; 
 																if(($row[5]!='CMT DA OM') and ($row[5]!='FISCAL DA OM')) { echo "disabled";}
 																if ($row[9]=='SIM') { echo "checked";} 
 																if($row[10]=='-') { echo " style='display:none' ";} 
 																echo "></td>
 					<td> <input type='text' name='gerente1' value='Gerente de Combustível' "; if($row[10]=='-') { echo "style='display:none'";} echo " readonly></td</tr>";

   echo "<tr><td align='right'>Situação do Usuário:* </td><td><select name='situacao' size='1'>";
            						echo "<option value='PRONTO NA OM' "; if ($row[6]=='PRONTO NA OM'){ echo 'selected';} echo ">PRONTO NA OM</option>";
            						echo "<option value='DESLIGADO DA OM' "; if ($row[6]=='DESLIGADO DA OM'){ echo 'selected';} echo ">DESILGADO DA OM</option>";
          					echo "</select></td></tr>"; 

   echo "<!--tr><td align='right'>Login do Usuário:* </td><td><input type='text' size='20' maxlength='15' name='login' value='$row[7]'></td></tr-->";
   echo "<tr><td align='right'>Senha do Usuário: </td><td><input type='password' size='20' maxlength='15' name='senha' value='$row[8]'></td></tr>";
   echo "<input type='hidden' name='senha_ant' value='$row[8]'>";
   echo "<input type='hidden' name='operacao' value='2'>";
   echo "<tr><td></td><td></td></tr>";
   echo "<tr><td align='right'><input type='submit' value= 'Gravar'></td><td><input type='reset' value= '  Cancelar  '>&nbsp;<input type='button' value= 'Retornar' onclick='history.go(-1)'></td></tr>";
   echo "</table>";


echo "</form>";
?>
<script language='JavaScript'>
	$("input[name='gerente1']").css({background:'transparent', border:'none',})
</script>


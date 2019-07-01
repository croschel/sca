<?php
  if(empty($_SESSION['usuario']))
    include "verifica_sessao.php";
?>
<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<script language="JavaScript" type="text/javascript">
function mascara(dt_abast){
              var mdata = '';
              mdata = mdata + dt_abast;
              if (mdata.length == 2){
                  mdata = mdata + '/';
                  document.forms[1].dt_abast.value = mdata;
              }
              if (mdata.length == 5){
                  mdata = mdata + '/';
                  document.forms[1].dt_abast.value = mdata;
              }
              if (mdata.length == 10){
                  return true;
              }
          }
      
$(document).ready(function () {
	$("input[name='qtd_comb']").css({width:'60px',})
	$("#posto").change(function () {
		
		$("input[name='qtd_comb']").attr("max",Math.floor($("option:selected",this).attr("id")));
		$("input[name='qtd_max']").attr("value",Math.floor($("option:selected",this).attr("id"))+' Lts');
		
		if( $("option:selected",this).attr("id") > 0){		
			$("#btconfirmar").attr('disabled',false);		
			$("#btcancelar").attr('disabled',false);				
		}else{
			alert("ATENÇÃO: Saldo Insuficiente para abastecimento!")	
		}
		
	})
	$("input[name='qtd_max']").css({background:'transparent', border:'none', color:'red',})

})

</script>
<?php 
	echo "<center>";
	echo "<br>";
	echo "<font size='5'><b>Abastecimento com Autorização por Vale </font>";
	echo "<hr><br>";
	echo "<table border='0' width='100%' align='left' valign='top'>";
	echo "<form name='frm_autz' method='POST' action='index.php?nomeArquivo=abast_vale.php' >";
	$qry_om="SELECT DISTINCT e.om_codom, o.sigla
				FROM om o, estoque e
				WHERE o.codom = e.om_codom
					AND e.reservatorio_codigo IN (SELECT codigo FROM reservatorio	WHERE om_codom =  '".$_SESSION['codom']."')";
	$result = mysql_query($qry_om);
	echo "<tr>
				<td align='right' width='50%'><b><I>OM Detentora do Compbustível: </I></td>
				<td width='50%'><select name='om'>";
					echo "<option value='' > </option>";
					while ($om = mysql_fetch_array($result, MYSQL_NUM)) {
						echo "<option value='$om[0]' "; if($om[0]==$_POST['om']) { echo "selected";} echo ">$om[1] </option>";
					}
			echo "</select></td></tr>";
	echo "<tr><td align='right'><b><I>Digite o EB da Viatura: </I></td><td><input type='text' size='10' maxlength='10' name='eb' value='".$_POST['eb']."'>&nbsp;<input type='submit' value= 'Localizar'></td></tr>";
	echo "<tr><td COLSPAN='2'><hr></td></tr>";
	echo "</form>";	
	
	echo "<form name='frm_autz_vale' method='POST' action='index.php?nomeArquivo=gravar_abast_vale.php' >";
	   
	/*Seleciona a Viatura*/
	$query = "SELECT o.codom, o.sigla, v.marca, v.modelo, c.nome, v.eb, c.codigo
			FROM om o, viatura v, combustivel c
			WHERE o.codom=v.om_codom
			and c.codigo=v.combustivel_codigo
			and v.eb='".$_POST['eb']."'";
	$result = mysql_query($query,$conexao);
	if ($_POST['eb'] != ''){
		$row = mysql_fetch_row($result);
	}

	echo "<tr><td align='right'><I>OM da Viatura: </I></td><td> <b><i>$row[1]</td></tr>";
	echo "<tr><td align='right'><I>EB da Viatura: </I></td><td><b><i>$row[5]</td></tr>";
	echo "<input type='hidden' name='ebv' value='$row[5]'>";
	echo "<tr><td align='right'><I>Viatura: </I></td><td><i><b>$row[2] - $row[3]</td></tr>";
	echo "<tr><td align='right'><I>Combustível: </I></td><td><i><b>$row[4]</td></tr>";

	/*Seleciona quem autorizou*/
	$qry_autz="SELECT idt, post_grad, nome_guerra
			FROM usuario
			WHERE om_codom='".$_POST['om']."'
				and perfil in ('CMT DA OM','FISCAL DA OM')
			order by post_grad";
		$result = mysql_query($qry_autz);
	echo "<tr><td align='right'><I>Autorizado por: </td><td><select name='mil_autz'>";
				echo "<option value='' > </option>";
			while ($fisc = mysql_fetch_array($result, MYSQL_NUM)) {
				echo "<option value='$fisc[0]' >".substr($fisc[1],1)." $fisc[2] </option>";
			}
			echo "</select></td></tr>";

	/*Seleciona o Reservatório*/
	$query = "SELECT e.reservatorio_codigo, t.tipo_cota, truncate(sum(e.qtd_atual),0), t.cod_tp_cota, e.om_codom
				FROM tipo_cota t, estoque e
				WHERE t.cod_tp_cota=e.cod_tp_cota
					and e.combustivel_codigo = '$row[6]'
					and e.om_codom = '".$_POST['om']."'
					and e.reservatorio_codigo in( select codigo from reservatorio where om_codom='".$_SESSION['codom']."')
				group by e.reservatorio_codigo, e.cod_tp_cota";
	$result = mysql_query($query);
	echo "<tr><td align='right'><I>Tipo de Combustível: </I></td><td><select name='reservatorio' id='posto'>";
			echo "<option  value='' > </option>";
			while ($comb = mysql_fetch_array($result, MYSQL_NUM)) {
				$qrya = "select sum(qtd_autorizada) from abastecimento where reservatorio_codigo='$comb[0]' and cod_tp_cota='$comb[3]' and cota_om='$comb[4]' and qtd_abastecida is null";
				$resa = mysql_query($qrya);
				$qtda = mysql_fetch_row($resa);
				$comb[2] = $comb[2] - $qtda[0];
				echo "<option id='$comb[2]' value='$comb[0],$comb[3],$comb[4]' >$comb[1]</option>";
			}
			echo "</select> <font color='red'><i> Disponibilidade:&nbsp;<input type='text' name='qtd_max' size='10' value='$valor' readonly> </td></tr>";

	echo "<tr><td align='right'>Data da Autorização: </td><td><input type='text' OnKeyUp='mascara(this.value);' size='10' maxlength='10' name='dt_abast' id='dt_ini'><I>'dd/mm/aaaa'</I></td></tr>";
	echo "<tr><td align='right'><I>Quantidade Autorizada: </I></td><td> <input type='text' size='5' maxlength='5' name='qtd_autz'> Lts</td></tr>";
	echo "<tr><td align='right'><I>Quantidade Abastecida: </td><td width='18%'><input type='number' min='1' max='$valor' name='qtd_comb'> Lts</td></tr>";
	echo "<tr><td align='right'><I>Motorista: </I></td><td> <input type='text' size='30' maxlength='30' name='motorista'></td></tr>";
	echo "<tr><td align='right'><I>Odômetro Atual: </I></td><td> <input type='text' size='10' maxlength='10' name='odometro'></td></tr>";
	echo "<tr><td align='right'><I>Missão: </td><td><input type='text' size='30' maxlength='50' name='missao'></td></tr>";
	echo "<tr><td COLSPAN='2'>&nbsp;</td></tr>";
	
	echo "<tr><td align='right'><input type='submit' value= 'Confirmar' id='btconfirmar' disabled>&nbsp;&nbsp;</td><td>&nbsp;&nbsp;<input type='reset' value= '  Cancelar  ' id='btcancelar' disabled></td></tr>	";

	echo "<tr><td COLSPAN='2'><hr></td></tr>";
	echo "</form>";
	echo "</table>";
	echo "<p>";
?>


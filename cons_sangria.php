<?php
  if(empty($_SESSION['usuario']))
    include "verifica_sessao.php";

?>
<script language="JavaScript" type="text/javascript">
function mascara(dt_sangria){
              var mdata = '';
              mdata = mdata + dt_sangria;
              if (mdata.length == 2){
                  mdata = mdata + '/';
                  document.forms[0].dt_sangria.value = mdata;
              }
              if (mdata.length == 5){
                  mdata = mdata + '/';
                  document.forms[0].dt_sangria.value = mdata;
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
	})
	$("input[name='qtd_max']").css({background:'transparent', border:'none', color:'red',})

})
</script>

<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>

<?php
	if($_GET['ano']=='') { $_GET['ano']=date('Y');}
	if($_GET['mes']=='') { $_GET['mes']=date('m');}
	if($_GET['mes']>'12') { $_GET['mes']='01'; $_GET['ano']=$_GET['ano']+1;}
	if($_GET['mes']<'1') { $_GET['mes']='12'; $_GET['ano']=$_GET['ano']-1;}

 	switch($_GET['mes']) {
		case 1: $mes_a = 'Dezembro / '.($_GET['ano']-1);$mes = 'Janiero'; $p_mes = 'Fevereiro / '.$_GET['ano']; break;
		case 2: $mes_a = 'Janiero / '.$_GET['ano'];$mes = 'Fevereiro'; $p_mes = 'Março / '.$_GET['ano']; break;
		case 3: $mes_a = 'Fevereiro / '.$_GET['ano'];$mes = 'Março'; $p_mes = 'Abril / '.$_GET['ano']; break;
		case 4: $mes_a = 'Março / '.$_GET['ano'];$mes = 'Abril'; $p_mes = 'Maio / '.$_GET['ano']; break;
		case 5: $mes_a = 'Abril / '.$_GET['ano'];$mes = 'Maio'; $p_mes = 'Junho / '.$_GET['ano']; break;
		case 6: $mes_a = 'Maio / '.$_GET['ano'];$mes = 'Junho'; $p_mes = 'Julho / '.$_GET['ano']; break;
		case 7: $mes_a = 'Junho / '.$_GET['ano'];$mes = 'Julho'; $p_mes = 'Agosto / '.$_GET['ano']; break;
		case 8: $mes_a = 'Julho / '.$_GET['ano'];$mes = 'Agosto'; $p_mes = 'Setembro / '.$_GET['ano']; break;
		case 9: $mes_a = 'Agosto / '.$_GET['ano'];$mes = 'Setembro'; $p_mes = 'Outubro / '.$_GET['ano']; break;
		case 10: $mes_a = 'Setembro / '.$_GET['ano'];$mes = 'Outubro'; $p_mes = 'Novembro / '.$_GET['ano']; break;
		case 11: $mes_a = 'Outubro / '.$_GET['ano'];$mes = 'Novembro'; $p_mes = 'Dezembro / '.$_GET['ano']; break;
		case 12: $mes_a = 'Novembro / '.$_GET['ano'];$mes = 'Dezembro'; $p_mes = 'Janiero / '.($_GET['ano']+1); break;
	}
	echo "<table border='0' width='80%' align='left' valign='top'>";
	echo "<tr><td colspan='4' align='center'>&nbsp;</td></tr>";
	echo "<tr><td colspan='4' align='center'><font size='5'><b>Combustível Retirado por Sangria</td></tr>";
	echo "<tr bgcolor='$cor1'><font size='4'><td colspan='4' align='center'>&nbsp;</td></tr>";
	echo "<tr bgcolor='$cor3' align='center'><td colspan='4'><i>
					<a href='index.php?nomeArquivo=cons_sangria.php&oc=".$_GET['oc']."&ano=".$_GET['ano']."&mes=".($_GET['mes']-1)."' style='text-decoration:none' title='Mês Anterior - $mes_a'>&nbsp;Anterior&nbsp;</a>&nbsp;
					<font size='4' color='blue'><b>- $mes / ".$_GET[ano]."</font></b> - 
					<a href='index.php?nomeArquivo=cons_sangria.php&oc=".$_GET['oc']."&ano=".$_GET['ano']."&mes=".($_GET['mes']+1)."' style='text-decoration:none' title='Próximo Mês - $p_mes'>&nbsp; Próximo&nbsp;</a></td></tr>";	   

  	/*/Seleciona a relação de Reservatorios*/
	if ((($_SESSION['perfil']=='FISCAL DA OM') and ($_SESSION['gerente']=='SIM')) or ($_SESSION['perfil']=='ABASTECEDOR')){
		$qry = "select o.codom, r.codigo, o.sigla, c.nome, o.subordinacao
					from om o, reservatorio r, combustivel c
					where o.codom=r.om_codom
						and c.codigo=r.combustivel_codigo
					and o.codom='".$_SESSION['codom']."'
					order by 5,r.codigo";
	} elseif (($_SESSION['perfil']=='FISCAL DA OM') and ($_SESSION['gerente']=='NãO')) {
		$qry = "select d.om_codom, d.reservatorio_codigo, o.sigla, c.nome, o.subordinacao
					from om o, reservatorio r, disponibilidade d, combustivel c
					where o.codom=r.om_codom
						and c.codigo=r.combustivel_codigo
					and r.codigo=d.reservatorio_codigo
					and d.om_codom='".$_SESSION['codom']."'
					order by 5,d.reservatorio_codigo";
	}	elseif (($_SESSION['perfil']=='ADMINISTRADOR') or ($_SESSION['perfil']=='CONSULTOR')) {
		$qry = "select o.codom, r.codigo, o.sigla, c.nome, o.subordinacao
					from om o, reservatorio r, combustivel c
					where o.codom=r.om_codom
						and c.codigo=r.combustivel_codigo
					and o.oc='".$_SESSION['codom']."'
					order by 5,r.codigo";
	} elseif (($_SESSION['perfil']=='ADMINISTRADOR *') or ($_SESSION['perfil']=='CONSULTOR *')) {
		$qry = "select o.codom, r.codigo, o.sigla, c.nome, o.subordinacao
					from om o, reservatorio r, combustivel c
					where o.codom=r.om_codom
						and c.codigo=r.combustivel_codigo
					order by 5,r.codigo";
	}

	$resultado = mysql_query($qry);
	while ($row = mysql_fetch_array($resultado, MYSQL_NUM)) {
		$total=0;
		if($row[4]!=$cmdo) {
			$qryCmdo="select sigla from om where codom='$row[4]'";
			$resCmdo = mysql_query($qryCmdo);
			$Cmdo = mysql_fetch_row($resCmdo);
			echo "<tr bgcolor='$cor1'><td colspan='5'><br>&nbsp;&nbsp;<coluna>Comando: <font color='$cor_submenu'> &nbsp;$Cmdo[0] <p></td></tr>";
			$cmdo=$row[4];
		}
		echo "<tr bgcolor='$cor2'><td colspan='2' width='50%'>&nbsp;&nbsp;<coluna>Reservatório: <font color='$cor_submenu'> &nbsp;&nbsp;Nº $row[1] ($row[2])</td><td width='25%' align='center'><coluna>Data</td><td width='25%' align='center'><coluna>Quantidade</td></tr>";
		$query='';
		if (($_SESSION['perfil']=='FISCAL DA OM') and ($_SESSION['gerente']=='NÃO')){
			$query="select s.data_sangria, sum(s.qtd_retirada)
					from estoque e, sangria s
					where e.nr_repasse=s.nr_repasse
						and e.om_codom=s.om_codom
						and s.data_sangria between '".$_GET['ano']."-".$_GET['mes']."-01' and '".$_GET['ano']."-".$_GET['mes']."-31'
						and e.reservatorio_codigo='".$row[1]."' 
						and e.om_codom='".$_SESSION['codom']."' 
					group by s.data_sangria
					order by s.data_sangria";
		} else {
			$query="select s.data_sangria, sum(s.qtd_retirada)
					from estoque e, sangria s
					where e.nr_repasse=s.nr_repasse
						and e.om_codom=s.om_codom
						and s.data_sangria between '".$_GET['ano']."-".$_GET['mes']."-01' and '".$_GET['ano']."-".$_GET['mes']."-31'
						and e.reservatorio_codigo='".$row[1]."' 
					group by s.data_sangria
					order by s.data_sangria";			
		}
//		echo $query;
		$result = mysql_query($query);
		while ($qtd = mysql_fetch_array($result, MYSQL_NUM)) {
			$dt_sang=substr($qtd[0],8,2)."/".substr($qtd[0],5,2)."/".substr($qtd[0],0,4);
			echo "<tr><td colspan='2'></td><td align='center' bgcolor='$cor3'><lin><a href='index.php?nomeArquivo=cons_sangria_data.php&posto=$row[2]&dt_reg=$qtd[0]&res=$row[1]&comb=$row[3]' style='text-decoration:none'>$dt_sang</a></td><td align='right' bgcolor='$cor3'><lin>".number_format($qtd[1],0,',','.')." Lts &nbsp;</td></tr>";
			$total=$total+$qtd[1];
		}
		echo "<tr ><td colspan='2'></td><td align='center' bgcolor='$cor3'><coluna>Total</td><td align='right' bgcolor='$cor3'><coluna>".number_format($total,0,',','.')." Lts &nbsp;</td></tr>";
		echo "<tr><td colspan='4'>&nbsp;</td></tr>";
	}
	
	echo "</tr>";
	
	if (($_SESSION['perfil']=='FISCAL DA OM') and ($_SESSION['gerente']=='SIM')){
		echo "<tr><td colspan=4 align='center'><hr></td></tr>
		<tr><td colspan=4 align='center'><font size=5><b>Registro de Nova Sangria</td></tr>
		<form name='frm_sangria' method='POST' action='index.php?nomeArquivo=gravar_sangria.php' >";
		/*/Seleciona o reservatório*/
		$qry_res="SELECT r.codigo, o.sigla, sum(e.qtd_atual)
			FROM reservatorio r, om o, estoque e
			WHERE o.codom = r.om_codom
				and r.codigo = e.reservatorio_codigo
				and o.codom='".$_SESSION['codom']."'
				and e.qtd_atual > 0
			group by r.codigo
			order by r.codigo";
		$result = mysql_query($qry_res);
		echo "<tr><td align='right'><b><i>Reservatório: </td><td colspan=3 ><select name='reservatorio' id='posto'>";
			echo "<option value='' > </option>";
		while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
			echo "<option  id='$row[2]' value='$row[0],$row[2]' >Nº $row[0] - $row[1]</option>";
		}
			 echo "</select></td></tr>";
		echo "<tr><td align='right'><b><I>Quantidade Atual: </td><td colspan='3'> <input type='text' name='qtd_max' size='10' value='$valor' readonly></td></tr>
		<tr><td align='right'><b><I>Data da Sangria: </td><td colspan='3'><input type='text' id='dt_ini' OnKeyUp='mascara(this.value);' size='10' maxlength='10' name='dt_sangria'><I> dd/mm/aaaa</I></td></tr>
		<tr><td align='right'><b><I>Quantidade Retirada: </td><td colspan='3'> <input type='text' size='5' maxlength='3' name='qtd_retirada'><I> Litros</td></tr>
		<tr><td COLSPAN='4'>&nbsp;</td></tr>
		<tr><td align='right'><input type='submit' value= 'Confirmar'>&nbsp;&nbsp;</td><td colspan=3>&nbsp;&nbsp;<input type='reset' value= '  Cancelar  '></td></tr>	
		<tr><td COLSPAN='4'><hr></td></tr>";
		echo "</form>";
	}
	echo "</table>";
?>



<?php
	if(empty($_SESSION['usuario']))
		include 'verifica_sessao.php';
        //Conexão ao Banco de Dados
    	include 'conexao.php';
?>
<head>
	<META http-equiv='Content-Type' content='text/html; charset=utf-8' />
</head>
<Script Language="JavaScript">
function getOM(what) {
      var cod_om = what.value;
      document.location=('index.php?nomeArquivo=auditoria.php&codom=' + cod_om );
}

function getUsuario(what) {  
      var idt = what.value;
      document.location=('index.php?nomeArquivo=auditoria.php&codom=' + idt);
}

</Script>

<font size=5><br><b>Auditoria de Atividades dos Usuários</b><p></font>

<?php
 
echo "<table border='0' width='100%'>";
echo "<tr><td>";   
	if (($_SESSION['perfil']=='ADMINISTRADOR *') or ($_SESSION['perfil']=='	CONSULTOR *')){
  		$query="select * from om 
  					where tipo in('OC','Integrante')
					order by sigla";
	} elseif(($_SESSION['perfil']=='ADMINISTRADOR') or ($_SESSION['perfil']=='CONSULTOR')) {
		$query="select * from om 
					where oc='".$_SESSION['codom']."'
					order by sigla";
	} else {
		$query="select * from om 
					where codom='".$_SESSION['codom']."'
					order by sigla";
	}

   $result = mysql_query($query);
	if(!$_GET['ano']){$_GET['ano']=date(Y);}

	echo "<table border='0' width='100%' align='left' valign='top'>";
	echo "<tr><td colspan='5'><hr></td></tr>";
	echo "<tr><td align='right' width='150'><coluna>Escolha a OM: &nbsp;</td><td width='50'> <select name='om' onChange='getOM(this);'>";
						if(mysql_num_rows($result)>1) {
     						echo "<option value='TODAS'>TODAS</option>";
     					} else {
     						$_GET['codom']=$_SESSION['codom'];
						}
						while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
							echo "<option value='$row[0]'"; if(str_pad($_GET['codom'], 6, 0, STR_PAD_LEFT)==$row[0]){echo "selected";} echo " >$row[1]</option>";
						}
						echo "</select></td>";

			if(($_GET['codom']!='TODAS') and ($_GET['codom']!='')) {
				$query1="select idt, substr(post_grad,2), nome_guerra, om_codom
						from usuario 
						where om_codom='".str_pad($_GET['codom'], 6, 0, STR_PAD_LEFT)."'
								and situacao='PRONTO NA OM'
						order by post_grad ";
				   $result1 = mysql_query($query1);
			echo "<td align='right' width='200'><coluna>Escolha o Usuário: &nbsp;</td><td width='50'> <select name='usuario' onChange='getUsuario(this);'>";
     					echo "<option value='".str_pad($_GET['codom'], 6, 0, STR_PAD_LEFT)."&idt=TODOS'>TODOS</option>";
						while ($row1 = mysql_fetch_array($result1, MYSQL_NUM)) {
							echo "<option value='$row1[3]&idt=$row1[0]'"; if($_GET['idt']==$row1[0]){echo "selected";} echo " >$row1[1] $row1[2]</option>";
						}
				echo "</select>";
			}
						
		echo "</td><td>&nbsp;</td></tr>";	
	echo "<tr><td colspan='5'><hr></td></tr>";
	echo "</table>";
	echo "</td></tr>";	
	echo "<tr><td>";
	if($_GET['inicio']=='') {
		$i=0;
	}else {
		$i=$_GET['inicio'];
	}
	/*Seleciona a quantidade de linhas de auditoria*/
	$query="select o.sigla, u.post_grad, u.nome_guerra, a.dt_atividade, a.atividade
				from om o, usuario u, auditoria a
				where o.codom=u.om_codom
				and u.idt=a.usuario_idt ";

	if(($_GET['codom']=='TODAS') or ($_GET['codom']=='')) {
		if(($_SESSION['perfil']=='ADMINISTRADOR') or ($_SESSION['perfil']=='CONSULTOR')) {
			$query=$query."and o.oc = '".$_SESSION['codom']."' ";
		} elseif(($_SESSION['perfil']=='FISCAL DA OM') or ($_SESSION['perfil']=='CMT DA OM')) {
			$query=$query."and o.codom='".$_SESSION['codom']."' ";
		}
	} else {
		if(($_GET['idt']!='TODOS') and ($_GET['idt']!='')) {
			$query=$query."and u.idt = '".$_GET['idt']."' ";
		} else {
			$query=$query."and u.om_codom='".$_GET['codom']."' ";
		}
	}

	$query=$query."order by 4 desc
						Limit ".($i*100).",100";


	$resultado = mysql_query($query);
	$num_linhas = mysql_num_rows($resultado);
/*	$num_pag = floor($num_linhas / 100);*/
/*	$resto = $num_linhas[0] % 100;*/


	echo "<table border='0' width='100%' align='center' valign='top'>";
	echo "<tr >";
		echo "<td COLSPAN=4 > <b><i>Página: </b></i>";
		for($j=1; $j<=$i; $j++) {
			if($i-$j<=9) {
				echo "<i><lin><a href='index.php?nomeArquivo=auditoria.php&codom=".$_GET['codom']."&idt=".$_GET['idt']."&inicio=".($j-1)."' style='text-decoration:none'>".($j)."</a> </i>";
			} elseif($i-$j==10) {
				echo "<i><lin><a href='index.php?nomeArquivo=auditoria.php&codom=".$_GET['codom']."&idt=".$_GET['idt']."&inicio=".($j)."' style='text-decoration:none'>Anterior</a>&nbsp;...&nbsp;</i>";
			}
		}
		echo "<b>".($i+1)."</b> ";
		for($j=1; $j<=10; $j++) {
			echo "<i><lin><a href='index.php?nomeArquivo=auditoria.php&codom=".$_GET['codom']."&idt=".$_GET['idt']."&inicio=".($i+$j)."' style='text-decoration:none'>".($i+1+$j)."</a> </i>";
		}
		echo "<i><lin>...&nbsp;<a href='index.php?nomeArquivo=auditoria.php&codom=".$_GET['codom']."&idt=".$_GET['idt']."&inicio=".($i+$j)."' style='text-decoration:none'>Próxima</a> </i>";
		echo "</td>";
	echo "</tr>";
	echo "<tr align='center' bgcolor='$cor1'>";
		echo "<td width='8%'><coluna>Data</td><td width='7%'><coluna>Hora</td><td width='20%'><coluna>Usuário</td><td width='15%'><coluna>OM</td><td width='50%'><coluna>Atividade</td>";
	echo "</tr>";
	echo "<tr bgcolor='#eeeeee'>";
   //Seleciona os registros conforme o perfil

	$result = mysql_query($query);	
   while ($row = mysql_fetch_array($resultado, MYSQL_NUM)) {
		/*Formata a data*/
		$ano=substr($row[3],0,4);
                $mes=substr($row[3],5,2);
                $dia=substr($row[3],8,2);
		$hora=substr($row[3],11,8);
                $data = $dia.'/'.$mes.'/'.$ano;
      $val_usuario = substr($row[1],1)." ".$row[2];
     
		echo "<tr bgcolor='$cor3' class='marcar_linha'><td align='center'><lin>$data</td><td align='center'><lin>$hora</td><td><lin>$val_usuario</lin></td><td align='center'><lin>$row[0]</td><td><lin>$row[4]</td></tr>";
    	}
	echo "</tr>";
	echo "<tr align='right' bgcolor='#6CA6CD'>";
	echo "</td></tr>";
	echo "</table>";
echo "</td></tr>";
echo "</table>";
?>

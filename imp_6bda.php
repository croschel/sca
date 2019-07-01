
<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>



<?php    
  //Conexão ao Banco de Dados
  include "conexao.php";
  //Seleciona a relação de Unidades

//IMPORTA��O DA TABELA OM
/*
echo 	$query = "SELECT * FROM `om_6` where `codom` not in (select codom from om)";
   $result = mysql_query($query);
	$nr=0;
echo 	"<br>".$qryi = "insert into om values"; 
   while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
   	if ($row[4]==''){
			$qryi = "('$row[0]', '$row[1]', '$row[2]','$row[3]',null ),";
		} else {
			$qryi = "('$row[0]', '$row[1]', '$row[2]','$row[3]','$row[4]')";
		}		
		echo "<br>".$qryi;
		$rc = mysql_query($qryi);
		$nr++;
 	}
*/

//Importa��o de viaturas 
/*
 	$query = "desc `viatura`";
   $result = mysql_query($query);
    while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
    	echo "$row[0]' --- '$row[1]' --- '$row[2]' --- '$row[3]' --- '$row[4]'<br>";
    }

echo 	"<br>".$query = "SELECT * FROM `viatura_6` where `eb` not in (select eb from viatura)";
   $result = mysql_query($query);
	$nr=0;
   while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$qryi = "insert into viatura values('$row[0]', '$row[1]', '$row[2]', '$row[3]', '$row[4]', '$row[5]', '$row[6]', '$row[7]', 'Em Carga' )";
		$rc = mysql_query($qryi);
		if($rc){
			$nr++;			
		} else {
			echo "<br>".mysql_error();
			echo "<br>".$qryi."<br>";
			$nr1++;
		}
			
 	}
 	echo "<br>Total $nr de viaturas importadas";
 	echo "<br>Total $nr1 de viaturas com erro";
 */
 
//Importa��o de Usu�rios
/*
 	$query = "desc `usuario`";
   $result = mysql_query($query);
    while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
    	echo "$row[0]' --- '$row[1]' --- '$row[2]' --- '$row[3]' --- '$row[4]'<br>";
    }

echo 	"<br>".$query = "SELECT * FROM `usuario_6` where `idt` not in (select idt from usuario)";
   $result = mysql_query($query);
	$nr=0;
   while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$qryi = "insert into usuario values('$row[0]', '$row[1]', '$row[2]', '$row[3]', '$row[4]', '$row[5]', '$row[6]', '$row[7]', '$row[8]', '$row[9]' )";
		$rc = mysql_query($qryi);
		if($rc){
			$nr++;			
		} else {
			echo "<br>".mysql_error();
			echo "<br>".$qryi."<br>";
			$nr1++;
		}
			
 	}
 	echo "<br>Total $nr de usuarios importados";
 	echo "<br>Total $nr1 de usuarios com erro";
*/

//Importa��o de Recebimento
/*
 	$query = "desc `recebimento`";
   $result = mysql_query($query);
    while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
    	echo "$row[0]' --- '$row[1]' --- '$row[2]' --- '$row[3]' --- '$row[4]'<br>";
    }
   $query = "SELECT * FROM `recebimento_6`";
   $result = mysql_query($query);
	$nr=0;
   while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$qryi = "insert into recebimento values('$row[0]', '$row[1]', '$row[2]', '$row[3]', '$row[4]', '$row[5]', '$row[6]', '$row[7]' )";
		$rc = mysql_query($qryi);
		if($rc){
			$nr++;			
		} else {
			echo "<br>".mysql_error();
			echo "<br>".$qryi."<br>";
			$nr1++;
		}
			
 	}
 	echo "<br>Total: $nr de recebimentos importados";
 	echo "<br>Total: $nr1 de recebimentos com erro";
 	
 	
 */

//Importa��o de Cotas
/*
 	$query = "desc `cotas_6`";
   $result = mysql_query($query);
    while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
    	echo "$row[0]' --- '$row[1]' --- '$row[2]' --- '$row[3]' --- '$row[4]'<br>";
    }
   $query = "SELECT * FROM `cotas_6`";
   $result = mysql_query($query);
	$nr=0;
   while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$qryi = "insert into cotas values('$row[0]', '$row[1]', '$row[2]', '$row[3]', '$row[4]', '$row[5]', '$row[6]', '$row[7]', '$row[8]' )";
		$rc = mysql_query($qryi);
		if($rc){
			$nr++;			
		} else {
			echo "<br>".mysql_error();
			echo "<br>".$qryi."<br>";
			$nr1++;
		}
			
 	}
 	echo "<br>Total: $nr de cotas importados";
 	echo "<br>Total: $nr1 de cotas com erro";

 */

//Importa��o de Disponibilidade
/*
 	$query = "desc `disponibilidade_6`";
   $result = mysql_query($query);
    while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
    	echo "$row[0]' --- '$row[1]' --- '$row[2]' --- '$row[3]' --- '$row[4]'<br>";
    }
   $query = "SELECT * FROM `disponibilidade_6`";
   $result = mysql_query($query);
	$nr=0;
   while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$qryi = "insert into disponibilidade values('$row[0]', '$row[1]', '$row[2]' )";
		$rc = mysql_query($qryi);
		if($rc){
			$nr++;			
		} else {
			echo "<br>".mysql_error();
			echo "<br>".$qryi."<br>";
			$nr1++;
		}
			
 	}
 	echo "<br>Total: $nr de disponibilidade importados";
 	echo "<br>Total: $nr1 de disponibilidade com erro";

*/

//Importa��o de Abastecimentos
/*
 	$query = "desc `abastecimento_6`";
   $result = mysql_query($query);
    while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
    	echo "$row[0]' --- '$row[1]' --- '$row[2]' --- '$row[3]' --- '$row[4]'<br>";
    }
   $query = "SELECT * FROM `abastecimento_6`";
   $result = mysql_query($query);
	$nr=0;
   while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$qryi = "insert into abastecimento values(\"$row[0]\", \"$row[1]\", \"$row[2]\", \"$row[3]\", \"$row[4]\", \"$row[5]\", \"$row[6]\", \"$row[7]\", \"$row[8]\", \"$row[9]\", \"$row[10]\", \"$row[11]\", \"$row[12]\" )";
		$rc = mysql_query($qryi);
		if($rc){
			$nr++;			
		} else {
			echo "<br>".mysql_error();
			echo "<br>".$qryi."<br>";
			$nr1++;
		}
			
 	}
 	echo "<br>Total: $nr de abastecimentos importados";
 	echo "<br>Total: $nr1 de cotas com erro";
*/

?>

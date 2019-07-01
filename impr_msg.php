<?php
include ('./fpdf/fpdf.php');
$pdf = new FPDF('P','cm','A4');

$pdf -> SetMargins(2, 1, 2);
$pdf->AddPage();

$pdf->SetFont('Arial', '', 8);
$pdf->Image("./imagens/brasao.png", 9.5,2,2);
$pdf->SetXY(2,4);
$pdf->Cell(0, 0.3,'Ministério da Defesa', 0, 1, 'C');
$pdf->Cell(0, 0.3,'Exército Brasileiro', 0, 1, 'C');
$pdf->Cell(0, 0.3,'Comando Logístico', 0, 1, 'C');
$pdf->Cell(0, 0.3,'Diretoria de Material', 0, 1, 'C');
$pdf->SetFont('Arial', 'U', 8);
$pdf->Cell(0, 0.3,'Escritório de Ligação do Projeto Leopard', 0, 1, 'C');
$pdf->Cell(0, 0.5,'', 0, 1, 'C');

//Conexãoo ao Banco de Dados
include("conexao.php");

$query1 = "select u.post_grad, u.nome_guerra, m.data_hora, m.remetente_idt, m.assunto, m.texto 
				from usuario u, mensagem m
				where u.idt=m.remetente_idt
					and m.data_hora='".$_GET['dt_hr']."' 
					and m.remetente_idt='".$_GET['idt']."'";

$sql1 = mysql_query($query1, $conexao);

$pdf->SetFont('Arial', 'BI', 12);
$pdf->Cell(0, 0.5, 'Impressão de Mensagem',0,1,'C');

$substituir = array("<br>", "&quot;");
$substituto = array("\n", '"');

$pdf->Cell(0, 0.5,'', 0, 1, 'C');
$pdf->MultiCell(0, 0.05, '',0,1,1,'C');

$pdf->SetFont('Arial', 'BI', 10);
$pdf->Cell(1.7, 0.5, 'Assunto: ',0,0,'L');

$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(0, 0.5, str_replace($substituir,$substituto,mysql_result($sql1, 0, "assunto")),0,1,'L');

$pdf->SetFont('Arial', 'BI', 10);
$pdf->Cell(2.1, 0.5, 'Remetente: ',0,0,'L');
$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(0, 0.5, mysql_result($sql1, 0, "post_grad")." - ".mysql_result($sql1, 0, "nome_guerra"),0,1,'L');

$pdf->SetFont('Arial', 'BI', 10);
$pdf->Cell(2.6, 0.5, 'Destinatário(s): ',0,0,'L');
$pdf->SetFont('Arial', 'I', 10);


//Seleciona os desinatarios e a hora de leitura
$query2 = "select u.post_grad, u.nome_guerra, om.sigla, d.leitura
			from om, usuario u, destinatario d 
			where om.codom=u.om_codom
			and u.idt=d.destinatario_idt
			and d.data_hora='".$_GET['dt_hr']."' 
			and d.remetente_idt='".$_GET['idt']."' ";

$sql2 = mysql_query($query2, $conexao);
$nl = mysql_num_rows($sql2);

//EXIBE OS REGISTROS
for($i=0; $i<$nl; $i++) {
	if($i>0){	
		$pdf->Cell(2.6, 0.5, ' ',0,0,'L');
	}
	if(mysql_result($sql2, $i, "leitura")>''){	
		$pdf->Cell(0, 0.5, " - ".mysql_result($sql2, $i, "post_grad")." ". mysql_result($sql2, $i, "nome_guerra")." do(a) ". mysql_result($sql2, $i, "sigla")." - Leu em: ". substr(mysql_result($sql2, $i, "leitura"),8,2)."/". substr(mysql_result($sql2, $i, "leitura"),5,2)."/". substr(mysql_result($sql2, $i, "leitura"),0,4)." às ". substr(mysql_result($sql2, $i, "leitura"),11,5)." hs", 0, 1, 'L');
	} else {
		$pdf->Cell(0, 0.5, " - ".mysql_result($sql2, $i, "post_grad")." ". mysql_result($sql2, $i, "nome_guerra")." do(a) ". mysql_result($sql2, $i, "sigla")." - Ainda não leu", 0, 1, 'L');
	}
}

$pdf->Cell(0, 0.5, ' ',0,1,'L');
$pdf->MultiCell(0, 0.05, '',0,1,1,'C');

$pdf->SetFont('Arial', 'BI', 10);
$pdf->Cell(1.3, 0.5, 'Texto: ',0,0,'L');
$pdf->SetFont('Arial', 'I', 10);
$pdf->MultiCell(0, 0.5, str_replace($substituir,$substituto,mysql_result($sql1, 0, "texto")),0,1,0,'L');

$pdf->Cell(0, 0.5, ' ',0,1,'L');
$pdf->MultiCell(0, 0.05, '',0,1,1,'C');
$pdf->SetFont('Arial', 'I', 6);
$pdf->Cell(1.6, 0.4, 'Data e Hora: '.date('d/m/Y H:i:s').' hs.',0,1,'L');


$pdf->Output();
?>





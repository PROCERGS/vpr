<?PHP
$db = mysql_connect( "seplag_vpr.mysql-teste.procergs.reders" , "t_seplag_vpr_si" , "vprs1!" );
mysql_select_db( "seplag_vpr" , $db );
$corede = "select id_regiao from regiao order by id_regiao";
$selCorede = mysql_query( $corede , $db );
while( $regCor = mysql_fetch_row( $selCorede ) ) {
	$id_corede = $regCor[0];
	echo "Corede: ".$id_corede."<br>";
		$dir = $_SERVER["DOCUMENT_ROOT"]."app/eleitores/";
		$nm_arq = $dir.$id_corede."cedula2013.txt";
		$arquivo  = fopen( $nm_arq , "w+" );
		if (!$arquivo)
  		{
    		echo "<p>O arquivo ".$nm_arq." nao pode ser aberto</p>";
    		exit;
  		}
		$cedula = "SELECT id_area_tematica, nm_demanda, vlr_demanda, cod_projeto FROM cedula WHERE id_regiao = $id_corede AND id_votacao = 2 order by cod_projeto";
		$selCel = mysql_query( $cedula , $db );
		while( $regCel = mysql_fetch_row( $selCel ) ) {
			$id_area_tematica = $regCel[0];
			if ($id_area_tematica)  {
				$area = "select nm_area_tematica from area_tematica where id_area_tematica = $id_area_tematica";
				$selArea = mysql_query( $area , $db );
				if ($regArea = mysql_fetch_row( $selArea )) {
					$nm_area = $regArea[0]." - ";
				}
			} else {
				$nm_area = "";
			}
			$nm_demanda = $regCel[1];
			$vlr_demanda = $regCel[2];
			$cod_projeto = $regCel[3];

    		$reg = "INSERT INTO POP_DESCRICAO_CEDULA "
				 . "values (pop_seq_cod_desc_cedula.nextval, "
				 . "'".$nm_area.$nm_demanda;
			if (($vlr_demanda) && ($vlr_demanda != "0.00")) {
				$reg .= " Valor: R$ ".number_format($vlr_demanda,2,',','.');
			} 
			$reg .= "', ".$cod_projeto.", ".$id_corede.", 2013, sysdate,  44396, null, null);". "\r\n";
			//$limpar = fputs( $abrir , $reg . "\r\n" );
			fwrite($arquivo, $reg);
		} 		
		$enquete = "SELECT pq.question, po.option, po.cod_pop FROM poll_option po, poll_question pq, poll p WHERE po.poll_question_id = pq.id AND poll_id = p.id AND p.votacao_id = 2 order by po.cod_pop";
		$selEnq = mysql_query( $enquete , $db );
		while( $regEnq = mysql_fetch_row( $selEnq ) ) {
			$nm_area = $regEnq[0]." - ";
			$nm_demanda = $regEnq[1];
			$cod_projeto = $regEnq[2];

    		$reg = "INSERT INTO POP_DESCRICAO_CEDULA "
				 . "values (pop_seq_cod_desc_cedula.nextval, "
				 . "'".$nm_area.$nm_demanda
			     . "', ".$cod_projeto.", ".$id_corede.", 2013, sysdate,  44396, null, null);". "\r\n";
			//$limpar = fputs( $abrir , $reg . "\r\n" );
			fwrite($arquivo, $reg);
		} //os comandos abaixo executaram dentro do loop, mas acho que teria que ser fora porque gerou muitos duplicados
			$reg = "INSERT INTO  POP_DEMANDA "
				. "select  pop_seq_cod_desc_cedula.nextval, "
        		. "TXT_DESC_CEDULA, "
        		. "9913, 'Unidade', 0, null, null, 1, 2013, null, null, "
        		. "1, null, null, COD_COREDE * 100 + NRO_ORDEM "
  				. "from  POP_DESCRICAO_CEDULA "
  				. "where nro_ano_corrente = 2013 and COD_COREDE = " . $id_corede . "; \r\n";
			fwrite($arquivo, $reg);
			$reg = "INSERT INTO  POP_DEMANDA_PLEITO "
				. "select  pop_seq_cod_demanda_pleito.nextval, "
        		. "de.cod_demanda, 'Estadual', dc.cod_desc_cedula, 1, dc.cod_corede, 326, 2013, 0, sysdate, 2, null, null, 13, 0 "
  				. "from  pop_descricao_cedula dc inner join "
        		. "pop_demanda de on(de.nro_controle_temp = dc.cod_corede * 100 + nro_ordem) "
  				. "where dc.nro_ano_corrente = 2013 and dc.cod_corede = ".$id_corede." and de.nro_controle_temp = dc.cod_corede * 100 + nro_ordem and de.nro_ano_corrente = 2013; \r\n";
			fwrite($arquivo, $reg);
		//}
		$reg = "INSERT INTO  POP_ARQUIVO_COREDE "
				. "select pop_seq_cod_arquivo.nextval,'Processo espec 2013', ".$id_corede.", sysdate, 999999, 2013, sysdate, 999999, 1, NULL FROM pop_coredes where nro_int_corede = ".$id_corede."; \r\n";
		fwrite($arquivo, $reg);
		fclose($arquivo);
}
?>
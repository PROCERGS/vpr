<?php require VIEWS_PATH . 'main.php'; ?>
<?php
startblock('title');
echo self::getTitle();
endblock();
?>
<?php startblock('main'); ?>
<div id="conteiner">
	<div id="cabecalho">
		<div id="logotipo">
			<h1>Sistema Estadual de Participação Popular e Cidadã</h1>
			<img src="../images/logotipo_internas.png" alt="Votação de Prioridades - Orçamento 2013" width="265" height="104" border="0" />
		</div>
		<div id="brasaoEstadoBG">
            <a href="http://www.estado.rs.gov.br/" target="_blank"><img src="../images/logoGoverno.png" alt="Logotipo do Governo" title="Governo do Estado"  border="0"/></a>
		</div>
	</div>
	<div id="identificacao">    
		<h2>Votação de Prioridades - Orçamento 2013</h2>
		<!--		<b>Airton Jordani Jardim Filho</b><br />Região Central-->
	</div>
	<div id="conteudo">
		<div id="colunaA"><br />

			<h2>Instruções</h2>
			<p>Selecione até 4 (quatro) opções da lista abaixo.<br />Clique na proposta para visualizar o seu detalhamento.</p>
			<hr />
			<?php foreach ($grupos as $grupo) { ?>
				<h3><?php echo $grupo->getNmGrupoDemanda(); ?></h3>
				<ul>
					<?php
					$last_area_id = 0;
					foreach ($options[$grupo->getIdGrupoDemanda()] as $option) {
						$html_id = 'proposal' . $option->getIdCedula();
						if ($last_area_id != $option->getIdAreaTematica()) {
							$last_area_id = $option->getIdAreaTematica();
							?>
							<h4><?php echo $areas[$last_area_id]->getNmAreaTematica(); ?></h4>
						<?php } ?>
						<li><input type="checkbox" disabled="" id="<?php echo $html_id; ?>" />
							<label for="<?php echo $html_id; ?>"><strong><?php echo $option->getCodProjeto(); ?></strong> - <?php echo $option->getNmDemanda(); ?></label>
						</li>
					<?php } ?>
					<hr />
				<?php } ?>
			</ul>
		</div>
	</div>
	<div id="rodape">
		<ul>
			<li><a href="<?= $html->url(array('controller' => 'Locais', 'action' => 'comoVotar')) ?>">Como Votar</a></li>
			<li><a href="http://www.participa.rs.gov.br/" target="_blank">Portal da Participação</a></li>
			<li><a href="http://www.procergs.rs.gov.br/" target="_blank">PROCERGS</a></li>
			<li style="border: none"><a href="http://www.estado.rs.gov.br/" target="_blank">Site Governo do Estado do RS</a></li>     
		</ul>
	</div>
</div>
<?php endblock('main'); ?>
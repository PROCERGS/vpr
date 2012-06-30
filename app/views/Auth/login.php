<?php require VIEWS_PATH . 'main.php'; ?>
<?php startblock('title'); echo self::getTitle(); endblock(); ?>
<?php startblock('content'); ?>
<div class="row">
	<div class="twelvecol last login">
		<h2>Identificação</h2>
		
		<p><strong>Importante:</strong></p>
		<p>O título de eleitor e a carteira de identidade devem pertencer ao estado do Rio Grande do Sul.</p>
		<p>Os campos marcados com (*) são obrigatórios</p>
		<p>Ao inserir seu número de telefone celular e/ou endereço de e-mail, você concorda em receber mensagens do Sistema Estadual de Participação Popular e Cidadã (sem nenhum custo para o cidadão)</p>
		
		<form action="<?php echo $html->url(array('controller' => 'Auth', 'action' => 'login')) ?>" method="post">
			<fieldset class="required">
				<legend>Campos obrigatórios</legend>
				<label for="titulo">
					Título de Eleitor:
					<input type="number" name="Cidadao[nro_titulo]" id="titulo" min="0" value="" />
					<?php echo $html->link("Consultar Título", "http://www.tse.jus.br/eleitor/titulo-e-local-de-votacao/consulta-por-nome", array('target' => '_blank')); ?>
				</label>
				
				<label for="rg">
					RG (Carteira de Identidade):
					<input type="number" name="Cidadao[rg]" id="rg" min="0" value="" />
				</label>
			</fieldset>
			
			<fieldset>
				<legend>Campos opcionais</legend>
				<label for="celular">
					Celular:
					<input type="tel" name="Cidadao[nro_telefone]" id="celular" />
				</label>
				
				<label for="email">
					Email:
					<input type="email" name="Cidadao[ds_email]" id="email" />
				</label>
			
				<button type="submit">Prosseguir</button>
			</fieldset>
		</form>
	</div>
</div>
<?php endblock() ?>

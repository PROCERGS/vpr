<?php require VIEWS_PATH . 'main.php'; ?>
<?php startblock('title'); echo self::getTitle(); endblock(); ?>
<?php startblock('content'); ?>
<div class="row">
	<div class="twelvecol last login">
		<h2>Identificação</h2>
		
		<p><strong>Importante:</strong></p>
		<p>O título de eleitor e o RG devem pertencer ao RS.</p>
		<p>Os campos marcados com (*) são obrigatórios</p>
<?php include_once VIEWS_PATH.'flash.php'; ?>
		<form action="<?php echo $html->url(array('controller' => 'Auth', 'action' => 'login')) ?>" method="post">
			<fieldset class="required">
				<legend>Campos obrigatórios</legend>
				<label for="titulo">
					Título de Eleitor:
					<input tabindex="1" type="number" name="Cidadao[nro_titulo]" id="titulo" min="0" value="<?php echo isset($flash_extra)?@$flash_extra['titulo']:''; ?>" />
					<?php echo $html->link("Consultar Título", "http://www.tse.jus.br/eleitor/titulo-e-local-de-votacao/consulta-por-nome", array('target' => '_blank')); ?>
				</label>
				
				<label for="rg">
					RG (Carteira de Identidade):
					<input tabindex="2" type="number" name="Cidadao[rg]" id="rg" min="0" value="<?php echo isset($flash_extra)?@$flash_extra['rg']:''; ?>" />
				</label>
			</fieldset>
			
			<fieldset class="optional">
				<legend>Estes campos são opcionais. Ao informar seu número de celular e/ou e-mail, você concorda em receber mensagens, sem nenhum custo para o cidadão, do Sistema Estadual de Participação Popular e Cidadã.</legend>
				<label for="celular">
					Celular:
					<input tabindex="3" type="tel" name="Cidadao[nro_telefone]" id="celular" value="<?php echo isset($flash_extra)?@$flash_extra['celular']:''; ?>" />
				</label>
				
				<label for="email">
					Email:
					<input tabindex="4" type="email" name="Cidadao[ds_email]" id="email" value="<?php echo isset($flash_extra)?@$flash_extra['email']:''; ?>" />
				</label>
			
				<button type="submit">Prosseguir</button>
			</fieldset>
		</form>
	</div>
</div>
<?php endblock() ?>

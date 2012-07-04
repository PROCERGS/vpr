<?php require VIEWS_PATH . 'main.php'; ?>
<?php startblock('title'); echo self::getTitle(); endblock(); ?>
<?php startblock('content'); ?>
	<div class="row">
		<div class="twelvecol last stats">
			<h2>Resumo</h2>
			<p>Totais atualizados em tempo real. Dados referentes ao dia <strong><?php echo date("d/m/Y à\s H:i:s"); ?></strong></p>
			
			<table>
				<thead>
					<tr>
						<th colspan="5">Eleitores</th>
					</tr>
					<tr>
						<th>Região</th>
<?php foreach ($meios_votacao as $meio) { ?>
						<th><?php echo $meio; ?></th>
<?php } ?>
						<th>Total da Região</th>
					</tr>
				</thead>
				<tbody>
<?php $i = 0;
		foreach ($eleitores as $regiao => $meios) {
			if ($regiao == 'totais') continue;
			$odd = $i++%2==0?' class="odd"':''; ?>
					<tr<?php echo $odd; ?>>
						<td><?php echo $regiao; ?></td>
<?php 		foreach ($meios_votacao as $meio) { ?>
						<td><?php echo str_pad(number_format(@$meios[$meio], 0, ',', '.'), 1, '0', STR_PAD_LEFT); ?></td>
<?php 		} ?>
						<td><?php echo str_pad(number_format(@$meios['total'], 0, ',', '.'), 1, '0', STR_PAD_LEFT); ?></td>
					</tr>
<?php 	}
		$odd = $i++%2==0?' odd':''; ?>
					<tr class="total <?php echo $odd; ?>">
						<td>Total</td>
<?php foreach ($meios_votacao as $meio) { ?>
						<td><?php echo str_pad(number_format(@$eleitores['totais'][$meio], 0, ',', '.'), 1, '0', STR_PAD_LEFT); ?></td>
<?php } ?>
						<td>-</td>
					</tr>
				</tbody>
			</table>
			
			
			<table class="votos">
				<thead>
					<tr>
						<th colspan="5">Votos</th>
					</tr>
					<tr>
						<th>Região</th>
<?php foreach ($meios_votacao as $meio) { ?>
						<th><?php echo $meio; ?></th>
<?php } ?>
						<th>Total da Região</th>
					</tr>
				</thead>
				<tbody>
<?php $i = 0;
		foreach ($votos as $regiao => $meios) {
			if ($regiao == 'totais') continue;
			$odd = $i++%2==0?' class="odd"':''; ?>
					<tr<?php echo $odd; ?>>
						<td><?php echo $regiao; ?></td>
<?php 		foreach ($meios_votacao as $meio) { ?>
						<td><?php echo str_pad(number_format(@$meios[$meio], 0, ',', '.'), 1, '0', STR_PAD_LEFT); ?></td>
<?php 		} ?>
						<td><?php echo str_pad(number_format(@$meios['total'], 0, ',', '.'), 1, '0', STR_PAD_LEFT); ?></td>
					</tr>
<?php 	}
		$odd = $i++%2==0?' odd':''; ?>
					<tr class="total <?php echo $odd; ?>">
						<td>Total</td>
<?php foreach ($meios_votacao as $meio) { ?>
						<td><?php echo str_pad(number_format(@$votos['totais'][$meio], 0, ',', '.'), 1, '0', STR_PAD_LEFT); ?></td>
<?php } ?>
						<td>-</td>
					</tr>
				</tbody>
			</table>

		</div>
	</div>
<?php endblock('content'); ?>

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
						<th>Item</th>
						<th>Total</th>
					</tr>
				</thead>
				<tbody>
					<tr class="odd">
						<td>Total de Eleitores</td>
						<td><?php echo number_format($totalCidadaos['total'], 0, ',', '.') + 0; ?></td>
					</tr>
					<tr>
						<td>Total de Votos</td>
						<td><?php echo number_format($totalVotos['total'], 0, ',', '.') + 0; ?></td>
					</tr>
				</tbody>
			</table>
			
			<p class="meiosVotacao">Totais agrupados por meio de votação:</p>
			
			<table class="meiosVotacao">
				<thead>
					<tr>
						<th>Meio de Votação</th>
						<th>Total de Votos</th>
						<th>Total de Eleitores</th>
					</tr>
				</thead>
				<tbody>
<?php $i = 0;
		foreach ($meios_votacao as $meio_votacao) {
			$odd = $i++%2==0?' class="odd"':''; ?>
					<tr<?php echo $odd; ?>>
						<td><?php echo $meio_votacao; ?> </td>
						<td><?php echo number_format(@$totalVotosByMeioVotacao[$meio_votacao], 0, ',', '.') + 0; ?></td>
						<td><?php echo number_format(@$totalCidadaosByMeioVotacao[$meio_votacao], 0, ',', '.') + 0; ?></td>
					</tr>
<?php 	} ?>
				</tbody>
			</table>
		</div>
	</div>
<?php endblock('content'); ?>

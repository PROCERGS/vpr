<?php require VIEWS_PATH . 'main.php'; ?>
<?php startblock('title'); echo self::getTitle(); endblock(); ?>
<?php startblock('content'); ?>
	<div class="row">
		<div class="twelvecol last">
			<table>
				<thead>
					<tr>
						<td>Item</td>
						<td>Total</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Total de Eleitores</td>
						<td><?php echo $totalCidadaos['total'] + 0; ?></td>
					</tr>
					<tr>
						<td>Total de Votos</td>
						<td><?php echo $totalVotos['total'] + 0; ?></td>
					</tr>
				</tbody>
			</table>
			
			<table>
				<thead>
					<tr>
						<td>Meio de Votação</td>
						<td>Total de Votos</td>
						<td>Total de Eleitores</td>
					</tr>
				</thead>
				<tbody>
<?php foreach ($meios_votacao as $meio_votacao) { ?>
					<tr>
						<td><?php echo $meio_votacao; ?> </td>
						<td><?php echo @$totalVotosByMeioVotacao[$meio_votacao] + 0; ?></td>
						<td><?php echo @$totalCidadaosByMeioVotacao[$meio_votacao]['total'] + 0; ?></td>
					</tr>
<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
<?php endblock('content'); ?>

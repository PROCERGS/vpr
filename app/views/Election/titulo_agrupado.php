					<dl>
<?php 		foreach ($currentGroup['areas'] as $idArea => $options) {
				if ($idArea > 0)
					$nm_area_tematica = $areasTematicas[$idArea]->getNmAreaTematica();
				else
					throw new ErrorException("Inconsistent database: Cedula[".$option->getIdCedula()."] has no id_area_tematica!");
?>
						<dt><?php echo $nm_area_tematica ?></dt>
<?php 			$groupByAreaTematica = TRUE;
				include VIEWS_PATH.'Election/option.php';
			} ?>
					</dl>
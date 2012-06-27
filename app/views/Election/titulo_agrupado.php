					<dl>
<?php 		foreach ($currentGroup['areas'] as $idArea => $options) {
				$areasTematicas = $currentGroup['areasTematicas'];
				if ($idArea > 0)
					$nm_area_tematica = $areasTematicas[$idArea]->getNmAreaTematica();
				else
					throw new ErrorException("Inconsistent database: Cedula[".$option->getIdCedula()."] has no id_area_tematica!");
				?>
						<dt><?php echo $nm_area_tematica ?></dt>
<?php 			foreach ($options as $option) { ?>
<?php 				$id = $option->getIdCedula();
					if (!$readonly)
						$selected = Vote::isVoted($option)?'checked="checked"':'';
				else 
					$selected = 'disabled="disabled"';
					
					$label = $option->getLabel(); ?>
						<dd>
							<input type="checkbox" name="selected[]" value="<?php echo $id; ?>" id="proposal<?php echo $id; ?>" <?php echo $selected; ?> />
							<label for="proposal<?php echo $id; ?>">
								<span class="cod_projeto"><?php echo $option->getCodProjeto(); ?></span> - <?php echo $label; ?></label>
						</dd>
<?php 			}
			} ?>
					</dl>
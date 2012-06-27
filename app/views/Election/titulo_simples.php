					<dl>
<?php 		foreach ($currentGroup['options'] as $option) {
				$areasTematicas = $currentGroup['areasTematicas']; 
/*						<dt><?php echo $areasTematicas[$options[0]->getIdAreaTematica()]->getNmAreaTematica(); ?></dt>*/ ?>
<?php 			$id = $option->getIdCedula();
				if (!$readonly)
					$selected = Vote::isVoted($option)?'checked="checked"':'';
				else 
					$selected = 'disabled="disabled"';
				
				$id_area_tematica = $option->getIdAreaTematica();
				if (is_numeric($id_area_tematica) && $id_area_tematica > 0)
					$nm_area_tematica = $areasTematicas[$option->getIdAreaTematica()]->getNmAreaTematica();
				else
					$nm_area_tematica = NULL;
				$label = $option->getLabel($nm_area_tematica); ?>
						<dd>
							<input type="checkbox" name="selected[]" value="<?php echo $id; ?>" id="proposal<?php echo $id; ?>" <?php echo $selected; ?> />
							<label for="proposal<?php echo $id; ?>">
								<span class="cod_projeto"><?php echo $option->getCodProjeto(); ?></span> - <?php echo $label; ?></label>
						</dd>
<?php 		} ?>
					</dl>
					<dl>
<?php 		foreach ($currentGroup['options'] as $option) {
				$areasTematicas = $currentGroup['areasTematicas']; 
/*						<dt><?php echo $areasTematicas[$options[0]->getIdAreaTematica()]->getNmAreaTematica(); ?></dt>*/ ?>
<?php 			$id = $option->getIdCedula();
				if (!$readonly)
					$selected = Vote::isVoted($option)?'checked="checked"':'';
				
				$label = array();
				$label[] = $areasTematicas[$option->getIdAreaTematica()]->getNmAreaTematica();
				$label[] = $option->getNmDemanda();
				
				if (strlen($option->getVlrDemanda()) > 0 && floatval($option->getVlrDemanda()) > 0)
					$label[] = 'Valor: R$'.$option->getVlrDemanda();
				if (strlen($option->getDsAbrangencia()) > 0)
					$label[] = 'Abrangência: '.$option->getDsAbrangencia();
				
				$label = join(' - ', $label); ?>
						<dd>
							<input type="checkbox" name="selected[]" value="<?php echo $id; ?>" id="proposal<?php echo $id; ?>" <?php echo $selected; ?> />
							<label for="proposal<?php echo $id; ?>">
								<span class="cod_projeto"><?php echo $option->getCodProjeto(); ?></span> - <?php echo $label; ?></label>
						</dd>
<?php 		} ?>
					</dl>
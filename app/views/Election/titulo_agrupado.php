					<dl>
<?php 		foreach ($currentGroup['areas'] as $idArea => $options) {
				$areasTematicas = $currentGroup['areasTematicas'];
				if ($idArea > 0)
					$nm_area_tematica = $areasTematicas[$idArea]->getNmAreaTematica();
				else
					$nm_area_tematica = 'SEM ÁREA TEMÁTICA LERO LERO! LA LA LA!';
				?>
						<dt><?php echo $nm_area_tematica ?></dt>
<?php 			foreach ($options as $option) { ?>
<?php 				$id = $option->getIdCedula();
					if (!$readonly)
						$selected = Vote::isVoted($option)?'checked="checked"':'';
					else
						$selected = '';
					
					$label = array();
					$label[] = $option->getNmDemanda();
					
					if (strlen($option->getVlrDemanda()) > 0 && floatval($option->getVlrDemanda()) > 0)
						$label[] = 'Valor: R$'.number_format($option->getVlrDemanda(), 2, ',', '.');
					if (strlen($option->getDsAbrangencia()) > 0)
						$label[] = 'Abrangência: '.$option->getDsAbrangencia();
					
					$label = join(' - ', $label); ?>
						<dd>
							<input type="checkbox" name="selected[]" value="<?php echo $id; ?>" id="proposal<?php echo $id; ?>" <?php echo $selected; ?> />
							<label for="proposal<?php echo $id; ?>">
								<span class="cod_projeto"><?php echo $option->getCodProjeto(); ?></span> - <?php echo $label; ?></label>
						</dd>
<?php 			}
			} ?>
					</dl>
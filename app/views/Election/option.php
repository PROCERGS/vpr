<?php 		foreach ($options as $option) {
				$id = $option->getIdCedula();
				if (!@$readonly)
					$selected = Vote::isVoted($option)?'checked="checked"':'';
				else 
					$selected = 'disabled="disabled"';
				
				$id_area_tematica = $option->getIdAreaTematica();
				if (!$groupByAreaTematica && is_numeric($id_area_tematica) && $id_area_tematica > 0)
					$nm_area_tematica = $areasTematicas[$option->getIdAreaTematica()]->getNmAreaTematica();
				else
					$nm_area_tematica = NULL;
				$label = $option->getLabel($nm_area_tematica); ?>
						<dd>
							<input type="checkbox" name="selected[]" value="<?php echo $id; ?>" id="proposal<?php echo $id; ?>" <?php echo $selected; ?> />
							<label for="proposal<?php echo $id; ?>">
								<span class="cod_projeto"><?php echo $option->getCodProjeto(); ?></span> - <?php echo $label; ?>
							</label>
<?php 			if ($option->isExpandable()) {
					$description = trim($option->getDsDemanda()); 
					$scope = trim($option->getDsAbrangencia()); ?>
							<a href="#" class="toggleDetails">ver mais</a>
							<div class="details">
<?php 				if (strlen($scope) > 0) { ?>
								<p class="scope"><strong>Abrangência:</strong> <?php echo $scope; ?></p>
<?php 				}
					if (strlen($description) > 0) { ?>
								<p class="description"><strong>Descrição:</strong> <?php echo $description; ?></p>
<?php 				} ?>
							</div>
<?php 			} ?>
						</dd>
<?php 		} ?>
<div class="row poll">
    <div class="twelvecol last">

        <?php 
            $poll_errors = Session::get('poll_errors');
            Session::delete('poll_errors');
        ?>

        <?php if($poll_errors) { ?>
            <div class="flash error">
                <?php foreach($poll_errors as $error) { ?>
                    <p><?php echo $error; ?></p>
                <?php } ?>
            </div>
        <? } ?>

        <div class="container">
            
            <?php if(!@$readonly) { ?>
            <form id="poll" class="vote" action="/Polls/confirm" method="post">
            <?php } ?>
                
                <div class="row group"> 
                    <div class="twelvecol last">
                        <h2><?php echo $poll->getTitle(); ?></h2>
                        <p class="description-poll">As recentes manifestações por melhorias que inundaram as ruas do Brasil levaram a Coordenação Estadual da Participação Popular e Cidadã – PPC, a propor uma CONSULTA SOBRE REFORMA POLÍTICA. Por isso foi incluído o Campo 3 na cédula de votação de prioridades, cujas questões estão abaixo. Manifeste sua opinião sobre o futuro do Brasil!</p>
                        <fieldset>
                            <?php foreach($poll->getQuestions() as $i => $question) { ?>
                                <dl contextmenu="<?php echo $question->getMaxSelection(); ?>">
                                    <dt><?php echo $question->getSequence(); ?> - <?php echo $question->getQuestion(); ?></dt>
                                    <?php foreach($question->getOptions() as $option) { ?>
                                        <dd>
                                            <?php 
                                                if (!@$readonly) { 
                                                    $selected = $option->isChecked() ? 'checked="checked"' : '';
                                                } else {
                                                    $selected = 'disabled="disabled"'; 
                                                }
                                            ?>
                                            
                                            <?php if ($question->getMaxSelection() > 1) { ?>
                                                <input type="checkbox" <?php echo $selected; ?> id="option_<?php echo $option->getId(); ?>" value="<?php echo $option->getId(); ?>" name="selected[<?php echo $question->getId(); ?>][]">
                                            <?php } else { ?>
                                                <input type="radio" <?php echo $selected; ?> id="option_<?php echo $option->getId(); ?>" value="<?php echo $option->getId(); ?>" name="selected[<?php echo $question->getId(); ?>][]">
                                            <?php } ?>

                                            <label for="option_<?php echo $option->getId(); ?>">
                                                <?php echo $option->getOption(); ?>
                                            </label>
                                        </dd>
                                    <?php } ?>
                                </dl>
                            <?php } ?>
                        </fieldset>

                        <?php if(!@$readonly) { ?>
                            <button type="submit">Confirmar pesquisa</button>
                        <?php } ?>

                    </div>
                </div>

            <?php if(!@$readonly) { ?>
            </form>
            <?php } ?>

        </div>        
    </div>
</div>
<div class="row poll">
    <div class="twelvecol last">
        <h2>Pesquisa Opcional</h2>

        <?php 
            $poll_errors = Session::get('poll_errors');
            Session::delete('poll_errors');
        ?>

        <?php if($poll_errors) { ?>
            <div class="flash warning">
                <?php foreach($poll_errors as $error) { ?>
                    <p><?php echo $error; ?></p>
                <?php } ?>
            </div>
        <? } ?>

        <form id="poll" class="vote" action="/Polls/confirm" method="post">
            <div class="container">
                <div class="row group"> 
                    <div class="twelvecol last">
                        <h2><?php echo $poll->getTitle(); ?></h2>
                        <fieldset>
                            <?php foreach($poll->getQuestions() as $i => $question) { ?>
                                <dl contextmenu="<?php echo $question->getMaxSelection(); ?>">
                                    <dt><?php echo $question->getSequence(); ?> - <?php echo $question->getQuestion(); ?></dt>
                                    <?php foreach($question->getOptions() as $option) { ?>
                                        <dd>
                                            <?php $selected = $option->isChecked() ? 'checked="checked"' : ''; ?>

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
                        <button type="submit">Confirmar pesquisa</button>
                    </div>
                </div>
            </form>
        </div>        
    </div>
</div>
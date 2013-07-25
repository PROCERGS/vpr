<?php require VIEWS_PATH . 'main.php'; ?>
<?php startblock('title'); echo self::getTitle(); endblock(); ?>
<?php startblock('content'); ?>
<div class="row">
	<div class="twelvecol last select_regiao">
		<h2>Voto enviado!</h2>
		<p><strong>Obrigado por sua participação!</strong></p>
	</div>
</div>

<?php if($poll) { ?>
    <div class="row poll">
        <div class="twelvecol last">
            <h2>Pesquisa Opcional</h2>

            <form id="poll" class="vote" action="/polls/confirm" method="post">
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
                                                <?php if ($question->getMaxSelection() > 1) { ?>
                                                    <input type="checkbox" id="option_<?php echo $option->getId(); ?>" value="<?php echo $option->getId(); ?>" name="selected[<?php echo $i; ?>][]">
                                                <?php } else { ?>
                                                    <input type="radio" id="option_<?php echo $option->getId(); ?>" value="<?php echo $option->getId(); ?>" name="selected[<?php echo $i; ?>][]">
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
<?php } else { ?>
    <div class="row home">
        <div class="twelvecol last">
            <?php echo $html->link('<button type="button">Voltar para a Página inicial</button>', array('controller' => 'Application', 'action' => 'index'), array('class' => 'home')); ?>
        </div>
    </div>
<? } ?>
<?php endblock('content'); ?>

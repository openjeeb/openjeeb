<?php
/**
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2009, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2009, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.cake.console.libs.templates.views
 * @since         CakePHP(tm) v 1.2.0.5234
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
?>
<div class="grid_4">
    <div class="box">
        <?php //// ACTIONS ?>
        <h2>
            <a href="#" id="toggle-shortlinks">دسترسی سریع</a>
        </h2>
        <div class="block" id="shortlinks">
            <ul class="menu">
                <li><?php echo "<?php echo \$this->Html->link('$pluralHumanName', array('action' => 'index'));?>";?></li>
            </ul>
        </div>
    </div>
</div>

<div class="grid_12">
    <div class="box">
    <h2><?php echo "Edit $singularHumanName"; ?></h2>
    <div class="<?php echo $pluralVar;?> form">
        <?php echo "<?php echo \$this->Form->create('{$modelClass}');?>\n";?>
        <fieldset>
	<?php
			echo "\t<?php\n";
			foreach ($fields as $field) {
				if (strpos($action, 'add') !== false && $field == $primaryKey) {
					continue;
				} elseif (!in_array($field, array('created', 'modified', 'updated'))) {
					echo "\t\techo \$this->Form->input('{$field}',array('label'=>'{$field}'));\n";
				}
			}
			if (!empty($associations['hasAndBelongsToMany'])) {
				foreach ($associations['hasAndBelongsToMany'] as $assocName => $assocData) {
					echo "\t\techo \$this->Form->input('{$assocName}',array('label'=>'{$assocName}'));\n";
				}
			}
			echo "\t?>\n";
	?>
        </fieldset>
	<?php
		echo "<?php echo \$this->Form->end('ثبت');?>\n";
	?>
    </div>

    </div>

</div>
<div class="clear"></div>

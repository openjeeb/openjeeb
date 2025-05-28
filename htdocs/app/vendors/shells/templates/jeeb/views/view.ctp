<?php
/**
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
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
                <li><?php echo "<?php echo \$this->Html->link('$pluralHumanName', array('action' => 'index')); ?>"; ?></li>
                <li><?php echo "<?php echo \$this->Html->link('ویرایش', array('action' => 'edit')); ?>"; ?></li>
                <li><?php echo "<?php echo \$this->Html->link('پاک کردن', array('action' => 'delete')); ?>"; ?></li>
            </ul>
        </div>
    </div>
</div>

<div class="grid_12">
    <div class="box">
            <div class="<?php echo $pluralVar;?> view">
            <h2><?php echo "$singularHumanName";?></h2>
                    <div class="block">
                            <dl><?php echo "<?php \$i = 0; \$class = ' class=\"altrow\"';?>\n";?>
                                    <?php
                                    foreach ($fields as $field) {
                                            $isKey = false;
                                            if (!empty($associations['belongsTo'])) {
                                                    foreach ($associations['belongsTo'] as $alias => $details) {
                                                            if ($field === $details['foreignKey']) {
                                                                    $isKey = true;
                                                                    echo "\t\t<dt<?php if (\$i % 2 == 0) echo \$class;?>><?php __('" . Inflector::humanize(Inflector::underscore($alias)) . "'); ?></dt>\n";
                                                                    echo "\t\t<dd<?php if (\$i++ % 2 == 0) echo \$class;?>>\n\t\t\t<?php echo \$this->Html->link(\${$singularVar}['{$alias}']['{$details['displayField']}'], array('controller' => '{$details['controller']}', 'action' => 'view', \${$singularVar}['{$alias}']['{$details['primaryKey']}'])); ?>\n\t\t\t&nbsp;\n\t\t</dd>\n";
                                                                    break;
                                                            }
                                                    }
                                            }
                                            if ($isKey !== true) {
                                                    echo "\t\t<dt<?php if (\$i % 2 == 0) echo \$class;?>><?php __('" . Inflector::humanize($field) . "'); ?></dt>\n";
                                                    echo "\t\t<dd<?php if (\$i++ % 2 == 0) echo \$class;?>>\n\t\t\t<?php echo \${$singularVar}['{$modelClass}']['{$field}']; ?>\n\t\t\t&nbsp;\n\t\t</dd>\n";
                                            }
                                    }
                                    ?>
                            </dl>
                    </div>
            </div>
    </div>
</div>
<div class="clear"></div>
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
<div class="grid_16 box" style="width: 920px;">
    <h2><?php echo "New $singularHumanName";?></h2>
    <div id="new" class="<?php echo $pluralVar; ?> form">
    <?php echo "<?php echo \$this->Form->create('{$modelClass}'); ?>\n"; ?>
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
    <?php echo "<?php echo \$this->Form->end('ثبت');?>\n"; ?>
    </div>

</div>
<div class="clear"></div>

<div class="grid_16">
    <h2 id="page-heading"><?php echo $pluralHumanName; ?></h2>

    <?php //// TABLE WITH RECORDS ?>

    <table id="dataTable" cellpadding="0" cellspacing="0">
    <?php
    //// TABLE HEADERS
    echo "<?php \$tableHeaders = \$html->tableHeaders(array(";

    foreach($fields as $field) {
    	echo "\$paginator->sort('{$field}','{$field}'),";
    }
    echo "'Actions'));\n";

    echo "echo '<thead>'.\$tableHeaders.'</thead>'; ?>\n\n";
    
    //// TABLE ROWS
    
	echo "<?php
	\$i = 0;
	foreach (\${$pluralVar} as \${$singularVar}):
		\$class = null;
		if (\$i++ % 2 == 0) {
			\$class = ' class=\"altrow\"';
		}
	?>\n";
	echo "\t<tr<?php echo \$class;?>>\n";
		foreach ($fields as $field) {
			$isKey = false;
			if (!empty($associations['belongsTo'])) {
				foreach ($associations['belongsTo'] as $alias => $details) {
					if ($field === $details['foreignKey']) {
						$isKey = true;
						echo "\t\t<td>\n\t\t\t<?php echo \$this->Html->link(\${$singularVar}['{$alias}']['{$details['displayField']}'], array('controller' => '{$details['controller']}', 'action' => 'view', \${$singularVar}['{$alias}']['{$details['primaryKey']}'])); ?>\n\t\t</td>\n";
						break;
					}
				}
			}
			if ($isKey !== true) {
				echo "\t\t<td><?php echo \${$singularVar}['{$modelClass}']['{$field}']; ?>&nbsp;</td>\n";
			}
		}
		echo "\t\t<td>\n\t\t\t<?php echo \$this->Html->link('نمایش', array('action' => 'view', \${$singularVar}['{$modelClass}']['{$primaryKey}'])); ?>\n\t\t";
		echo "\n\t\t\t<?php echo \$this->Html->link('ویرایش', array('action' => 'edit', \${$singularVar}['{$modelClass}']['{$primaryKey}'])); ?>\n\t\t";
		echo "\n\t\t\t<?php echo \$this->Html->link('پاک کردن', array('action' => 'delete', \${$singularVar}['{$modelClass}']['{$primaryKey}']), null, sprintf('آیا مطمئنید که میخواهید این اطلاعات را پاک کنید؟', \${$singularVar}['{$modelClass}']['{$primaryKey}'])); ?>\n\t\t</td>\n";
	echo "\t</tr>\n";
        
	echo "<?php endforeach; ?>\n";
	
	//// TABLE FOOTER
    echo "<?php echo '<tfoot class=\'dark\'>'.\$tableHeaders.'</tfoot>'; ?>";
    ?>
    </table>
    
    <?php //// PAGINATION ?>
      
	<p align="center">
	<?php echo "<?php
	echo \$this->Paginator->counter(array(
                    'format' => 'صفحه %page% از %pages%, در حال نمایش %current% مورد از %count%, از %start% تا %end%'
                ));
	?>";?>
	</p>

	<div  align="center" class="paging">
	<?php echo "\t<?php echo \$this->Paginator->prev('<< قبلی', array(), null, array('class' => 'disabled'));?>\n";?>
	 | <?php echo "\t<?php echo \$this->Paginator->numbers();?>\n"?>&nbsp;|
	<?php echo "\t<?php echo \$this->Paginator->next('بعدی >>', array(), null, array('class' => 'disabled'));?>\n";?>
	</div>
		
</div>
<div class="clear"></div>

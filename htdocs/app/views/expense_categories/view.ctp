<div class="col-xs-16 col-md-4">
    <div class="box">
                <h2>
            <a href="#" id="toggle-shortlinks">دسترسی سریع</a>
        </h2>
        <div class="block" id="shortlinks">
            <ul class="menu">
                <li><?php echo $this->Html->link('Expense Categories', array('action' => 'index')); ?></li>
                <li><?php echo $this->Html->link('ویرایش', array('action' => 'edit')); ?></li>
                <li><?php echo $this->Html->link('پاک کردن', array('action' => 'delete')); ?></li>
            </ul>
        </div>
    </div>
</div>

<div class="col-xs-16 col-md-12">
    <div class="box">
            <div class="expenseCategories view">
            <h2>Expense Category</h2>
                    <div class="block">
                            <dl><?php $i = 0; $class = ' class="altrow"';?>
                                    		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $expenseCategory['ExpenseCategory']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $expenseCategory['ExpenseCategory']['name']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('User'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($expenseCategory['User']['email'], array('controller' => 'users', 'action' => 'view', $expenseCategory['User']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Created'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $expenseCategory['ExpenseCategory']['created']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Modified'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $expenseCategory['ExpenseCategory']['modified']; ?>
			&nbsp;
		</dd>
                            </dl>
                    </div>
            </div>
    </div>
</div>
<div class="clear"></div>
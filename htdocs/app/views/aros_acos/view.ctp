<div class="col-xs-16 col-md-4">
    <div class="box">
        <h2>
            <a href="#" id="toggle-shortlinks">دسترسی سریع</a>
        </h2>
        <div class="block" id="shortlinks">
            <ul class="menu">
                <li><?php echo $this->Html->link('دسترسی‌ها', array('action' => 'index')); ?></li>
                <li><?php echo $this->Html->link('ویرایش', array('action' => 'edit', $arosAco['ArosAco']['id'])); ?></li>
            </ul>
        </div>
    </div>
</div>

<div class="col-xs-16 col-md-12">
    <div class="box">
        <div class="arosAcos view">
            <h2>دسترسی</h2>
            <div class="block">
                <dl><?php $i = 0;
                        $class = ' class="altrow"'; ?>
                    <dt<?php if ($i % 2 == 0)
                        echo $class; ?>>ردیف</dt>
                    <dd<?php if ($i++ % 2 == 0)
                            echo $class; ?>>
                        <?php echo $arosAco['ArosAco']['id']; ?>
                        &nbsp;
                    </dd>
                    <dt<?php if ($i % 2 == 0)
                            echo $class; ?>>گروه کاربری</dt>
                    <dd<?php if ($i++ % 2 == 0)
                            echo $class; ?>>
                        <?php echo $this->Html->link($arosAco['Aro']['id'], array('controller' => 'aros', 'action' => 'view', $arosAco['Aro']['id'])); ?>
                        &nbsp;
                    </dd>
                    <dt<?php if ($i % 2 == 0)
                            echo $class; ?>>دسترسی به</dt>
                    <dd<?php if ($i++ % 2 == 0)
                            echo $class; ?>>
                        <?php echo $this->Html->link($arosAco['Aco']['id'], array('controller' => 'acos', 'action' => 'view', $arosAco['Aco']['id'])); ?>
                        &nbsp;
                    </dd>
                    <dt<?php if ($i % 2 == 0)
                            echo $class; ?>>ایجاد</dt>
                    <dd<?php if ($i++ % 2 == 0)
                            echo $class; ?>>
                        <?php echo $arosAco['ArosAco']['_create']; ?>
                        &nbsp;
                    </dd>
                    <dt<?php if ($i % 2 == 0)
                            echo $class; ?>>خواندن</dt>
                    <dd<?php if ($i++ % 2 == 0)
                            echo $class; ?>>
                        <?php echo $arosAco['ArosAco']['_read']; ?>
                        &nbsp;
                    </dd>
                    <dt<?php if ($i % 2 == 0)
                            echo $class; ?>>بروزرسانی</dt>
                    <dd<?php if ($i++ % 2 == 0)
                            echo $class; ?>>
                        <?php echo $arosAco['ArosAco']['_update']; ?>
                        &nbsp;
                    </dd>
                    <dt<?php if ($i % 2 == 0)
                            echo $class; ?>>پاک کردن</dt>
                    <dd<?php if ($i++ % 2 == 0)
                            echo $class; ?>>
                        <?php echo $arosAco['ArosAco']['_delete']; ?>
                        &nbsp;
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</div>
<div class="clear"></div>
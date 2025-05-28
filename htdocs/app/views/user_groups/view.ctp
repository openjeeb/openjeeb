<div class="col-xs-16 col-md-4">
    <div class="box">
        <h2>
            <a href="#" id="toggle-shortlinks">دسترسی سریع</a>
        </h2>
        <div class="block" id="shortlinks">
            <ul class="menu">
                <li><?php echo $this->Html->link('گروه‌های کاربری', array('action' => 'index')); ?></li>
                <li><?php echo $this->Html->link('ویرایش', array('action' => 'edit',$userGroup['UserGroup']['id'])); ?></li>
            </ul>
        </div>
    </div>
</div>

<div class="col-xs-16 col-md-12">
    <div class="box">
        <div class="userGroups view">
            <h2>گروه کاربری</h2>
            <div class="block">
                <dl><?php $i = 0;
                    $class = ' class="altrow"'; ?>
                    <dt<?php if ($i % 2 == 0)
                            echo $class; ?>>ردیف</dt>
                    <dd<?php if ($i++ % 2 == 0)
                            echo $class; ?>>
                        <?php echo $userGroup['UserGroup']['id']; ?>
                        &nbsp;
                    </dd>
                    <dt<?php if ($i % 2 == 0)
                            echo $class; ?>>نام</dt>
                    <dd<?php if ($i++ % 2 == 0)
                            echo $class; ?>>
                        <?php echo $userGroup['UserGroup']['name']; ?>
                        &nbsp;
                    </dd>
                    <dt<?php if ($i % 2 == 0)
                            echo $class; ?>>ایجاد</dt>
                    <dd<?php if ($i++ % 2 == 0)
                            echo $class; ?>>
                        <?php echo $userGroup['UserGroup']['created']; ?>
                        &nbsp;
                    </dd>
                    <dt<?php if ($i % 2 == 0)
                            echo $class; ?>>ویرایش</dt>
                    <dd<?php if ($i++ % 2 == 0)
                            echo $class; ?>>
                        <?php echo $userGroup['UserGroup']['modified']; ?>
                        &nbsp;
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</div>
<div class="clear"></div>
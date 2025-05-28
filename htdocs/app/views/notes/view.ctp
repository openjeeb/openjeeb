<div class="col-xs-16 col-md-4">
    <div class="box">
        <h2>
            <a href="#" id="toggle-shortlinks">دسترسی سریع</a>
        </h2>
        <div class="block" id="shortlinks">
            <ul class="menu">
                <li><?php echo $this->Html->link( 'یادداشت‌ها', array( 'action' => 'index' ) ); ?></li>
            </ul>
            <br/>
        </div>
    </div>
</div>

<div class="col-xs-16 col-md-12">
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-primary">
            <th colspan="2" class="centered">یادداشت</th>
            </thead>
            <tbody>
                <tr>
                    <th>عنوان</th>
                    <td><?php echo $note['Note']['subject']; ?></td>
                </tr>
                <tr>
                    <th>متن</th>
                    <td><?php echo str_replace('\n', "\n",$note['Note']['content']); ?></td>
                </tr>
                <tr>
                    <th>وضعیت</th>
                    <td><?php if($note['Note']['status']=='due'){ echo 'انجام نشده';}else{ echo 'انجام شده';}; ?></td>
                </tr>
                <tr>
                    <th>ایجاد</th>
                    <td><?php echo $note['Note']['created']; ?></td>
                </tr>
                <tr>
                    <th>ویرایش</th>
                    <td><?php echo $note['Note']['modified']; ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<h3>گزارش اشکال</h3>
<p>شماره کاربر:<?php echo $bugData['user_id']; ?></p>
<p>ایمیل کاربر: <?php echo $bugData['user_email']; ?></p>
<p>مرورگر کاربر: <?php echo $bugData['browser']; ?></p>
<p>IP کاربر: <?php echo $bugData['contact_ip']; ?></p>
<p>صفحه خطا: <?php echo $bugData['refering']; ?></p>
<br/><br/>
<p>عنوان اشکال: <?php echo $bugData['title']; ?></p>
<p>نوع اشکال: <?php echo $bugData['type']; ?></p>
<p>توضیحات: <?php echo $bugData['description']; ?></p>
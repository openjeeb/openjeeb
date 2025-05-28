<?php
$pd = new PersianDate();
$today = $pd->pdate('Y/m/d');
$thismonth = $pd->pdate('m');
$thisyear = $pd->pdate('Y');
list($thisyearstg) = $pd->jalali_to_gregorian($thisyear, 1, 1);
$yesterday = $pd->pdate('Y/m/d',strtotime('yesterday'));
$thissaturday = strtotime('this saturday');
$thissaturday = ($thissaturday>time())? $thissaturday-(3600*24*7) : $thissaturday;
$thisfriday = $thissaturday+(3600*24*6);
$thisweekst = $pd->pdate('Y/m/d', $thissaturday );
$thisweeken = $pd->pdate('Y/m/d', $thisfriday );
$lastweekst = $pd->pdate('Y/m/d', $thissaturday-(3600*24*7) );
$lastweeken = $pd->pdate('Y/m/d', $thisfriday-(3600*24*7) );
$thismonthst = $pd->pdate('Y/m/01');
$thismonthen = $pd->pdate('Y/m')."/".$pd->lastday(date('m'), 11, date('Y'));
$thisyearst = $pd->pdate('Y/01/01');
$thisyearen = $pd->pdate('Y/12/').$pd->lastday(03, 11, $thisyearstg+1);
?>
<div style="float:right;<?php if(isset($oneline) && $oneline) { echo "margin: 4px 10px 0 0;"; } ?>">
    <a class="white_button" onclick="jeeb.fillDate('<?php echo $start_date ?>','<?php echo $end_date ?>','<?php echo $today ?>','<?php echo $today ?>');">امروز</a>
    <a class="white_button" onclick="jeeb.fillDate('<?php echo $start_date ?>','<?php echo $end_date ?>','<?php echo $yesterday ?>','<?php echo $yesterday ?>');">دیروز</a>
    <a class="white_button" onclick="jeeb.fillDate('<?php echo $start_date ?>','<?php echo $end_date ?>','<?php echo $thisweekst ?>','<?php echo $thisweeken ?>');">هفته جاری</a>
    <a class="white_button" onclick="jeeb.fillDate('<?php echo $start_date ?>','<?php echo $end_date ?>','<?php echo $lastweekst ?>','<?php echo $lastweeken ?>');">هفته گذشته</a>
    <a class="white_button" onclick="jeeb.fillDate('<?php echo $start_date ?>','<?php echo $end_date ?>','<?php echo $thismonthst ?>','<?php echo $thismonthen; ?>');">ماه جاری</a>
    <?php if(isset($showthisyear) && $showthisyear): ?><a class="white_button" onclick="jeeb.fillDate('<?php echo $start_date ?>','<?php echo $end_date ?>','<?php echo $thisyearst ?>','<?php echo $thisyearen ?>');">سال جاری</a><?php endif; ?>
</div>
<div class="clear"></div>
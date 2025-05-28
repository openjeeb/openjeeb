<?php
// Persian date helper these functions are simillar to date function in PHP you can find more about it in http://persiandates.osp.ir
// It is convertor to and from Gregorian and shamsi Date calendars.
// Copyright (C) 2000  Roozbeh Pournader and Mohammad Toossi
// Copyright (C) 2003-2007  Milad Rastian
// Copyright (C) 2003-2007  Arash Hemmat
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// vesrion 1.0
class PersianDate
{
	/*
	 * convert and formats the given time stamp string to the Persian date.
	 * format strings are the same with php date function. 
	 */
	function pdate_format($time_stamp,$format='j F Y')
	{
		return $this->pdate($format,strtotime($time_stamp));
	}

        function pdate_format_reverse($datetime,$dataSeperator='/',$dateTimeSeperator=' ')
        {
          @list($date,$time) = @explode($dateTimeSeperator,$datetime);
          @list($year,$month,$day) = @explode($dataSeperator,$date);
          list($year, $month, $day) = self::jalali_to_gregorian($year,$month,$day);
          $result=intval($year)."-".intval($month)."-".intval($day);
          if(!empty($time)) {
            $result.= $dateTimeSeperator.$time;
          }
          return $result;
        }
	
	function pdate($type,$maket="now")
	{
		//set 1 if you want translate number to farsi or if you don't like set 0
		$transnumber=0;
		///chosse your timezone
		$TZhours=0;
		$TZminute=0;

		if($maket=="now"){
			$year=date("Y");
			$month=date("m");
			$day=date("d");
			list( $jyear, $jmonth, $jday ) = $this->gregorian_to_jalali($year, $month, $day);
			$maket=$this->pmktime(date("h")+$TZhours,date("i")+$TZminute,date("s"),$jmonth,$jday,$jyear);
		}else{
			$maket+=$TZhours*3600+$TZminute*60;
			$date=date("Y-m-d",$maket);
			list( $year, $month, $day ) = preg_split ( '/-/', $date );

			list( $jyear, $jmonth, $jday ) = $this->gregorian_to_jalali($year, $month, $day);
		}

		$need= $maket;
		$year=date("Y",$need);
		$month=date("m",$need);
		$day=date("d",$need);
		$result=null;
		$i=0;
		while($i<strlen($type))
		{
			$subtype=substr($type,$i,1);
			switch ($subtype)
			{

				case "A":
					$result1=date("a",$need);
					if($result1=="pm") $result.= "بعدازظهر";
					else $result.="قبل‌از‌ظهر";
					break;

				case "a":
					$result1=date("a",$need);
					if($result1=="pm") $result.= "ب.ظ";
					else $result.="ق.ظ";
					break;
				case "d":
					list( $jyear, $jmonth, $jday ) = $this->gregorian_to_jalali($year, $month, $day);
					if($jday<10)$result1="0".$jday;
					else 	$result1=$jday;
					if($transnumber==1) $result.=$this->Convertnumber2farsi($result1);
					else $result.=$result1;
					break;
				case "D":
					$result1=date("D",$need);
                                        if($result1=="Thu") $result1="پ";
                                        else if($result1=="Sat") $result1="ش";
                                        else if($result1=="Sun") $result1="ى";
                                        else if($result1=="Mon") $result1="د";
                                        else if($result1=="Tue") $result1="س";
                                        else if($result1=="Wed") $result1="چ";
                                        else if($result1=="Thu") $result1="پ";
                                        else if($result1=="Fri") $result1="ج";
                                        $result.=$result1;
					break;
				case"F":
					list( $jyear, $jmonth, $jday ) = $this->gregorian_to_jalali($year, $month, $day);
					$result.=$this->monthname($jmonth);
					break;
				case "g":
					$result1=date("g",$need);
					if($transnumber==1) $result.=$this->Convertnumber2farsi($result1);
					else $result.=$result1;
					break;
				case "G":
					$result1=date("G",$need);
					if($transnumber==1) $result.=$this->Convertnumber2farsi($result1);
					else $result.=$result1;
					break;
				case "h":
					$result1=date("h",$need);
					if($transnumber==1) $result.=$this->Convertnumber2farsi($result1);
					else $result.=$result1;
					break;
				case "H":
					$result1=date("H",$need);
					if($transnumber==1) $result.=$this->Convertnumber2farsi($result1);
					else $result.=$result1;
					break;
				case "i":
					$result1=date("i",$need);
					if($transnumber==1) $result.=$this->Convertnumber2farsi($result1);
					else $result.=$result1;
					break;
				case "j":
					list( $jyear, $jmonth, $jday ) = $this->gregorian_to_jalali($year, $month, $day);
					$result1=$jday;
					if($transnumber==1) $result.=$this->Convertnumber2farsi($result1);
					else $result.=$result1;
					break;
				case "l":
					$result1=date("l",$need);
                                        if($result1=="Saturday") $result1="شنبه";
                                        else if($result1=="Sunday") $result1="یكشنبه";
                                        else if($result1=="Monday") $result1="دوشنبه";
                                        else if($result1=="Tuesday") $result1="سه شنبه";
                                        else if($result1=="Wednesday") $result1="چهارشنبه";
                                        else if($result1=="Thursday") $result1="پنجشنبه";
                                        else if($result1=="Friday") $result1="جمعه";
                                        $result.=$result1;
					break;
				case "m":
					list( $jyear, $jmonth, $jday ) = $this->gregorian_to_jalali($year, $month, $day);
					if($jmonth<10) $result1="0".$jmonth;
					else	$result1=$jmonth;
					if($transnumber==1) $result.=$this->Convertnumber2farsi($result1);
					else $result.=$result1;
					break;
				case "M":
					list( $jyear, $jmonth, $jday ) = $this->gregorian_to_jalali($year, $month, $day);
					$result.=$this->monthname($jmonth);
					break;
				case "n":
					list( $jyear, $jmonth, $jday ) = $this->gregorian_to_jalali($year, $month, $day);
					$result1=$jmonth;
					if($transnumber==1) $result.=$this->Convertnumber2farsi($result1);
					else $result.=$result1;
					break;
				case "s":
					$result1=date("s",$need);
					if($transnumber==1) $result.=$this->Convertnumber2farsi($result1);
					else $result.=$result1;
					break;
				case "S":
					$result.="ام";
					break;
				case "t":
					$result.=$this->lastday ($month,$day,$year);
					break;
				case "w":
					$result1=date("w",$need);
					if($transnumber==1) $result.=$this->Convertnumber2farsi($result1);
					else $result.=$result1;
					break;
				case "y":
					list( $jyear, $jmonth, $jday ) = $this->gregorian_to_jalali($year, $month, $day);
					$result1=substr($jyear,2,4);
					if($transnumber==1) $result.=$this->Convertnumber2farsi($result1);
					else $result.=$result1;
					break;
				case "Y":
					list( $jyear, $jmonth, $jday ) = $this->gregorian_to_jalali($year, $month, $day);
					$result1=$jyear;
					if($transnumber==1) $result.=$this->Convertnumber2farsi($result1);
					else $result.=$result1;
					break;
				default:
					$result.=$subtype;
			}
			$i++;
		}
		return $result;
	}



	function pmktime($hour,$minute,$second,$jmonth,$jday,$jyear)
	{
		list( $year, $month, $day ) = $this->jalali_to_gregorian($jyear, $jmonth, $jday);
		$i=mktime($hour,$minute,$second,$month,$day,$year);
		return $i;
	}


	///Find Day Begining Of Month
	function mstart($month,$day,$year)
	{
		list( $jyear, $jmonth, $jday ) = $this->gregorian_to_jalali($year, $month, $day);
		list( $year, $month, $day ) = $this->jalali_to_gregorian($jyear, $jmonth, "1");
		$timestamp=mktime(0,0,0,$month,$day,$year);
		return date("w",$timestamp);
	}

	//Find Number Of Days In This Month
	function lastday ($month,$day,$year)
	{
		$lastdayen=date("d",mktime(0,0,0,$month+1,0,$year));
		list( $jyear, $jmonth, $jday ) = $this->gregorian_to_jalali($year, $month, $day);
		$lastdatep=$jday;
		$jday=@$jday2;
		while(@$jday2!="1")
		{
			if($day<$lastdayen)
			{
				$day++;
				list( $jyear, $jmonth, $jday2 ) = $this->gregorian_to_jalali($year, $month, $day);
				if(@$pdate2=="1") break;
				if(@$pdate2!="1") $lastdatep++;
			}
			else
			{
				$day=0;
				$month++;
				if($month==13)
				{
					$month="1";
					$year++;
				}
			}

		}
		return $lastdatep-1;
	}
    
	//translate number of month to name of month
	function monthname($month)
	{

                if($month=="01") return "فروردین";

                if($month=="02") return "اردیبهشت";

                if($month=="03") return "خرداد";

                if($month=="04") return  "تیر";

                if($month=="05") return "مرداد";

                if($month=="06") return "شهریور";

                if($month=="07") return "مهر";

                if($month=="08") return "آبان";

                if($month=="09") return "آذر";

                if($month=="10") return "دی";

                if($month=="11") return "بهمن";

                if($month=="12") return "اسفند";
	}

	////here convert to  number in persian
	function Convertnumber2farsi($srting)
	{
                $num0="۰";
                $num1="۱";
                $num2="۲";
                $num3="۳";
                $num4="۴";
                $num5="۵";
                $num6="۶";
                $num7="۷";
                $num8="۸";
                $num9="۹";
		$stringtemp="";
		$len=strlen($srting);
		for($sub=0;$sub<$len;$sub++)
		{
			if(substr($srting,$sub,1)=="0")$stringtemp.=$num0;
			elseif(substr($srting,$sub,1)=="1")$stringtemp.=$num1;
			elseif(substr($srting,$sub,1)=="2")$stringtemp.=$num2;
			elseif(substr($srting,$sub,1)=="3")$stringtemp.=$num3;
			elseif(substr($srting,$sub,1)=="4")$stringtemp.=$num4;
			elseif(substr($srting,$sub,1)=="5")$stringtemp.=$num5;
			elseif(substr($srting,$sub,1)=="6")$stringtemp.=$num6;
			elseif(substr($srting,$sub,1)=="7")$stringtemp.=$num7;
			elseif(substr($srting,$sub,1)=="8")$stringtemp.=$num8;
			elseif(substr($srting,$sub,1)=="9")$stringtemp.=$num9;
			else $stringtemp.=substr($srting,$sub,1);

		}
		return   $stringtemp;

	}///end conver to number in persian






	function div($a,$b) {
		return (int) ($a / $b);
	}

	function gregorian_to_jalali ($g_y, $g_m, $g_d)
	{
		$g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		$j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);

		$gy = $g_y-1600;
		$gm = $g_m-1;
		$gd = $g_d-1;

		$g_day_no = 365*$gy+$this->div($gy+3,4)-$this->div($gy+99,100)+$this->div($gy+399,400);

		for ($i=0; $i < $gm; ++$i)
		$g_day_no += $g_days_in_month[$i];
		if ($gm>1 && (($gy%4==0 && $gy%100!=0) || ($gy%400==0)))
		/* leap and after Feb */
		$g_day_no++;
		$g_day_no += $gd;

		$j_day_no = $g_day_no-79;

		$j_np = $this->div($j_day_no, 12053); /* 12053 = 365*33 + 32/4 */
		$j_day_no = $j_day_no % 12053;

		$jy = 979+33*$j_np+4*$this->div($j_day_no,1461); /* 1461 = 365*4 + 4/4 */

		$j_day_no %= 1461;

		if ($j_day_no >= 366) {
			$jy += $this->div($j_day_no-1, 365);
			$j_day_no = ($j_day_no-1)%365;
		}

		for ($i = 0; $i < 11 && $j_day_no >= $j_days_in_month[$i]; ++$i)
		$j_day_no -= $j_days_in_month[$i];
		$jm = $i+1;
		$jd = $j_day_no+1;

		return array($jy, $jm, $jd);
	}

	function jalali_to_gregorian($j_y, $j_m, $j_d)
	{
		$g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		$j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);

		$jy = $j_y-979;
		$jm = $j_m-1;
		$jd = $j_d-1;

		$j_day_no = 365*$jy + $this->div($jy, 33)*8 + $this->div($jy%33+3, 4);
		for ($i=0; $i < $jm; ++$i)
		$j_day_no += $j_days_in_month[$i];

		$j_day_no += $jd;

		$g_day_no = $j_day_no+79;

		$gy = 1600 + 400*$this->div($g_day_no, 146097); /* 146097 = 365*400 + 400/4 - 400/100 + 400/400 */
		$g_day_no = $g_day_no % 146097;

		$leap = true;
		if ($g_day_no >= 36525) /* 36525 = 365*100 + 100/4 */
		{
			$g_day_no--;
			$gy += 100*$this->div($g_day_no,  36524); /* 36524 = 365*100 + 100/4 - 100/100 */
			$g_day_no = $g_day_no % 36524;

			if ($g_day_no >= 365)
			$g_day_no++;
			else
			$leap = false;
		}

		$gy += 4*$this->div($g_day_no, 1461); /* 1461 = 365*4 + 4/4 */
		$g_day_no %= 1461;

		if ($g_day_no >= 366) {
			$leap = false;

			$g_day_no--;
			$gy += $this->div($g_day_no, 365);
			$g_day_no = $g_day_no % 365;
		}

		for ($i = 0; $g_day_no >= $g_days_in_month[$i] + ($i == 1 && $leap); $i++)
		$g_day_no -= $g_days_in_month[$i] + ($i == 1 && $leap);
		$gm = $i+1;
		$gd = $g_day_no+1;

		return array($gy, $gm, $gd);
	}
    
    function diff($format, $f,$l=null)
    {
        //"Years:%Y,Months:%M,Days:%d,Hours:%H,Minutes:%i,Seconds:%s"
        $f = $this->pdate_format_reverse($f);
        $l = $l? $this->pdate_format_reverse($l) : date('Y-m-d');
        $intervalo = date_diff(date_create($f), date_create($l));
        return $intervalo->format($format);
    }

}



?>

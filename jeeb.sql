SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `jeeb`
--

DELIMITER $$
--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `gdate`(`jy` smallint, `jm` smallint, `jd` smallint ) RETURNS datetime
BEGIN
	DECLARE
		i,j,e,k,mo,
		gy, gm, gd,
		g_day_no,j_day_no, bkab,jmm, mday,g_day_mo
    INT DEFAULT 0;
	DECLARE resout char(100);
  DECLARE fdate datetime;
  SET bkab = __mymod(jy,33);

  IF (bkab = 1 or bkab= 5 or bkab = 9 or bkab = 13 or bkab = 17 or bkab = 22 or bkab = 26 or bkab = 30) THEN
    SET j=1;
  end IF;

	CASE jm
		WHEN 1 THEN IF jd > _jdmarray2(jm) or jd <= 0 THEN SET e=1; end IF;
		WHEN 2 THEN IF jd > _jdmarray2(jm) or jd <= 0 THEN SET e=1; end IF;
		WHEN 3 THEN IF jd > _jdmarray2(jm) or jd <= 0 THEN SET e=1; end IF;
		WHEN 4 THEN IF jd > _jdmarray2(jm) or jd <= 0 THEN SET e=1; end IF;
		WHEN 5 THEN IF jd > _jdmarray2(jm) or jd <= 0 THEN SET e=1; end IF;
		WHEN 6 THEN IF jd > _jdmarray2(jm) or jd <= 0 THEN SET e=1; end IF;
		WHEN 7 THEN IF jd > _jdmarray2(jm) or jd <= 0 THEN SET e=1; end IF;
		WHEN 8 THEN IF jd > _jdmarray2(jm) or jd <= 0 THEN SET e=1; end IF;
		WHEN 9 THEN IF jd > _jdmarray2(jm) or jd <= 0 THEN SET e=1; end IF;
		WHEN 10 THEN IF jd > _jdmarray2(jm) or jd <= 0 THEN SET e=1; end IF;
		WHEN 11 THEN IF jd > _jdmarray2(jm) or jd <= 0 THEN SET e=1; end IF;
		WHEN 12 THEN IF jd > _jdmarray2(jm)+j or jd <= 0 THEN SET e=1; end IF;
	END CASE;
  IF jm > 12 or jm <= 0 THEN SET e=1; end IF;
  IF jy <= 0 THEN SET e=1; end IF;

  IF e>0 THEN
    RETURN 0;
  end IF;

  IF (jm>=11) or (jm=10 and jd>=11) THEN
    SET i=1;
  end IF;
  SET gy = jy + 621 + i;

  IF (__mymod(gy-1,4)=0 and __mymod(gy-1,100)<>0) or (__mymod(gy-1,400)=0) THEN
    SET k=1;
  end IF;

  SET jmm=jm-1;

  WHILE (jmm > 0) do
    SET mday=mday+_jdmarray2(jmm);
    SET jmm=jmm-1;
  end WHILE;

  SET j_day_no=(jy-1)*365+(__mydiv(jy,4))+mday+jd;
  SET g_day_no=j_day_no+226899;

  SET g_day_no=g_day_no-(__mydiv(gy-1,4));
  SET g_day_mo=__mymod(g_day_no,365);

  SET mo=0;
  SET gm=gm+1;
  while g_day_mo>_gdmarray(mo) do
    SET g_day_mo=g_day_mo-_gdmarray(mo);
    SET mo=mo+1;
    SET gm=gm+1;
  end WHILE;
  SET gd=g_day_mo;

  RETURN CONCAT_WS('-',gy,gm,gd);

END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `gdatestr`(`jdat` char(10) ) RETURNS datetime
BEGIN
	DECLARE
		i,j,e,k,mo,
		gy, gm, gd,
		g_day_no,j_day_no, bkab,jmm, mday,g_day_mo, jd, jy, jm
    INT DEFAULT 0;
	DECLARE resout char(100);
	DECLARE jdd, jyd, jmd, jt varchar(100);
  DECLARE fdate datetime;

	SET jdd = SUBSTRING_INDEX(jdat,'/',-1);
	SET jt = SUBSTRING_INDEX(jdat,'/',2);
	SET jyd = SUBSTRING_INDEX(jt,'/',1);
	SET jmd = SUBSTRING_INDEX(jt,'/',-1);
	
	SET jd = CAST(jdd as SIGNED);
	SET jy = CAST(jyd as SIGNED);
	SET jm = CAST(jmd as SIGNED);

  SET bkab = __mymod(jy,33);

  IF (bkab = 1 or bkab= 5 or bkab = 9 or bkab = 13 or bkab = 17 or bkab = 22 or bkab = 26 or bkab = 30) THEN
    SET j=1;
  end IF;

	CASE jm
		WHEN 1 THEN IF jd > _jdmarray2(jm) or jd <= 0 THEN SET e=1; end IF;
		WHEN 2 THEN IF jd > _jdmarray2(jm) or jd <= 0 THEN SET e=1; end IF;
		WHEN 3 THEN IF jd > _jdmarray2(jm) or jd <= 0 THEN SET e=1; end IF;
		WHEN 4 THEN IF jd > _jdmarray2(jm) or jd <= 0 THEN SET e=1; end IF;
		WHEN 5 THEN IF jd > _jdmarray2(jm) or jd <= 0 THEN SET e=1; end IF;
		WHEN 6 THEN IF jd > _jdmarray2(jm) or jd <= 0 THEN SET e=1; end IF;
		WHEN 7 THEN IF jd > _jdmarray2(jm) or jd <= 0 THEN SET e=1; end IF;
		WHEN 8 THEN IF jd > _jdmarray2(jm) or jd <= 0 THEN SET e=1; end IF;
		WHEN 9 THEN IF jd > _jdmarray2(jm) or jd <= 0 THEN SET e=1; end IF;
		WHEN 10 THEN IF jd > _jdmarray2(jm) or jd <= 0 THEN SET e=1; end IF;
		WHEN 11 THEN IF jd > _jdmarray2(jm) or jd <= 0 THEN SET e=1; end IF;
		WHEN 12 THEN IF jd > _jdmarray2(jm)+j or jd <= 0 THEN SET e=1; end IF;
	END CASE;
  IF jm > 12 or jm <= 0 THEN SET e=1; end IF;
  IF jy <= 0 THEN SET e=1; end IF;

  IF e>0 THEN
    RETURN 0;
  end IF;

  IF (jm>=11) or (jm=10 and jd>=11) THEN
    SET i=1;
  end IF;
  SET gy = jy + 621 + i;

  IF (__mymod(gy-1,4)=0 and __mymod(gy-1,100)<>0) or (__mymod(gy-1,400)=0) THEN
    SET k=1;
  end IF;

  SET jmm=jm-1;

  WHILE (jmm > 0) do
    SET mday=mday+_jdmarray2(jmm);
    SET jmm=jmm-1;
  end WHILE;

  SET j_day_no=(jy-1)*365+(__mydiv(jy,4))+mday+jd;
  SET g_day_no=j_day_no+226899;

  SET g_day_no=g_day_no-(__mydiv(gy-1,4));
  SET g_day_mo=__mymod(g_day_no,365);

  SET mo=0;
  SET gm=gm+1;
  while g_day_mo>_gdmarray(mo) do
    SET g_day_mo=g_day_mo-_gdmarray(mo);
    SET mo=mo+1;
    SET gm=gm+1;
  end WHILE;
  SET gd=g_day_mo;

  RETURN CONCAT_WS('-',gy,gm,gd);

END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `pdate`(`gdate` datetime) RETURNS char(100) CHARSET utf8
BEGIN
	DECLARE 
		i,
		gy, gm, gd,
		g_day_no,j_day_no, j_np,
		jy,jm,jd INT DEFAULT 0;
	DECLARE resout char(100);
	DECLARE ttime CHAR(20);

	SET gy = YEAR(gdate)-1600;
	SET gm = MONTH(gdate)-1;
	SET gd = DAY(gdate)-1;
	SET ttime = TIME(gdate);
	SET g_day_no = ((365 *  gy) + __mydiv( gy+3, 4 ) - __mydiv( gy+99 , 100 )+ __mydiv ( gy+399, 400 ) );
        SET i = 0;

	WHILE (i < gm) do
		SET  g_day_no = g_day_no + _gdmarray(i);
		SET i = i+1; 
	end WHILE;

	if  gm > 1 and (( gy% 4 = 0 and gy%100 <> 0 )) or gy % 400 = 0 THEN 
		SET 	g_day_no =	g_day_no +1;
	end IF;
	
	SET g_day_no = g_day_no + gd; 

	SET j_day_no = g_day_no -79;
	SET j_np =  j_day_no DIV 12053;
	set j_day_no = j_day_no % 12053;
	SET jy = 979 + 33 * j_np + 4 * __mydiv(j_day_no,1461);
	SET j_day_no = j_day_no % 1461;

	if j_day_no >= 366 then 
		SET jy = jy + __mydiv(j_day_no-1, 365);
		SET j_day_no =( j_day_no-1) % 365;
	end if;

	SET i = 0;

	WHILE ( i < 11 and j_day_no >= _jdmarray(i) ) do
		SET  j_day_no = j_day_no -  _jdmarray(i);
		SET i = i+1;
	end WHILE;

	SET jm = i+1;
	SET jd = j_day_no+1;
     	SET resout = CONCAT_WS ('-',jy,jm,jd);

	if (ttime <> '00:00:00' ) then
		SET resout = CONCAT_WS(' ',resout,ttime);
	END IF;

	RETURN  	resout;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `pday`(`gdate` datetime) RETURNS char(100) CHARSET utf8
BEGIN
	DECLARE
		i,
		gy, gm, gd,
		g_day_no,j_day_no, j_np,
		jy,jm,jd INT DEFAULT 0;
	DECLARE resout char(100);
	DECLARE ttime CHAR(20);

	SET gy = YEAR(gdate)-1600;
	SET gm = MONTH(gdate)-1;
	SET gd = DAY(gdate)-1;
	SET ttime = TIME(gdate);
	SET g_day_no = ((365 *  gy) + __mydiv( gy+3, 4 ) - __mydiv( gy+99 , 100 )+ __mydiv ( gy+399, 400 ) );
        SET i = 0;

	WHILE (i < gm) do
		SET  g_day_no = g_day_no + _gdmarray(i);
		SET i = i+1;
	end WHILE;

	if  gm > 1 and (( gy% 4 = 0 and gy%100 <> 0 )) or gy % 400 = 0 THEN
		SET 	g_day_no =	g_day_no +1;
	end IF;
	
	SET g_day_no = g_day_no + gd;

	SET j_day_no = g_day_no -79;
	SET j_np =  j_day_no DIV 12053;
	set j_day_no = j_day_no % 12053;
	SET jy = 979 + 33 * j_np + 4 * __mydiv(j_day_no,1461);
	SET j_day_no = j_day_no % 1461;

	if j_day_no >= 366 then
		SET jy = jy + __mydiv(j_day_no-1, 365);
		SET j_day_no =( j_day_no-1) % 365;
	end if;

	SET i = 0;

	WHILE ( i < 11 and j_day_no >= _jdmarray(i) ) do
		SET  j_day_no = j_day_no -  _jdmarray(i);
		SET i = i+1;
	end WHILE;

	SET jm = i+1;
	SET jd = j_day_no+1;
	RETURN  	jd;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `PMONTH`(`gdate` datetime) RETURNS char(100) CHARSET utf8
BEGIN
	DECLARE 
		i,
		gy, gm, gd,
		g_day_no,j_day_no, j_np,
		jy,jm,jd INT DEFAULT 0;
	DECLARE resout char(100);
	DECLARE ttime CHAR(20);

	SET gy = YEAR(gdate)-1600;
	SET gm = MONTH(gdate)-1;
	SET gd = DAY(gdate)-1;
	SET ttime = TIME(gdate);
	SET g_day_no = ((365 *  gy) + __mydiv( gy+3, 4 ) - __mydiv( gy+99 , 100 )+ __mydiv ( gy+399, 400 ) );
        SET i = 0;

	WHILE (i < gm) do
		SET  g_day_no = g_day_no + _gdmarray(i);
		SET i = i+1; 
	end WHILE;

	if  gm > 1 and (( gy% 4 = 0 and gy%100 <> 0 )) or gy % 400 = 0 THEN 
		SET 	g_day_no =	g_day_no +1;
	end IF;
	
	SET g_day_no = g_day_no + gd; 

	SET j_day_no = g_day_no -79;
	SET j_np =  j_day_no DIV 12053;
	set j_day_no = j_day_no % 12053;
	SET jy = 979 + 33 * j_np + 4 * __mydiv(j_day_no,1461);
	SET j_day_no = j_day_no % 1461;

	if j_day_no >= 366 then 
		SET jy = jy + __mydiv(j_day_no-1, 365);
		SET j_day_no =( j_day_no-1) % 365;
	end if;

	SET i = 0;

	WHILE ( i < 11 and j_day_no >= _jdmarray(i) ) do
		SET  j_day_no = j_day_no -  _jdmarray(i);
		SET i = i+1;
	end WHILE;

	SET jm = i+1;
	SET jd = j_day_no+1;
	RETURN  	jm;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `pmonthname`(`gdate` datetime) RETURNS varchar(100) CHARSET utf8
BEGIN

CASE PMONTH(gdate)
	WHEN 1 THEN 	RETURN 'فروردين';
	WHEN 2 THEN RETURN 'ارديبهشت';
	WHEN 3 THEN	RETURN 'خرداد';
	WHEN 4 THEN	RETURN 'تير';
	WHEN 5 THEN	RETURN 'مرداد';
	WHEN 6 THEN	 RETURN 'شهريور';
	WHEN 7 THEN	RETURN 'مهر';
	WHEN 8 THEN	RETURN 'آبان';
	WHEN 9 THEN	RETURN 'آذر';
	WHEN 10 THEN RETURN	'دي';
	WHEN 11 THEN RETURN	'بهمن';
	WHEN 12 THEN RETURN	'اسفند';
end CASE;


END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `pmonthnamebynumber`(`number` int) RETURNS varchar(100) CHARSET utf8
BEGIN

CASE number
	WHEN 1 THEN 	RETURN 'فروردين';
	WHEN 2 THEN     RETURN 'ارديبهشت';
	WHEN 3 THEN	RETURN 'خرداد';
	WHEN 4 THEN	RETURN 'تير';
	WHEN 5 THEN	RETURN 'مرداد';
	WHEN 6 THEN	RETURN 'شهريور';
	WHEN 7 THEN	RETURN 'مهر';
	WHEN 8 THEN	RETURN 'آبان';
	WHEN 9 THEN	RETURN 'آذر';
	WHEN 10 THEN    RETURN	'دي';
	WHEN 11 THEN    RETURN	'بهمن';
	WHEN 12 THEN    RETURN	'اسفند';
end CASE;


END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `pyear`(`gdate` datetime) RETURNS char(100) CHARSET utf8
BEGIN
	DECLARE
		i,
		gy, gm, gd,
		g_day_no,j_day_no, j_np,
		jy,jm,jd INT DEFAULT 0;
	DECLARE resout char(100);
	DECLARE ttime CHAR(20);

	SET gy = YEAR(gdate)-1600;
	SET gm = MONTH(gdate)-1;
	SET gd = DAY(gdate)-1;
	SET ttime = TIME(gdate);
	SET g_day_no = ((365 *  gy) + __mydiv( gy+3, 4 ) - __mydiv( gy+99 , 100 )+ __mydiv ( gy+399, 400 ) );
        SET i = 0;

	WHILE (i < gm) do
		SET  g_day_no = g_day_no + _gdmarray(i);
		SET i = i+1;
	end WHILE;

	if  gm > 1 and (( gy% 4 = 0 and gy%100 <> 0 )) or gy % 400 = 0 THEN
		SET 	g_day_no =	g_day_no +1;
	end IF;
	
	SET g_day_no = g_day_no + gd;

	SET j_day_no = g_day_no -79;
	SET j_np =  j_day_no DIV 12053;
	set j_day_no = j_day_no % 12053;
	SET jy = 979 + 33 * j_np + 4 * __mydiv(j_day_no,1461);
	SET j_day_no = j_day_no % 1461;

	if j_day_no >= 366 then
		SET jy = jy + __mydiv(j_day_no-1, 365);
		SET j_day_no =( j_day_no-1) % 365;
	end if;

	SET i = 0;

	WHILE ( i < 11 and j_day_no >= _jdmarray(i) ) do
		SET  j_day_no = j_day_no -  _jdmarray(i);
		SET i = i+1;
	end WHILE;

	SET jm = i+1;
	SET jd = j_day_no+1;
	RETURN  	jy;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_gdmarray`(`m` smallint) RETURNS smallint(2)
BEGIN
	CASE m
		WHEN 0 THEN RETURN 31;
		WHEN 1 THEN RETURN 28;
		WHEN 2 THEN RETURN 31;
		WHEN 3 THEN RETURN 30;
		WHEN 4 THEN RETURN 31;
		WHEN 5 THEN RETURN 30;
		WHEN 6 THEN RETURN 31;
		WHEN 7 THEN RETURN 31;
		WHEN 8 THEN RETURN 30;
		WHEN 9 THEN RETURN 31;
		WHEN 10 THEN RETURN 30;
		WHEN 11 THEN RETURN 31;
	END CASE;
   

END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_jdmarray`(`m` smallint) RETURNS smallint(2)
BEGIN
	CASE m
		WHEN 0 THEN RETURN 31;
		WHEN 1 THEN RETURN 31;
		WHEN 2 THEN RETURN 31;
		WHEN 3 THEN RETURN 31;
		WHEN 4 THEN RETURN 31;
		WHEN 5 THEN RETURN 31;
		WHEN 6 THEN RETURN 30;
		WHEN 7 THEN RETURN 30;
		WHEN 8 THEN RETURN 30;
		WHEN 9 THEN RETURN 30;
		WHEN 10 THEN RETURN 30;
		WHEN 11 THEN RETURN 29;
	END CASE;
   

END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_jdmarray2`(`m` smallint) RETURNS smallint(2)
BEGIN
	CASE m
		WHEN 1 THEN RETURN 31;
		WHEN 2 THEN RETURN 31;
		WHEN 3 THEN RETURN 31;
		WHEN 4 THEN RETURN 31;
		WHEN 5 THEN RETURN 31;
		WHEN 6 THEN RETURN 31;
		WHEN 7 THEN RETURN 30;
		WHEN 8 THEN RETURN 30;
		WHEN 9 THEN RETURN 30;
		WHEN 10 THEN RETURN 30;
		WHEN 11 THEN RETURN 30;
		WHEN 12 THEN RETURN 29;
	END CASE;
   

END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `__mydiv`(`a` int,`b` int) RETURNS int(11)
BEGIN
 return a DIV b;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `__mymod`(`a` int,`b` int) RETURNS int(11)
BEGIN
  return (a - b * (a DIV b));
END$$

DELIMITER ;

USE jeeb;

--
-- Table structure for table `accounts`
--

CREATE TABLE IF NOT EXISTS `accounts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_persian_ci NOT NULL,
  `balance` bigint(20) NOT NULL,
  `init_balance` bigint(20) NOT NULL,
  `description` varchar(512) COLLATE utf8_persian_ci DEFAULT NULL,
  `type` enum('cash','deposit','check','other') COLLATE utf8_persian_ci NOT NULL DEFAULT 'cash',
  `status` enum('active','inactive') COLLATE utf8_persian_ci NOT NULL DEFAULT 'active',
  `bank_id` int(11) unsigned DEFAULT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `sort` int(2) NOT NULL DEFAULT '0',
  `delete` enum('yes','no') COLLATE utf8_persian_ci NOT NULL DEFAULT 'yes',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `deleted` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_ACCOUNT_USER` (`user_id`),
  KEY `FK_ACCOUNT_BANK` (`bank_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `acos`
--

CREATE TABLE IF NOT EXISTS `acos` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) DEFAULT NULL,
  `model` varchar(255) COLLATE utf8_persian_ci DEFAULT NULL,
  `foreign_key` int(10) DEFAULT NULL,
  `alias` varchar(255) COLLATE utf8_persian_ci DEFAULT NULL,
  `lft` int(10) DEFAULT NULL,
  `rght` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `foreign_key` (`foreign_key`),
  KEY `foreign_key_2` (`foreign_key`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE IF NOT EXISTS `applications` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_code` varchar(255) COLLATE utf8_persian_ci DEFAULT NULL,
  `activation_code` varchar(255) COLLATE utf8_persian_ci DEFAULT NULL,
  `count` int(11) NOT NULL DEFAULT '0',
  `amount` int(11) unsigned NOT NULL DEFAULT '0',
  `is_sold` enum('yes','no') COLLATE utf8_persian_ci NOT NULL DEFAULT 'no',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_code` (`user_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `application_orders`
--

CREATE TABLE IF NOT EXISTS `application_orders` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8_persian_ci NOT NULL,
  `plan` varchar(255) COLLATE utf8_persian_ci NOT NULL,
  `amount` int(20) NOT NULL,
  `application_id` int(11) unsigned DEFAULT NULL,
  `discount_code_id` int(11) DEFAULT NULL,
  `authority` varchar(32) COLLATE utf8_persian_ci DEFAULT NULL,
  `status` int(20) DEFAULT NULL,
  `result` enum('pending','success','fail') COLLATE utf8_persian_ci NOT NULL DEFAULT 'pending',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_APPLICATION_ORDER_APPLICATION` (`application_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `aros`
--

CREATE TABLE IF NOT EXISTS `aros` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) DEFAULT NULL,
  `model` varchar(255) COLLATE utf8_persian_ci DEFAULT NULL,
  `foreign_key` int(10) DEFAULT NULL,
  `alias` varchar(255) COLLATE utf8_persian_ci DEFAULT NULL,
  `lft` int(10) DEFAULT NULL,
  `rght` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `foreign_key` (`foreign_key`),
  KEY `foreign_key_2` (`foreign_key`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `aros_acos`
--

CREATE TABLE IF NOT EXISTS `aros_acos` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `aro_id` int(10) NOT NULL,
  `aco_id` int(10) NOT NULL,
  `_create` varchar(2) COLLATE utf8_persian_ci NOT NULL DEFAULT '0',
  `_read` varchar(2) COLLATE utf8_persian_ci NOT NULL DEFAULT '0',
  `_update` varchar(2) COLLATE utf8_persian_ci NOT NULL DEFAULT '0',
  `_delete` varchar(2) COLLATE utf8_persian_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ARO_ACO_KEY` (`aro_id`,`aco_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `banks`
--

CREATE TABLE IF NOT EXISTS `banks` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_persian_ci NOT NULL,
  `description` text COLLATE utf8_persian_ci,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `budgets`
--

CREATE TABLE IF NOT EXISTS `budgets` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `expense_category_id` int(11) unsigned NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `pyear` int(4) NOT NULL,
  `pmonth` int(2) NOT NULL,
  `amount` bigint(20) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `expense_category_id` (`expense_category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Triggers `budgets`
--
DROP TRIGGER IF EXISTS `trigger_before_insert_budgets`;
DELIMITER //
CREATE TRIGGER `trigger_before_insert_budgets` BEFORE INSERT ON `budgets`
 FOR EACH ROW BEGIN
        SET NEW.`pyear` = PYEAR(NEW.`start_date`), NEW.`pmonth` = PMONTH(NEW.`start_date`);
    END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `trigger_before_update_budgets`;
DELIMITER //
CREATE TRIGGER `trigger_before_update_budgets` BEFORE UPDATE ON `budgets`
 FOR EACH ROW BEGIN
        IF NEW.start_date <> OLD.start_date THEN
            SET NEW.`pyear` = PYEAR(NEW.`start_date`), NEW.`pmonth` = PMONTH(NEW.`start_date`);
        END IF;
    END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `cake_sessions`
--

CREATE TABLE IF NOT EXISTS `cake_sessions` (
  `id` varchar(255) NOT NULL,
  `data` text,
  `expires` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `checks`
--

CREATE TABLE IF NOT EXISTS `checks` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `amount` bigint(20) NOT NULL,
  `due_date` date NOT NULL,
  `pyear` int(4) NOT NULL,
  `pmonth` int(2) NOT NULL,
  `serial` varchar(255) COLLATE utf8_persian_ci DEFAULT NULL,
  `description` text COLLATE utf8_persian_ci,
  `type` enum('drawed','received') COLLATE utf8_persian_ci NOT NULL DEFAULT 'drawed',
  `status` enum('due','done','ignore') COLLATE utf8_persian_ci NOT NULL DEFAULT 'due',
  `notify` enum('yes','no') COLLATE utf8_persian_ci NOT NULL DEFAULT 'yes',
  `account_id` int(11) unsigned DEFAULT NULL,
  `bank_id` int(11) unsigned DEFAULT NULL,
  `individual_id` int(11) unsigned DEFAULT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `clear_transaction_id` int(11) unsigned DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_CHECK_USER` (`user_id`),
  KEY `FK_CHECK_BANK` (`bank_id`),
  KEY `FK_CHECK_ACCOUNT` (`account_id`),
  KEY `FK_CHECK_INDIVIDUAL` (`individual_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `check_tags`
--

CREATE TABLE IF NOT EXISTS `check_tags` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `check_id` int(11) unsigned NOT NULL,
  `tag_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `tag_id` (`tag_id`),
  KEY `check_id` (`check_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `configs`
--

CREATE TABLE IF NOT EXISTS `configs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `key` varchar(32) COLLATE utf8_persian_ci NOT NULL,
  `value` varchar(256) COLLATE utf8_persian_ci DEFAULT NULL,
  `type` enum('boolean','integer','double','string','array','object','resource') COLLATE utf8_persian_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`key`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `currencies`
--

CREATE TABLE IF NOT EXISTS `currencies` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_persian_ci NOT NULL,
  `persian_name` varchar(255) COLLATE utf8_persian_ci NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `currency_rates`
--

CREATE TABLE IF NOT EXISTS `currency_rates` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `currency_id` int(11) unsigned NOT NULL,
  `current` decimal(10,2) unsigned DEFAULT NULL,
  `min` decimal(10,2) unsigned DEFAULT NULL,
  `max` decimal(10,2) unsigned DEFAULT NULL,
  `average` decimal(10,2) unsigned DEFAULT NULL,
  `count` int(5) unsigned DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_CURRENCY_RATE_CURRENCY` (`currency_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `debts`
--

CREATE TABLE IF NOT EXISTS `debts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_persian_ci NOT NULL,
  `amount` bigint(20) NOT NULL,
  `due_date` date NOT NULL,
  `pyear` int(4) NOT NULL,
  `pmonth` int(2) NOT NULL,
  `type` enum('debt','credit') COLLATE utf8_persian_ci NOT NULL DEFAULT 'debt',
  `status` enum('due','part','done') COLLATE utf8_persian_ci NOT NULL DEFAULT 'due',
  `notify` enum('yes','no') COLLATE utf8_persian_ci NOT NULL DEFAULT 'yes',
  `individual_id` int(11) unsigned DEFAULT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `transaction_id` int(11) unsigned DEFAULT NULL,
  `clear_transaction_id` int(11) unsigned DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_DEBT_USER` (`user_id`),
  KEY `FK_DEBT_INDIVIDUAL` (`individual_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `debt_settlements`
--

CREATE TABLE IF NOT EXISTS `debt_settlements` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `amount` bigint(20) DEFAULT NULL,
  `debt_id` int(11) unsigned NOT NULL,
  `transaction_id` int(11) unsigned DEFAULT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_DEBT_SETTLEMENT_DEBT` (`debt_id`),
  KEY `FK_DEBT_SETTLEMENT_TRANSACTION` (`transaction_id`),
  KEY `FK_DEBT_SETTLEMENT_USER` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `debt_tags`
--

CREATE TABLE IF NOT EXISTS `debt_tags` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `debt_id` int(11) unsigned NOT NULL,
  `tag_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `debt_id` (`debt_id`),
  KEY `tag_id` (`tag_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `discount_codes`
--

CREATE TABLE IF NOT EXISTS `discount_codes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8_persian_ci NOT NULL,
  `type` enum('user','package','windows') COLLATE utf8_persian_ci NOT NULL,
  `amount` int(20) DEFAULT NULL,
  `valid_plan` varchar(255) COLLATE utf8_persian_ci DEFAULT NULL,
  `used` int(11) unsigned NOT NULL DEFAULT '0',
  `count` int(11) unsigned NOT NULL DEFAULT '1',
  `expire_date` date DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE IF NOT EXISTS `expenses` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `transaction_id` int(11) unsigned NOT NULL,
  `description` text COLLATE utf8_persian_ci,
  `user_id` int(11) unsigned NOT NULL,
  `expense_category_id` int(11) unsigned NOT NULL,
  `expense_sub_category_id` int(11) unsigned DEFAULT NULL,
  `individual_id` int(11) unsigned DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_EXPENSE_EXPENSE_CATEGORY` (`expense_category_id`),
  KEY `FK_EXPENSE_EXPENSE_SUB_CATEGORY` (`expense_sub_category_id`),
  KEY `FK_EXPENSE_USER` (`user_id`),
  KEY `FK_EXPENSE_TRANSACTION` (`transaction_id`),
  KEY `FK_EXPENSE_INDIVIDUAL` (`individual_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expense_categories`
--

CREATE TABLE IF NOT EXISTS `expense_categories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_persian_ci NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `delete` enum('yes','no') COLLATE utf8_persian_ci NOT NULL DEFAULT 'yes',
  `sort` int(2) NOT NULL DEFAULT '0',
  `status` enum('active','inactive') COLLATE utf8_persian_ci NOT NULL DEFAULT 'active',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_EXPENSE_CATEGORY_USER` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expense_sub_categories`
--

CREATE TABLE IF NOT EXISTS `expense_sub_categories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_persian_ci NOT NULL,
  `expense_category_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_EXPENSE_SUB_CATEGORY_EXPENSE_CATEGORY` (`expense_category_id`),
  KEY `FK_EXPENSE_SUB_CATEGORY_USER` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expense_tags`
--

CREATE TABLE IF NOT EXISTS `expense_tags` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `expense_id` int(11) unsigned NOT NULL,
  `tag_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_EXPENSE_TAG_EXPENSE` (`expense_id`),
  KEY `FK_EXPENSE_TAG_TAG` (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `incomes`
--

CREATE TABLE IF NOT EXISTS `incomes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `transaction_id` int(11) unsigned NOT NULL,
  `description` text COLLATE utf8_persian_ci,
  `user_id` int(11) unsigned NOT NULL,
  `income_type_id` int(11) unsigned DEFAULT NULL,
  `income_sub_type_id` int(11) unsigned DEFAULT NULL,
  `individual_id` int(11) unsigned DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_INCOME_USER` (`user_id`),
  KEY `FK_INCOME_INCOME_TYPE` (`income_type_id`),
  KEY `FK_INCOME_TRANSACTION` (`transaction_id`),
  KEY `FK_INCOME_INCOME_SUB_TYPE` (`income_sub_type_id`),
  KEY `FK_INCOME_INDIVIDUAL` (`individual_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `income_sub_types`
--

CREATE TABLE IF NOT EXISTS `income_sub_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_persian_ci NOT NULL,
  `income_type_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_INCOME_SUB_TYPE_INCOME_TYPE` (`income_type_id`),
  KEY `FK_INCOME_SUB_TYPE_USER` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `income_types`
--

CREATE TABLE IF NOT EXISTS `income_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_persian_ci NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `delete` enum('yes','no') COLLATE utf8_persian_ci NOT NULL DEFAULT 'yes',
  `sort` int(2) NOT NULL DEFAULT '0',
  `status` enum('active','inactive') COLLATE utf8_persian_ci NOT NULL DEFAULT 'active',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_INCOME_TYPE_USER` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `individuals`
--

CREATE TABLE IF NOT EXISTS `individuals` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_persian_ci NOT NULL,
  `description` text COLLATE utf8_persian_ci,
  `user_id` int(11) unsigned NOT NULL,
  `sort` int(2) NOT NULL DEFAULT '0',
  `status` enum('active','inactive') COLLATE utf8_persian_ci NOT NULL DEFAULT 'active',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_INDIVIDUAL_USER` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `installments`
--

CREATE TABLE IF NOT EXISTS `installments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `amount` bigint(20) NOT NULL,
  `due_date` date NOT NULL,
  `pyear` int(4) DEFAULT NULL,
  `pmonth` int(2) DEFAULT NULL,
  `pday` int(2) DEFAULT NULL,
  `status` enum('due','done') COLLATE utf8_persian_ci NOT NULL DEFAULT 'due',
  `notify` enum('yes','no') COLLATE utf8_persian_ci NOT NULL DEFAULT 'yes',
  `loan_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `description` text COLLATE utf8_persian_ci,
  `clear_transaction_id` int(11) unsigned DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_INSTALLMENT_USER` (`user_id`),
  KEY `FK_INSTALLMENT_LOAN` (`loan_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

--
-- Triggers `installments`
--
DROP TRIGGER IF EXISTS `trigger_before_insert_installment`;
DELIMITER //
CREATE TRIGGER `trigger_before_insert_installment` BEFORE INSERT ON `installments`
 FOR EACH ROW BEGIN
        SET NEW.`pyear` = PYEAR(NEW.`due_date`), NEW.`pmonth` = PMONTH(NEW.`due_date`), NEW.`pday` = PDAY(NEW.`due_date`);
    END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `trigger_before_update_installment`;
DELIMITER //
CREATE TRIGGER `trigger_before_update_installment` BEFORE UPDATE ON `installments`
 FOR EACH ROW BEGIN
        IF NEW.due_date <> OLD.due_date THEN
            SET NEW.`pyear` = PYEAR(NEW.`due_date`), NEW.`pmonth` = PMONTH(NEW.`due_date`), NEW.`pday` = PDAY(NEW.`due_date`);
        END IF;
    END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `investments`
--

CREATE TABLE IF NOT EXISTS `investments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_persian_ci NOT NULL,
  `amount` bigint(20) NOT NULL,
  `date` date NOT NULL,
  `currency_id` int(11) unsigned DEFAULT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_INVESTMENT_USER` (`user_id`),
  KEY `FK_INVESTMENT_CURRENCY` (`currency_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invitations`
--

CREATE TABLE IF NOT EXISTS `invitations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8_persian_ci NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `invited` enum('yes','no') COLLATE utf8_persian_ci NOT NULL DEFAULT 'no',
  `notifications` enum('yes','no') COLLATE utf8_persian_ci NOT NULL DEFAULT 'yes',
  `unsubscribe_code` varchar(255) COLLATE utf8_persian_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`,`user_id`),
  KEY `FK_INVITATION_USER` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loans`
--

CREATE TABLE IF NOT EXISTS `loans` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_persian_ci NOT NULL,
  `amount` bigint(20) NOT NULL DEFAULT '0',
  `description` text COLLATE utf8_persian_ci,
  `status` enum('due','done') COLLATE utf8_persian_ci NOT NULL DEFAULT 'due',
  `notify` enum('yes','no') COLLATE utf8_persian_ci NOT NULL DEFAULT 'yes',
  `bank_id` int(11) unsigned NOT NULL,
  `transaction_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) unsigned NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_LOAN_USER` (`user_id`),
  KEY `FK_LOAN_BANK` (`bank_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loan_tags`
--

CREATE TABLE IF NOT EXISTS `loan_tags` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `loan_id` int(11) unsigned NOT NULL,
  `tag_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `loan_id` (`loan_id`),
  KEY `tag_id` (`tag_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `newsletters`
--

CREATE TABLE IF NOT EXISTS `newsletters` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `subject` varchar(255) COLLATE utf8_persian_ci NOT NULL,
  `query` text COLLATE utf8_persian_ci NOT NULL,
  `head_title` varchar(255) COLLATE utf8_persian_ci NOT NULL,
  `head_text` text COLLATE utf8_persian_ci NOT NULL,
  `head_image` varchar(255) COLLATE utf8_persian_ci NOT NULL,
  `title1` varchar(255) COLLATE utf8_persian_ci NOT NULL,
  `content1` text COLLATE utf8_persian_ci NOT NULL,
  `title2` varchar(255) COLLATE utf8_persian_ci NOT NULL,
  `content2` text COLLATE utf8_persian_ci NOT NULL,
  `title3` varchar(255) COLLATE utf8_persian_ci NOT NULL,
  `content3` text COLLATE utf8_persian_ci NOT NULL,
  `sent` enum('yes','no') COLLATE utf8_persian_ci NOT NULL DEFAULT 'no',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE IF NOT EXISTS `notes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `subject` varchar(255) COLLATE utf8_persian_ci NOT NULL,
  `content` text COLLATE utf8_persian_ci NOT NULL,
  `status` enum('due','done') COLLATE utf8_persian_ci NOT NULL DEFAULT 'due',
  `notify` tinyint(1) NOT NULL DEFAULT '1',
  `date` datetime DEFAULT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL,
  `temp_user_id` int(11) unsigned DEFAULT NULL,
  `bank` varchar(255) COLLATE utf8_persian_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_persian_ci NOT NULL,
  `amount` int(20) NOT NULL,
  `plan` varchar(255) COLLATE utf8_persian_ci DEFAULT NULL,
  `discount_code_id` int(11) DEFAULT NULL,
  `authority` varchar(255) COLLATE utf8_persian_ci DEFAULT NULL,
  `status` int(20) DEFAULT NULL,
  `‫‪sale_reference_id` varchar(255) COLLATE utf8_persian_ci DEFAULT NULL,
  `type` enum('registration','extension','package') COLLATE utf8_persian_ci NOT NULL DEFAULT 'registration',
  `result` enum('pending','success','fail','unsettled') COLLATE utf8_persian_ci NOT NULL DEFAULT 'pending',
  `gateway` enum('parsian','mellat') COLLATE utf8_persian_ci DEFAULT NULL,
  `referer` varchar(255) COLLATE utf8_persian_ci DEFAULT NULL,
  `rid` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `created` (`created`),
  KEY `result` (`result`),
  KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE IF NOT EXISTS `packages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) COLLATE utf8_persian_ci DEFAULT NULL,
  `identifier` varchar(32) COLLATE utf8_persian_ci NOT NULL,
  `price` int(11) NOT NULL DEFAULT '0',
  `fromdate` datetime DEFAULT NULL,
  `todate` datetime DEFAULT NULL,
  `status` set('active','inactive') COLLATE utf8_persian_ci NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `package_services`
--

CREATE TABLE IF NOT EXISTS `package_services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `package_id` int(11) NOT NULL,
  `service_identifier` varchar(32) COLLATE utf8_persian_ci NOT NULL,
  `usebase` set('count','credit','date') COLLATE utf8_persian_ci NOT NULL,
  `amount` int(11) NOT NULL DEFAULT '0',
  `duration` varchar(16) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'Like 1 MONTH, 30 DAY. will be used in interval',
  PRIMARY KEY (`id`),
  KEY `package_id` (`package_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `phinxlog`
--

CREATE TABLE IF NOT EXISTS `phinxlog` (
  `version` bigint(20) NOT NULL,
  `migration_name` varchar(100) DEFAULT NULL,
  `start_time` timestamp NULL DEFAULT NULL,
  `end_time` timestamp NULL DEFAULT NULL,
  `breakpoint` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `reminders`
--

CREATE TABLE IF NOT EXISTS `reminders` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `type` enum('check','debt','installment','note') COLLATE utf8_persian_ci NOT NULL,
  `reference_id` bigint(20) NOT NULL,
  `time` datetime NOT NULL,
  `medium` set('sms','email') COLLATE utf8_persian_ci NOT NULL,
  `owner` int(11) DEFAULT NULL,
  `status` enum('sent','pending') COLLATE utf8_persian_ci NOT NULL DEFAULT 'pending',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`type`,`reference_id`),
  KEY `deleted` (`deleted`),
  KEY `owner` (`owner`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reminder_logs`
--

CREATE TABLE IF NOT EXISTS `reminder_logs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `reference_id` bigint(20) NOT NULL DEFAULT '0',
  `type` varchar(32) COLLATE utf8_persian_ci DEFAULT NULL,
  `medium` set('email','sms') COLLATE utf8_persian_ci NOT NULL,
  `cell` varchar(13) COLLATE utf8_persian_ci NOT NULL,
  `email` varchar(128) COLLATE utf8_persian_ci DEFAULT NULL,
  `subject` varchar(128) COLLATE utf8_persian_ci DEFAULT NULL,
  `textsms` text COLLATE utf8_persian_ci,
  `textemail` text COLLATE utf8_persian_ci,
  `msg_count` int(2) NOT NULL,
  `charge` tinyint(1) NOT NULL DEFAULT '1',
  `sms_status` enum('delivered','faileddelivery','deliverycheck','failed','sending','sent','pending','blocked','notset') COLLATE utf8_persian_ci NOT NULL DEFAULT 'notset',
  `email_status` enum('sent','pending','sending','notset') COLLATE utf8_persian_ci NOT NULL DEFAULT 'notset',
  `identifier` varchar(32) COLLATE utf8_persian_ci DEFAULT NULL,
  `result` int(10) DEFAULT NULL,
  `senddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `resultdate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `user_id_2` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reminder_settings`
--

CREATE TABLE IF NOT EXISTS `reminder_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `check_freq` set('1','3','7') COLLATE utf8_persian_ci DEFAULT NULL,
  `check_type` set('email','sms','push') COLLATE utf8_persian_ci DEFAULT NULL,
  `reminder_freq` set('1','3','7') COLLATE utf8_persian_ci DEFAULT NULL,
  `reminder_type` set('email','sms','push') COLLATE utf8_persian_ci DEFAULT NULL,
  `debt_freq` set('1','3','7') COLLATE utf8_persian_ci DEFAULT NULL,
  `debt_type` set('email','sms','push') COLLATE utf8_persian_ci DEFAULT NULL,
  `installment_freq` set('1','3','7') COLLATE utf8_persian_ci DEFAULT NULL,
  `installment_type` set('email','sms','push') COLLATE utf8_persian_ci DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `resellers`
--

CREATE TABLE IF NOT EXISTS `resellers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8_persian_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_persian_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_persian_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8_persian_ci DEFAULT NULL,
  `description` text COLLATE utf8_persian_ci,
  `percent` int(11) unsigned NOT NULL DEFAULT '40',
  `status` enum('active','suspended','locked') COLLATE utf8_persian_ci NOT NULL DEFAULT 'suspended',
  `forgot_password_verification_code` varchar(255) COLLATE utf8_persian_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reseller_settlements`
--

CREATE TABLE IF NOT EXISTS `reseller_settlements` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `reseller_id` int(11) unsigned DEFAULT NULL,
  `amount` int(20) DEFAULT NULL,
  `iban` varchar(255) COLLATE utf8_persian_ci DEFAULT NULL,
  `account_holder` varchar(255) COLLATE utf8_persian_ci DEFAULT NULL,
  `description` text COLLATE utf8_persian_ci,
  `status` enum('pending','settled','rejected') COLLATE utf8_persian_ci NOT NULL DEFAULT 'pending',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_RESELLER_SETTLEMENT_RESELLER` (`reseller_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE IF NOT EXISTS `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(32) COLLATE utf8_persian_ci DEFAULT NULL,
  `identifier` varchar(32) COLLATE utf8_persian_ci NOT NULL,
  `unitfee` int(11) NOT NULL DEFAULT '1',
  `usebase` set('count','credit','date') COLLATE utf8_persian_ci NOT NULL,
  `status` set('active','inactive') COLLATE utf8_persian_ci NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`),
  KEY `identifier` (`identifier`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service_transactions`
--

CREATE TABLE IF NOT EXISTS `service_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `service_id` int(11) NOT NULL,
  `service_identifier` varchar(32) COLLATE utf8_persian_ci NOT NULL,
  `creditor` int(11) NOT NULL DEFAULT '0',
  `debtor` int(11) NOT NULL DEFAULT '0',
  `remain` int(11) NOT NULL DEFAULT '0',
  `begindate` datetime DEFAULT NULL,
  `expdate` datetime DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `service_id` (`service_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sms_reads`
--

CREATE TABLE IF NOT EXISTS `sms_reads` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL,
  `identifier` varchar(16) COLLATE utf8_persian_ci NOT NULL,
  `fromnumber` varchar(16) COLLATE utf8_persian_ci NOT NULL,
  `tonumber` varchar(16) COLLATE utf8_persian_ci NOT NULL,
  `date` datetime DEFAULT NULL,
  `body` text COLLATE utf8_persian_ci,
  `processed` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `identifier` (`identifier`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE IF NOT EXISTS `tags` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_persian_ci NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `status` enum('active','inactive') COLLATE utf8_persian_ci NOT NULL DEFAULT 'active',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_TAG_USER` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tag_folders`
--

CREATE TABLE IF NOT EXISTS `tag_folders` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_persian_ci NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_TAG_FOLDER_USER` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `temp_users`
--

CREATE TABLE IF NOT EXISTS `temp_users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8_persian_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_persian_ci DEFAULT NULL,
  `reference_user_id` int(11) unsigned DEFAULT NULL,
  `registered` enum('yes','no') COLLATE utf8_persian_ci NOT NULL DEFAULT 'no',
  `plan` varchar(255) COLLATE utf8_persian_ci NOT NULL,
  `discount_code_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tokens`
--

CREATE TABLE IF NOT EXISTS `tokens` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `token` varchar(255) COLLATE utf8_persian_ci NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `expire` datetime NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE IF NOT EXISTS `transactions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `amount` bigint(20) NOT NULL,
  `date` date NOT NULL,
  `pyear` int(4) DEFAULT NULL,
  `pmonth` int(2) DEFAULT NULL,
  `pday` int(2) DEFAULT NULL,
  `type` enum('debt','credit') COLLATE utf8_persian_ci NOT NULL,
  `account_id` int(11) unsigned NOT NULL,
  `expense_id` int(11) unsigned DEFAULT NULL,
  `income_id` int(11) unsigned DEFAULT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_TRANSACTION_ACCOUNT` (`account_id`),
  KEY `FK_TRANSACTION_USER` (`user_id`),
  KEY `FK_TRANSACTION_EXPENSE` (`expense_id`),
  KEY `FK_TRANSACTION_INCOME` (`income_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

--
-- Triggers `transactions`
--
DROP TRIGGER IF EXISTS `trigger_before_insert_transaction`;
DELIMITER //
CREATE TRIGGER `trigger_before_insert_transaction` BEFORE INSERT ON `transactions`
 FOR EACH ROW BEGIN
        SET NEW.`pyear` = PYEAR(NEW.`date`), NEW.`pmonth` = PMONTH(NEW.`date`), NEW.`pday` = PDAY(NEW.`date`);
    END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `trigger_before_update_transaction`;
DELIMITER //
CREATE TRIGGER `trigger_before_update_transaction` BEFORE UPDATE ON `transactions`
 FOR EACH ROW BEGIN
        IF NEW.date <> OLD.date THEN
            SET NEW.`pyear` = PYEAR(NEW.`date`), NEW.`pmonth` = PMONTH(NEW.`date`), NEW.`pday` = PDAY(NEW.`date`);
        END IF;
    END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_tags`
--

CREATE TABLE IF NOT EXISTS `transaction_tags` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `transaction_id` int(11) unsigned NOT NULL,
  `tag_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `tag_id` (`tag_id`),
  KEY `transaction_id` (`transaction_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transfers`
--

CREATE TABLE IF NOT EXISTS `transfers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `transaction_debt_id` int(11) unsigned NOT NULL,
  `transaction_credit_id` int(11) unsigned NOT NULL,
  `description` text COLLATE utf8_persian_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `transaction_credit_id` (`transaction_credit_id`),
  KEY `transaction_debt_id` (`transaction_debt_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_group_id` int(5) unsigned NOT NULL DEFAULT '1',
  `email` varchar(255) COLLATE utf8_persian_ci NOT NULL,
  `cell` varchar(13) COLLATE utf8_persian_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_persian_ci DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `last_ip` varchar(25) COLLATE utf8_persian_ci DEFAULT NULL,
  `verification_code` varchar(255) COLLATE utf8_persian_ci DEFAULT NULL,
  `reference_user_id` int(11) unsigned DEFAULT NULL,
  `forgot_password_verification_code` varchar(255) COLLATE utf8_persian_ci DEFAULT NULL,
  `forgot_password_request_date` date DEFAULT NULL,
  `free` enum('yes','no') COLLATE utf8_persian_ci NOT NULL DEFAULT 'no',
  `verified` enum('yes','no') COLLATE utf8_persian_ci NOT NULL DEFAULT 'no',
  `status` enum('active','suspended','locked') COLLATE utf8_persian_ci NOT NULL DEFAULT 'active',
  `expire_date` date NOT NULL DEFAULT '2012-01-01',
  `referer` varchar(255) COLLATE utf8_persian_ci DEFAULT NULL,
  `rid` int(11) DEFAULT NULL,
  `notifications` enum('yes','no') COLLATE utf8_persian_ci NOT NULL DEFAULT 'yes',
  `blocked` tinyint(1) NOT NULL DEFAULT '0',
  `force_init` tinyint(1) NOT NULL DEFAULT '0',
  `unsubscribe_code` varchar(255) COLLATE utf8_persian_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `FK_USER_USER_GROUP` (`user_group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_groups`
--

CREATE TABLE IF NOT EXISTS `user_groups` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_persian_ci NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accounts`
--
ALTER TABLE `accounts`
  ADD CONSTRAINT `FK_ACCOUNT_BANK` FOREIGN KEY (`bank_id`) REFERENCES `banks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_ACCOUNT_USER` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `application_orders`
--
ALTER TABLE `application_orders`
  ADD CONSTRAINT `FK_APPLICATION_ORDER_APPLICATION` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `budgets`
--
ALTER TABLE `budgets`
  ADD CONSTRAINT `budgets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `budgets_ibfk_2` FOREIGN KEY (`expense_category_id`) REFERENCES `expense_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `checks`
--
ALTER TABLE `checks`
  ADD CONSTRAINT `FK_CHECK_ACCOUNT` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_CHECK_BANK` FOREIGN KEY (`bank_id`) REFERENCES `banks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_CHECK_INDIVIDUAL` FOREIGN KEY (`individual_id`) REFERENCES `individuals` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `FK_CHECK_USER` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `check_tags`
--
ALTER TABLE `check_tags`
  ADD CONSTRAINT `check_tags_ibfk_1` FOREIGN KEY (`check_id`) REFERENCES `checks` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `check_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `configs`
--
ALTER TABLE `configs`
  ADD CONSTRAINT `configs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `currency_rates`
--
ALTER TABLE `currency_rates`
  ADD CONSTRAINT `FK_CURRENCY_RATE_CURRENCY` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `debts`
--
ALTER TABLE `debts`
  ADD CONSTRAINT `FK_DEBT_INDIVIDUAL` FOREIGN KEY (`individual_id`) REFERENCES `individuals` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_DEBT_USER` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `debt_settlements`
--
ALTER TABLE `debt_settlements`
  ADD CONSTRAINT `FK_DEBT_SETTLEMENT_DEBT` FOREIGN KEY (`debt_id`) REFERENCES `debts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_DEBT_SETTLEMENT_TRANSACTION` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_DEBT_SETTLEMENT_USER` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `debt_tags`
--
ALTER TABLE `debt_tags`
  ADD CONSTRAINT `debt_tags_ibfk_1` FOREIGN KEY (`debt_id`) REFERENCES `debts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `debt_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `FK_EXPENSE_EXPENSE_CATEGORY` FOREIGN KEY (`expense_category_id`) REFERENCES `expense_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_EXPENSE_EXPENSE_SUB_CATEGORY` FOREIGN KEY (`expense_sub_category_id`) REFERENCES `expense_sub_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_EXPENSE_INDIVIDUAL` FOREIGN KEY (`individual_id`) REFERENCES `individuals` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_EXPENSE_TRANSACTION` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_EXPENSE_USER` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `expense_categories`
--
ALTER TABLE `expense_categories`
  ADD CONSTRAINT `FK_EXPENSE_CATEGORY_USER` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `expense_sub_categories`
--
ALTER TABLE `expense_sub_categories`
  ADD CONSTRAINT `FK_EXPENSE_SUB_CATEGORY_EXPENSE_CATEGORY` FOREIGN KEY (`expense_category_id`) REFERENCES `expense_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_EXPENSE_SUB_CATEGORY_USER` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `expense_tags`
--
ALTER TABLE `expense_tags`
  ADD CONSTRAINT `FK_EXPENSE_TAG_EXPENSE` FOREIGN KEY (`expense_id`) REFERENCES `expenses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_EXPENSE_TAG_TAG` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `incomes`
--
ALTER TABLE `incomes`
  ADD CONSTRAINT `FK_INCOME_INCOME_SUB_TYPE` FOREIGN KEY (`income_sub_type_id`) REFERENCES `income_sub_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_INCOME_INCOME_TYPE` FOREIGN KEY (`income_type_id`) REFERENCES `income_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_INCOME_INDIVIDUAL` FOREIGN KEY (`individual_id`) REFERENCES `individuals` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_INCOME_TRANSACTION` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_INCOME_USER` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `income_sub_types`
--
ALTER TABLE `income_sub_types`
  ADD CONSTRAINT `FK_INCOME_SUB_TYPE_INCOME_TYPE` FOREIGN KEY (`income_type_id`) REFERENCES `income_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_INCOME_SUB_TYPE_USER` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `income_types`
--
ALTER TABLE `income_types`
  ADD CONSTRAINT `FK_INCOME_TYPE_USER` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `individuals`
--
ALTER TABLE `individuals`
  ADD CONSTRAINT `FK_INDIVIDUAL_USER` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `installments`
--
ALTER TABLE `installments`
  ADD CONSTRAINT `FK_INSTALLMENT_LOAN` FOREIGN KEY (`loan_id`) REFERENCES `loans` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_INSTALLMENT_USER` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `investments`
--
ALTER TABLE `investments`
  ADD CONSTRAINT `FK_INVESTMENT_CURRENCY` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_INVESTMENT_USER` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `invitations`
--
ALTER TABLE `invitations`
  ADD CONSTRAINT `FK_INVITATION_USER` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `loans`
--
ALTER TABLE `loans`
  ADD CONSTRAINT `FK_LOAN_BANK` FOREIGN KEY (`bank_id`) REFERENCES `banks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_LOAN_USER` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `loan_tags`
--
ALTER TABLE `loan_tags`
  ADD CONSTRAINT `loan_tags_ibfk_1` FOREIGN KEY (`loan_id`) REFERENCES `loans` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `loan_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `notes`
--
ALTER TABLE `notes`
  ADD CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `package_services`
--
ALTER TABLE `package_services`
  ADD CONSTRAINT `package_services_ibfk_1` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reminders`
--
ALTER TABLE `reminders`
  ADD CONSTRAINT `reminders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reminder_logs`
--
ALTER TABLE `reminder_logs`
  ADD CONSTRAINT `reminder_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reminder_settings`
--
ALTER TABLE `reminder_settings`
  ADD CONSTRAINT `reminder_settings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reseller_settlements`
--
ALTER TABLE `reseller_settlements`
  ADD CONSTRAINT `FK_RESELLER_SETTLEMENT_RESELLER` FOREIGN KEY (`reseller_id`) REFERENCES `resellers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `service_transactions`
--
ALTER TABLE `service_transactions`
  ADD CONSTRAINT `service_transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sms_reads`
--
ALTER TABLE `sms_reads`
  ADD CONSTRAINT `sms_reads_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tags`
--
ALTER TABLE `tags`
  ADD CONSTRAINT `FK_TAG_USER` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tag_folders`
--
ALTER TABLE `tag_folders`
  ADD CONSTRAINT `FK_TAG_FOLDER_USER` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tokens`
--
ALTER TABLE `tokens`
  ADD CONSTRAINT `FK_TOKEN_USER` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `FK_TRANSACTION_ACCOUNT` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_TRANSACTION_EXPENSE` FOREIGN KEY (`expense_id`) REFERENCES `expenses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_TRANSACTION_INCOME` FOREIGN KEY (`income_id`) REFERENCES `incomes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_TRANSACTION_USER` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaction_tags`
--
ALTER TABLE `transaction_tags`
  ADD CONSTRAINT `transaction_tags_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transfers`
--
ALTER TABLE `transfers`
  ADD CONSTRAINT `transfers_ibfk_1` FOREIGN KEY (`transaction_debt_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transfers_ibfk_2` FOREIGN KEY (`transaction_credit_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `FK_USER_USER_GROUP` FOREIGN KEY (`user_group_id`) REFERENCES `user_groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

INSERT INTO `user_groups` (`id`, `name`, `created`, `modified`) VALUES
(1, 'Users', '2011-07-14 13:44:04', '2011-07-14 13:44:04'),
(2, 'Admins', '2011-07-28 14:21:47', '2011-07-28 14:21:47');

INSERT INTO `acos` (`id`, `parent_id`, `model`, `foreign_key`, `alias`, `lft`, `rght`) VALUES
(1, NULL, '', NULL, 'controllers', 1, 1062),
(2, 1, '', NULL, 'Pages', 2, 57),
(3, 2, '', NULL, 'home', 3, 4),
(4, 2, '', NULL, 'faq', 5, 6),
(5, 2, '', NULL, 'help', 7, 8),
(6, 2, '', NULL, 'add', 9, 10),
(7, 2, '', NULL, 'edit', 11, 12),
(8, 2, '', NULL, 'index', 13, 14),
(9, 2, '', NULL, 'view', 15, 16),
(10, 2, '', NULL, 'delete', 17, 18),
(11, 1, '', NULL, 'IncomeTypes', 58, 81),
(12, 11, '', NULL, 'index', 59, 60),
(13, 11, '', NULL, 'edit', 61, 62),
(14, 11, '', NULL, 'delete', 63, 64),
(15, 11, '', NULL, 'add', 65, 66),
(16, 11, '', NULL, 'view', 67, 68),
(17, 1, '', NULL, 'Debts', 82, 105),
(18, 17, '', NULL, 'index', 83, 84),
(19, 17, '', NULL, 'edit', 85, 86),
(20, 17, '', NULL, 'delete', 87, 88),
(21, 17, '', NULL, 'add', 89, 90),
(22, 17, '', NULL, 'view', 91, 92),
(23, 1, '', NULL, 'ExpenseCategories', 106, 131),
(24, 23, '', NULL, 'index', 107, 108),
(25, 23, '', NULL, 'edit', 109, 110),
(26, 23, '', NULL, 'delete', 111, 112),
(27, 23, '', NULL, 'ajaxGetSubCategories', 113, 114),
(28, 23, '', NULL, 'add', 115, 116),
(29, 23, '', NULL, 'view', 117, 118),
(30, 1, '', NULL, 'Checks', 132, 163),
(31, 30, '', NULL, 'index', 133, 134),
(32, 30, '', NULL, 'edit', 135, 136),
(33, 30, '', NULL, 'delete', 137, 138),
(34, 30, '', NULL, 'csv', 139, 140),
(35, 30, '', NULL, 'ajaxGetWeekChecks', 141, 142),
(36, 30, '', NULL, 'ajaxGetMonthChecks', 143, 144),
(37, 30, '', NULL, 'ajaxCheckDone', 145, 146),
(38, 30, '', NULL, 'add', 147, 148),
(39, 30, '', NULL, 'view', 149, 150),
(40, 1, '', NULL, 'ExpenseSubCategories', 164, 183),
(41, 40, '', NULL, 'add', 165, 166),
(42, 40, '', NULL, 'edit', 167, 168),
(43, 40, '', NULL, 'delete', 169, 170),
(44, 40, '', NULL, 'index', 171, 172),
(45, 40, '', NULL, 'view', 173, 174),
(46, 1, '', NULL, 'Incomes', 184, 207),
(47, 46, '', NULL, 'index', 185, 186),
(48, 46, '', NULL, 'edit', 187, 188),
(49, 46, '', NULL, 'delete', 189, 190),
(50, 46, '', NULL, 'csv', 191, 192),
(51, 46, '', NULL, 'add', 193, 194),
(52, 46, '', NULL, 'view', 195, 196),
(53, 1, '', NULL, 'Expenses', 208, 233),
(54, 53, '', NULL, 'index', 209, 210),
(55, 53, '', NULL, 'edit', 211, 212),
(56, 53, '', NULL, 'delete', 213, 214),
(57, 53, '', NULL, 'csv', 215, 216),
(58, 53, '', NULL, 'add', 217, 218),
(59, 53, '', NULL, 'view', 219, 220),
(60, 1, '', NULL, 'Users', 234, 291),
(61, 60, '', NULL, 'login', 235, 236),
(62, 60, '', NULL, 'logout', 237, 238),
(63, 60, '', NULL, 'join', 239, 240),
(64, 60, '', NULL, 'edit', 241, 242),
(65, 60, '', NULL, 'verify', 243, 244),
(66, 60, '', NULL, 'forgotPassword', 245, 246),
(67, 60, '', NULL, 'reset', 247, 248),
(68, 60, '', NULL, 'ajaxEmailDuplicateChecker', 249, 250),
(69, 60, '', NULL, 'add', 251, 252),
(70, 60, '', NULL, 'index', 253, 254),
(71, 60, '', NULL, 'view', 255, 256),
(72, 60, '', NULL, 'delete', 257, 258),
(73, 1, '', NULL, 'ArosAcos', 292, 313),
(74, 73, '', NULL, 'index', 293, 294),
(75, 73, '', NULL, 'add', 295, 296),
(76, 73, '', NULL, 'view', 297, 298),
(77, 73, '', NULL, 'edit', 299, 300),
(78, 73, '', NULL, 'delete', 301, 302),
(79, 73, '', NULL, 'build_acl', 303, 304),
(80, 1, '', NULL, 'UserGroups', 314, 333),
(81, 80, '', NULL, 'index', 315, 316),
(82, 80, '', NULL, 'view', 317, 318),
(83, 80, '', NULL, 'edit', 319, 320),
(84, 80, '', NULL, 'delete', 321, 322),
(85, 80, '', NULL, 'add', 323, 324),
(86, 1, '', NULL, 'Installments', 334, 357),
(87, 86, '', NULL, 'edit', 335, 336),
(88, 86, '', NULL, 'delete', 337, 338),
(89, 86, '', NULL, 'ajaxInstallmentDone', 339, 340),
(90, 86, '', NULL, 'add', 341, 342),
(91, 86, '', NULL, 'index', 343, 344),
(92, 86, '', NULL, 'view', 345, 346),
(93, 1, '', NULL, 'Reports', 358, 409),
(94, 93, '', NULL, 'index', 359, 360),
(95, 93, '', NULL, 'incomes', 361, 362),
(96, 93, '', NULL, 'summery', 363, 364),
(97, 93, '', NULL, 'weekAlerts', 365, 366),
(98, 93, '', NULL, 'monthAlerts', 367, 368),
(99, 93, '', NULL, 'add', 369, 370),
(100, 93, '', NULL, 'edit', 371, 372),
(101, 93, '', NULL, 'view', 373, 374),
(102, 93, '', NULL, 'delete', 375, 376),
(103, 1, '', NULL, 'Loans', 410, 433),
(104, 103, '', NULL, 'index', 411, 412),
(105, 103, '', NULL, 'view', 413, 414),
(106, 103, '', NULL, 'edit', 415, 416),
(107, 103, '', NULL, 'delete', 417, 418),
(108, 103, '', NULL, 'add', 419, 420),
(109, 1, '', NULL, 'Asset Compress', 434, 479),
(110, 109, '', NULL, 'CssFiles', 435, 456),
(111, 110, '', NULL, 'get', 436, 437),
(112, 110, '', NULL, 'add', 438, 439),
(113, 110, '', NULL, 'edit', 440, 441),
(114, 110, '', NULL, 'index', 442, 443),
(115, 110, '', NULL, 'view', 444, 445),
(116, 110, '', NULL, 'delete', 446, 447),
(117, 109, '', NULL, 'JsFiles', 457, 478),
(118, 117, '', NULL, 'get', 458, 459),
(119, 117, '', NULL, 'add', 460, 461),
(120, 117, '', NULL, 'edit', 462, 463),
(121, 117, '', NULL, 'index', 464, 465),
(122, 117, '', NULL, 'view', 466, 467),
(123, 117, '', NULL, 'delete', 468, 469),
(124, 1, '', NULL, 'Localized', 480, 547),
(125, 124, '', NULL, 'CssFiles', 481, 502),
(126, 125, '', NULL, 'get', 482, 483),
(127, 125, '', NULL, 'add', 484, 485),
(128, 125, '', NULL, 'edit', 486, 487),
(129, 125, '', NULL, 'index', 488, 489),
(130, 125, '', NULL, 'view', 490, 491),
(131, 125, '', NULL, 'delete', 492, 493),
(132, 124, '', NULL, 'JsFiles', 503, 524),
(133, 132, '', NULL, 'get', 504, 505),
(134, 132, '', NULL, 'add', 506, 507),
(135, 132, '', NULL, 'edit', 508, 509),
(136, 132, '', NULL, 'index', 510, 511),
(137, 132, '', NULL, 'view', 512, 513),
(138, 132, '', NULL, 'delete', 514, 515),
(139, 2, '', NULL, 'whatisjeeb', 19, 20),
(140, 2, '', NULL, 'features', 21, 22),
(141, 2, '', NULL, 'about', 23, 24),
(142, 2, '', NULL, 'contact', 25, 26),
(143, 2, '', NULL, 'bugs', 27, 28),
(144, 2, '', NULL, 'getBrowser', 29, 30),
(145, 11, '', NULL, 'getBrowser', 69, 70),
(146, 17, '', NULL, 'ajaxDebtDone', 93, 94),
(147, 17, '', NULL, 'getBrowser', 95, 96),
(148, 23, '', NULL, 'getBrowser', 119, 120),
(149, 30, '', NULL, 'getBrowser', 151, 152),
(150, 40, '', NULL, 'getBrowser', 175, 176),
(151, 46, '', NULL, 'getBrowser', 197, 198),
(152, 53, '', NULL, 'getBrowser', 221, 222),
(153, 60, '', NULL, 'getBrowser', 259, 260),
(154, 73, '', NULL, 'getBrowser', 305, 306),
(155, 80, '', NULL, 'getBrowser', 325, 326),
(156, 86, '', NULL, 'getBrowser', 347, 348),
(157, 93, '', NULL, 'expenses', 377, 378),
(158, 93, '', NULL, 'dashboard', 379, 380),
(159, 93, '', NULL, 'getBrowser', 381, 382),
(160, 103, '', NULL, 'getBrowser', 421, 422),
(161, 110, '', NULL, 'getBrowser', 448, 449),
(162, 117, '', NULL, 'getBrowser', 470, 471),
(163, 125, '', NULL, 'getBrowser', 494, 495),
(164, 132, '', NULL, 'getBrowser', 516, 517),
(165, 93, '', NULL, 'monthly', 383, 384),
(169, 30, '', NULL, 'export', 153, 154),
(170, 46, '', NULL, 'export', 199, 200),
(171, 53, '', NULL, 'export', 223, 224),
(172, 2, '', NULL, 'discount', 31, 32),
(173, 2, '', NULL, 'detectBrowser', 33, 34),
(174, 11, '', NULL, 'detectBrowser', 71, 72),
(175, 17, '', NULL, 'detectBrowser', 97, 98),
(176, 23, '', NULL, 'detectBrowser', 121, 122),
(177, 30, '', NULL, 'detectBrowser', 155, 156),
(178, 40, '', NULL, 'detectBrowser', 177, 178),
(179, 46, '', NULL, 'detectBrowser', 201, 202),
(180, 53, '', NULL, 'detectBrowser', 225, 226),
(181, 60, '', NULL, 'detectBrowser', 267, 268),
(182, 73, '', NULL, 'detectBrowser', 307, 308),
(183, 80, '', NULL, 'detectBrowser', 327, 328),
(184, 86, '', NULL, 'detectBrowser', 349, 350),
(185, 93, '', NULL, 'detectBrowser', 385, 386),
(186, 103, '', NULL, 'detectBrowser', 423, 424),
(187, 110, '', NULL, 'detectBrowser', 450, 451),
(188, 117, '', NULL, 'detectBrowser', 472, 473),
(189, 125, '', NULL, 'detectBrowser', 496, 497),
(190, 132, '', NULL, 'detectBrowser', 518, 519),
(191, 2, '', NULL, 'getRemainingDays', 35, 36),
(192, 11, '', NULL, 'getRemainingDays', 73, 74),
(193, 17, '', NULL, 'getRemainingDays', 99, 100),
(194, 23, '', NULL, 'getRemainingDays', 123, 124),
(195, 30, '', NULL, 'getRemainingDays', 157, 158),
(196, 40, '', NULL, 'getRemainingDays', 179, 180),
(197, 46, '', NULL, 'getRemainingDays', 203, 204),
(198, 53, '', NULL, 'getRemainingDays', 227, 228),
(199, 60, '', NULL, 'extend', 269, 270),
(200, 60, '', NULL, 'payExtend', 271, 272),
(201, 60, '', NULL, 'getRemainingDays', 273, 274),
(202, 73, '', NULL, 'getRemainingDays', 309, 310),
(203, 80, '', NULL, 'getRemainingDays', 329, 330),
(204, 86, '', NULL, 'getRemainingDays', 351, 352),
(205, 93, '', NULL, 'getRemainingDays', 387, 388),
(206, 103, '', NULL, 'getRemainingDays', 425, 426),
(207, 110, '', NULL, 'getRemainingDays', 452, 453),
(208, 117, '', NULL, 'getRemainingDays', 474, 475),
(209, 125, '', NULL, 'getRemainingDays', 498, 499),
(210, 132, '', NULL, 'getRemainingDays', 520, 521),
(211, 2, '', NULL, 'captchaImage', 37, 38),
(212, 2, '', NULL, 'getReferer', 39, 40),
(213, 11, '', NULL, 'getReferer', 75, 76),
(214, 17, '', NULL, 'getReferer', 101, 102),
(215, 23, '', NULL, 'getReferer', 125, 126),
(216, 30, '', NULL, 'getReferer', 159, 160),
(217, 40, '', NULL, 'getReferer', 181, 182),
(218, 46, '', NULL, 'getReferer', 205, 206),
(219, 53, '', NULL, 'getReferer', 229, 230),
(220, 60, '', NULL, 'getReferer', 275, 276),
(221, 73, '', NULL, 'getReferer', 311, 312),
(222, 80, '', NULL, 'getReferer', 331, 332),
(223, 86, '', NULL, 'getReferer', 353, 354),
(224, 93, '', NULL, 'getReferer', 389, 390),
(225, 103, '', NULL, 'getReferer', 427, 428),
(226, 1, '', NULL, 'Managements', 548, 575),
(227, 226, '', NULL, 'index', 549, 550),
(228, 226, '', NULL, 'email', 551, 552),
(229, 226, '', NULL, 'getReferer', 553, 554),
(230, 226, '', NULL, 'detectBrowser', 555, 556),
(231, 226, '', NULL, 'getRemainingDays', 557, 558),
(232, 226, '', NULL, 'add', 559, 560),
(233, 226, '', NULL, 'edit', 561, 562),
(234, 226, '', NULL, 'view', 563, 564),
(235, 226, '', NULL, 'delete', 565, 566),
(236, 110, '', NULL, 'getReferer', 454, 455),
(237, 117, '', NULL, 'getReferer', 476, 477),
(238, 125, '', NULL, 'getReferer', 500, 501),
(239, 132, '', NULL, 'getReferer', 522, 523),
(240, 2, '', NULL, 'application', 41, 42),
(242, 60, '', NULL, 'unsubscribe', 279, 280),
(243, 226, '', NULL, 'orders', 567, 568),
(244, 17, '', NULL, 'export', 103, 104),
(245, 226, '', NULL, 'userInfo', 569, 570),
(246, 2, '', NULL, 'tell', 43, 44),
(247, 1, '', NULL, 'Yahoo', 576, 593),
(248, 247, '', NULL, 'index', 577, 578),
(249, 247, '', NULL, 'getReferer', 579, 580),
(250, 247, '', NULL, 'detectBrowser', 581, 582),
(251, 247, '', NULL, 'getRemainingDays', 583, 584),
(252, 247, '', NULL, 'add', 585, 586),
(253, 247, '', NULL, 'edit', 587, 588),
(254, 247, '', NULL, 'view', 589, 590),
(255, 247, '', NULL, 'delete', 591, 592),
(256, 1, '', NULL, 'Invitations', 594, 629),
(257, 256, '', NULL, 'index', 595, 596),
(258, 256, '', NULL, 'yahooRequestToken', 597, 598),
(259, 256, '', NULL, 'yahooCallback', 599, 600),
(260, 256, '', NULL, 'yahooGetContacts', 601, 602),
(261, 256, '', NULL, 'getReferer', 603, 604),
(262, 256, '', NULL, 'detectBrowser', 605, 606),
(263, 256, '', NULL, 'getRemainingDays', 607, 608),
(264, 256, '', NULL, 'add', 609, 610),
(265, 256, '', NULL, 'edit', 611, 612),
(266, 256, '', NULL, 'view', 613, 614),
(267, 256, '', NULL, 'delete', 615, 616),
(268, 256, '', NULL, 'googleRequestToken', 617, 618),
(269, 256, '', NULL, 'googleCallback', 619, 620),
(270, 60, '', NULL, 'ajaxCheckDiscountCode', 281, 282),
(271, 103, '', NULL, 'export', 429, 430),
(272, 86, '', NULL, 'export', 355, 356),
(273, 2, '', NULL, 'offlinePurchase', 45, 46),
(274, 1, '', NULL, 'Transactions', 630, 649),
(275, 274, '', NULL, 'index', 631, 632),
(276, 274, '', NULL, 'delete', 633, 634),
(277, 274, '', NULL, 'export', 635, 636),
(278, 274, '', NULL, 'getReferer', 637, 638),
(279, 274, '', NULL, 'detectBrowser', 639, 640),
(280, 274, '', NULL, 'getRemainingDays', 641, 642),
(281, 274, '', NULL, 'add', 643, 644),
(282, 274, '', NULL, 'edit', 645, 646),
(283, 274, '', NULL, 'view', 647, 648),
(284, 30, '', NULL, 'drawedcheckdDone', 161, 162),
(285, 1, '', NULL, 'Accounts', 650, 681),
(286, 285, '', NULL, 'index', 651, 652),
(287, 285, '', NULL, 'view', 653, 654),
(288, 285, '', NULL, 'edit', 655, 656),
(289, 285, '', NULL, 'delete', 657, 658),
(290, 285, '', NULL, 'ajaxSaveInitBalance', 659, 660),
(291, 285, '', NULL, 'getReferer', 661, 662),
(292, 285, '', NULL, 'detectBrowser', 663, 664),
(293, 285, '', NULL, 'getRemainingDays', 665, 666),
(294, 285, '', NULL, 'add', 667, 668),
(295, 1, '', NULL, 'IncomeSubTypes', 682, 699),
(296, 295, '', NULL, 'add', 683, 684),
(297, 295, '', NULL, 'edit', 685, 686),
(298, 295, '', NULL, 'delete', 687, 688),
(299, 295, '', NULL, 'getReferer', 689, 690),
(300, 295, '', NULL, 'detectBrowser', 691, 692),
(301, 295, '', NULL, 'getRemainingDays', 693, 694),
(302, 295, '', NULL, 'index', 695, 696),
(303, 295, '', NULL, 'view', 697, 698),
(304, 1, '', NULL, 'Debug Kit', 700, 723),
(305, 304, '', NULL, 'ToolbarAccess', 701, 722),
(306, 305, '', NULL, 'history_state', 702, 703),
(307, 305, '', NULL, 'sql_explain', 704, 705),
(308, 305, '', NULL, 'getReferer', 706, 707),
(309, 305, '', NULL, 'detectBrowser', 708, 709),
(310, 305, '', NULL, 'getRemainingDays', 710, 711),
(311, 305, '', NULL, 'add', 712, 713),
(312, 305, '', NULL, 'edit', 714, 715),
(313, 305, '', NULL, 'index', 716, 717),
(314, 305, '', NULL, 'view', 718, 719),
(315, 305, '', NULL, 'delete', 720, 721),
(316, 124, '', NULL, 'ToolbarAccess', 525, 546),
(317, 316, '', NULL, 'history_state', 526, 527),
(318, 316, '', NULL, 'sql_explain', 528, 529),
(319, 316, '', NULL, 'getReferer', 530, 531),
(320, 316, '', NULL, 'detectBrowser', 532, 533),
(321, 316, '', NULL, 'getRemainingDays', 534, 535),
(322, 316, '', NULL, 'add', 536, 537),
(323, 316, '', NULL, 'edit', 538, 539),
(324, 316, '', NULL, 'index', 540, 541),
(325, 316, '', NULL, 'view', 542, 543),
(326, 316, '', NULL, 'delete', 544, 545),
(327, 2, '', NULL, 'mobile', 47, 48),
(328, 1, '', NULL, 'Investments', 724, 741),
(329, 328, '', NULL, 'index', 725, 726),
(330, 328, '', NULL, 'view', 727, 728),
(331, 328, '', NULL, 'edit', 729, 730),
(332, 328, '', NULL, 'delete', 731, 732),
(333, 328, '', NULL, 'getReferer', 733, 734),
(334, 328, '', NULL, 'detectBrowser', 735, 736),
(335, 328, '', NULL, 'getRemainingDays', 737, 738),
(336, 328, '', NULL, 'add', 739, 740),
(337, 285, '', NULL, 'balance', 669, 670),
(338, 256, '', NULL, 'invite', 621, 622),
(339, 2, '', NULL, 'windows', 49, 50),
(340, 1, '', NULL, 'Newsletters', 742, 759),
(341, 340, '', NULL, 'index', 743, 744),
(342, 340, '', NULL, 'view', 745, 746),
(343, 340, '', NULL, 'edit', 747, 748),
(344, 340, '', NULL, 'delete', 749, 750),
(345, 340, '', NULL, 'getReferer', 751, 752),
(346, 340, '', NULL, 'detectBrowser', 753, 754),
(347, 340, '', NULL, 'getRemainingDays', 755, 756),
(348, 340, '', NULL, 'add', 757, 758),
(349, 256, '', NULL, 'unsubscribe', 623, 624),
(350, 60, '', NULL, 'account', 283, 284),
(351, 1, '', NULL, 'Notes', 760, 781),
(352, 351, '', NULL, 'index', 761, 762),
(353, 351, '', NULL, 'view', 763, 764),
(354, 351, '', NULL, 'edit', 765, 766),
(355, 351, '', NULL, 'delete', 767, 768),
(356, 351, '', NULL, 'getReferer', 769, 770),
(357, 351, '', NULL, 'detectBrowser', 771, 772),
(358, 351, '', NULL, 'getRemainingDays', 773, 774),
(359, 351, '', NULL, 'add', 775, 776),
(360, 60, '', NULL, 'resetData', 285, 286),
(361, 1, '', NULL, 'DebtSettlements', 782, 799),
(362, 361, '', NULL, 'delete', 783, 784),
(363, 361, '', NULL, 'getReferer', 785, 786),
(364, 361, '', NULL, 'detectBrowser', 787, 788),
(365, 361, '', NULL, 'getRemainingDays', 789, 790),
(366, 361, '', NULL, 'add', 791, 792),
(367, 361, '', NULL, 'edit', 793, 794),
(368, 361, '', NULL, 'index', 795, 796),
(369, 361, '', NULL, 'view', 797, 798),
(370, 351, '', NULL, 'markDone', 777, 778),
(371, 1, '', NULL, 'ApplicationOrders', 800, 831),
(372, 371, '', NULL, 'index', 801, 802),
(373, 371, '', NULL, 'order', 803, 804),
(376, 371, '', NULL, 'getReferer', 809, 810),
(377, 371, '', NULL, 'detectBrowser', 811, 812),
(378, 371, '', NULL, 'getRemainingDays', 813, 814),
(379, 371, '', NULL, 'add', 815, 816),
(380, 371, '', NULL, 'edit', 817, 818),
(381, 371, '', NULL, 'view', 819, 820),
(382, 371, '', NULL, 'delete', 821, 822),
(383, 1, '', NULL, 'Individuals', 832, 855),
(384, 383, '', NULL, 'index', 833, 834),
(385, 383, '', NULL, 'view', 835, 836),
(386, 383, '', NULL, 'edit', 837, 838),
(387, 383, '', NULL, 'delete', 839, 840),
(388, 383, '', NULL, 'getReferer', 841, 842),
(389, 383, '', NULL, 'detectBrowser', 843, 844),
(390, 383, '', NULL, 'getRemainingDays', 845, 846),
(391, 383, '', NULL, 'add', 847, 848),
(392, 383, '', NULL, 'export', 849, 850),
(393, 2, '', NULL, 'activation', 51, 52),
(394, 285, '', NULL, 'export', 671, 672),
(395, 383, '', NULL, 'sort', 851, 852),
(396, 383, '', NULL, 'toggleshow', 853, 854),
(397, 23, '', NULL, 'sort', 127, 128),
(398, 23, '', NULL, 'toggleshow', 129, 130),
(399, 11, '', NULL, 'sort', 77, 78),
(400, 11, '', NULL, 'toggleshow', 79, 80),
(401, 285, '', NULL, 'sort', 673, 674),
(402, 285, '', NULL, 'toggleshow', 675, 676),
(403, 93, '', NULL, 'accounts', 391, 392),
(404, 93, '', NULL, 'export', 393, 394),
(405, 351, '', NULL, 'export', 779, 780),
(406, 60, '', NULL, 'changemail', 287, 288),
(407, 285, '', NULL, 'showbalance', 677, 678),
(408, 285, '', NULL, 'exportaccounts', 679, 680),
(409, 93, '', NULL, 'individuals', 395, 396),
(410, 371, '', NULL, 'extend', 823, 824),
(411, 371, '', NULL, 'verifyPayment', 825, 826),
(412, 1, '', NULL, 'Reminders', 856, 883),
(413, 412, '', NULL, 'index', 857, 858),
(414, 412, '', NULL, 'add', 859, 860),
(415, 412, '', NULL, 'edit', 861, 862),
(416, 412, '', NULL, 'view', 863, 864),
(417, 412, '', NULL, 'delete', 865, 866),
(418, 412, '', NULL, 'logview', 867, 868),
(419, 412, '', NULL, 'help', 869, 870),
(420, 412, '', NULL, 'export', 871, 872),
(421, 412, '', NULL, 'ajaxShowText', 873, 874),
(422, 412, '', NULL, 'getReferer', 875, 876),
(423, 412, '', NULL, 'detectBrowser', 877, 878),
(424, 412, '', NULL, 'getRemainingDays', 879, 880),
(425, 1, '', NULL, 'Orders', 884, 901),
(426, 425, '', NULL, 'index', 885, 886),
(427, 425, '', NULL, 'view', 887, 888),
(428, 425, '', NULL, 'getReferer', 889, 890),
(429, 425, '', NULL, 'detectBrowser', 891, 892),
(430, 425, '', NULL, 'getRemainingDays', 893, 894),
(431, 425, '', NULL, 'add', 895, 896),
(432, 425, '', NULL, 'edit', 897, 898),
(433, 425, '', NULL, 'delete', 899, 900),
(434, 1, '', NULL, 'Package', 902, 927),
(435, 434, '', NULL, 'index', 903, 904),
(436, 434, '', NULL, 'lists', 905, 906),
(437, 434, '', NULL, 'buy', 907, 908),
(438, 434, '', NULL, 'order', 909, 910),
(440, 434, '', NULL, 'getReferer', 913, 914),
(441, 434, '', NULL, 'detectBrowser', 915, 916),
(442, 434, '', NULL, 'getRemainingDays', 917, 918),
(443, 434, '', NULL, 'add', 919, 920),
(444, 434, '', NULL, 'edit', 921, 922),
(445, 434, '', NULL, 'view', 923, 924),
(446, 434, '', NULL, 'delete', 925, 926),
(447, 1, '', NULL, 'Packages', 928, 959),
(448, 447, '', NULL, 'index', 929, 930),
(449, 447, '', NULL, 'lists', 931, 932),
(450, 447, '', NULL, 'buy', 933, 934),
(451, 447, '', NULL, 'order', 935, 936),
(454, 447, '', NULL, 'getReferer', 941, 942),
(455, 447, '', NULL, 'detectBrowser', 943, 944),
(456, 447, '', NULL, 'getRemainingDays', 945, 946),
(457, 447, '', NULL, 'add', 947, 948),
(458, 447, '', NULL, 'edit', 949, 950),
(459, 447, '', NULL, 'view', 951, 952),
(460, 447, '', NULL, 'delete', 953, 954),
(461, 412, '', NULL, 'unblock', 881, 882),
(462, 447, '', NULL, 'ajaxcheckdiscountcode', 955, 956),
(463, 371, '', NULL, 'ajaxCheckDiscountCode', 827, 828),
(464, 226, '', NULL, 'statistic', 571, 572),
(465, 447, '', NULL, 'ajaxCheckDiscountCode', 957, 958),
(466, 2, '', NULL, 'smstonote', 53, 54),
(467, 2, '', NULL, 'loans', 55, 56),
(468, 93, '', NULL, 'expenses_new', 397, 398),
(469, 93, '', NULL, 'incomes_new', 399, 400),
(470, 93, '', NULL, 'expense_comparison', 401, 402),
(471, 93, '', NULL, 'income_comparison', 403, 404),
(472, 93, '', NULL, 'tags', 405, 406),
(473, 1, '', NULL, 'Bills', 960, 983),
(474, 473, '', NULL, 'index', 961, 962),
(475, 473, '', NULL, 'newbill', 963, 964),
(476, 473, '', NULL, 'pay', 965, 966),
(477, 473, '', NULL, 'view', 967, 968),
(478, 473, '', NULL, 'edit', 969, 970),
(479, 473, '', NULL, 'export', 971, 972),
(480, 473, '', NULL, 'getReferer', 973, 974),
(481, 473, '', NULL, 'detectBrowser', 975, 976),
(482, 473, '', NULL, 'getRemainingDays', 977, 978),
(483, 473, '', NULL, 'add', 979, 980),
(484, 473, '', NULL, 'delete', 981, 982),
(485, 103, '', NULL, 'test', 431, 432),
(486, 1, '', NULL, 'Tags', 984, 1003),
(487, 486, '', NULL, 'index', 985, 986),
(488, 486, '', NULL, 'edit', 987, 988),
(489, 486, '', NULL, 'delete', 989, 990),
(490, 486, '', NULL, 'toggleshow', 991, 992),
(491, 486, '', NULL, 'getReferer', 993, 994),
(492, 486, '', NULL, 'detectBrowser', 995, 996),
(493, 486, '', NULL, 'getRemainingDays', 997, 998),
(494, 486, '', NULL, 'add', 999, 1000),
(495, 486, '', NULL, 'view', 1001, 1002),
(496, 53, '', NULL, 'batch', 231, 232),
(497, 1, '', NULL, 'Budgets', 1004, 1023),
(498, 497, '', NULL, 'index', 1005, 1006),
(499, 497, '', NULL, 'edit', 1007, 1008),
(500, 497, '', NULL, 'delete', 1009, 1010),
(501, 497, '', NULL, 'export', 1011, 1012),
(502, 497, '', NULL, 'getReferer', 1013, 1014),
(503, 497, '', NULL, 'detectBrowser', 1015, 1016),
(504, 497, '', NULL, 'getRemainingDays', 1017, 1018),
(505, 497, '', NULL, 'add', 1019, 1020),
(506, 497, '', NULL, 'view', 1021, 1022),
(507, 93, '', NULL, 'budgets', 407, 408),
(508, 371, '', NULL, 'sendapplication', 829, 830),
(509, 60, '', NULL, 'demo', 289, 290),
(510, 1, '', NULL, 'Tasks', 1024, 1041),
(512, 510, '', NULL, 'detectBrowser', 1027, 1028),
(513, 510, '', NULL, 'getRemainingDays', 1029, 1030),
(514, 510, '', NULL, 'add', 1031, 1032),
(515, 510, '', NULL, 'edit', 1033, 1034),
(516, 510, '', NULL, 'index', 1035, 1036),
(517, 510, '', NULL, 'view', 1037, 1038),
(518, 510, '', NULL, 'delete', 1039, 1040),
(521, 1, '', NULL, 'EmailHooks', 1042, 1061),
(524, 521, '', NULL, 'detectBrowser', 1047, 1048),
(525, 521, '', NULL, 'getRemainingDays', 1049, 1050),
(526, 521, '', NULL, 'add', 1051, 1052),
(527, 521, '', NULL, 'edit', 1053, 1054),
(528, 521, '', NULL, 'index', 1055, 1056),
(529, 521, '', NULL, 'view', 1057, 1058),
(530, 521, '', NULL, 'delete', 1059, 1060);


INSERT INTO `aros` (`id`, `parent_id`, `model`, `foreign_key`, `alias`, `lft`, `rght`) VALUES
(1, NULL, 'UserGroup', 1, NULL, 1, 2);

INSERT INTO `aros_acos` (`id`, `aro_id`, `aco_id`, `_create`, `_read`, `_update`, `_delete`) VALUES
(1, 1, 1, '1', '1', '1', '1'),
(2, 1, 73, '-1', '-1', '-1', '-1'),
(4, 1, 80, '-1', '-1', '-1', '-1'),
(5, 1, 340, '-1', '-1', '-1', '-1'),
(6, 1, 226, '-1', '-1', '-1', '-1'),
(7, 1, 508, '-1', '-1', '-1', '-1');

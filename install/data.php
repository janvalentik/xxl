<?php

/*-- kontrola jadra, priprava --*/
if(!defined('_core')){exit;}
$dbver=_checkVersion("database", null, true);
$dbver=$dbver[0];
$sql_error=false;

/*-- spusteni sql dotazu --*/

$sql="
CREATE TABLE `"._mysql_prefix."-articles` (`id` int(11) NOT NULL,`title` tinytext NOT NULL,`perex` text NOT NULL,`content` longtext NOT NULL,`infobox` text NOT NULL,`author` int(11) NOT NULL,`home1` int(11) NOT NULL,`home2` int(11) NOT NULL,`home3` int(11) NOT NULL,`time` int(11) NOT NULL,`visible` tinyint(1) NOT NULL,`public` tinyint(1) NOT NULL,`comments` tinyint(1) NOT NULL,`commentslocked` tinyint(1) NOT NULL,`confirmed` tinyint(1) NOT NULL,`showinfo` tinyint(1) NOT NULL,`readed` int(11) NOT NULL,`rateon` tinyint(1) NOT NULL,`ratenum` int(11) NOT NULL,`ratesum` int(11) NOT NULL,`keywords` tinytext,`seotitle` tinytext,`description` text,PRIMARY KEY (`id`),KEY `author` (`author`,`home1`,`home2`,`home3`,`time`,`visible`,`public`,`confirmed`)) ENGINE=MyISAM DEFAULT CHARSET=utf8
CREATE TABLE `"._mysql_prefix."-boxes` (`id` int(11) NOT NULL,`ord` int(11) NOT NULL,`title` tinytext NOT NULL,`content` text NOT NULL,`visible` tinyint(1) NOT NULL,`public` tinyint(1) NOT NULL,`column` tinyint(4) NOT NULL,PRIMARY KEY (`id`),KEY `visible` (`visible`,`public`,`column`)) ENGINE=MyISAM DEFAULT CHARSET=utf8
CREATE TABLE `"._mysql_prefix."-download` (`id` int(11) NOT NULL AUTO_INCREMENT,`stazeno` int(11) NOT NULL,`cesta` varchar(255) COLLATE utf8_bin NOT NULL,`public` tinyint(1) NOT NULL DEFAULT 1,PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8
CREATE TABLE `"._mysql_prefix."-groups` (`id` int(11) NOT NULL,`title` tinytext NOT NULL,`level` int(11) NOT NULL,`icon` tinytext NOT NULL,`blocked` tinyint(1) NOT NULL,`reglist` tinyint(1) NOT NULL,`administration` tinyint(1) NOT NULL,`adminsettings` tinyint(1) NOT NULL,`adminusers` tinyint(1) NOT NULL,`admingroups` tinyint(1) NOT NULL,`admincontent` tinyint(1) NOT NULL,`adminsection` tinyint(1) NOT NULL,`admincategory` tinyint(1) NOT NULL,`adminbook` tinyint(1) NOT NULL,`adminseparator` tinyint(1) NOT NULL,`admingallery` tinyint(1) NOT NULL,`adminlink` tinyint(1) NOT NULL,`adminintersection` tinyint(1) NOT NULL,`adminforum` tinyint(1) NOT NULL,`adminart` tinyint(1) NOT NULL,`adminallart` tinyint(1) NOT NULL,`adminchangeartauthor` tinyint(1) NOT NULL,`adminconfirm` tinyint(1) NOT NULL,`adminneedconfirm` tinyint(1) NOT NULL,`adminpoll` tinyint(1) NOT NULL,`adminpollall` tinyint(1) NOT NULL,`adminsbox` tinyint(1) NOT NULL,`adminbox` tinyint(1) NOT NULL,`admindownload` tinyint(1) NOT NULL,`adminsitemap` tinyint(1) NOT NULL,`adminfman` tinyint(1) NOT NULL,`adminfmanlimit` tinyint(1) NOT NULL,`adminfmanplus` tinyint(1) NOT NULL,`adminhcmphp` tinyint(1) NOT NULL,`adminbackup` tinyint(1) NOT NULL,`adminmassemail` tinyint(1) NOT NULL,`adminstatsword` tinyint(1) NOT NULL,`adminbans` tinyint(1) NOT NULL,`adminposts` tinyint(1) NOT NULL,`changeusername` tinyint(1) NOT NULL,`postcomments` tinyint(1) NOT NULL,`unlimitedpostaccess` tinyint(1) NOT NULL,`artrate` tinyint(1) NOT NULL,`pollvote` tinyint(1) NOT NULL,`selfdestruction` tinyint(1) NOT NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8
CREATE TABLE `"._mysql_prefix."-images` (`id` int(11) NOT NULL,`home` int(11) NOT NULL,`ord` float NOT NULL,`title` tinytext NOT NULL,`prev` tinytext NOT NULL,`full` tinytext NOT NULL,PRIMARY KEY (`id`),KEY `home` (`home`)) ENGINE=MyISAM DEFAULT CHARSET=utf8
CREATE TABLE `"._mysql_prefix."-iplog` (`id` int(11) NOT NULL,`ip` tinytext NOT NULL,`type` tinyint(4) NOT NULL,`time` int(11) NOT NULL,`var` int(11) NOT NULL,PRIMARY KEY (`id`),KEY `ip` (`ip`(15),`type`,`time`,`var`)) ENGINE=MyISAM DEFAULT CHARSET=utf8
CREATE TABLE `"._mysql_prefix."-messages` (`id` int(11) NOT NULL,`sender` int(11) NOT NULL,`receiver` int(11) NOT NULL,`readed` tinyint(1) NOT NULL,`subject` tinytext NOT NULL,`text` text NOT NULL,`time` int(11) NOT NULL,PRIMARY KEY (`id`),KEY `sender` (`sender`,`receiver`,`readed`,`time`)) ENGINE=MyISAM DEFAULT CHARSET=utf8
CREATE TABLE `"._mysql_prefix."-polls` (`id` int(11) NOT NULL,`author` int(11) NOT NULL,`question` tinytext NOT NULL,`answers` text NOT NULL,`locked` tinyint(1) NOT NULL,`votes` text NOT NULL,PRIMARY KEY (`id`),KEY `author` (`author`)) ENGINE=MyISAM DEFAULT CHARSET=utf8
CREATE TABLE `"._mysql_prefix."-posts` (`id` int(11) NOT NULL,`type` tinyint(4) NOT NULL,`home` int(11) NOT NULL,`xhome` int(11) NOT NULL,`subject` tinytext NOT NULL,`text` text NOT NULL,`author` int(11) NOT NULL,`guest` tinytext NOT NULL,`time` int(11) NOT NULL,`ip` tinytext NOT NULL,PRIMARY KEY (`id`),KEY `type` (`type`,`home`,`xhome`,`author`,`time`)) ENGINE=MyISAM DEFAULT CHARSET=utf8
CREATE TABLE `"._mysql_prefix."-root` (`id` int(11) NOT NULL,`title` tinytext NOT NULL,`type` tinyint(4) NOT NULL,`intersection` int(11) NOT NULL,`intersectionperex` text NOT NULL,`ord` float NOT NULL,`content` longtext NOT NULL,`visible` tinyint(1) NOT NULL,`public` tinyint(1) NOT NULL,`var1` mediumint(9) NOT NULL,`var2` mediumint(9) NOT NULL,`var3` mediumint(9) NOT NULL,`var4` mediumint(9) NOT NULL,`var5` mediumint(9) NOT NULL,`keywords` tinytext,`seotitle` tinytext,`description` text,PRIMARY KEY (`id`),KEY `type` (`type`,`intersection`,`visible`,`public`)) ENGINE=MyISAM DEFAULT CHARSET=utf8
CREATE TABLE `"._mysql_prefix."-sboxes` (`id` int(11) NOT NULL,`title` tinytext NOT NULL,`locked` tinyint(1) NOT NULL,`public` tinyint(1) NOT NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8
CREATE TABLE `"._mysql_prefix."-settings` (`var` tinytext NOT NULL,`val` text NOT NULL) ENGINE=MyISAM DEFAULT CHARSET=utf8
CREATE TABLE `"._mysql_prefix."-statssearch` (`id` int(11) NOT NULL AUTO_INCREMENT,`word` text COLLATE utf8_bin NOT NULL,`time` int(11) NOT NULL,`users` text COLLATE utf8_bin NOT NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8
CREATE TABLE `"._mysql_prefix."-users` (`id` int(11) NOT NULL,`group` int(11) NOT NULL,`levelshift` tinyint(1) NOT NULL,`username` tinytext NOT NULL,`publicname` tinytext NOT NULL,`password` tinytext NOT NULL,`salt` tinytext NOT NULL,`logincounter` int(11) NOT NULL,`registertime` int(11) NOT NULL,`activitytime` int(11) NOT NULL,`blocked` tinyint(1) NOT NULL,`massemail` tinyint(1) NOT NULL,`wysiwyg` tinyint(1) NOT NULL,`language` tinytext NOT NULL,`ip` tinytext NOT NULL,`email` tinytext NOT NULL,`avatar` tinytext NOT NULL,`web` tinytext NOT NULL,`skype` tinytext NOT NULL,`msn` tinytext NOT NULL,`icq` int(11) NOT NULL,`jabber` tinytext NOT NULL,`note` text NOT NULL,`code` varchar(6) DEFAULT NULL,PRIMARY KEY (`id`),KEY `group` (`group`,`username`(4),`registertime`,`activitytime`)) ENGINE=MyISAM DEFAULT CHARSET=utf8
INSERT INTO `"._mysql_prefix."-boxes` (`id`, `ord`, `title`, `content`, `visible`, `public`, `column`) VALUES (1, 1, 'Menu', '[hcm]menu[/hcm]', 0, 1, 1), (2, 3, 'Uživatel', '[hcm]usermenu[/hcm]', 1, 1, 1), (3, 2, 'Vyhledávání', '[hcm]search[/hcm]', 1, 1, 1)
INSERT INTO `"._mysql_prefix."-groups` (`id`, `title`, `level`, `icon`, `blocked`, `reglist`, `administration`, `adminsettings`, `adminusers`, `admingroups`, `admincontent`, `adminsection`, `admincategory`, `adminbook`, `adminseparator`, `admingallery`, `adminlink`, `adminintersection`, `adminforum`, `adminart`, `adminallart`, `adminchangeartauthor`, `adminconfirm`, `adminneedconfirm`, `adminpoll`, `adminpollall`, `adminsbox`, `adminbox`, `admindownload`, `adminsitemap`, `adminfman`, `adminfmanlimit`, `adminfmanplus`, `adminhcmphp`, `adminbackup`, `adminmassemail`, `adminstatsword`, `adminbans`, `adminposts`, `changeusername`, `postcomments`, `unlimitedpostaccess`, `artrate`, `pollvote`, `selfdestruction`) VALUES (1, 'Hlavní administrátoři', 10000, 'root.gif', 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 1, 1, 1, 1, 1, 1, 1, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),(2, 'Neregistrovaní', 0, '', 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 1, 1, 1),(3, 'Čtenáři', 1, '', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 1, 1, 1),(4, 'Administrátoři', 100, 'admin.gif', 0, 0, 1, 0, 1, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 0, 1, 1, 0),(5, 'Redaktoři', 50, 'editor.gif', 0, 0, 1, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 1, 0),(6, 'Newsletter', 0, 'newsletter.gif', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1)
INSERT INTO `"._mysql_prefix."-root` (`id`, `title`, `type`, `intersection`, `intersectionperex`, `ord`, `content`, `visible`, `public`, `var1`, `var2`, `var3`) VALUES (1, 'Index', 1, -1, '', 1, '<h1>Dokončeno</h1><p>Instalace SunLight CMS "._systemversion." byla dokončena. Nyní se již můžete <a href=\"admin/index.php?_formData[username]=Root\">přihlásit do administrace</a>. Přednastavené uživatelské jméno je Root, heslo jste si zvolil(a) při instalaci.</p>', 1, 1, 0, 0, 0)
INSERT INTO `"._mysql_prefix."-settings` (`var`, `val`) VALUES ('postsendexpire', '30'),('pollvoteexpire', '604800'),('artreadexpire', '18000'),('maxloginexpire', '1800'),('maxloginattempts', '20'),('pagingmode', '2'),('profileemail', '0'),('wysiwyg', '1'),('captcha', '1'),('template', 'default'),('title', '".$title."'),('description', '".$descr."'),('commentsperpage', '20'),('smileys', '1'),('postadmintime', '43200'),('keywords', ''),('adminscheme', '0'),('dbversion', '".$dbver."'),('atreplace', '[zavinac]'),('bbcode', '1'),('defaultgroup', '3'),('mailerusefrom', '0'),('showpages', '4'),('ulist', '1'),('registration', '1'),('language', 'default'),('modrewrite', '0'),('titleseparator', '-'),('url', '".$url."'),('notpublicsite', '0'),('comments', '1'),('artrateexpire', '604800'),('lightbox', '1'),('rss', '1'),('messages', '1'),('messagesperpage', '50'),('search', '1'),('banned', ''),('author', 'Root'),('titletype', '1'),('adminlinkprivate', '0'),('language_allowcustom', '0'),('lostpass', '1'),('registration_grouplist', '0'),('favicon', '0'),('footer', ''),('rules', ''),('printart', '1'),('extratopicslimit', '15'),('rsslimit', '30'),('sboxmemory', '30'),('ratemode', '2'),('time_format', 'j.n.Y G:i'),('uploadavatar', '1'),('fcbbutton', '1'),('activation', '1'),('socialtopclanky', '1'),('socialfcb', '1'),('socialtwitter', '1'),('socialdelicio', '1'),('socialjagg', '1'),('sociallinkuj', '1'),('socialvybralisme', '1'),('fcborsystemcomments', '1'),('avatars', '1'),('selectart', '1'),('pocitadlo', ''),('postlinks', '1'),('linkimage', '1'),('urlgenerate', '1'),('note', ''),('footer',''),('actmail','Pro dokončení aktivace účtu klikněte prosím na následující odkaz.')
INSERT INTO `"._mysql_prefix."-users` (`id`, `group`, `levelshift`, `username`, `publicname`, `password`, `salt`, `logincounter`, `registertime`, `activitytime`, `blocked`, `massemail`, `wysiwyg`, `language`, `ip`, `email`, `avatar`, `web`, `skype`, `msn`, `icq`, `jabber`, `note`) VALUES (0, 1, 1, 'Root', '', '".$pass[0]."', '".$pass[1]."', 0, ".time().", ".time().", 0, 1, 1, '', '"._userip."', '".$email."', '', '', '', '', 0, '', '')
";

$sql=explode("\n", trim($sql));
foreach($sql as $line){
  @mysql_query($line);
  if(mysql_error()!=false){$sql_error=mysql_error(); break;}
}
?>
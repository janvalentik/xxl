<?php

/*---- kontrola jadra ----*/
if(!defined('_core')){exit;}






/*---- vypis kodu boxu daneho sloupce ----*/

function _templateBoxes($column=1, $return=false){

$output="\n";

if(!_notpublicsite or _loginindicator){

  //verejny
  if(_loginindicator){$public="";}
  else{$public=" AND public=1";}

  //obsah
  if(_template_boxes_parent!=""){$output.="<"._template_boxes_parent.">\n";}
  $query=mysql_query("SELECT title,content FROM `"._mysql_prefix."-boxes` WHERE visible=1 AND `column`=".$column.$public." ORDER BY ord");
    while($item=mysql_fetch_array($query)){

      //kod titulku
      if($item['title']!=""){$title="<"._template_boxes_title." class='box-title'>".$item['title']."</"._template_boxes_title.">\n";}
      else{$title="";}

    /*titulek venku*/    if(_template_boxes_title_inside==0 and $title!=""){$output.=$title;}
    /*starttag polozky*/ if(_template_boxes_item!=""){$output.="<"._template_boxes_item." class='box-item'>\n";}
    /*titulek vevnitr*/  if(_template_boxes_title_inside==1 and $title!=""){$output.=$title;}
    /*obsah*/            $output.=_parseHCM($item['content']);
    /*endtag polozky*/   if(_template_boxes_item!=""){$output.="\n</"._template_boxes_item.">";}
    /*spodek boxu*/      if(_template_boxes_bottom==1){$output.="<"._template_boxes_item." class='box-bottom'></"._template_boxes_item.">\n\n";}else{$output.="\n\n";}
    ;
    }
  if(_template_boxes_parent!=""){$output.="</"._template_boxes_parent.">\n";}
  
}

//vypis vysledku
if(!$return){echo $output;}
else{return $output;}

}






/*---- vypis obsahu ----*/

function _templateContent($return=false){
if(!$return){echo _indexOutput_content;}
else{return _indexOutput_content;}
}






/*---- vypis obsahu xhtml hlavicky ----*/

function _templateHead(){
global $query,$_lang;
  //titulek
  $title=($query['seotitle']!='' ? $query['seotitle']:(_titletype==1? _title.' '._titleseparator.' '._indexOutput_title:_indexOutput_title.' '._titleseparator.' '._title));
  $keywords=($query['keywords']!='' ? $query['keywords']:_keywords);
  $description=($query['description']!='' ? $query['description']:_description);
  $usertemplate=(isset($_SESSION['template']) ? $_SESSION['template']:_template);

echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta name="keywords" content="'.$keywords.'" />
<meta name="description" content="'.$description.'" />
<meta name="author" content="'._author.'" />
<meta name="generator" content="SunLight CMS 7.4.2 XXL" />
<meta name="robots" content="index, follow" />
<!-- CSS XXL -->
<link href="'._indexroot.'libs/style/xxl.css" type="text/css" rel="stylesheet" />
<!-- CSS Core -->
<link href="'._indexroot.'templates/'.$usertemplate.'/style/system.css" type="text/css" rel="stylesheet" />
<link href="'._indexroot.'templates/'.$usertemplate.'/style/layout.css" type="text/css" rel="stylesheet" />
<!-- JS XXL -->
<script type="text/javascript" src="'._indexroot.'libs/tr-hover/tr-hover.js"></script>
';
if(_rss){
echo '
<link rel="alternate" type="application/rss+xml" href="'._indexroot.'remote/rss.php?tp=4&amp;id=-1" title="'.$_lang['rss.recentarticles'].'" />';
}
if(_favicon){
echo '
<link rel="shortcut icon" href="./favicon.ico" />';
}
echo '
<script type="text/javascript" src="'._indexroot.'libs/jquery/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="'._indexroot.'remote/javascript.php"></script>
<title>'.$title.'</title>
';
if(_lightbox){
echo '<script type="text/javascript" src="'._indexroot.'libs/colorbox/jquery.colorbox-min.js"></script>
<script type="text/javascript" src="'._indexroot.'remote/colorbox.php"></script>
<link href="'._indexroot.'libs/colorbox/colorbox.css" rel="stylesheet" type="text/css" />
';
}
}






/*---- vypis odkazu ----*/

function _templateLinks($left_separator=false){
global $_lang;
if($left_separator){echo " "._template_listinfoseparator." ";}
echo "<a href='http://sunlight.shira.cz/'>SunLight CMS</a>".((!_adminlinkprivate or (_loginindicator and _loginright_administration))?" "._template_listinfoseparator." <a href='"._indexroot."admin/index.php'>".$_lang['admin.link']."</a>":'');
}



/*---- sestaveni adresy k obrazku motivu ----*/

function _templateImage($path){
return _indexroot."templates/"._template."/images/".$path;
}






/*---- sestaveni kodu menu ----*/

function _templateMenu($ord_start=null, $ord_end=null){
global $__shid_total;
$output="";
if(defined("_indexOutput_pid")){$pid=_indexOutput_pid;}else{$pid=-1;}
if(!_notpublicsite or _loginindicator){

  //limit
  if($ord_start===null or $ord_end===null){$ord_limit="";}
  else{$ord_limit=" AND ord>=".intval($ord_start)." AND ord<=".intval($ord_end);}

  //obsah menu
  $query=mysql_query("SELECT id,type,title,var1,var2 FROM `"._mysql_prefix."-root` WHERE visible=1 AND intersection=-1 AND type!=4".$ord_limit." ORDER BY ord");
  $output.="<"._template_menu_parent." class='menu'>\n";
  $firstclass=" class='first'";
    while($item=mysql_fetch_array($query)){
    
      if(!($item['type']==7 and $item['var2']==1)){
        if($item['id']==$pid){$class=" class='act'";}else{$class="";}
        if($item['type']==6 and $item['var1']==1){$target=" target='_blank'";}else{$target="";}
     
      if (_linkimage){
      $link="<a href='"._linkRoot($item['id'])."'".$class.$target." id='link-".$item['id']."'>".$item['title']."</a>";}
      else{
      $link="<a href='"._linkRoot($item['id'])."'".$class.$target.">".$item['title']."</a>";}
      }
      else{
        $__shid_total+=1;
        $shid=$__shid_total;
        $iquery=mysql_query("SELECT id,type,title,var1 FROM `"._mysql_prefix."-root` WHERE intersection=".$item['id']." AND visible=1 ORDER BY ord");
        $childactive=false;
        
          $link_sublistitems='';
          while($iitem=mysql_fetch_array($iquery)){
          if($iitem['id']==$pid){$class=" class='act'"; $childactive=true;}else{$class="";}
          if($iitem['type']==6 and $iitem['var1']==1){$target=" target='_blank'";}else{$target="";}
          $link_sublistitems.="<li><a href='"._linkRoot($iitem['id'])."'".$class.$target.">".$iitem['title']."</a></li>";
          }
        if(!$childactive and $item['id']==$pid){$childactive=true;}
        $link="<a href='"._linkRoot($item['id'])."' class='hs_".($childactive?'opened':'closed').(($item['id']==$pid)?' act':'')."' onclick=\"return _sysHideShow('sh".$shid."', this)\">".$item['title']."</a>
<ul class='hs_content".($childactive?'':' hs_hidden')."' id='sh".$shid."'>
".$link_sublistitems."
</ul>\n";
      }

    $output.="<"._template_menu_child.$firstclass.">".$link."</"._template_menu_child.">\n";
    if($firstclass!=""){$firstclass="";}
    }
  
  $output.="</"._template_menu_parent.">";
  
}

return $output;

}






/*---- vypis titulku aktualni stranky ----*/

function _templateTitle(){
echo _indexOutput_title;
}






/*---- vypis kodu uzivatelskeho menu ----*/

function _templateUserMenu($return=false){
global $_lang;

$output="";

  if(_template_usermenu_parent!=""){$output.="<"._template_usermenu_parent.">\n";}

  if(!_loginindicator){
  /*prihlaseni*/                    $output.=_template_usermenu_item_start."<a href='"._indexroot."index.php?m=login'>".$_lang['usermenu.login']."</a>"._template_usermenu_item_end."\n";
  if(_registration){/*registrace*/  $output.=_template_usermenu_item_start."<a href='"._indexroot."index.php?m=reg'>".$_lang['usermenu.registration']."</a>"._template_usermenu_item_end."\n";}
  }
  else{
  /*vzkazy*/    if(_messages){$messages_count=mysql_result(mysql_query("SELECT COUNT(id) FROM `"._mysql_prefix."-messages` WHERE receiver="._loginid." AND readed=0"), 0); if($messages_count!=0){$messages_count=" [".$messages_count."]";}else{$messages_count="";} $output.=_template_usermenu_item_start."<a href='"._indexroot."index.php?m=messages'>".$_lang['usermenu.messages'].$messages_count."</a>"._template_usermenu_item_end."\n";}
  /*nastaveni*/ $output.=_template_usermenu_item_start."<a href='"._indexroot."index.php?m=settings'>".$_lang['usermenu.settings']."</a>"._template_usermenu_item_end."\n";
  /*odhlaseni*/ $output.=_template_usermenu_item_start."<a href='"._indexroot."remote/logout.php?_return=".urlencode(_indexOutput_url)."'>".$_lang['usermenu.logout'].(_template_usermenu_showusername?" ["._loginname."]":'')."</a>"._template_usermenu_item_end."\n";
  }
  
  if(_ulist and (!_notpublicsite or _loginindicator)){
  /*uziv. menu*/ $output.=_template_usermenu_item_start."<a href='"._indexroot."index.php?m=ulist'>".$_lang['usermenu.ulist']."</a>"._template_usermenu_item_end."\n";
  }
  
  if(_template_usermenu_parent!=""){$output.="</"._template_usermenu_parent.">\n";}

if(_template_usermenu_trim==1){$output=trim($output); $output=trim($output, _template_usermenu_item_start); $output=trim($output, _template_usermenu_item_end);}
if(!$return){echo $output;}else{return $output;}

}

?>
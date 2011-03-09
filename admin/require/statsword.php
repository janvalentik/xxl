<?php   

    $output.="<fieldset><legend>".$_lang['xxl.statsword.delete']."</legend>";
    $output.="<p>".$_lang['xxl.statsword.text']."</p>";
    $output.="<form name='delete' method='post'  action=''>";
    $output.="<input name='delete' type='submit' value='".$_lang['xxl.statsword.ok']."' onclick=\"return _sysConfirm();\" />";
    $output.="</form>";
    $output.="</fieldset>";

    if (!isset($_POST['delete']));
    else {
    mysql_query("truncate table `" . _mysql_prefix . "-statssearch`");
    $output.=_formMessage(1, "".$_lang['xxl.statsword.deleted.complete']."");} 



    $output.="<p>".$_lang['xxl.statsword.p']."</p>";

    $strana = 1;           
    $flow = 50;            
  
    if(isset($_GET['strana'])) $strana = $_GET['strana'];
    $pocet_radku = 1;   
    $pocet_radku = mysql_result(mysql_query("SELECT COUNT(*) FROM `" . _mysql_prefix . "-statssearch`"), 0);
    if($pocet_radku <= $flow) $pocet_stranek = 1;
    else
    {
        $a = $pocet_radku/$flow;
        $a = (int)$a;
        $pocet_stranek = $a +1;
    }

    if($strana>=$pocet_stranek)$strana = $pocet_stranek;
    elseif($strana<1) $strana=1;
    
    $aktualni_strana = 1; 
    
    $result = mysql_query("SELECT * FROM `" . _mysql_prefix . "-statssearch` ORDER BY ID DESC LIMIT ".$strana*$flow);
    if (!mysql_num_rows($result)) {
    $output.=_formMessage(2, "".$_lang['xxl.admin.nowhile']."");
    } else {
    
    $output.="<fieldset><legend>".$_lang['xxl.statsword.n']."</legend><table>";
    $output.="<tr><td class='lpad'>ID</td><td class='lpad'>".$_lang['xxl.statsword.w']."</td><td class='lpad'>".$_lang['xxl.statsword.t']."</td><td class='lpad'>".$_lang['xxl.statsword.u']."</td></tr>";
  
    while($item=mysql_fetch_array($result)){
      if ($item['users'] == -1) {$uzivatel_format = "guest";} else {$uzivatel_format = _linkUser($item['users']);}
    
        if($aktualni_strana>($strana*$flow-$flow)){
        $output.="<tr onmouseover=\"highlight_row(this, 1);\" onmouseout=\"highlight_row(this, 0);\">\n";
        $output.="<td class='lpad'>".$item['id'].".</td>\n";
        $output.="<td class='lpad'>".$item['word']."</td>\n";
        $output.="<td class='lpad'>"._formatTime($item['time'])."</td>\n";
        $output.="<td class='lpad'>".$uzivatel_format."</td>\n";
        $output.="</tr>\n";
        }
        $aktualni_strana++;
        }
    $output.="</table>";
    $output.="</fieldset>";
    }

    
     $output.="<p>";
    if($strana>1)
     $output.="<a href='index.php?p=statsword&amp;strana=".($strana-1)."'>".$_lang['xxl.statsword.new']." » </a>";
    
    
    if($strana<$pocet_stranek)  $output.="<a href='index.php?p=statsword&amp;strana=".($strana+1)."'> « ".$_lang['xxl.statsword.old']."</a>";
     $output.="</p>";  

?>
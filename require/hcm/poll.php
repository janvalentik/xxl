<?php

/*--- kontrola jadra ---*/
if(!defined('_core')){exit;}

/*--- definice funkce modulu ---*/
function _HCM_poll($id=null, $sirka=150){
  
  //nacteni promennych
  $id=intval($id);
  if(isset($sirka)){$sirka=intval($sirka);}else{$sirka=150;}
  if($sirka<100){$sirka=100;}
  
  //nacteni dat ankety
  $vpolldata=mysql_query("SELECT * FROM `"._mysql_prefix."-polls` WHERE id=".$id);
  if(mysql_num_rows($vpolldata)!=0){$vpolldata=mysql_fetch_array($vpolldata); $rcontinue=true;}
  else{$rcontinue=false;}
  
  //sestaveni kodu
  if($rcontinue){
  
    //odpovedi
    $ranswers=explode("#", $vpolldata['answers']);
    $rvotes=explode("-", $vpolldata['votes']);
    $rvotes_sum=array_sum($rvotes);
    if(_loginright_pollvote==1 and $vpolldata['locked']!=1 and _iplogCheck(4, $id)){$rallowvote=true;}else{$rallowvote=false;}
    
    if($rallowvote){$ranswers_code="<form action='"._indexroot."remote/hcm/pvote.php?_return=".urlencode(_indexOutput_url."#hcm_poll_".$GLOBALS['__hcm_uid'])."' method='post'>\n<input type='hidden' name='pid' value='".$vpolldata['id']."' />";}
    else{$ranswers_code="";}
    
              $ranswer_id=0;
              foreach($ranswers as $item){
                if($rvotes_sum!=0 and $rvotes[$ranswer_id]!=0){$rpercent=$rvotes[$ranswer_id]/$rvotes_sum; $rbarwidth=round($rpercent*($sirka-_template_votebarwidthreduction));}else{$rpercent=0; $rbarwidth=1;}
                if($rallowvote){$item="<label><input type='radio' name='option' value='".$ranswer_id."' /> ".$item." [".$rvotes[$ranswer_id]."/".round($rpercent*100)."%]</label>";}else{$item.=" [".$rvotes[$ranswer_id]."/".round($rpercent*100)."%]";}
                $ranswers_code.="<div class='poll-answer'>".$item."<div style='width:".$rbarwidth."px;'></div></div>\n";
                $ranswer_id++;
              }
    
            $ranswers_code.="<div class='poll-answer'>";
            if($rallowvote){$ranswers_code.="<input type='submit' value='".$GLOBALS['_lang']['hcm.poll.vote']."' class='votebutton' />";}
            $ranswers_code.=$GLOBALS['_lang']['hcm.poll.votes'].":&nbsp;".$rvotes_sum."</div>";
            if($rallowvote){$ranswers_code.="</form>\n";}
  
            return "
<div class='anchor'><a name='hcm_poll_".$GLOBALS['__hcm_uid']."'></a></div>
<div class='poll' style='width:".$sirka."px;'>
<div class='poll-content'>

<div class='poll-question'>
".$vpolldata['question']."
".(($vpolldata['locked']==1)?"<div>(".$GLOBALS['_lang']['hcm.poll.locked'].")</div>":'')."
</div>

".$ranswers_code."

</div>
</div>\n
";

  }

}

?>
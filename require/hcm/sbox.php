<?php
/*--- kontrola jadra ---*/
if(!defined('_core')) {
    exit;
}

/*--- definice funkce modulu ---*/
function _HCM_sbox($id = null, $chatmod = false, $chat_refresh = 20)
{

    //priprava
    $result = "";
    $id = intval($id);
    if(isset($chatmod) and $chatmod == 1) {
        $bezchatu = 0;
    } else {
        $bezchatu = 1;
    }
    $chat_refresh = intval($chat_refresh);
    if($chat_refresh > 5) {
        $rchatreload = intval($chat_refresh);
    } else {
        $rchatreload = 20;
    }

    //nacteni dat shoutboxu
    $sboxdata = mysql_query("SELECT * FROM `"._mysql_prefix."-sboxes` WHERE id=".$id);
    if(mysql_num_rows($sboxdata) != 0) {
        $sboxdata = mysql_fetch_assoc($sboxdata);
        $rcontinue = true;
    } else {
        $rcontinue = false;
    }

    //sestaveni kodu
    if($rcontinue) {

        $result = "
    <div class='anchor'><a name='hcm_sbox_".$GLOBALS['__hcm_uid']."'></a></div>
    <div class='sbox'>
    <div class='sbox-content'>
    ".(($sboxdata['title'] != "") ? "<div class='sbox-title'>".$sboxdata['title']."</div>" : '')."<div class='sbox-item'".(($sboxdata['title'] == "") ? " style='border-top:none;'" : '').">";

        //formular na pridani
        if($sboxdata['locked'] != 1 and _publicAccess($sboxdata['public']) and _publicAccess($bezchatu)) {

            //priprava bunek
            //$captcha = _captchaInit();
            if(!_loginindicator) {
                $inputs[] = array($GLOBALS['_lang']['posts.guestname'], "<input type='text' name='guest' class='sbox-input' maxlength='22' />");
            }
            $inputs[] = array($GLOBALS['_lang']['posts.text'], "<input type='text' name='text' class='sbox-input' maxlength='255' /><input type='hidden' name='_posttype' value='4' /><input type='hidden' name='_posttarget' value='".$id."' />");
            if(!_loginindicator) {
                $inputs[1][2] = true;
                //$inputs[] = $captcha;
            }

            //kontrolni prvky pro chat
            if($bezchatu == 0) {
                $rcontrols = _getPostformControls("hcm_sboxform_".$GLOBALS['__hcm_uid'], "text", true)." <span id='hcm_timer_".$GLOBALS['__hcm_uid']."' class='lpad'></span>
<script type='text/javascript'>
//<![CDATA[
_sysSboxChatInit(".$GLOBALS['__hcm_uid'].", ".$rchatreload.");
//]]>
</script>
<noscript><a href='"._indexOutput_url."'>".$GLOBALS['_lang']['global.reload']."</a></noscript>";
            } else {
                $rcontrols = null;
            }

            $result .= _formOutput("hcm_sboxform_".$GLOBALS['__hcm_uid'], _indexroot."remote/post.php?_return=".urlencode(_indexOutput_url."#hcm_sbox_".$GLOBALS['__hcm_uid']), $inputs, null, null, $rcontrols);

        } else {
            if($sboxdata['locked'] != 1) {
                $result .= $GLOBALS['_lang']['posts.loginrequired'];
            } else {
                $result .= "<img src='"._templateImage("icons/lock.png")."' alt='locked' class='icon' /> ".$GLOBALS['_lang']['posts.locked2'];
            }
        }

        $result .= "\n</div>\n<div class='sbox-posts'>";
        //vypis prispevku
        $sposts = mysql_query("SELECT id,text,author,guest,time,ip FROM `"._mysql_prefix."-posts` WHERE home=".$id." AND type=4 ORDER BY id DESC");
        if(mysql_num_rows($sposts) != 0) {
            while($spost = mysql_fetch_assoc($sposts)) {

                //nacteni autora
                if($spost['author'] != -1) {
                    $author = _linkUser($spost['author'], "post-author' title='"._formatTime($spost['time']), false, false, 16, ":");
                } else {
                    $author = "<span class='post-author-guest' title='"._formatTime($spost['time']).", ip=".$spost['ip']."'>".$spost['guest'].":</span>";
                }

                //odkaz na spravu
                if(_postAccess($spost)) {
                    $alink = " <a href='index.php?m=editpost&amp;id=".$spost['id']."'><img src='"._templateImage("icons/edit.png")."' alt='edit' class='icon' /></a>";
                } else {
                    $alink = "";
                }

                //kod polozky
                $result .= "<div class='sbox-item'>".$author.$alink." "._parsePost($spost['text'], true, false, false)."</div>\n";

            }
        } else {
            $result .= "\n<div class='sbox-item'>".$GLOBALS['_lang']['posts.noposts']."</div>\n";
        }

        $result .= "
  </div>
  </div>
  </div>
  ";

    }

    return $result;

}

?>
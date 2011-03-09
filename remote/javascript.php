<?php

/*--- inicializace jadra ---*/
define('_indexroot', '../');
define('_tmp_customheader', 'Content-type: application/javascript; charset=utf-8');
session_cache_limiter('public');
require(_indexroot."core.php");

?>
/**
 * Systemove funkce
 */

//otevreni okna
function _sysOpenWindow(url, width, height){
return !window.open(url, "_blank", "width="+width+",height="+height+",toolbar=0,location=0,status=0,menubar=0,resizable=1,scrollbars=1");
}

//script loader
function _sysScriptLoader(url){

    //smazani predchoziho loaderu
    var head=document.getElementsByTagName('head')[0];
    var dataLoader=document.getElementById('scriptLoader');
    if(dataLoader){head.removeChild(dataLoader);}

    //vytvoreni noveho elementu script
    script=document.createElement('script');
    script.id='scriptLoader';
    script.src =url+'&amp;r='+Math.random();

    //vlozeni skriptu
    head.appendChild(script);

}

//vypnuti prvku formulare
function _sysDisableField(checked, form, field){
 if(checked){eval("document."+form+"."+field+".disabled=true;");}
 else{eval("document."+form+"."+field+".disabled=false;");}
}

//systemova zprava
function _sysAlert(id){

  switch(id){
  case 1: text="<?php echo $_lang['javascript.alert.someempty']; ?>"; break;
  case 2: text="<?php echo $_lang['javascript.alert.toolong']; ?>"; break;
  }

alert(text);
}

//potvrzeni
function _sysConfirm(){
return confirm("<?php echo $_lang['javascript.confirm']; ?>");
}

//nahrazeni znaku zavinace
function _sysMai_lto(f) {
	var re = "<?php echo _atreplace; ?>";
	var addr = f.innerHTML.replace(re,'@');
	f.href = 'mai'+'lt'+'o:'+addr;
	return true;
}

//pridani smajlu
function _sysAddSmiley(fid, aid, text){
eval("txtarea=document."+fid+"."+aid+";");
text=" *"+text+"* ";
if (document.all) {
    if (txtarea.createTextRange && txtarea.caretPos) {
      var caretPos = txtarea.caretPos;
      caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? text+caretPos.text + ' ' : text+caretPos.text;
    }
    else {txtarea.value = txtarea.value + text; }
}
else if (document.getElementById) {
    var selLength = txtarea.textLength;
    var selStart = txtarea.selectionStart;
    var selEnd = txtarea.selectionEnd;
    if (selEnd==1 || selEnd==2) selEnd=selLength;
    var s1 = (txtarea.value).substring(0,selStart);
    var s2 = (txtarea.value).substring(selStart, selEnd)
    var s3 = (txtarea.value).substring(selEnd, selLength);
    txtarea.value = s1 + text + s2 + s3;
    txtarea.selectionStart=selStart+text.length;
    txtarea.selectionEnd=selStart+text.length;
    }

txtarea.focus();
return false;
}

//vlozeni bbcode zagu
function _sysAddBBCode(fid, aid, text){
eval("txtarea=document."+fid+"."+aid+";");
if (document.all) {
  text="[" + text + "]" + "[/" + text + "]";
    if (txtarea.createTextRange && txtarea.caretPos) {
      var caretPos = txtarea.caretPos;
      caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? text+caretPos.text + ' ' : text+caretPos.text;
    }
    else {txtarea.value = txtarea.value + text; }
}
else if (document.getElementById) {
    var selLength = txtarea.textLength;
    var selStart = txtarea.selectionStart;
    var selEnd = txtarea.selectionEnd;
    if (selEnd==1 || selEnd==2) selEnd=selLength;
    var s1 = (txtarea.value).substring(0,selStart);
    var s2 = (txtarea.value).substring(selStart, selEnd)
    var s3 = (txtarea.value).substring(selEnd, selLength);
    txtarea.value = s1 + "[" + text + "]" + s2 + "[/" + text + "]" + s3;
    txtarea.selectionStart=selStart+text.length*2+5+s2.length;
    txtarea.selectionEnd=selStart+text.length*2+5+s2.length;
    }

txtarea.focus();
return false;
}




//rozbalovaci menu
function _sysHideShow(id, trigger) {
  var el=document.getElementById(id);
  if(el.className=="hs_content hs_hidden"){el.className="hs_content"; trigger.className="hs_opened";}
  else{el.className="hs_content hs_hidden"; trigger.className="hs_closed";}
  return false;
}

//inicializace sbox-chatu
_sboxtimers=new Array();

function _sysSboxChatInit(hid, rtime){
  _sboxtimers[hid]=rtime+1;
  setTimeout("_sysSboxChatStep("+hid+");", 1);
}

//krok odpoctu sbox-chatu
function _sysSboxChatStep(hid){
if(_sboxtimers[hid]!=-1){
  
  eval("text=document.hcm_sboxform_"+hid+".text.value;");
  if(text==""){
    if(_sboxtimers[hid]>0){
      //odpocet
      _sboxtimers[hid]=_sboxtimers[hid]-1;
      document.getElementById("hcm_timer_"+hid).innerHTML="<?php echo $_lang['hcm.sbox.timer']; ?> "+_sboxtimers[hid];
      setTimeout("_sysSboxChatStep("+hid+");", 1000);
    }
    else{
      //obnoveni
      location.reload(true);
    }
  }
  else{
    document.getElementById("hcm_timer_"+hid).innerHTML="<?php echo $_lang['hcm.sbox.timerbroken']; ?>";
  }

}
}

/**
 * SWFObject v1.5.1: Flash Player detection and embed - http://blog.deconcept.com/swfobject/
 */
if(typeof deconcept=="undefined"){var deconcept={};}if(typeof deconcept.util=="undefined"){deconcept.util={};}if(typeof deconcept.SWFObjectUtil=="undefined"){deconcept.SWFObjectUtil={};}deconcept.SWFObject=function(_1,id,w,h,_5,c,_7,_8,_9,_a){if(!document.getElementById){return;}this.DETECT_KEY=_a?_a:"detectflash";this.skipDetect=deconcept.util.getRequestParameter(this.DETECT_KEY);this.params={};this.variables={};this.attributes=[];if(_1){this.setAttribute("swf",_1);}if(id){this.setAttribute("id",id);}if(w){this.setAttribute("width",w);}if(h){this.setAttribute("height",h);}if(_5){this.setAttribute("version",new deconcept.PlayerVersion(_5.toString().split(".")));}this.installedVer=deconcept.SWFObjectUtil.getPlayerVersion();if(!window.opera&&document.all&&this.installedVer.major>7){if(!deconcept.unloadSet){deconcept.SWFObjectUtil.prepUnload=function(){__flash_unloadHandler=function(){};__flash_savedUnloadHandler=function(){};window.attachEvent("onunload",deconcept.SWFObjectUtil.cleanupSWFs);};window.attachEvent("onbeforeunload",deconcept.SWFObjectUtil.prepUnload);deconcept.unloadSet=true;}}if(c){this.addParam("bgcolor",c);}var q=_7?_7:"high";this.addParam("quality",q);this.setAttribute("useExpressInstall",false);this.setAttribute("doExpressInstall",false);var _c=(_8)?_8:window.location;this.setAttribute("xiRedirectUrl",_c);this.setAttribute("redirectUrl","");if(_9){this.setAttribute("redirectUrl",_9);}};deconcept.SWFObject.prototype={useExpressInstall:function(_d){this.xiSWFPath=!_d?"expressinstall.swf":_d;this.setAttribute("useExpressInstall",true);},setAttribute:function(_e,_f){this.attributes[_e]=_f;},getAttribute:function(_10){return this.attributes[_10]||"";},addParam:function(_11,_12){this.params[_11]=_12;},getParams:function(){return this.params;},addVariable:function(_13,_14){this.variables[_13]=_14;},getVariable:function(_15){return this.variables[_15]||"";},getVariables:function(){return this.variables;},getVariablePairs:function(){var _16=[];var key;var _18=this.getVariables();for(key in _18){_16[_16.length]=key+"="+_18[key];}return _16;},getSWFHTML:function(){var _19="";if(navigator.plugins&&navigator.mimeTypes&&navigator.mimeTypes.length){if(this.getAttribute("doExpressInstall")){this.addVariable("MMplayerType","PlugIn");this.setAttribute("swf",this.xiSWFPath);}_19="<embed type=\"application/x-shockwave-flash\" src=\""+this.getAttribute("swf")+"\" width=\""+this.getAttribute("width")+"\" height=\""+this.getAttribute("height")+"\" style=\""+(this.getAttribute("style")||"")+"\"";_19+=" id=\""+this.getAttribute("id")+"\" name=\""+this.getAttribute("id")+"\" ";var _1a=this.getParams();for(var key in _1a){_19+=[key]+"=\""+_1a[key]+"\" ";}var _1c=this.getVariablePairs().join("&");if(_1c.length>0){_19+="flashvars=\""+_1c+"\"";}_19+="/>";}else{if(this.getAttribute("doExpressInstall")){this.addVariable("MMplayerType","ActiveX");this.setAttribute("swf",this.xiSWFPath);}_19="<object id=\""+this.getAttribute("id")+"\" classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" width=\""+this.getAttribute("width")+"\" height=\""+this.getAttribute("height")+"\" style=\""+(this.getAttribute("style")||"")+"\">";_19+="<param name=\"movie\" value=\""+this.getAttribute("swf")+"\" />";var _1d=this.getParams();for(var key in _1d){_19+="<param name=\""+key+"\" value=\""+_1d[key]+"\" />";}var _1f=this.getVariablePairs().join("&");if(_1f.length>0){_19+="<param name=\"flashvars\" value=\""+_1f+"\" />";}_19+="</object>";}return _19;},write:function(_20){if(this.getAttribute("useExpressInstall")){var _21=new deconcept.PlayerVersion([6,0,65]);if(this.installedVer.versionIsValid(_21)&&!this.installedVer.versionIsValid(this.getAttribute("version"))){this.setAttribute("doExpressInstall",true);this.addVariable("MMredirectURL",escape(this.getAttribute("xiRedirectUrl")));document.title=document.title.slice(0,47)+" - Flash Player Installation";this.addVariable("MMdoctitle",document.title);}}if(this.skipDetect||this.getAttribute("doExpressInstall")||this.installedVer.versionIsValid(this.getAttribute("version"))){var n=(typeof _20=="string")?document.getElementById(_20):_20;n.innerHTML=this.getSWFHTML();return true;}else{if(this.getAttribute("redirectUrl")!=""){document.location.replace(this.getAttribute("redirectUrl"));}}return false;}};deconcept.SWFObjectUtil.getPlayerVersion=function(){var _23=new deconcept.PlayerVersion([0,0,0]);if(navigator.plugins&&navigator.mimeTypes.length){var x=navigator.plugins["Shockwave Flash"];if(x&&x.description){_23=new deconcept.PlayerVersion(x.description.replace(/([a-zA-Z]|\s)+/,"").replace(/(\s+r|\s+b[0-9]+)/,".").split("."));}}else{if(navigator.userAgent&&navigator.userAgent.indexOf("Windows CE")>=0){var axo=1;var _26=3;while(axo){try{_26++;axo=new ActiveXObject("ShockwaveFlash.ShockwaveFlash."+_26);_23=new deconcept.PlayerVersion([_26,0,0]);}catch(e){axo=null;}}}else{try{var axo=new ActiveXObject("ShockwaveFlash.ShockwaveFlash.7");}catch(e){try{var axo=new ActiveXObject("ShockwaveFlash.ShockwaveFlash.6");_23=new deconcept.PlayerVersion([6,0,21]);axo.AllowScriptAccess="always";}catch(e){if(_23.major==6){return _23;}}try{axo=new ActiveXObject("ShockwaveFlash.ShockwaveFlash");}catch(e){}}if(axo!=null){_23=new deconcept.PlayerVersion(axo.GetVariable("$version").split(" ")[1].split(","));}}}return _23;};deconcept.PlayerVersion=function(_29){this.major=_29[0]!=null?parseInt(_29[0]):0;this.minor=_29[1]!=null?parseInt(_29[1]):0;this.rev=_29[2]!=null?parseInt(_29[2]):0;};deconcept.PlayerVersion.prototype.versionIsValid=function(fv){if(this.major<fv.major){return false;}if(this.major>fv.major){return true;}if(this.minor<fv.minor){return false;}if(this.minor>fv.minor){return true;}if(this.rev<fv.rev){return false;}return true;};deconcept.util={getRequestParameter:function(_2b){var q=document.location.search||document.location.hash;if(_2b==null){return q;}if(q){var _2d=q.substring(1).split("&");for(var i=0;i<_2d.length;i++){if(_2d[i].substring(0,_2d[i].indexOf("="))==_2b){return _2d[i].substring((_2d[i].indexOf("=")+1));}}}return "";}};deconcept.SWFObjectUtil.cleanupSWFs=function(){var _2f=document.getElementsByTagName("OBJECT");for(var i=_2f.length-1;i>=0;i--){_2f[i].style.display="none";for(var x in _2f[i]){if(typeof _2f[i][x]=="function"){_2f[i][x]=function(){};}}}};if(!document.getElementById&&document.all){document.getElementById=function(id){return document.all[id];};}var getQueryParamValue=deconcept.util.getRequestParameter;var FlashObject=deconcept.SWFObject;var SWFObject=deconcept.SWFObject;

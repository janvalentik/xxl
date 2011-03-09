<?php

/*--- inicializace jadra ---*/
define('_indexroot', '../../');
define('_tmp_customheader', 'Content-type: application/javascript; charset=utf-8');
session_cache_limiter('public');
require(_indexroot."core.php");

?>

/**
 * Systemove funkce
 */

sl_administration = true;

//promenne
var _hook="http://sunlight.shira.cz/feedback/hook.php?ver=<?php echo _systemversion; ?>";

//soub. manazer - zaskrtnout vse, odskrtnout vse, invertovat
function _sysFmanSelect(number, action){
var tmp=1;
  while(tmp<=number){
    switch(action){
    case 1: eval("document.filelist.f"+tmp+".checked=true"); break;
    case 2: eval("document.filelist.f"+tmp+".checked=false"); break;
    case 3: eval("document.filelist.f"+tmp+".checked=!document.filelist.f"+tmp+".checked"); break;
    }
  tmp+=1;
  }
return false;
}

//soub. manazer - presunout vybrane
function _sysFmanMoveSelected(){
newdir=prompt("<?php echo $_lang['admin.fman.selected.move.prompt']; ?>:", '');
if(newdir!="" && newdir!=null){
document.filelist.action.value='move';
document.filelist.param.value=newdir;
document.filelist.submit();
}
}

//soub. manazer - smazat vybrane
function _sysFmanDeleteSelected(){
if(confirm("<?php echo $_lang['admin.fman.selected.delete.confirm']; ?>")){
document.filelist.action.value='deleteselected';
document.filelist.submit();
}
}

//soub. manazer - pridat vyber do galerie
function _sysFmanAddSelectedToGallery(){
document.filelist.action.value='addtogallery_showform';
document.filelist.submit();
}

//soub. manazer - upload souboru
var _totalfiles=1;
function _sysFmanAddFile(){
newfile=document.createElement('span');
newfile.id="file"+_totalfiles;
newfile.innerHTML="<br /><input type='file' name='upf"+_totalfiles+"' /> <a href=\"#\" onclick=\"return _sysFmanRemoveFile("+_totalfiles+");\"><?php echo $_lang['global.cancel']; ?></a>";
document.getElementById("fmanFiles").appendChild(newfile);
_totalfiles+=1;
}

function _sysFmanRemoveFile(id){
document.getElementById("fmanFiles").removeChild(document.getElementById("file"+id));
}

//galerie - prochazeni slozek
function _sysGalBrowse(path){
    gal_currentpath=path;
    AjaxRequest.post({
        'url':'remote/galbrowser.php?dir='+escape(path),
        'onSuccess':function(req){document.getElementById('gallery-browser').innerHTML=req.responseText; myLightbox.updateImageList();},
    });
return false;
}

//galerie - vlozeni obrazku
function _sysGalSelect(imgpath){
    gal_dialog_imgpath=imgpath;
    if(!document.addform.autoprev.checked){
        dialog=document.getElementById('gallery-browser-dialog');
        if(dialog!=null){document.body.removeChild(dialog);}
        browser=document.getElementById('gallery-browser');
        browser_pos=_sysFindPos(browser);
        dialog=document.createElement('div');
        dialog.id='gallery-browser-dialog';
        dialog.style.left=browser_pos[0]+(browser.offsetWidth-350)/2+'px';
        dialog.style.top=browser_pos[1]+(browser.offsetHeight-48)/2+'px';
        dialog.innerHTML="<div><span><?php echo $_lang['admin.content.manageimgs.insert.browser.useas']; ?>:</span><a href='#' onclick=\"return _sysGalSelectButton(0);\"><?php echo $_lang['admin.content.manageimgs.full']; ?></a><a href='#' onclick=\"return _sysGalSelectButton(1);\"><?php echo $_lang['admin.content.manageimgs.prev']; ?></a><a href='#' onclick=\"return _sysGalSelectButton(2);\"><?php echo $_lang['global.cancel2']; ?></a></div>";
        document.body.appendChild(dialog);
    } else{
        _sysGalSelectButton(0);
    }
    return false;
}

//galerie - tlacitko dialogu
function _sysGalSelectButton(n){
    switch(n){
        case 0:
        document.addform.full.value=gal_dialog_imgpath;
        break;
        
        case 1:
        if(document.addform.autoprev.checked){_sysDisableField(false, 'addform', 'prev'); document.addform.autoprev.checked=false;}
        document.addform.prev.value=gal_dialog_imgpath;
        break;
    }
dialog=document.getElementById('gallery-browser-dialog');
if(dialog!=null){document.body.removeChild(dialog);}
return false;
}

//galerie - zachovani cesty po odeslani formulare
function _sysGalTransferPath(form){
  if(typeof(window['gal_currentpath'])!='undefined'){
    form.action=form.action+'&browserpath='+escape(gal_currentpath);
  }
}

//najit pozici prvku
function _sysFindPos(obj) {
	var curleft = curtop = 0;
	if (obj.offsetParent) {
	do {
			curleft += obj.offsetLeft;
			curtop += obj.offsetTop;
		} while (obj = obj.offsetParent);
	return [curleft,curtop];
	}
}


/**
 * AjaxToolBox - http://ajaxtoolbox.com/
 */
function AjaxRequest(){var req =new Object();
req.timeout =null;
req.generateUniqueUrl =true;
req.url =window.location.href;
req.method ="GET";
req.async =true;
req.username =null;
req.password =null;
req.parameters =new Object();
req.requestIndex =AjaxRequest.numAjaxRequests++;
req.responseReceived =false;
req.groupName =null;
req.queryString ="";
req.responseText =null;
req.responseXML =null;
req.status =null;
req.statusText =null;
req.aborted =false;
req.xmlHttpRequest =null;
req.onTimeout=null;
req.onLoading=null;
req.onLoaded=null;
req.onInteractive=null;
req.onComplete=null;
req.onSuccess=null;
req.onError=null;
req.onGroupBegin=null;
req.onGroupEnd=null;
req.xmlHttpRequest =AjaxRequest.getXmlHttpRequest();
if(req.xmlHttpRequest==null){return null;}req.xmlHttpRequest.onreadystatechange =
function(){if(req==null || req.xmlHttpRequest==null){return;}if(req.xmlHttpRequest.readyState==1){req.onLoadingInternal(req);}if(req.xmlHttpRequest.readyState==2){req.onLoadedInternal(req);}if(req.xmlHttpRequest.readyState==3){req.onInteractiveInternal(req);}if(req.xmlHttpRequest.readyState==4){req.onCompleteInternal(req);}};
req.onLoadingInternalHandled=false;
req.onLoadedInternalHandled=false;
req.onInteractiveInternalHandled=false;
req.onCompleteInternalHandled=false;
req.onLoadingInternal=
function(){if(req.onLoadingInternalHandled){return;}AjaxRequest.numActiveAjaxRequests++;
if(AjaxRequest.numActiveAjaxRequests==1 && typeof(window['AjaxRequestBegin'])=="function"){AjaxRequestBegin();}if(req.groupName!=null){if(typeof(AjaxRequest.numActiveAjaxGroupRequests[req.groupName])=="undefined"){AjaxRequest.numActiveAjaxGroupRequests[req.groupName] =0;}AjaxRequest.numActiveAjaxGroupRequests[req.groupName]++;
if(AjaxRequest.numActiveAjaxGroupRequests[req.groupName]==1 && typeof(req.onGroupBegin)=="function"){req.onGroupBegin(req.groupName);}}if(typeof(req.onLoading)=="function"){req.onLoading(req);}req.onLoadingInternalHandled=true;};
req.onLoadedInternal=
function(){if(req.onLoadedInternalHandled){return;}if(typeof(req.onLoaded)=="function"){req.onLoaded(req);}req.onLoadedInternalHandled=true;};
req.onInteractiveInternal=
function(){if(req.onInteractiveInternalHandled){return;}if(typeof(req.onInteractive)=="function"){req.onInteractive(req);}req.onInteractiveInternalHandled=true;};
req.onCompleteInternal=
function(){if(req.onCompleteInternalHandled || req.aborted){return;}req.onCompleteInternalHandled=true;
AjaxRequest.numActiveAjaxRequests--;
if(AjaxRequest.numActiveAjaxRequests==0 && typeof(window['AjaxRequestEnd'])=="function"){AjaxRequestEnd(req.groupName);}if(req.groupName!=null){AjaxRequest.numActiveAjaxGroupRequests[req.groupName]--;
if(AjaxRequest.numActiveAjaxGroupRequests[req.groupName]==0 && typeof(req.onGroupEnd)=="function"){req.onGroupEnd(req.groupName);}}req.responseReceived =true;
req.status =req.xmlHttpRequest.status;
req.statusText =req.xmlHttpRequest.statusText;
req.responseText =req.xmlHttpRequest.responseText;
req.responseXML =req.xmlHttpRequest.responseXML;
if(typeof(req.onComplete)=="function"){req.onComplete(req);}if(req.xmlHttpRequest.status==200 && typeof(req.onSuccess)=="function"){req.onSuccess(req);}else if(typeof(req.onError)=="function"){req.onError(req);}delete req.xmlHttpRequest['onreadystatechange'];
req.xmlHttpRequest =null;};
req.onTimeoutInternal=
function(){if(req!=null && req.xmlHttpRequest!=null && !req.onCompleteInternalHandled){req.aborted =true;
req.xmlHttpRequest.abort();
AjaxRequest.numActiveAjaxRequests--;
if(AjaxRequest.numActiveAjaxRequests==0 && typeof(window['AjaxRequestEnd'])=="function"){AjaxRequestEnd(req.groupName);}if(req.groupName!=null){AjaxRequest.numActiveAjaxGroupRequests[req.groupName]--;
if(AjaxRequest.numActiveAjaxGroupRequests[req.groupName]==0 && typeof(req.onGroupEnd)=="function"){req.onGroupEnd(req.groupName);}}if(typeof(req.onTimeout)=="function"){req.onTimeout(req);}delete req.xmlHttpRequest['onreadystatechange'];
req.xmlHttpRequest =null;}};
req.process =
function(){if(req.xmlHttpRequest!=null){if(req.generateUniqueUrl && req.method=="GET"){req.parameters["AjaxRequestUniqueId"] =new Date().getTime() + "" + req.requestIndex;}var content =null;
for(var i in req.parameters){if(req.queryString.length>0){req.queryString +="&";}req.queryString +=encodeURIComponent(i) + "=" + encodeURIComponent(req.parameters[i]);}if(req.method=="GET"){if(req.queryString.length>0){req.url +=((req.url.indexOf("?")>-1)?"&":"?") + req.queryString;}}req.xmlHttpRequest.open(req.method,req.url,req.async,req.username,req.password);
if(req.method=="POST"){if(typeof(req.xmlHttpRequest.setRequestHeader)!="undefined"){req.xmlHttpRequest.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');}content =req.queryString;}if(req.timeout>0){setTimeout(req.onTimeoutInternal,req.timeout);}req.xmlHttpRequest.send(content);}};
req.handleArguments =
function(args){for(var i in args){if(typeof(req[i])=="undefined"){req.parameters[i] =args[i];}else{req[i] =args[i];}}};
req.getAllResponseHeaders =
function(){if(req.xmlHttpRequest!=null){if(req.responseReceived){return req.xmlHttpRequest.getAllResponseHeaders();}alert("Cannot getAllResponseHeaders because a response has not yet been received");}};
req.getResponseHeader =
function(headerName){if(req.xmlHttpRequest!=null){if(req.responseReceived){return req.xmlHttpRequest.getResponseHeader(headerName);}alert("Cannot getResponseHeader because a response has not yet been received");}};
return req;}AjaxRequest.getXmlHttpRequest =function(){if(window.XMLHttpRequest){return new XMLHttpRequest();}else if(window.ActiveXObject){/*@cc_on @*/
/*@if(@_jscript_version >=5)
try{return new ActiveXObject("Msxml2.XMLHTTP");}catch(e){try{return new ActiveXObject("Microsoft.XMLHTTP");}catch(E){return null;}}@end @*/}else{return null;}};
AjaxRequest.isActive =function(){return(AjaxRequest.numActiveAjaxRequests>0);};
AjaxRequest.get =function(args){AjaxRequest.doRequest("GET",args);};
AjaxRequest.post =function(args){AjaxRequest.doRequest("POST",args);};
AjaxRequest.doRequest =function(method,args){if(typeof(args)!="undefined" && args!=null){var myRequest =new AjaxRequest();
myRequest.method =method;
myRequest.handleArguments(args);
myRequest.process();}};
AjaxRequest.submit =function(theform, args){var myRequest =new AjaxRequest();
if(myRequest==null){return false;}var serializedForm =AjaxRequest.serializeForm(theform);
myRequest.method =theform.method.toUpperCase();
myRequest.url =theform.action;
myRequest.handleArguments(args);
myRequest.queryString =serializedForm;
myRequest.process();
return true;};
AjaxRequest.serializeForm =function(theform){var els =theform.elements;
var len =els.length;
var queryString ="";
this.addField =
function(name,value){if(queryString.length>0){queryString +="&";}queryString +=encodeURIComponent(name) + "=" + encodeURIComponent(value);};
for(var i=0;i<len;i++){var el =els[i];
if(!el.disabled){switch(el.type){case 'text': case 'password': case 'hidden': case 'textarea':
this.addField(el.name,el.value);
break;
case 'select-one':
if(el.selectedIndex>=0){this.addField(el.name,el.options[el.selectedIndex].value);}break;
case 'select-multiple':
for(var j=0;j<el.options.length;j++){if(el.options[j].selected){this.addField(el.name,el.options[j].value);}}break;
case 'checkbox': case 'radio':
if(el.checked){this.addField(el.name,el.value);}break;}}}return queryString;};
AjaxRequest.numActiveAjaxRequests =0;
AjaxRequest.numActiveAjaxGroupRequests =new Object();
AjaxRequest.numAjaxRequests =0;
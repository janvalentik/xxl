//NOTE: The leet button also removes formatting!
(function() {
   	tinymce.create('tinymce.plugins.LeetPlugin', {
      		init : function(ed, url) {
	 		ed.addCommand('mceLeet', function(ui, v) {
				ed.selection.setContent(leetspeak(ed.selection.getContent({format: 'text'}),60));
			});
	 		ed.addButton('leet', { 
	    			title : 'Translate to l33t',
				image : url+'/images/leet.png',
				cmd : 'mceLeet'
			}); 
		}, 
		getInfo : function() {
	 		return {
	    			longname : 'Leet Generator', 
				author : 'Jess Mann',
				authorurl : 'http://jess-mann.com', 
				infourl : 'http://jess-mann.com', 
				version : 1
			}; 
		}
	}); 
	tinymce.PluginManager.add('leet', tinymce.plugins.LeetPlugin); 
})(); 

function leetspeak(text,level) {
	if (level < 1) {
		return text;
	}

	//Word conversions first
	text = text.replace(/\b(is|am)\b/gi,'be');
	text = text.replace(/\bare\b/gi,'is');
	text = text.replace(/\bthe\b/gi,'teh');
	text = text.replace(/\ban\b/gi,'a');
	text = text.replace(/ing\b/gi,'in');
	text = text.replace(/\b(l)?ate(r)?\b/gi,'$18$2');
	text = text.replace(/\bare?\b/gi,'R');
	text = text.replace(/\bbe(e|a)?\b/gi,'b');
	text = text.replace(/\btw?oo?\b/gi,'2');
	text = text.replace(/\bat\b/gi,'@');
	text = text.replace(/\bfo?r\b/gi,'4');
	text = text.replace(/\b(thank\s+(yo)?u|gracias|t(han)?ks)\b/gi,'TY');
	text = text.replace(/\b(software\s)?(architect|enginee?r|desig?ner|hac?ker|coder|programm?er)(s|z)?\b/gi,'haxor$3');
	text = text.replace(/\b(hack|broke?)(ed|s|z)?\b/gi,'haxor$2');
	text = text.replace(/\b(software|program(s|z)?)(s|z)?\b/gi,'wares');
	text = text.replace(/\b(luve?|love|wuve?|like|enjoy)(s|z)?\b/gi,'wub$2');
	text = text.replace(/\b(newbi?e?|rooki?e?|new|child|kidi?e?|young)(s|z)?\b/gi,'noob$2');
	text = text.replace(/\b(money|stuff|eq(uipment)?|posess?ions)(s|z)?\b/gi,'loot');
	text = text.replace(/\bknew\b/gi,'gnu');
	text = text.replace(/\bknow\b/gi,'gno');
	text = text.replace(/\b(elite|eleet)\b/gi,'leet');
	text = text.replace(/\b((am|is|are)\s+(\w+)\s+(terrific|senstational|fantastic|cool|wicked|phat|awesome|great|good))\b/gi,'$3 rocks');
	text = text.replace(/\b((am|is|are)\s+(terrific|sensational|fantastic|cool|wicked|phat|awesome|great|good))\b/gi,'rocks');
	text = text.replace(/\b(really|very|extremely|absolutely|completely)\b/gi,'totally');
	text = text.replace(/\b(because|cause|cuz|bc(ause)?)\b/gi,'coz');
	text = text.replace(/\b(due\s+to|as\s+a\s+result\s+of)\b/gi,'coz of');
	text = text.replace(/(legendary|memorable|notorious|infamous)\b/gi,'epic');
	text = text.replace(/\b(win|own|kill|beat|shoot|hit|destroy|sink)(s|z)?\b/gi,'pwn$2');
	text = text.replace(/\b(winn?|own|kill?|beat|destroy|champion)((e|o|a)r)?\b/gi,'pwnzor');
	text = text.replace(/\b(won|owned|beat(\s+up)?|knoc?k\s+out|killed|shot|hit|destroyed|sunk)\b/gi,'pwnt');
	text = text.replace(/\b(is|are|am +)?(suck|sux|bad|terrible|awe?full?)(ed|s|z|ing?)?\b/gi,'suxor$3');
	text = text.replace(/\b(wa(s|z)|were|had\s+been)/gi,'wuz');
	text = text.replace(/\byou\b/gi,'joo');
	text = text.replace(/\b(big|large|huge|humungo?us|enormo?us|giant|gargantuan|mass?ive|impressive)\b/gi,'fat');
	text = text.replace(/\b(rock)(s|z|ed)?\b/gi,'roxor$2');
	text = text.replace(/\b(porn(o(graph(y|ic))?)?)\b/gi,'pron');
	text = text.replace(/\b(dork|jerk|moron|idiot|baff?oon|sucker)\b/gi,'loser');
	text = text.replace(/\bwh?at\b/gi,'wot');
	text = text.replace(/\bth(at|is|ose)\b/gi,'d$1');
	text = text.replace(/\b(ha|hoo?ray?|yipp?ee?|yay(a(h|y)?)?)\b/gi,'woot');
	text = text.replace(/\b(man|dude|guy|boy)(s|z)?\b/gi,'dood$2');
	text = text.replace(/\b(men)\b/gi,'doods');
	text = text.replace(/\b(overjoyed|ecstatic|euphoric|stoked|happy|exc?ited|thrill?ed|stimulated)\b/gi,'geeked');
	text = text.replace(/\b(unhappy|sad|depressed|miserable|sorry)\b/gi,'bummed');
	text = text.replace(/\b(and|an)\b/gi,'n');
	text = text.replace(/\b(lol(s|z)|laugh(s|z)|humor)\b/gi,'lulz');
	text = text.replace(/\b(lol|laugh)(ing?|ed)\b/gi,'lawl$2');
	text = text.replace(/\b(shout|yell|growl|hiss)(ed|s|z)\b/gi,'rawr$2');
	text = text.replace(/\b((good)?bye)\b/gi,'ttyl');
	text = text.replace(/\blight\b/gi,'lite');
	text = text.replace(/\bfuck(ed|s|z)\b/gi,'fuxxor$1');
	text = text.replace(/\bsperm(ed|s|z)\b/gi,'spooge$1');
	text = text.replace(/\b(breast|boob|tit|knocker|hooter)(s|z)\b/gi,'h00tr$2');
	text = text.replace(/\b(tech?ni(q|k)u?e)(s|z)\b/gi,'tekniq$3');
	text = text.replace(/\bbreak(ed|s|z|ing?)\b/gi,'nerf$1');
	text = text.replace(/\b(abuse|harr?ass?|ann?oi?y?|piss\s+off|irr?itate)(ed|s|z|ing?)\b/gi,'flame$2');
	text = text.replace(/\b(griefer|instigator)\b/gi,'troll');
	text = text.replace(/\b(griefing|instigating)\b/gi,'trolling');
	text = text.replace(/\bor\b/gi,'orz');
	text = text.replace(/\bt(au|o)(gh)?t\b/gi,'schooled');
	text = text.replace(/\b(slut|w?hore|bitch|skank|prostitute)(\s|\-|\_)?(s|z|ed|ing?|whore)\b/gi,'skrut$3');
	text = text.replace(/\bsex\b/gi,'sexor');

	//Letter conversions, for the hardcore l33ters
	if (level > 99) {
		text = text.replace(/g/gi,'(_+');
		text = text.replace(/r/gi,'|2');
		text = text.replace(/o/gi,'()');
		text = text.replace(/a/gi,'/-\\');
		text = text.replace(/h/gi,'|-|');
		text = text.replace(/d/gi,'|)');
		text = text.replace(/d/gi,'|3');
		text = text.replace(/j/gi,'_)');
		text = text.replace(/y/gi,'\'/');
	}
	if (level > 90) {
		text = text.replace(/x/gi,'}{');
		text = text.replace(/f/gi,'|=');
		text = text.replace(/l/gi,'|_');
		text = text.replace(/k/gi,'|{');
	}
	if (level > 80) {
		text = text.replace(/ck/gi,'k');
		text = text.replace(/m/gi,'/\\/\\');
		text = text.replace(/n/gi,'|\\|');
		text = text.replace(/u/gi,'|_|');
		text = text.replace(/v/gi,'\\/');
		text = text.replace(/w/gi,'(/\\)');
		text = text.replace(/\ba\b/gi,'@');
	}
	if (level > 70) {
		text = text.replace(/h/gi,'#');
		text = text.replace(/i/gi,'!');
	}
	if (level > 60) {
		text = text.replace(/t/gi,'+');
		text = text.replace(/and/gi,'&');
	}
	if (level > 50) {
		text = text.replace(/g/gi,'6');
	}
	if (level > 40) {
		text = text.replace(/f/gi,'ph');
		text = text.replace(/z/gi,'2');
	}
	if (level > 30) {
		text = text.replace(/t/gi,'7');
		text = text.replace(/a/gi,'4');
	}
	if (level > 20) {
		text = text.replace(/s\b/gi,'z');
	}
	if (level > 10) {
		text = text.replace(/e/gi,'3');
		text = text.replace(/l/gi,'1');
		text = text.replace(/o/gi,'0');
	}
	return text;
}

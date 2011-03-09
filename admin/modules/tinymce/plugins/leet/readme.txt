ABOUT THIS PLUGIN
-----------------------------------------------------------------------
TinyMCE l33t plugin by Jess Mann
Description: Adds a "Translate to 1337" button to tinymce, which 
converts selected text to "leetspeak" in varying degrees.
Author: Jess Mann
Site: http://jess-mann.com
Copyright: 2009 Jess Mann
License: LGPL - http://www.gnu.org/copyleft/lesser.html

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

EXAMPLES
-----------------------------------------------------------------------
0%:   This is an example of an elite programmer. He's really cool.
20%:  dis b a 3xamp13 0f a 133t hax0r. H3's t0ta11y c001.
60%:  diz b 4 3x4mp13 0ph 4 1337 h4x0r. H3'z 707411y c001.
80%:  d!z b 4 3x4mp13 0ph 4 133+ #4x0r. #3'z +0+411y c001. 
90%:  d!z b @ 3x4/\/\p13 0ph @ 133+ #4x0r. #3'z +0+411y c001. 
100%: |)!z b /-\ 3}{/-\/\/\p|_3 ()|= /-\ |_33+ |-|/-\}{()r. |-|3'z +()+/-\|_|_'/ c()()|_. 

THANKS
-----------------------------------------------------------------------
The icon used is from the "silk icon set" at famfamfam.com. I've used
this icon set more than once, and I'm truly appreciative of the author
for their hard work. 
The icon is licensed under the Creative Commons Attribution 2.5 license.
http://creativecommons.org/licenses/by/2.5/

Also, thanks to the various l33t dictionaries found on google for 
the help with translations... since, I don't really speak 1337 myself.

INSTALLATION
-----------------------------------------------------------------------
To use this plugin, after unzipping the archive, place this directory
in the "tinymce/plugins" folder of your tinymce install. Then, place 
"leet" in the list of plugins when initializing tinyMCE, and "leet" 
in the button list, to place a button in the appropriate area. 
For example:

	tinyMCE.init({
        	mode : "textareas",
                theme : "advanced",
               	theme_advanced_buttons1:"bold,italic,underline,strikethrough,leet",
                plugins:"safari,fullscreen,leet",
	});


USE
-----------------------------------------------------------------------
To make use of the plugin, first highlight the text you wish to translate
with your mouse, then click the "Leet Translator" button to see it 
converted. That's it! Please note, it does remove all formatting, so
create your text first, convert it, then format to your heart's content.

CUSTOMIZATION
-----------------------------------------------------------------------
To change the appearance of the button, place the image you would like
to use in the /images directory, named "leet.png". Refresh your browser
once (Hold CONTROL and press F5) to see the change. (I've had to 
refresh multiple times, or restart my browser, in some cases - depending
on the browser, version and operating system you're using.)

To change the amount of "leet-ness" the plugin converts to, open up
the "editor_plugin.js" file, and find the 6th line:
	ed.selection.setContent(leetspeak(ed.selection.getContent({format: 'text'}),60));
Change the number 60 to any number between 0 and 100 (0 being less leet, 
and 100 being most leet). For example, try any of the following:
	ed.selection.setContent(leetspeak(ed.selection.getContent({format: 'text'}),1));
	ed.selection.setContent(leetspeak(ed.selection.getContent({format: 'text'}),20));
	ed.selection.setContent(leetspeak(ed.selection.getContent({format: 'text'}),40));
	ed.selection.setContent(leetspeak(ed.selection.getContent({format: 'text'}),80));
	ed.selection.setContent(leetspeak(ed.selection.getContent({format: 'text'}),100));

To change the dictionary, or any of the character conversions, you will 
need to know a bit of javascript, and a fair amount of regular expressions.
You may take a look at the script starting on line 27, but unless you
are proficient with both js and regex, I would advise against changing
anything.

KNOWN ISSUES
-----------------------------------------------------------------------
* All converted text is stripped of formatting, if any. This will not
  be fixed, as the user can easily learn to translate the text prior
  to formatting, and frankly, my head hurts enough from just having
  created the tool to begin with.

ABOUT THE AUTHOR
-----------------------------------------------------------------------
I am a professional software/web developer, and I freelance for a living.
If you're interested in hiring me for work, feel free to contact me
at my email address: jess@jess-mann.com, and view my portfolio online
on my website: http://jess-mann.com

Also, if you've found my work to be useful, you are welcome to donate.
My paypal account is my email address: jess@jess-mann.com

I hope you've found this plugin helpful. Good Luck!

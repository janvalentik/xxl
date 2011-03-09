<?php
	/**
	 * language pack
	 * @authors Pavel Sindelka (pavel@sindelka.cz), Josef Čech (uzivatelcech@centrum.cz)
	 * @link www.sindelka.cz
	 * @since 28/September/2009
	 */
	define('DATE_TIME_FORMAT', 'd.m.Y H:i:s');
	//Common
  	//Menu	
  	define('MENU_SELECT', 'Vybrat');
  	define('MENU_DOWNLOAD', 'Stáhnout');
  	define('MENU_PREVIEW', 'Náhled');
  	define('MENU_RENAME', 'Přejmenovat');
  	define('MENU_EDIT', 'Editovat');
  	define('MENU_CUT', 'Vyjmout');
  	define('MENU_COPY', 'Kopírovat');
  	define('MENU_DELETE', 'Smazat');
  	define('MENU_PLAY', 'Play');
  	define('MENU_PASTE', 'Vložit');	
  	//Label
  		//Top Action
  		define('LBL_ACTION_REFRESH', 'Obnovit');
  		define("LBL_ACTION_DELETE", 'Odstranit');
  		define('LBL_ACTION_CUT', 'Vyjmout');
  		define('LBL_ACTION_COPY', 'Kopírovat');
  		define('LBL_ACTION_PASTE', 'Vložit');
  		define('LBL_ACTION_CLOSE', 'Zavřít');
  		define('LBL_ACTION_SELECT_ALL', 'Vybrat vše');
  		//File Listing
    	define('LBL_NAME', 'Název');
    	define('LBL_SIZE', 'Velikost');
    	define('LBL_MODIFIED', 'Změněno');
  		//File Information
    	define('LBL_FILE_INFO', 'Informace o souboru:');
    	define('LBL_FILE_NAME', 'Název:');	
    	define('LBL_FILE_CREATED', 'Vytvořen:');
    	define("LBL_FILE_MODIFIED", 'Upraven:');
    	define("LBL_FILE_SIZE", 'Velikost:');
    	define('LBL_FILE_TYPE', 'Typ souboru:');
    	define("LBL_FILE_WRITABLE", 'Zápis?');
    	define("LBL_FILE_READABLE", 'Čtení?');
  		//Folder Information
    	define('LBL_FOLDER_INFO', 'Informace o adresáři');
    	define("LBL_FOLDER_PATH", 'Cesta:');
    	define("LBL_FOLDER_CREATED", 'Vytvořen:');
    	define("LBL_FOLDER_MODIFIED", 'Upraven:');
    	define('LBL_FOLDER_SUDDIR', 'Podadresáře:');
    	define("LBL_FOLDER_FIELS", 'Soubory:');
    	define("LBL_FOLDER_WRITABLE", 'Zápis?');
    	define("LBL_FOLDER_READABLE", 'Čtení?');
    	define('LBL_CURRENT_FOLDER_PATH', 'Současná cesta k adresáři:');
    	define('LBL_FOLDER_ROOT', 'Kořenový adresář');
  		//Preview
    	define("LBL_PREVIEW", 'Náhled');
    	define('LBL_CLICK_PREVIEW', 'Klikněte pro náhled.');
    	//Buttons
    	define('LBL_BTN_SELECT', 'Vybrat');
    	define('LBL_BTN_CANCEL', 'Zrušit');
    	define("LBL_BTN_UPLOAD", 'Upload');
    	define('LBL_BTN_CREATE', 'Vytvořit');
    	define('LBL_BTN_CLOSE', 'Zavřít');
    	define("LBL_BTN_NEW_FOLDER", 'Nový adresář');
    	define('LBL_BTN_EDIT_IMAGE', 'Upravit');
    	define('LBL_BTN_NEW_FILE', 'Nový soubor');
    	define('LBL_BTN_VIEW', 'Vybrat zobrazení');
    	define('LBL_BTN_VIEW_TEXT', 'Text');
    	define('LBL_BTN_VIEW_DETAILS', 'Detaily');
    	define('LBL_BTN_VIEW_THUMBNAIL', 'Miniatury');
    	define('LBL_BTN_VIEW_OPTIONS', 'Zobraz jako:');
  	//Pagination
  	define('PAGINATION_NEXT', 'Další');
  	define('PAGINATION_PREVIOUS', 'Předchozí');
  	define('PAGINATION_LAST', 'Poslední');
  	define('PAGINATION_FIRST', 'První');
  	define('PAGINATION_ITEMS_PER_PAGE', 'Zobraz %s položek na stránku');
  	define('PAGINATION_GO_PARENT', 'Rodičovský adresář');
	//System
	define('SYS_DISABLED', 'Přístup odepřen.');
	//Cut
	define('ERR_NOT_DOC_SELECTED_FOR_CUT', 'Nevybral jste soubor(y) pro akci ´Vyjmout´.');
	//Copy
	define('ERR_NOT_DOC_SELECTED_FOR_COPY', 'Nevybral jste soubor(y) pro akci ´Kopírovat´.');
	//Paste
	define('ERR_NOT_DOC_SELECTED_FOR_PASTE', 'Nevybral jste soubor(y) pro akci ´Vložit´.');
	define('WARNING_CUT_PASTE', 'Jste si jistý, že chcete přesunout vybrané soubory do aktuálního adresáře?');
	define('WARNING_COPY_PASTE', 'Jste si jistý, že chcete zkopírovat vybrané soubory do aktuálního adresáře?');
	define('ERR_NOT_DEST_FOLDER_SPECIFIED', 'Nebyl vybrán cílový adresář.');
	define('ERR_DEST_FOLDER_NOT_FOUND', 'Cílový adresář nenalezen.');
	define('ERR_DEST_FOLDER_NOT_ALLOWED', 'Nemáte povolení přesunout soubory do tohoto adresáře.');
	define('ERR_UNABLE_TO_MOVE_TO_SAME_DEST', 'Chyba při přesouvání souboru (%s): Původní adresář je stejný jako cílový.');
	define('ERR_UNABLE_TO_MOVE_NOT_FOUND', 'Chyba při přesouvání souboru (%s): Soubor neexistuje.');
	define('ERR_UNABLE_TO_MOVE_NOT_ALLOWED', 'Chyba při přesouvání souboru (%s): Přístup k souboru odepřen.');
	define('ERR_NOT_FILES_PASTED', 'Žádný(/é) soubor nebyl vložen.');
	//Search
	define('LBL_SEARCH', 'Hledat');
	define('LBL_SEARCH_NAME', 'Celé/částečné jméno souboru:');
	define('LBL_SEARCH_FOLDER', 'Náhled:');
	define('LBL_SEARCH_QUICK', 'Rychlé hledání');
	define('LBL_SEARCH_MTIME', 'Čas změny souboru(rozmezí):');
	define('LBL_SEARCH_SIZE', 'Velikost souboru:');
	define('LBL_SEARCH_ADV_OPTIONS', 'Pokročilé volby:');
	define('LBL_SEARCH_FILE_TYPES', 'Typy souborů:');
	define('SEARCH_TYPE_EXE', 'Aplikace');
	define('SEARCH_TYPE_IMG', 'Obrázek');
	define('SEARCH_TYPE_ARCHIVE', 'Archiv');
	define('SEARCH_TYPE_HTML', 'HTML');
	define('SEARCH_TYPE_VIDEO', 'Video');
	define('SEARCH_TYPE_MOVIE', 'Film');
	define('SEARCH_TYPE_MUSIC', 'Hudba');
	define('SEARCH_TYPE_FLASH', 'Flash');
	define('SEARCH_TYPE_PPT', 'PowerPoint');
	define('SEARCH_TYPE_DOC', 'Dokument');
	define('SEARCH_TYPE_WORD', 'Word');
	define('SEARCH_TYPE_PDF', 'PDF');
	define('SEARCH_TYPE_EXCEL', 'Excel');
	define('SEARCH_TYPE_TEXT', 'Text');
	define('SEARCH_TYPE_UNKNOWN', 'Neznámý');
	define('SEARCH_TYPE_XML', 'XML');
	define('SEARCH_ALL_FILE_TYPES', 'Všechny typy souborů:');
	define('LBL_SEARCH_RECURSIVELY', 'Hledat rekurzivně:');
	define('LBL_RECURSIVELY_YES', 'Ano');
	define('LBL_RECURSIVELY_NO', 'Ne');
	define('BTN_SEARCH', 'Hledat nyní');
	//Thickbox
	define('THICKBOX_NEXT', 'Další&gt;');
	define('THICKBOX_PREVIOUS', '&lt;Předchozí');
	define('THICKBOX_CLOSE', 'Zavřít');
	//Calendar
	define('CALENDAR_CLOSE', 'Zavřít');
	define('CALENDAR_CLEAR', 'Vyčistit');
	define('CALENDAR_PREVIOUS', '&lt;Předchozí');
	define('CALENDAR_NEXT', 'Další&gt;');
	define('CALENDAR_CURRENT', 'Dnes');
	define('CALENDAR_MON', 'Po');
	define('CALENDAR_TUE', 'Út');
	define('CALENDAR_WED', 'St');
	define('CALENDAR_THU', 'Čt');
	define('CALENDAR_FRI', 'Pá');
	define('CALENDAR_SAT', 'So');
	define('CALENDAR_SUN', 'Ne');
	define('CALENDAR_JAN', 'Led.');
	define('CALENDAR_FEB', 'Únor');
	define('CALENDAR_MAR', 'Bře.');
	define('CALENDAR_APR', 'Dub.');
	define('CALENDAR_MAY', 'Kvě.');
	define('CALENDAR_JUN', 'Črv.');
	define('CALENDAR_JUL', 'Črc.');
	define('CALENDAR_AUG', 'Srp.');
	define('CALENDAR_SEP', 'Zří');
	define('CALENDAR_OCT', 'Říj.');
	define('CALENDAR_NOV', 'Lis.');
	define('CALENDAR_DEC', 'Pro.');
	//ERROR MESSAGES
		//deletion
  	define('ERR_NOT_FILE_SELECTED', 'Prosím, vyberte nějaký soubor.');
  	define('ERR_NOT_DOC_SELECTED', 'Nevybral jste soubor(y) pro akci ´Odstranit´.');
  	define('ERR_DELTED_FAILED', 'Není možné odstranit vybrané soubor(y).');
  	define('ERR_FOLDER_PATH_NOT_ALLOWED', 'Cesta adresáře není povolená.');
		//class manager
  	define("ERR_FOLDER_NOT_FOUND", 'Nelze nalézt specifický adresář: ');
		//rename
  	define('ERR_RENAME_FORMAT', 'Název souboru/adresáře může obsahovat jen písmena bez diakritiky, číslice, mezery, pomlčky a podtržítka.');
  	define('ERR_RENAME_EXISTS', 'Adresář již existuje, zkuste jiný unikátní název.');
  	define('ERR_RENAME_FILE_NOT_EXISTS', 'Soubor/adresář neexistuje.');
  	define('ERR_RENAME_FAILED', 'Není možné přejmenovat, zkuste znovu.');
  	define('ERR_RENAME_EMPTY', 'Vyplňte název.');
  	define("ERR_NO_CHANGES_MADE", 'Nebyly provedeny žádné zmeny.');
  	define('ERR_RENAME_FILE_TYPE_NOT_PERMITED', 'Není povoleno měnit příponu souboru.');
		//folder creation
  	define('ERR_FOLDER_FORMAT', 'Název souboru/adresáře může obsahovat jen písmena bez diakritiky, číslice, mezery, pomlčky a podtržítka.');
  	define('ERR_FOLDER_EXISTS', 'Adresář již existuje, zkuste jiný unikátní název.');
  	define('ERR_FOLDER_CREATION_FAILED', 'Není možné vytvořit adresář, zkuste znovu.');
  	define('ERR_FOLDER_NAME_EMPTY', 'Vyplňte název.');
  	define('FOLDER_FORM_TITLE', 'Nový adresář:');
  	define('FOLDER_LBL_TITLE', 'Název adresáře:');
  	define('FOLDER_LBL_CREATE', 'Vytvořit adresář');
	//New File
	define('NEW_FILE_FORM_TITLE', 'Nový soubor:');
	define('NEW_FILE_LBL_TITLE', 'Jméno souboru:');
	define('NEW_FILE_CREATE', 'Vytvořit soubor');
	//file upload
	define("ERR_FILE_NAME_FORMAT", 'Název souboru/adresáře může obsahovat jen písmena bez diakritiky, číslice, mezery, pomlčky a podtržítka.');
	define('ERR_FILE_NOT_UPLOADED', 'Není vybrán žádný soubor pro upload.');
	define('ERR_FILE_TYPE_NOT_ALLOWED', 'Nemáte právo uploadovat soubory s touto příponou.');
	define('ERR_FILE_MOVE_FAILED', 'Nepodařilo se presunout soubor.');
	define('ERR_FILE_NOT_AVAILABLE', 'Soubor je nedostupný.');
	define('ERROR_FILE_TOO_BID', 'Soubor je příliš velký. (max: %s)');
	define('FILE_FORM_TITLE', 'Nahrání souboru:');
	define('FILE_LABEL_SELECT', 'Vybrat soubor:');
	define('FILE_LBL_MORE', 'Přidat nový File Uploader');
	define('FILE_CANCEL_UPLOAD', 'Zrušit nahrávání souboru');
	define('FILE_LBL_UPLOAD', 'Uploadovat');
	//file download
	define('ERR_DOWNLOAD_FILE_NOT_FOUND', 'Není vybrán žádný soubor ke stažení.');
	//Rename
	define('RENAME_FORM_TITLE', 'Přejmenování:');
	define('RENAME_NEW_NAME', 'Nové jméno:');
	define('RENAME_LBL_RENAME', 'Přejmenovat');
	//Tips
	define('TIP_FOLDER_GO_DOWN', 'Jeden klik a dostanete se do tohoto adresáře...');
	define("TIP_DOC_RENAME", 'Dvojitý klik pro úpravu...');
	define('TIP_FOLDER_GO_UP', 'Jeden klik a dostanete se do rodičovského adresáře...');
	define("TIP_SELECT_ALL", 'Označit všechno');
	define("TIP_UNSELECT_ALL", 'Zrušit označené');
	//WARNING
	define('WARNING_DELETE', 'Opravdu chcete odstranit označené soubory?');
	define('WARNING_IMAGE_EDIT', 'Vyberte obrázek pro úpravu, prosím.');
	define('WARNING_NOT_FILE_EDIT', 'Vyberte soubor pro úpravu, prosím.');
	define('WARING_WINDOW_CLOSE', 'Opravdu chcete okno zavřít?');
	//Preview
	define('PREVIEW_NOT_PREVIEW', 'Náhled není dostupný.');
	define('PREVIEW_OPEN_FAILED', 'Není možné otevřít soubor.');
	define('PREVIEW_IMAGE_LOAD_FAILED', 'Není možné načíst obrázek.');
	//Login
	define('LOGIN_PAGE_TITLE', 'Ajax File Manager Login Formulář');
	define('LOGIN_FORM_TITLE', 'Login Formulář');
	define('LOGIN_USERNAME', 'Uživatelské jmeno:');
	define('LOGIN_PASSWORD', 'Heslo:');
	define('LOGIN_FAILED', 'Neplatné uživatelské jméno či heslo.');

	//88888888888   Below for Image Editor   888888888888888888888
		//Warning 
		define('IMG_WARNING_NO_CHANGE_BEFORE_SAVE', "Žádné obrázky nebyly změněné.");
		//General
		define('IMG_GEN_IMG_NOT_EXISTS', 'Obrázek neexistuje');
		define('IMG_WARNING_LOST_CHANAGES', 'Všechny neuložené úpravy budu ztraceny, opravdu chcete pokračovat?');
		define('IMG_WARNING_REST', 'Všechny neuložené úpravy budu ztraceny, opravdu chcete zrušit všechny změny?');
		define('IMG_WARNING_EMPTY_RESET', 'Obrázek zatím nebyl upraven');
		define('IMG_WARING_WIN_CLOSE', 'Opravdu chcete okno zavřít?');
		define('IMG_WARNING_UNDO', 'Opravdu chcete vrátit obrázek do původního stavu?');
		define('IMG_WARING_FLIP_H', 'Opravdu chcete překlopit obrázek vodorovně?');
		define('IMG_WARING_FLIP_V', 'Opravdu chcete překlopit obrázek svisle?');
		define('IMG_INFO', 'Info o obrázku');
		//Mode
		define('IMG_MODE_RESIZE', 'Změna velikosti :');
		define('IMG_MODE_CROP', 'Odříznout :');
		define('IMG_MODE_ROTATE', 'Otočit :');
		define('IMG_MODE_FLIP', 'Překlopit:');
		//Button
		define('IMG_BTN_ROTATE_LEFT', '90&deg;CCW');
		define('IMG_BTN_ROTATE_RIGHT', '90&deg;CW');
		define('IMG_BTN_FLIP_H', 'Překlopit vodorovně');
		define('IMG_BTN_FLIP_V', 'Překlopit svisle');
		define('IMG_BTN_RESET', 'Zrušit všechny změny');
		define('IMG_BTN_UNDO', 'Zpět');
		define('IMG_BTN_SAVE', 'Uložit');
		define('IMG_BTN_CLOSE', 'Zavřít');
		define('IMG_BTN_SAVE_AS', 'Uložit jako');
		define('IMG_BTN_CANCEL', 'Storno');			
		//Checkbox
		define('IMG_CHECKBOX_CONSTRAINT', 'Omezit?');
		//Label
		define('IMG_LBL_WIDTH', 'Šířka:');
		define('IMG_LBL_HEIGHT', 'Výška:');
		define('IMG_LBL_X', 'X:');
		define('IMG_LBL_Y', 'Y:');
		define('IMG_LBL_RATIO', 'Poměr:');
		define('IMG_LBL_ANGLE', 'Úhel:');
		define('IMG_LBL_NEW_NAME', 'Nový název:');
		define('IMG_LBL_SAVE_AS', 'Uložit jako...');
		define('IMG_LBL_SAVE_TO', 'Uložit jako:');
		define('IMG_LBL_ROOT_FOLDER', 'Kořenový adresář');			
		//Editor
		//Save as 
		define('IMG_NEW_NAME_COMMENTS', 'Prosím nezadávejte příponu souboru.');
		define('IMG_SAVE_AS_ERR_NAME_INVALID', 'Název souboru/adresáře může obsahovat jen písmena bez diakritiky, číslice, mezery, pomlčky a podtržítka.');
		define('IMG_SAVE_AS_NOT_FOLDER_SELECTED', 'Není zvolen cílový adresář.');	
		define('IMG_SAVE_AS_FOLDER_NOT_FOUND', 'Cílový adresář neexistuje.');
		define('IMG_SAVE_AS_NEW_IMAGE_EXISTS', 'There exists an image with same name.');			
		//Save
		define('IMG_SAVE_EMPTY_PATH', 'Cesta k obrázku je prázdná.');
		define('IMG_SAVE_NOT_EXISTS', 'Obrázek neexistuje.');
		define('IMG_SAVE_PATH_DISALLOWED', 'Nemáte přístup do tohoto souboru.');
		define('IMG_SAVE_UNKNOWN_MODE', 'Neočekávaný pracovní režim obrázku');
		define('IMG_SAVE_RESIZE_FAILED', 'Není možné změnit velikost obrázku.');
		define('IMG_SAVE_CROP_FAILED', 'Není možné odříznout obrázek.');
		define('IMG_SAVE_FAILED', 'Není možné uložit obrázek.');
		define('IMG_SAVE_BACKUP_FAILED', 'Není možné vytvořit zálohu původního obrázku.');
		define('IMG_SAVE_ROTATE_FAILED', 'Není možné otočit obrázek.');
		define('IMG_SAVE_FLIP_FAILED', 'Není možné překlopit obrázek.');
		define('IMG_SAVE_SESSION_IMG_OPEN_FAILED', 'Není možné otevřít obrázek ze session(relace).');
		define('IMG_SAVE_IMG_OPEN_FAILED', 'Není možné otevřít obrázek');
		//UNDO
		define('IMG_UNDO_NO_HISTORY_AVAIALBE', 'Historie není dostupná pro Zpět.');
		define('IMG_UNDO_COPY_FAILED', 'Není možné obnovit obrázek.');
		define('IMG_UNDO_DEL_FAILED', 'Není možné odstranit session obrázku');
	//88888888888   Above for Image Editor   888888888888888888888

	//88888888888   Session   888888888888888888888
		define("SESSION_PERSONAL_DIR_NOT_FOUND", 'Není možné najít příslušný adresář v adresáři relace(session).');
		define("SESSION_COUNTER_FILE_CREATE_FAILED", 'Není možné otevřít soubor relace(session).');
		define('SESSION_COUNTER_FILE_WRITE_FAILED', 'Není možné zapisovat do souboru relace(session).');
	//88888888888   Session   888888888888888888888
	
	//88888888888   Below for Text Editor   888888888888888888888
		define('TXT_FILE_NOT_FOUND', 'Soubor nenalezen.');
		define('TXT_EXT_NOT_SELECTED', 'Prosím zvolte příponu souboru.');
		define('TXT_DEST_FOLDER_NOT_SELECTED', 'Prosím zvolte cílový adresář');
		define('TXT_UNKNOWN_REQUEST', 'Neznámý požadavek.');
		define('TXT_DISALLOWED_EXT', 'Jste oprávněn měnit/přidávat soubory tohoto typu.');
		define('TXT_FILE_EXIST', 'Takovýto soubor již existuje.');
		define('TXT_FILE_NOT_EXIST', 'Takovýto soubor nenalezen.');
		define('TXT_CREATE_FAILED', 'Chyba při vytváření nového souboru.');
		define('TXT_CONTENT_WRITE_FAILED', 'Chyba při zápisu do souboru.');
		define('TXT_FILE_OPEN_FAILED', 'Chyba při otevírání souboru.');
		define('TXT_CONTENT_UPDATE_FAILED', 'Chyba při změně obsahu souboru.');
		define('TXT_SAVE_AS_ERR_NAME_INVALID', 'Název souboru/adresáře může obsahovat jen písmena bez diakritiky, číslice, mezery, pomlčky a podtržítka.');
	//88888888888   Above for Text Editor   888888888888888888888
	
<?php

// This is the English language file for Toby Web Mail.
//
// Because different languages have different rules for grammar, the context
// of each word or phrase is shown above it.  This is to show the tense
// and voice of any verbs used in the word or phrase, as well as distinguish
// between homonyms (words that are spelled the same but have different 
// meanings.
//
// To translate Toby, translate each of the lines to your choice of language,
// using the context lines as a guide.  *DO NOT CHANGE THE CAPITALIZED WORDS.*
// These are keywords used by Toby to reference the translated phrases. For
// example, to translate the line
//
// define('Adresse_BOOK','Adressees');
//
// into Spanish, you might change it to
//
// define('Adresse_BOOK','Direcciones');
//
// where 'Direcciones' is the Spanish equivalent to the English word "Adressees."
// (This may or may not be correct.  I only have a basic understanding of Spanish.
//
// Note: any apostrophes ("'") should be written as "'" to avoid syntax errors in the code.
// ie. the phrase "John's mail" should be written as "John's mail".
//
// If you would like to help in the translation of Toby, feel free to e-mail a 
// translated version of this file to chris@efinke.com, or contact chris@efinke.com
// for more information.  (You may wish to e-mail before doing any work on it,
// because someone else may already be translating to your language.)

// Do not change this line.
error_reporting(E_ALL ^ E_NOTICE);

// ################################################################################################### //
// ################# Here begins the English words and phrases that need translating ################# //
// ################################################################################################### //

// Months of the Year, pretty self-explanatory
$months =" array(1=">"Januar","Februar","März","April","Mai","Juni","Juli","August","September","Oktober","November","Dezember");

// Context: "Adressees" or "Adresse Book"
define('Adresse_BOOK','Adressen');

// Context: "Log in" to Toby Web Mail
define('LOG_IN','Login');

// Context: "Compose" an e-mail message
define('COMPOSE','Erstellen');

// Context: Reply to this message
define('REPLY','Antworten');

// Context: "Reply to All" recipients of this message
define('REPLY_TO_ALL','Allen antworten');

// Context: "Forward" this message to another person
define('FORWARD','Weiterleiten');

// Context: Switch to "text mode" for composing this message
define('TEXT_MODE','text modus');

// Context: Switch to "HTML mode" for composing this message
define('HTML_MODE','HTML modus');

// Context: "Send" this e-mail message
define('SEND','Senden');

// Context: "Get the messages" in the specified folder as a zip file
define('GET_MESSAGES','ausgewählte Nachrichten downloaden');

// Context: "Download messages" as a zip file
define('DOWNLOAD_MESSAGES','Nachriten downloaden');

// Context: New messages are stored in the "Inbox"
define('INBOX','Posteingang');

// Context: Deleted messages are stored in the "Trash"
define('TRASH','Mülleimer');

// Context: To permanently remove messages in the trash, "Empty" it.
define('EMPTY_TRASH','leeren');

// Context: I would like to "Add a Folder" to store messages in
define('ADD_FOLDER','Ordner hinzufügen');

// Context: "Delete the Folder" that I have specified
define('DELETE_FOLDER','Ordner löschen');

// Context: "Move the Folder" that I have specified into another folder.
define('MOVE_FOLDER','Ordner verschieben');

// Context: "Rename the Folder" that I have specified.
define('RENAME_FOLDER','Ordner umbenennen');

// Context: This application is called "Toby Web Mail"
define('APP_TITLE','Toby Web Mail');

// Context: I would like to "continue" logging in.
define('CONTINUE_BUTTON','Fortsetzen');

// Context: What is your "password"?
define('PASSWORD','Passwort');

// Context: "Add an Adresse" to the Adresse book
define('ADD_AN_Adresse','Eine Adresse hinzufügen');

// Context: What is the "name" of the person?
define('NAME','Name');

// Context: What is the "e-mail Adresse" of the person?
define('EMAIL_Adresse','E-mail Adresse');

// Context: "Add this Adresse" to the Adresse book
define('ADD_Adresse','Adresse hinzufügen');

// Context: "Update this Adresse" in the Adresse book.
define('UPDATE_Adresse','Adresse aktuallisieren');

// Context: "Delete the Adressees" that I have selected
define('DELETE_SELECTED_AdresseES','Ausgewählte Adresse(n) löschen');

// Context: This file is an "attachment" to this e-mail.
define('ATTACHMENT','Anlage');

// Context: I wish to edit the "attachments" for this message
define('ATTACHMENTS','Anlagen');

// Context: I wish to "Add an Attachment" to this e-mail
define('ADD_ATTACHMENT','Anlage hinzufügen');

// Context: Please "attach the file" that I have selecetd.
define('ATTACH_FILE','Datein anhängen');

// Context: the "name of this file" is abc.txt.
define('FILENAME','Dateiname');

// Context: the "size of this file" is 48 KB.
define('SIZE','Größe');

// Context: the "type of this file" is text/plain.
define('TYPE','Art');

// Context: This should be the abbreviation for kilobytes in this language.
define('KILOBYTES_UNIT','KB');

// Context: "Delete the attachments" that I have selected.
define('DELETE_SELECTED_ATTACHMENTS','Ausgewählte Anlage(n) löschen');

// Context: I would like to go "back to the message" that I am composing.
define('BACK_TO_MESSAGE','Zurück zur Nachricht');

// Context: On this screen, you can "manage the attachments" that you have added.
define('MANAGE_ATTACHMENTS','Anlagen verwalten');

// Context: This should be whatever is prefixed to the subject of a message when it is forwarded.
define('FORWARD_PREFIX','Fwd');

// Context: This should be whatever is prefixed to the subject of a message when it is replied to.
define('REPLY_PREFIX','Re');

// Context: I am sending this message "to" john@smith.com
define('SEND_TO','To');

// Context: This should be the abbreviation for sending a "carbon-copy" of an e-mail.
define('CC_TO','Cc');

// Context: This should be the abbreviation for sending a carbon-copy of an e-mail without the Adresse showing to the other recipients.
define('BCC_TO','Bcc');

// Context: The "subject" of this e-mail is "Hello".
define('SUBJECT','Betreff');

// Context: How many attachments are there? "None".
define('NONE','keine');

// Context: Please enter the "message" below.
define('MESSAGE','Nachricht');

// Context: I would like to "switch to" HTML mode.
define('SWITCH_MODE','Umschalten zu');

// Context: I would like to "save" a copy of this message "in" the selected folder.
define('SAVE_IN','Speichern im');

// Context: This message is "from" a friend.
define('FROM','Absender');

// Context: I "sent" this message.
define('SENT','Senden');

// Context: The "original message" to which I am replying appears below.
define('ORIGINAL_MESSAGE','Original Nachricht');

// Context: I would like to download message from "all of the folders".
define('ALL_FOLDERS','Alle Ordner');

// Context: I would like to download a "backup" of this message.
define('BACKUP','Backup');

// Context: This message has "no subject".
define('NO_SUBJECT','kein Betreff');

// Context: I would like to view the "folders" I have created.
define('FOLDERS','Ordner');

// Context: I would like to "view" my messages "by" the following criteria.
define('VIEW_BY','Sortiert nach');

// Context: The "sender" of the message.
define('SENDER','Absender');

// Context: The "recipient" of the message.
define('RECEIVER','Empfänger');

// Context: What "date" the message was received on.
define('DATE_STRING','Datum');

// Context: I would like to "upload the e-mail file" that I have selected.
define('UPLOAD_EMAIL','E-mail Datei hochladen');

// Context: I would like to "transfer the e-mail" in the folder I have selected.
define('TRANSFER_EMAIL','E-mail übertragen');

// Context: I would like to "cancel the transfer" that I have selected.
define('CANCEL_TRANSFER','Transfer abbrechen');

// Context: I would like to "retrieve the e-mail" that has been transferred to me.
define('RETRIEVE_EMAIL','E-mail abfragen');

// Context: I would like to "decline" this transfer.
define('DECLINE','Ablehnen');

// Context: the following are used to say 'Toby "successfully transfered # messages."'
define('TRANSFER_SUCCESS_BEGIN','Erfolgreich übertragen');
define('TRANSFER_SUCCESS_END','Nachricht(en).');

// Context: Would you like to add any "comments" to this message?
define('COMMENTS','Kommentare');

// Context: the "e-mail Adresse of the recipient" is abc@efg.com
define('DESTINATION_Adresse','E-mail ziel Adresse');

// Context: In what "language" would you like to use Toby?
define('LANGUAGE','Sprache');

// Context: I would like the change the "folder settings" of my account.
define('FOLDER_SETTINGS','Ordner Optionen');

// Context: I would like to "add this folder under" another folder.
define('ADD_UNDER','Hinzufügen unter...');

// Context: Please "select the folder you would like to rename."
define('SELECT_RENAME_FOLDER','Wähle den Ordner den du umbenennen willst');

// Context: Please "select the folder you would like to move."
define('SELECT_MOVE_FOLDER','Wähle den Ordner den du verschieben willst');

// Context: Please "select a new location" for this folder.
define('SELECT_NEW_LOCATION','Wähle eine neuen Ort');

// Context: Please "select the folder you would like to delete."
define('SELECT_DELETE_FOLDER','Wähle den Ordner den du löschen willst');

// Context: When I delete this folder, I would like to "delete the messages inside this folder."
define('DELETE_MESSAGES_IN_FOLDER','Delete messages inside folder');

// Context: "Click" here to return to the page
define('CLICK','Klick');

// Context: Click "here" to return to the page.
define('HERE','hier');

// Context: I would like to "log out" of the system.
define('LOG_OUT','Logout');

// Context: I would like to "view the default version" of this message.
define('VIEW_REGULAR','Zeige Standart Version');

// Context: This is the letter shown on the link to view the default version of a message.
define('VIEW_REGULAR_SHORT','S');

// Context: I would like to "view the full headers and text" of this message.
define('VIEW_FULL','Zeige kompletten Mailkopf und Text');

// Context: This is the letter shown on the link to view the full text version of a message.
define('VIEW_FULL_SHORT','K');

// Context: I would like to "view this message in plain text."
define('VIEW_TEXT','Zeige Nachricht im textformat');

// Context: This is the letter shown on the link to view the plain text version of a message.
define('VIEW_TEXT_SHORT','T');

// Context: I would like to "view this message in HTML".
define('VIEW_HTML','Zeige Nachricht im HTMLformat');

// Context: This is the letter shown on the link to view the HTML version of a message.
define('VIEW_HTML_SHORT','H');

// Context: This is the title of the navigation frame on the compose and settings pages.
define('NAVIGATION','Navigation');

// Context: I would like to change the "settings" of my account.
define('OPTIONS','Optionen');

// Context: I would like to "delete" this message.
define('DELETE_STRING','Löschen');

// Context: I would like to "upload messages" to Toby.
define('UPLOAD_MESSAGES','Nachricht hochladen');

// Context: I would like to "change the settings of Toby."
define('CHANGE_MAIN_SETTINGS','Toby Optionen ändern');

// Context: My "real name" is John Smith.
define('REAL_NAME','Echter Name');

// Context: The "default mode for message composition" is HTML
define('DEFAULT_MODE','Standart modus zum erstellen von E-mails');

// Context: This should be the abbreviation for Hypertext Markup Language.  Some webmail programs
// also refer to it as "rich text".
define('HTML','HTML');

// Context: This should be whatever mode a normal e-mail is in: My e-mail contains "text" and not pictures.
define('TEXT','Text');

// Context: I would like to "undelete" this message. OR I would like to "restore" this message to its folder.
define('UNDELETE','Wiederherstellen');

// Context: Here is a listing of your e-mail "messages"
define('MESSAGES','Nachrichten');

// Context: I would like to "move" this message to another folder.
define('MOVE','Verschieben');

// Context: I would like to "upload this message."
define('UPLOAD_MESSAGE','Nachricht hochladen');

// Context: I live in the Central Standard "Timezone"
define('TIMEZONE','Zeitzone');

// The context of the following phrases should be obvious.
define('FIRST_LOGIN_INTRO','Dies scheint ihr erster login bei '.APP_TITLE.' auf diesem Server zu sein. Bitte nimm dir einen moment zeit um deinen Akkount zu konfigurieren.');
define('FULL_NAME_QUESTION','Wie ist dein Vollständiger Name (Wie er bei versendeten Emails angezeigt werden soll)?');
define('MAIL_HOST_QUESTION','Url deines e-mail servers?');
define('SAVE_SENT_QUESTION','Soll Toby eine kopie deiner versendeten E-mails speichern?');
define('SAVE_INCOMING_QUESTION','Soll Toby bei jedem login dein E-mail Nachrichten auf dem Server speichern?');
define('SAVE_INCOMING_CAUTION','Achtung: Ja löscht alle herruntergladenen Nachrichten von deinem E-mail Server.');
define('YES','Ja');
define('NO','Nein');
define('STAY_LOGGED_IN','Lass mich eingeloggt.');
define('ENTER_HTML_CODE','Gib einigen HTML code hier ein:');
define('TRANSFER_INSTR','Um Nachrichten zwischen zwei E-mail Addressen die du mit Toby abfragst zu verschieben, gibt die E-mail Adresse zu der du die Nachrichten verschieben möchtest. Dann kannst du dich einlogen und unter der Adresse und die E-mails in den Ordner verschieben.');
define('UPLOAD_ERROR','Es ist ein Fehler aufgetreten und die Nachricht konnte nicht hochgeladen werden.');
define('DOWNLOAD_AS_ZIP','E-mail Nachrichten als Zipdatei herrunterladen');
define('MAIL_REFRESH_QUESTION','Alle __ Minuten auf neue Emails prüffen? (0 für kein automatisches prüfen.)');
define('UPLOAD_SUCCESS','Die Nachrichten wurden erfolgreich hochgeladen.');
define('NO_MESSAGES','Es gibt keine Nachrichten zum anzeigen.');
define('NO_MESSAGE','Es wurde keine Nachricht ausgewählt.');

// In the following lines, "query" refers to MySQL queries.
define('ERROR_IS','Der Fehlerbericht des Querys war wie folgt:');
define('ERROR_DISCOVERED','Es wurde ein fehler bei ausführen des folgenden querys festgestellt');

// The following two lines have the word "here" between them to complete the phrase. The word "here" is a link to the main page.
define('LOG_OUT_MESSAGE','Du hast dich jetzt aus Toby ausgelogt. Wenn diese Seite für länger als 5 Sekunden angezeigt wird, oder wenn du Javascript deaktiviert hast, Klick');
define('LOG_OUT_MESSAGE_END','um zur Login Seite zurück zukehren.');

// Context: This is the subject line of the email that is sent to the admin after an erroneous query
define('TOBY_ERROR_REPORT','Toby Error Report');

// Context: the following line is preceded by the words 'Click here'.
define('TO_RETURN','um zur Hauptseite dieses Frames zurück zukehren.');

// The following lines are the message shown to the user in the case that there is an error in a MySQL query.
define('ERROR_MESSAGE','Während der ausgeführten Aktion ist ein fehler aufgetreten. Der Systemadministrator wurde darüber benachrichtigt und wird sich mit ihnen in verbindung setzen.');

// The following lines make up a message such as the following"
// "The erro was triggered by user #4 on 2004/05/18 at 12:30 in the file functions.php on line 400."
define('ERROR_TRIGGER','Der Fehler wurde ausgelößt von User');
define('ON','am');
define('AT','um');
define('IN_THE_FILE','in der Datei');
define('ON_LINE','in Zeile');

?>
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
// define('ADDRESS_BOOK','Addresses');
//
// into Spanish, you might change it to
//
// define('ADDRESS_BOOK','Direcciones');
//
// where 'Direcciones' is the Spanish equivalent to the English word "Addresses."
// (This may or may not be correct.  I only have a basic understanding of Spanish.
//
// If you would like to help in the translation of Toby, feel free to e-mail a 
// translated version of this file to chris@efinke.com, or contact chris@efinke.com
// for more information.  (You may wish to e-mail before doing any work on it,
// because someone else may already be translating to your language.)

// Do not change this line.  I'm serious.
error_reporting(E_ALL ^ E_NOTICE);

// ################################################################################################### //
// ################# Here begins the English words and phrases that need translating ################# //
// ################################################################################################### //

// Months of the Year, pretty self-explanatory
$months = array(1=>"January","February","March","April","May","June","July","August","September","October","November","December");

// Context: "Addresses" or "Address Book"
define('ADDRESS_BOOK','Addresses');

// Context: "Log in" to Toby Web Mail
define('LOG_IN','Log In');

// Context: "Compose" an e-mail message
define('COMPOSE','Compose');

// Context: Reply to this message
define('REPLY','Reply');

// Context: "Reply to All" recipients of this message
define('REPLY_TO_ALL','Reply to All');

// Context: "Forward" this message to another person
define('FORWARD','Forward');

// Context: Switch to "text mode" for composing this message
define('TEXT_MODE','text mode');

// Context: Switch to "HTML mode" for composing this message
define('HTML_MODE','HTML mode');

// Context: "Send" this e-mail message
define('SEND','Send');

// Context: "Get the messages" in the specified folder as a zip file
define('GET_MESSAGES','Get Messages');

// Context: "Download messages" as a zip file
define('DOWNLOAD_MESSAGES','Download Messages');

// Context: New messages are stored in the "Inbox"
define('INBOX','Inbox');

// Context: Deleted messages are stored in the "Trash"
define('TRASH','Trash');

// Context: To permanently remove messages in the trash, "Empty" it.
define('EMPTY_TRASH','Empty');

// Context: I would like to "Add a Folder" to store messages in
define('ADD_FOLDER','Add Folder');

// Context: "Delete the Folder" that I have specified
define('DELETE_FOLDER','Delete Folder');

// Context: "Move the Folder" that I have specified into another folder.
define('MOVE_FOLDER','Move Folder');

// Context: "Rename the Folder" that I have specified.
define('RENAME_FOLDER','Rename Folder');

// Context: This application is called "Toby Web Mail"
define('APP_TITLE','Toby Web Mail');

// Context: I would like to "continue" logging in.
define('CONTINUE_BUTTON','Continue');

// Context: What is your "password"?
define('PASSWORD','Password');

// Context: "Add an address" to the address book
define('ADD_AN_ADDRESS','Add an Address');

// Context: What is the "name" of the person?
define('NAME','Name');

// Context: What is the "e-mail address" of the person?
define('EMAIL_ADDRESS','E-mail Address');

// Context: "Add this Address" to the address book
define('ADD_ADDRESS','Add Address');

// Context: "Update this Address" in the address book.
define('UPDATE_ADDRESS','Update Address');

// Context: "Delete the Addresses" that I have selected
define('DELETE_SELECTED_ADDRESSES','Delete Selected Addresses');

// Context: This file is an "attachment" to this e-mail.
define('ATTACHMENT','Attachment');

// Context: I wish to edit the "attachments" for this message
define('ATTACHMENTS','Attachments');

// Context: I wish to "Add an Attachment" to this e-mail
define('ADD_ATTACHMENT','Add Attachment');

// Context: Please "attach the file" that I have selecetd.
define('ATTACH_FILE','Attach File');

// Context: the "name of this file" is abc.txt.
define('FILENAME','Filename');

// Context: the "size of this file" is 48 KB.
define('SIZE','Size');

// Context: the "type of this file" is text/plain.
define('TYPE','Type');

// Context: This should be the abbreviation for kilobytes in this language.
define('KILOBYTES_UNIT','KB');

// Context: "Delete the attachments" that I have selected.
define('DELETE_SELECTED_ATTACHMENTS','Delete Selected Attachments');

// Context: I would like to go "back to the message" that I am composing.
define('BACK_TO_MESSAGE','Back to Message');

// Context: On this screen, you can "manage the attachments" that you have added.
define('MANAGE_ATTACHMENTS','Manage Attachments');

// Context: This should be whatever is prefixed to the subject of a message when it is forwarded.
define('FORWARD_PREFIX','Fwd');

// Context: This should be whatever is prefixed to the subject of a message when it is replied to.
define('REPLY_PREFIX','Re');

// Context: I am sending this message "to" john@smith.com
define('SEND_TO','To');

// Context: This should be the abbreviation for sending a "carbon-copy" of an e-mail.
define('CC_TO','Cc');

// Context: This should be the abbreviation for sending a carbon-copy of an e-mail without the address showing to the other recipients.
define('BCC_TO','Bcc');

// Context: The "subject" of this e-mail is "Hello".
define('SUBJECT','Subject');

// Context: How many attachments are there? "None".
define('NONE','None');

// Context: Please enter the "message" below.
define('MESSAGE','Message');

// Context: I would like to "switch to" HTML mode.
define('SWITCH_MODE','Switch to');

// Context: I would like to "save" a copy of this message "in" the selected folder.
define('SAVE_IN','Save in');

// Context: This message is "from" a friend.
define('FROM','From');

// Context: I "sent" this message.
define('SENT','Sent');

// Context: The "original message" to which I am replying appears below.
define('ORIGINAL_MESSAGE','Original Message');

// Context: I would like to download message from "all of the folders".
define('ALL_FOLDERS','All Folders');

// Context: I would like to download a "backup" of this message.
define('BACKUP','Backup');

// Context: This message has "no subject".
define('NO_SUBJECT','No Subject');

// Context: I would like to view the "folders" I have created.
define('FOLDERS','Folders');

// Context: I would like to "view" my messages "by" the following criteria.
define('VIEW_BY','View by');

// Context: The "sender" of the message.
define('SENDER','Sender');

// Context: The "recipient" of the message.
define('RECEIVER','Receiver');

// Context: What "date" the message was received on.
define('DATE_STRING','Date');

// Context: I would like to "upload the e-mail file" that I have selected.
define('UPLOAD_EMAIL','Upload E-mail File');

// Context: I would like to "transfer the e-mail" in the folder I have selected.
define('TRANSFER_EMAIL','Transfer E-mail');

// Context: I would like to "cancel the transfer" that I have selected.
define('CANCEL_TRANSFER','Cancel Transfer');

// Context: I would like to "retrieve the e-mail" that has been transferred to me.
define('RETRIEVE_EMAIL','Retrieve E-mail');

// Context: I would like to "decline" this transfer.
define('DECLINE','Decline');

// Context: the following are used to say 'Toby "successfully transfered # messages."'
define('TRANSFER_SUCCESS_BEGIN','Successfully transferred');
define('TRANSFER_SUCCESS_END','messages.');

// Context: Would you like to add any "comments" to this message?
define('COMMENTS','Comments');

// Context: the "e-mail address of the recipient" is abc@efg.com
define('DESTINATION_ADDRESS','Destination E-mail Address');

// Context: In what "language" would you like to use Toby?
define('LANGUAGE','Language');

// Context: I would like the change the "folder settings" of my account.
define('FOLDER_SETTINGS','Folder Settings');

// Context: I would like to "add this folder under" another folder.
define('ADD_UNDER','Add Under...');

// Context: Please "select the folder you would like to rename."
define('SELECT_RENAME_FOLDER','Select a Folder to Rename');

// Context: Please "select the folder you would like to move."
define('SELECT_MOVE_FOLDER','Select a Folder to Move');

// Context: Please "select a new location" for this folder.
define('SELECT_NEW_LOCATION','Select a New Location');

// Context: Please "select the folder you would like to delete."
define('SELECT_DELETE_FOLDER','Select a Folder to Delete');

// Context: When I delete this folder, I would like to "delete the messages inside this folder."
define('DELETE_MESSAGES_IN_FOLDER','Delete messages inside folder');

// Context: "Click" here to return to the page
define('CLICK','Click');

// Context: Click "here" to return to the page.
define('HERE','here');

// Context: I would like to "log out" of the system.
define('LOG_OUT','Log Out');

// Context: I would like to "view the default version" of this message.
define('VIEW_REGULAR','View Default Version');

// Context: This is the letter shown on the link to view the default version of a message.
define('VIEW_REGULAR_SHORT','D');

// Context: I would like to "view the full headers and text" of this message.
define('VIEW_FULL','View Full Headers and Text');

// Context: This is the letter shown on the link to view the full text version of a message.
define('VIEW_FULL_SHORT','F');

// Context: I would like to "view this message in plain text."
define('VIEW_TEXT','View message in plain text');

// Context: This is the letter shown on the link to view the plain text version of a message.
define('VIEW_TEXT_SHORT','T');

// Context: I would like to "view this message in HTML".
define('VIEW_HTML','View message in HTML');

// Context: This is the letter shown on the link to view the HTML version of a message.
define('VIEW_HTML_SHORT','H');

// Context: This is the title of the navigation frame on the compose and settings pages.
define('NAVIGATION','Navigation');

// Context: I would like to change the "settings" of my account.
define('OPTIONS','Settings');

// Context: I would like to "delete" this message.
define('DELETE_STRING','Delete');

// Context: I would like to "upload messages" to Toby.
define('UPLOAD_MESSAGES','Upload Messages');

// Context: I would like to "change the settings of Toby."
define('CHANGE_MAIN_SETTINGS','Change Toby Settings');

// Context: My "real name" is John Smith.
define('REAL_NAME','Real name');

// Context: The "default mode for message composition" is HTML
define('DEFAULT_MODE','Default mode for composing messages');

// Context: This should be the abbreviation for Hypertext Markup Language.  Some webmail programs
// also refer to it as "rich text".
define('HTML','HTML');

// Context: This should be whatever mode a normal e-mail is in: My e-mail contains "text" and not pictures.
define('TEXT','Text');

// Context: I would like to "undelete" this message. OR I would like to "restore" this message to its folder.
define('UNDELETE','Undelete');

// Context: Here is a listing of your e-mail "messages"
define('MESSAGES','Messages');

// Context: I would like to "move" this message to another folder.
define('MOVE','Move');

// Context: I would like to "upload this message."
define('UPLOAD_MESSAGE','Upload Message');

// Context: I live in the Central Standard "Timezone"
define('TIMEZONE','Timezone');

// The context of the following phrases should be obvious.
define('FIRST_LOGIN_INTRO','This appears to be the first time you have logged in using '.APP_TITLE.' on this server.  Please take a moment to configure your account.');
define('FULL_NAME_QUESTION','What is your full name (as you wish for it to appear on outgoing e-mails)?');
define('MAIL_HOST_QUESTION','What is your e-mail host?');
define('SAVE_SENT_QUESTION','Should Toby save a copy of your sent messages?');
define('SAVE_INCOMING_QUESTION','Would you like to have Toby save your e-mail messages on this server every time you log in?');
define('SAVE_INCOMING_CAUTION','Caution: Choosing yes will delete any downloaded messages from your e-mail server.');
define('YES','Yes');
define('NO','No');
define('STAY_LOGGED_IN','Keep me logged in.');
define('ENTER_HTML_CODE','Enter some HTML code here:');
define('TRANSFER_INSTR','To transfer mail between two e-mail addresses that you use Toby to check, enter the e-mail address to which you\'d like to move your mail.  You can then login under that address and transfer the e-mail to those folders on this same page.');
define('UPLOAD_ERROR','There was an error and the message was not uploaded.');
define('DOWNLOAD_AS_ZIP','Download E-mail Messages as Zip File');
define('MAIL_REFRESH_QUESTION','Check for new mail every __ minutes? (Enter 0 to disable.)');
define('UPLOAD_SUCCESS','The message was uploaded successfully.');
define('NO_MESSAGES','There are no messages to display.');
define('NO_MESSAGE','There is no message selected.');

// In the following lines, "query" refers to MySQL queries.
define('ERROR_IS','The error message for this query is as follows:');
define('ERROR_DISCOVERED','An error was discovered during the execution of the following query');

// The following two lines have the word "here" between them to complete the phrase. The word "here" is a link to the main page.
define('LOG_OUT_MESSAGE','You have now signed out of Toby.  If this screen appears for more than 5 seconds, or if you have Javascript disabled, click');
define('LOG_OUT_MESSAGE_END','to return to the login page.');

// Context: This is the subject line of the email that is sent to the admin after an erroneous query
define('TOBY_ERROR_REPORT','Toby Error Report');

// Context: the following line is preceded by the words 'Click here'.
define('TO_RETURN','to return to the main page of this frame.');

// The following lines are the message shown to the user in the case that there is an error in a MySQL query.
define('ERROR_MESSAGE','An error occurred during the execution of your requested action. The system administrator has been notified of this error and will contact you regarding this error.');

// The following lines make up a message such as the following"
// "The erro was triggered by user #4 on 2004/05/18 at 12:30 in the file functions.php on line 400."
define('ERROR_TRIGGER','The error was triggered by user');
define('ON','on');
define('AT','at');
define('IN_THE_FILE','in the file');
define('ON_LINE','on line');

?>
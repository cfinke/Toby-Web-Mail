<?php

// This is the Spanish language file for Toby Web Mail.
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

// Months of the Year, pretty self-explanatory
$months = array(1=>"Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

// Technically, all of these values should be unique, but 
// the following values must be unique to ensure that Toby is functional.
define('ADDRESS_BOOK','Direcciones');
define('LOG_IN','Entre');
define('COMPOSE','Componga');
define('REPLY','Conteste');
define('REPLY_TO_ALL','Conteste a Todo');
define('FORWARD','Remite');
define('TEXT_MODE','modo de texto');
define('HTML_MODE','modo de HTML');
define('SEND','Remita');
define('GET_MESSAGES','Consiga los mensajes');
define('DOWNLOAD_MESSAGES','Descargue los mensajes');
define('INBOX','Caja');
define('TRASH','Basura');
define('EMPTY_TRASH','Vacie');
define('ADD_FOLDER','Agregue una carpeta');
define('DELETE_FOLDER','Suprima una carpeta');
define('MOVE_FOLDER','Mueva una carpeta');
define('RENAME_FOLDER','Retitule una carpeta');


define('APP_TITLE','Toby Web Mail');
define('FIRST_LOGIN_INTRO','Esto aparece ser la primera vez que usted ha entrado usando el '.APP_TITLE.' en este computadora. Tome por favor un momento para configurar su cuenta.');
define('FULL_NAME_QUESTION','¿Cuál es su nombre, como usted desea para él aparecer en sus mensajes?');
define('MAIL_HOST_QUESTION','¿Cuál es su anfitrión del E-mail?');
define('SAVE_SENT_QUESTION','¿Usted desea ahorrar una copia de sus mensajes enviados?');
define('SAVE_INCOMING_QUESTION','¿Usted desea suprimir sus mensajes recibidos del servidor del E-mail?');
define('SAVE_INCOMING_CAUTION','Precaución: El elegir sí suprimirá cualquier mensaje descargado de su servidor del E-mail.');
define('YES','Sí');
define('NO','No');
define('CONTINUE_BUTTON','Continúe');

define('PASSWORD','Contraseña');
define('STAY_LOGGED_IN','Recuérdeme');

define('ADD_AN_ADDRESS','Agregue una dirección');
define('NAME','Nombre');
define('EMAIL_ADDRESS','Dirección del E-mail');
define('ADD_ADDRESS','Agregue esta dirección');
define('UPDATE_ADDRESS','Cambie esta dirección');
define('DELETE_SELECTED_ADDRESSES','Suprima las Direcciones Seleccionadas');

define('ATTACHMENT','Accesorio');
define('ATTACHMENTS','Accesorios');
define('ADD_ATTACHMENT','Agregue un Accesorio');
define('ATTACH_FILE','Agregue este Fichero');
define('FILENAME','Nombre del Fichero');
define('SIZE','Tamaño del Fichero');
define('TYPE','Tipo del Fichero');
define('KILOBYTES_UNIT','KB');
define('DELETE_SELECTED_ATTACHMENTS','Suprima las Accesorios Seleccionadas');
define('BACK_TO_MESSAGE','Vuelva al Mensaje');
define('MANAGE_ATTACHMENTS','Maneje los Accesorios');

define('FORWARD_PREFIX','Fwd');
define('REPLY_PREFIX','Re');

define('ENTER_HTML_CODE','Introduzca un cierto código del HTML aquí:');
define('SEND_TO','Recipiente');
define('CC_TO','Cc');
define('BCC_TO','Bcc');
define('SUBJECT','Tema');
define('NONE','Ninguno');
define('MESSAGE','Mensaje');
define('SWITCH_MODE','Cambie al');

define('SAVE_IN','Excepto en');

define('FROM','De');
define('SENT','Fue Enviado');
define('ORIGINAL_MESSAGE','Mensaje Original');

define('DOWNLOAD_AS_ZIP','Descargue los mensajes como un archivo de tipo .zip');
define('ALL_FOLDERS','Todas las Carpetas');
define('BACKUP','Reserva');
define('NO_SUBJECT','Ningún tema');

define('FOLDERS','Carpetas');
define('VIEW_BY','Clasifique por');
define('SENDER','Remitente');
define('RECEIVER','Recipiente');
define('DATE_STRING','Fecha');

define('FOLDER_SETTINGS','Opciones de las Carpetas');
define('ADD_UNDER','Agregue Adentro...');
define('SELECT_RENAME_FOLDER','Seleccione una Carpeta para Retitular');
define('SELECT_MOVE_FOLDER','Seleccione una Carpeta para Moverse');
define('SELECT_NEW_LOCATION','Seleccione una Nueva Localización');
define('SELECT_DELETE_FOLDER','Seleccione una Carpeta para Suprimir');
define('DELETE_MESSAGES_IN_FOLDER','Suprima los mensajes dentro de esta carpeta');

define('ERROR_DISCOVERED','Un error fue descubierto durante la ejecución de la pregunta siguiente');
define('ERROR_IS','El mensaje de error para esta pregunta está como sigue');
define('TOBY_ERROR_REPORT','Toby Informe Del Error');
define('CLICK','Chasque');
define('HERE','aquí');
define('TO_RETURN','para volver a la página principal de este bastidor.');
define('ERROR_MESSAGE','Un error ocurrió durante la ejecución de su acción solicitada. Han notificado de este error y le entrará en contacto con al administrador de sistema con respecto a este error.');
define('ERROR_TRIGGER','El error fue accionado por el usuario');
define('ON','en');
define('AT','en');
define('IN_THE_FILE','en el archivo');
define('ON_LINE','en la línea');

define('LOG_OUT','Salida');
define('LOG_OUT_MESSAGE','Usted ahora ha firmado fuera de Toby. Si esta pantalla aparece por más de 5 segundos, o si usted tiene Javascript inhabilitado, chasque');
define('LOG_OUT_MESSAGE_END','para volver a la página de la conexión.');

define('NO_MESSAGE','No hay mensaje seleccionado.');
define('VIEW_REGULAR','Mensaje Regular De la Visión');
define('VIEW_REGULAR_SHORT','R');

define('VIEW_FULL','Demuestre todos los jefes y el texto');
define('VIEW_FULL_SHORT','F');
define('VIEW_TEXT','Mensaje de la visión en texto llano');
define('VIEW_TEXT_SHORT','T');
define('VIEW_HTML','Mensaje de la visión en HTML');
define('VIEW_HTML_SHORT','H');

define('NAVIGATION','Navegación');
define('OPTIONS','Opciones');
define('DELETE_STRING','Suprima');
define('TRANSFER_EMAIL','Transfiera E-mail');
define('UPLOAD_MESSAGES','Upload los Mensajes');

define('CHANGE_MAIN_SETTINGS','Cambie los Ajustes de Toby');
define('REAL_NAME','Nombre');

define('DEFAULT_MODE','Modo de la composición del defecto');
define('HTML','HTML');
define('TEXT','Texto');

define('UNDELETE','Restaure');
define('MESSAGES','Mansajes');
define('MOVE','Mueva');
define('NO_MESSAGES','Aquí no hay mensajes a exhibir.');

define('UPLOAD_MESSAGE','Upload el Mensaje');
define('UPLOAD_SUCCESS','El mensaje fue ahorrado con éxito.');
define('UPLOAD_ERROR','Había un error y el mensaje no fue ahorrado.');
define('UPLOAD_EMAIL','Envíe el Archivo del E-mail');

define('CANCEL_TRANSFER','Cancele esta Transferencia');
define('RETRIEVE_EMAIL','Recupere el E-mail');
define('DECLINE','Declinación');
define('TRANSFER_SUCCESS_BEGIN','Con éxito transferido');
define('TRANSFER_SUCCESS_END','mensajes.');
define('COMMENTS','Comentarios');
define('TRANSFER_INSTR','Para transferir el correo entre dos direcciones del E-mail que usted utiliza Toby para comprobar, incorpore la dirección del E-mail a la cual usted quisiera mover su correo. Usted puede entonces conexión bajo esa dirección y transferir el E-mail a esas carpetas en esta misma página.');
define('DESTINATION_ADDRESS','Dirección Del E-mail De la Destinación');

define('LANGUAGE','Lengua');

define('TIMEZONE','Zona de Tiempo');

define('MAIL_REFRESH_QUESTION','¿Debe la comprobación para de Toby nuevo enviar cada __ minutos? (introduzca 0 para inhabilitar.)');

?>
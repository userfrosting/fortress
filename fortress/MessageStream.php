<?php

namespace Fortress;

// Implements a message stream, with i18n support via gettext

class MessageStream {

    protected $_messages = [];

    public function __construct(){
    }

    /* Clear the message stream */
    public function resetMessageStream(){
        //self::checkMessageStreamExists();
        $this->_messages = [];
    }    
    
    /*
    private static function checkMessageStreamExists(){
        if (!isset(self::$_message_stream) || !isset($_SESSION['Fortress']) || !isset($_SESSION['Fortress'][self::$_message_stream]))
            throw new \Exception("No message stream has been set!  Please use HTTPRequestFortress::setMessageStream to set a message stream.");    
    }
    */
     
    // Add a session message to the session message stream
    public function addMessage($type, $message){
        $alert = [
            "type" => $type,
            "message" => $message
        ];
        //self::checkMessageStreamExists();
        $this->_messages[] = $alert;
    }

    // Add a session message to the session message stream, translating as necessary
    public function addMessageTranslated($type, $message_id, $placeholders = []){
        $message = MessageTranslator::translate($message_id, $placeholders);
        $this->addMessage($type, $message);
    }    
    
    // Return the array of messages
    public function messages(){
        //self::checkMessageStreamExists();
        return $this->_messages;
    }
}




?>

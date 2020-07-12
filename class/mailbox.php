<?php

define('_GTD_MAILBOXTYPE_POP3', 1);
define('_GTD_MAILBOXTYPE_IMAP', 2);

//$Id: mailbox.php,v 1.2 2005/01/27 22:06:36 eric_juden Exp $
/**
 * gtdMailBox class
 *
 * Part of the email submission subsystem. Abstract class defining functions
 * needed to interact with a mailstore
 *
 * @author Nazar Aziz <nazar@panthersoftware.com>
 * @access public
 * @abstract
 * @package gtd
 */

class gtdMailBox {
  function connect($server, $port = 110){
  }
  //
  function login($username, $password){
  }
  //
  function messageCount(){
  }
  //
  function getHeaders($i) {
  }
  //
  function getBody($i) {
  }
  
  function getMsg($i) {
  }
  //
  function deleteMessage($i) {
  }
  //
  function disconnect(){
  }
}
?>
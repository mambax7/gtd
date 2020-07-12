<?php

/**
 * gtdTicketMailParser class
 *
 * Part of the email submission subsystem. Converts a parsed email into a ticket
 *
 * @author Nazar Aziz <nazar@panthersoftware.com>
 * @access public
 * @depreciated
 * @package gtd
 */
class gtdTicketMailParser 
{
    /**
     * Instance of Ticket Object
     * @access private
     */
    var $_ticket;
  
    /**
     * Class Constructor
     * @access public
     */  
    function gtdTicketMailParser() 
    {
        //any inits?
    }
  
    /**
     * Create a new ticket object
     * @param object Reference to a {@link gtdEmailParser} object
     * @param object Current {@link xoopsUser} object
     * @param object {@link gtdDepartment} Ticket Department
     * @param object {@link gtdDepartmentEmailServer} Originating Email Server
     * @return bool
     * @access public
     */
    function createTicket(&$mailParser, &$xoopsUser, &$department, &$server) 
    {
        //get ticket handler
        $hTicket =& gtdGetHandler('ticket');
        $ticket  =& $hTicket->create();
        //
        $ticket->setVar('uid',         $xoopsUser->uid());
        $ticket->setVar('subject',     $mailParser->getSubject());
        $ticket->setVar('department',  $department->getVar('id'));
        $ticket->setVar('description', $mailParser->getBody());
        $ticket->setVar('genre',    3);
        $ticket->setVar('posted',      time());
        $ticket->setVar('userIP',      _GTD_EMAIL_SCANNER_IP_COLUMN);
        $ticket->setVar('serverid',    $server->getVar('id'));
        $ticket->createEmailHash($mailParser->getEmail());
        //
        if ($hTicket->insert($ticket)){
            $this->_ticket = $ticket;
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Returns the ticket object for this email
     * @return object {@link gtdTicket} Ticket Object
     */
    function &getTicket() 
    {
        return $this->_ticket;
    }

}

?>
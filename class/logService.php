<?php
//$id$

/**
 * gtd_logService class
 *
 * Part of the Messaging Subsystem.  Uses the gtdlogMessageHandler class for logging
 *
 * @author Brian Wahoff <ackbarr@xoops.org>
 * @access public
 * @package gtd
 */
 
class gtd_logService
{
     /**
     * Instance of the gtdlogMessageHandler
     *    
     * @var	object
     * @access	private
     */
    var $_hLog;  
    
    
    /**
	 * Class Constructor
	 * 
	 * @access	public	
	 */	    
    function gtd_logService()
    {
        $this->_hLog =& gtdGetHandler('logMessage');
    }
    
    /**
	 * Callback function for the 'new_ticket' event
	 * @param	array	$args Array of arguments passed to EventService
	 * @return  bool True on success, false on error
	 * @access	public
	 */
    function new_ticket($args)
    {
        global $xoopsUser;
        list($ticket) = $args;
       
        $logMessage =& $this->_hLog->create();
        $logMessage->setVar('uid', $ticket->getVar('uid'));
        $logMessage->setVar('ticketid', $ticket->getVar('id'));
        $logMessage->setVar('lastUpdated', $ticket->getVar('posted'));
        $logMessage->setVar('posted', $ticket->getVar('posted'));
        
        if($xoopsUser->getVar('uid') == $ticket->getVar('uid')){
            $logMessage->setVar('action', _GTD_LOG_ADDTICKET);
        } else {
            // Will display who logged the ticket for the user
            $logMessage->setVar('action', sprintf(_GTD_LOG_ADDTICKET_FORUSER, $xoopsUser->getUnameFromId($ticket->getVar('uid')), $xoopsUser->getVar('uname')));
        }
        
        return $this->_hLog->insert($logMessage);
    }
    
    /**
	 * Callback function for the 'update_genre' event
	 * @param	array	$args Array of arguments passed to EventService
	 * @return  bool True on success, false on error
	 * @access	public
	 */
    function update_genre($args)
    {
        global $xoopsUser;
        list($ticket, $oldgenre) = $args;
        
        $logMessage =& $this->_hLog->create();
        $logMessage->setVar('uid', $xoopsUser->getVar('uid'));
        $logMessage->setVar('ticketid', $ticket->getVar('id'));
        $logMessage->setVar('lastUpdated', $ticket->getVar('lastUpdated'));
        $logMessage->setVar('posted', $ticket->getVar('posted'));
        $logMessage->setVar('action', sprintf(_GTD_LOG_UPDATE_GENRE,  $oldgenre, $ticket->getVar('genre')));
        return $this->_hLog->insert($logMessage);
    }

    /**
	 * Callback function for the 'update_status' event
	 * @param	array	$args Array of arguments passed to EventService
	 * @return  bool True on success, false on error
	 * @access	public
	 */    
    function update_status($args)
    {
        global $xoopsUser;
        list($ticket, $oldstatus, $newstatus) = $args;
        
        $logMessage =& $this->_hLog->create();
        $logMessage->setVar('uid', $xoopsUser->getVar('uid'));
        $logMessage->setVar('ticketid', $ticket->getVar('id'));
        $logMessage->setVar('lastUpdated', $ticket->getVar('lastUpdated'));
        $logMessage->setVar('posted', $ticket->getVar('posted'));        
        $logMessage->setVar('action', sprintf(_GTD_LOG_UPDATE_STATUS, $oldstatus->getVar('description'), $newstatus->getVar('description')));
        return $this->_hLog->insert($logMessage, true);
     }
     
   /**
    * Callback function for the 'update_owner' event
    * @param array $args Array of arguments passed to EventService
    * @returen bool True on success, false on error
    * @access public
    */
   function update_owner($args)
   {
        global $xoopsUser;
        list($ticket, $oldowner) = $args;
        
        $logMessage =& $this->_hLog->create();
        $logMessage->setVar('uid', $xoopsUser->getVar('uid'));
        $logMessage->setVar('ticketid', $ticket->getVar('id'));
        $logMessage->setVar('lastUpdated', $ticket->getVar('lastUpdated'));
        if ($xoopsUser->getVar('uid') == $ticket->getVar('ownership')) {
            //User claimed ownership
            $logMessage->setVar('action', _GTD_LOG_CLAIM_OWNERSHIP);
        } else {
            //Ownership was assigned
            $logMessage->setVar('action', sprintf(_GTD_LOG_ASSIGN_OWNERSHIP, $xoopsUser->getUnameFromId($ticket->getVar('ownership'))));
        }
        return $this->_hLog->insert($logMessage);
   }
   
     
   /**
    * Callback function for the reopen_ticket event
    * @param array $args Array of arguments passed to EventService
    * @return bool True on success, false on error
    * @access public
    */
    function reopen_ticket($args)
    {
        global $xoopsUser;
        list($ticket) = $args;
        
        $logMessage =& $this->_hLog->create();
        $logMessage->setVar('uid', $xoopsUser->getVar('uid'));
        $logMessage->setVar('ticketid', $ticket->getVar('id'));
        $logMessage->setVar('lastUpdated', $ticket->getVar('lastUpdated'));
        $logMessage->setVar('action', _GTD_LOG_REOPEN_TICKET);
        return $this->_hLog->insert($logMessage);
    }

    /**
    * Callback function for the close_ticket event
    * @param array $args Array of arguments passed to EventService
    * @return bool True on success, false on error
    * @access public
    */    
    function close_ticket($args)
    {
        global $xoopsUser;
        list($ticket) = $args;
        
        $logMessage =& $this->_hLog->create();
        $logMessage->setVar('uid', $xoopsUser->getVar('uid'));
        $logMessage->setVar('ticketid', $ticket->getVar('id'));
        $logMessage->setVar('lastUpdated',$ticket->getVar('lastUpdated'));
        $logMessage->setVar('action', _GTD_LOG_CLOSE_TICKET);
        return $this->_hLog->insert($logMessage);
    }
    
    /**
    * Callback function for the new_response event
    * @param array $args Array of arguments passed to EventService
    * @return bool True on success, false on error
    * @access public
    */
    function new_response(&$args)
    {
        global $xoopsUser;
        list($ticket, $newResponse) = $args;
        
        $logMessage =& $this->_hLog->create();
        $logMessage->setVar('uid', $xoopsUser->getVar('uid'));
        $logMessage->setVar('ticketid', $ticket->getVar('id'));
        $logMessage->setVar('action', _GTD_LOG_ADDRESPONSE);
        $logMessage->setVar('lastUpdated', $newResponse->getVar('updateTime'));
        return $this->_hLog->insert($logMessage);
    }

    /**
     * Callback function for the 'new_response_rating' event
     * @param array $args Array of arguments passed to EventService
     * @return bool True on success, false on error
     * @access public
    */
    function new_response_rating($args)
    {
        global $xoopsUser;
        list($rating) = $args;
        
        $logMessage =& $this->_hLog->create();
        $logMessage->setVar('uid', $xoopsUser->getVar('uid'));
        $logMessage->setVar('ticketid', $rating->getVar('ticketid'));
        $logMessage->setVar('action', sprintf(_GTD_LOG_ADDRATING, $rating->getVar('responseid')));
        $logMessage->setVar('lastUpdated', time());
        return $this->_hLog->insert($logMessage);
    }
    /**
	 * Callback function for the 'edit_ticket' event
	 * @param	array	$args Array of arguments passed to EventService
	 * @return  bool True on success, false on error
	 * @access	public
	 */                 
    function edit_ticket($args)
    {
        global $xoopsUser;
        list($oldTicket, $ticketInfo) = $args;
        
        $logMessage =& $this->_hLog->create();
        $logMessage->setVar('uid', $xoopsUser->getVar('uid'));
        $logMessage->setVar('ticketid', $ticketInfo->getVar('id'));
        $logMessage->setVar('lastUpdated', $ticketInfo->getVar('posted'));
        $logMessage->setVar('posted', $ticketInfo->getVar('posted'));
        $logMessage->setVar('action', _GTD_LOG_EDITTICKET);
        return $this->_hLog->insert($logMessage);
    }

    /**
	 * Callback function for the 'edit_response' event
	 * @param	array	$args Array of arguments passed to EventService
	 * @return  bool True on success, false on error
	 * @access	public
	 */             
    function edit_response($args)
    {
        global $xoopsUser;
        
        list($ticket, $response, $oldticket, $oldresponse) = $args;
        
        $logMessage =& $this->_hLog->create();
        $logMessage->setVar('uid', $xoopsUser->getVar('uid'));
        $logMessage->setVar('ticketid', $response->getVar('ticketid'));
        $logMessage->setVar('lastUpdated', $response->getVar('updateTime'));
        $logMessage->setVar('action', sprintf(_GTD_LOG_EDIT_RESPONSE, $response->getVar('id')));
        return $this->_hLog->insert($logMessage);
    } 
    
    function batch_dept($args)
    {
        global $xoopsUser;
        list($tickets, $dept) = $args;
        $hDept   =& gtdGetHandler('department');
        $deptObj =& $hDept->get($dept);
        
        foreach($tickets as $ticket) {
            $logMessage =& $this->_hLog->create();
            $logMessage->setVar('uid', $xoopsUser->getVar('uid'));
            $logMessage->setVar('ticketid', $ticket->getVar('id'));
            $logMessage->setVar('lastUpdated', time());
            $logMessage->setVar('action', sprintf(_GTD_LOG_SETDEPT, $deptObj->getVar('department')));
            $this->_hLog->insert($logMessage);
            unset($logMessage);
        }
        return true;        
    }
    
    function batch_genre($args)
    {
        global $xoopsUser;
        list($tickets, $genre) = $args;
        
        foreach($tickets as $ticket) {
            $logMessage =& $this->_hLog->create();
            $logMessage->setVar('uid', $xoopsUser->getVar('uid'));
            $logMessage->setVar('ticketid', $ticket->getVar('id'));
            $logMessage->setVar('lastUpdated', $ticket->getVar('lastUpdated'));
            $logMessage->setVar('posted', $ticket->getVar('posted'));
            $logMessage->setVar('action', sprintf(_GTD_LOG_UPDATE_GENRE,  $ticket->getVar('genre'), $genre));
            $this->_hLog->insert($logMessage);
        }
        return true;
    }
    
    function batch_owner($args)
    {
        global $xoopsUser;
        list($tickets, $owner) = $args;
        $updated   = time();
        $ownername = ($xoopsUser->getVar('uid') == $owner ? $xoopsUser->getVar('uname') : $xoopsUser->getUnameFromId($owner));
        foreach ($tickets as $ticket) {
            $logMessage =& $this->_hLog->create();
            $logMessage->setVar('uid', $xoopsUser->getVar('uid'));
            $logMessage->setVar('ticketid', $ticket->getVar('id'));
            $logMessage->setVar('lastUpdated', $updated);
            if ($xoopsUser->getVar('uid') == $owner) {
                $logMessage->setVar('action', _GTD_LOG_CLAIM_OWNERSHIP);
            } else {
                $logMessage->setVar('action', sprintf(_GTD_LOG_ASSIGN_OWNERSHIP, $ownername));
            }
            $this->_hLog->insert($logMessage);
            unset($logMessage);
        }
        return true;
    }
    
    function batch_status($args)
    {
        global $xoopsUser; 
        list ($tickets, $newstatus) = $args;
        $updated = time();
        $sStatus = gtdGetStatus($newstatus);
        $uid     = $xoopsUser->getVar('uid');
        foreach ($tickets as $ticket) {
            $logMessage =& $this->_hLog->create();
            $logMessage->setVar('uid', $uid);
            $logMessage->setVar('ticketid', $ticket->getVar('id'));
            $logMessage->setVar('lastUpdated', $updated); 
            $logMessage->setVar('action', sprintf(_GTD_LOG_UPDATE_STATUS, gtdGetStatus($ticket->getVar('status')), $sStatus));
            $this->_hLog->insert($logMessage, true);
            unset($logMessage);
        }
        return true;
    }
    
    function batch_response($args)
    {
        global $xoopsUser;
        list($tickets, $response, $timespent, $private) = $args;
        $updateTime = time();
        $uid        = $xoopsUser->getVar('uid');
        
        foreach($tickets as $ticket) {
            $logMessage =& $this->_hLog->create();
            $logMessage->setVar('uid', $uid);
            $logMessage->setVar('ticketid', $ticket->getVar('id'));
            $logMessage->setVar('action', _GTD_LOG_ADDRESPONSE);
            $logMessage->setVar('lastUpdated', $updateTime);
            $this->_hLog->insert($logMessage);
        }
        return true;
    }
    
    function merge_tickets($args)
    {
        global $xoopsUser;
        list($ticketid, $mergeTicketid, $newTicket) = $args;    // New ticket only passed in to match function for notificationService
        
        $logMessage =& $this->_hLog->create();
        $logMessage->setVar('uid', $xoopsUser->getVar('uid'));
        $logMessage->setVar('ticketid', $ticketid);
        $logMessage->setVar('action', sprintf(_GTD_LOG_MERGETICKETS, $mergeTicketid, $ticketid));
        $logMessage->setVar('lastUpdated', time());
        if($this->_hLog->insert($logMessage)){
            return true;
        }
        return false;
    }
    
    function delete_file($args)
    {
        global $xoopsUser;
        list($file) = $args;
        
        $filename = $file->getVar('filename');
        
        $logMessage =& $this->_hLog->create();
        $logMessage->setVar('uid', $xoopsUser->getVar('uid'));
        $logMessage->setVar('ticketid', $file->getVar('ticketid'));
        $logMessage->setVar('action', sprintf(_GTD_LOG_DELETEFILE, $filename));
        $logMessage->setVar('lastUpdated', time());
        
        if($this->_hLog->insert($logMessage, true)){
            return true;
        }
        return false;
    }
    
     /**
	 * Only have 1 instance of class used
	 * @return object {@link gtd_eventService}
	 * @access	public
	 */
    
    function &singleton()
    {
        // Declare a static variable to hold the object instance
        static $instance; 

        // If the instance is not there, create one
        if(!isset($instance)) { 
            $instance =& new gtd_logService(); 
        } 
        return($instance); 
    }
}
?>
<?php
//$Id: staffService.php,v 1.10 2005/02/15 16:58:03 ackbarr Exp $

/**
 * gtd_staffService class
 *
 * Part of the Messaging Subsystem.  Updates staff member information.
 *
 *
 * @author Brian Wahoff <ackbarr@xoops.org>
 * @access public
 * @package gtd
 */
 
class gtd_staffService
{
     /**
     * Instance of the xoopsStaffHandler
     *    
     * @var	object
     * @access	private
     */
    var $_hStaff;  
    
    /**
	 * Class Constructor
	 * 
	 * @access	public	
	 */	    
    function gtd_staffService()
    {
        $this->_hStaff =& gtdGetHandler('staff');
    }
    
    /**
    * Callback function for the 'new_response' event
    * @param array $args Array of arguments passed to EventService
    * @return bool True on success, false on error
    * @access public
    */
    function new_response($args)
    {
        global $xoopsUser;
        list($ticket, $response) = $args;
        
        //if first response for ticket, update staff responsetime
        $hResponse   =& gtdGetHandler('responses');
        $hMembership =& gtdGetHandler('membership');
        if ($hResponse->getStaffResponseCount($ticket->getVar('id')) == 1) {
            if ($hMembership->isStaffMember($response->getVar('uid'), $ticket->getVar('department'))) {
                $responseTime = abs($response->getVar('updateTime') - $ticket->getVar('posted'));
                $this->_hStaff->updateResponseTime($response->getVar('uid'), $responseTime);
            }
        }
                
    }
    
    function batch_response($args)
    {
        global $xoopsUser;
        list($tickets, $response, $timespent, $private) = $args; 
        $update    = time();
        $uid       = $xoopsUser->getVar('uid');
        $hResponse =& gtdGetHandler('responses');
        foreach ($tickets as $ticket) {        
            //if first response for ticket, update staff responsetime
            
            $hMembership =& gtdGetHandler('membership');
            if ($hResponse->getStaffResponseCount($ticket->getVar('id')) == 1) {
                $responseTime = abs($update - $ticket->getVar('posted'));
                $this->_hStaff->updateResponseTime($uid, $responseTime);
            }
        }

        
    }
    
    function batch_status($args)
    {
        global $xoopsUser;
        list($tickets, $newstatus) = $args;
        
        $uid = $xoopsUser->getVar('uid');
        
        //Update Calls Closed if $newstatus = closed
        if ($newstatus == 2) {
            $this->_hStaff->increaseCallsClosed($uid, count($tickets));
        }                
        
    }

    /**
    * Callback function for the 'close_ticket' event
    * @param array $args Array of arguments passed to EventService
    * @return bool True on success, false on error
    * @access public
    */    
    function close_ticket($args)
    {
        global $xoopsUser;
        list($ticket) = $args;
        
        $hMembership =& gtdGetHandler('membership');
        if ($hMembership->isStaffMember($ticket->getVar('closedBy'), $ticket->getVar('department'))) {
            $this->_hStaff->increaseCallsClosed($ticket->getVar('closedBy'), 1);
        }
        return true;
    }

    /**
    * Callback function for the 'reopen_ticket' event
    * @param array $args Array of arguments passed to EventService
    * @return bool True on success, false on error
    * @access public
    */        
    function reopen_ticket($args)
    {
        list($ticket) = $args;
        
        $hMembership =& gtdGetHandler('membership');
        if ($hMembership->isStaffMember($ticket->getVar('closedBy'), $ticket->getVar('department'))) {
            $this->_hStaff->increaseCallsClosed($ticket->getVar('closedBy'), -1);
        }
        return true;
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
        
        $hStaff =& gtdGetHandler('staff');
        return $hStaff->updateRating($rating->getVar('staffid'), $rating->getVar('rating'));
    }
    
    function view_ticket($args)
    {
        global $xoopsUser;
        list($ticket) = $args;
        
        $value = array();
        
        //Store a list of recent tickets in the gtd_recent_tickets cookie
        if (isset($_COOKIE['gtd_recent_tickets'])) {
            $oldvalue = explode(',', $_COOKIE['gtd_recent_tickets']);
        } else {
            $oldvalue = array();
        }
        
        $value[] = $ticket->getVar('id');
               
        $value = array_merge($value, $oldvalue);
        $value = $this->_array_unique($value);
        $value = array_slice($value, 0, 5);
        //Keep this value for 15 days
        setcookie('gtd_recent_tickets', implode(',', $value), time()+15 * 24 * 60 * 60, '/'); 
    }

    function delete_staff($args)
    {
        $xoopsDB =& Database::getInstance();
        list($staff) = $args;
        
        //Reset the ownership for tickets currently owned by staff member
        $sql = sprintf('UPDATE %s SET ownership = 0 WHERE ownership = %u', $xoopsDB->prefix('gtd_tickets'), $staff->getVar('uid'));
        $ret = $xoopsDB->query($sql);
        if (!$ret) {
            return false;
        }
        return true;
        
    } 
    
    /**
	 * Only have 1 instance of class used
	 * @return object {@link gtd_staffService}
	 * @access	public
	 */
    function &singleton()
    {
        // Declare a static variable to hold the object instance
        static $instance; 

        // If the instance is not there, create one
        if(!isset($instance)) { 
            $instance =& new gtd_staffService(); 
        }
        return($instance); 
    } 
    
    function _array_unique($array)
    {
        $out = array();
  
        //    loop through the inbound
        foreach ($array as $key=>$value) { 
            //    if the item isn't in the array
            if (!in_array($value, $out)) { //    add it to the array
                $out[$key] = $value;
            }
        }
  
        return $out;    
    }
}
?>
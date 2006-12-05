<?php
/***************************************************************************
Copyright (C) 2004 - 2006 Marcus Campbell
http://sourceforge.net/projects/scuttle/
http://scuttle.org/

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
***************************************************************************/

require_once('header.inc.php');
$userservice =& ServiceFactory::getServiceInstance('UserService');
$templateservice =& ServiceFactory::getServiceInstance('TemplateService');

$tplVars = array();
$completed = false;

if ($_POST['submitted']) {
	if (!$completed) {	
		$posteduser = trim(utf8_strtolower($_POST['username']));
		$postedpass = trim($_POST['password']);
		$postedconf = trim($_POST['passconf']);

        // Check token
        if (!isset($_SESSION['token']) || $_POST['token'] != $_SESSION['token']) {
			$tplVars['error'] = T_('Form could not be authenticated. Please try again.');

		// Check if form is incomplete
		} elseif (!($posteduser) || !($_POST['password']) || !($_POST['email'])) {
			$tplVars['error'] = T_('You <em>must</em> enter a username, password and e-mail address.');
	
		// Check if username is reserved
		} elseif ($userservice->isReserved($posteduser)) {
			$tplVars['error'] = T_('This username has been reserved. Please make another choice.');
	
		// Check if username already exists
		} elseif ($userservice->getUserByUsername($posteduser)) {
			$tplVars['error'] = T_('This username already exists. Please make another choice.');
		
		// Check that password is long enough
		} elseif ($postedpass != '' && strlen($postedpass) < 6) {
			$error = true;
			$tplVars['error'] = T_('Password must be at least 6 characters long.');       
		
		// Check if password matches confirmation
		} elseif ($postedpass != $postedconf) {
			$error = true;
			$tplVars['error'] = T_('Password and confirmation do not match.');

		// Check if e-mail address is blocked
		} elseif ($userservice->isBlockedEmail($_POST['email'])) {
			$tplVars['error'] = T_('This e-mail address is not permitted.');
	
		// Check if e-mail address is valid
		} elseif (!$userservice->isValidEmail($_POST['email'])) {
			$tplVars['error'] = T_('This e-mail address is not valid.');
	
		// Register details
		} elseif ($userservice->addUser($posteduser, $postedpass, $_POST['email'])) {
			// Retrieve stored registration time
			$userinfo = $userservice->getUserByUsername($posteduser);
			$datetime =& $userinfo['uDatetime'];
			$hash = md5($posteduser . $datetime);
			
			// Send confirmation e-mail
			$message = sprintf(T_('Welcome to %s!'), $sitename) ."\n\n".
				T_('Please click this link to verify your registration:') ."\n".
				createURL('verify', $posteduser .'/'. $hash);
			$message = wordwrap($message, 70);
			$headers = 'From: '. $adminemail;
			$mail = mail($_POST['email'], sprintf(T_('%s Account Verification'), $sitename), $message, $headers);		
		  
			$tplVars['msg'] = T_('Thank you for registering! Before you can start adding bookmarks you must verify your account - check your e-mail for instructions.');
			
			$completed = true;        
		} else {
			$tplVars['error'] = T_('Registration failed. Please try again.');
		}
	} else {
			$tplVars['msg'] = T_('Woah there, go easy on the Register button! Your registration was successful. Check your e-mail for instructions on how to verify your account.');	
	}
}

$token = md5(uniqid(rand(), TRUE));
$_SESSION['token'] = $token;
$_SESSION['token_time'] = time();

$tplVars['loadjs']      = true;
$tplVars['subtitle']    = T_('Register');
$tplVars['formaction']  = createURL('register');
$tplVars['token']       = $token;
$templateservice->loadTemplate('register.tpl', $tplVars);
?>

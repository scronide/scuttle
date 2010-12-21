<?php
/***************************************************************************
Copyright (c) 2004 - 2010 Marcus Campbell
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

require_once 'header.inc.php';
$userservice     =& ServiceFactory::getServiceInstance('UserService');
$templateservice =& ServiceFactory::getServiceInstance('TemplateService');

$tplVars   = array();
$completed = FALSE;

if ($_POST['submitted']) {
  if (!$completed) {  
    $posteduser = trim(utf8_strtolower($_POST['username']));
    $postedpass = trim($_POST['password']);
    $postedconf = trim($_POST['passconf']);
    $postedmail = trim($_POST['email']);

    // Check token
    if (!isset($_SESSION['token']) || $_POST['token'] != $_SESSION['token']) {
      $tplVars['error'] = T_('Form could not be authenticated. Please try again.');
    }

    // Check elapsed time
    if (!isset($_SESSION['token_time']) || time() - $_SESSION['token_time'] < 1) {
      $tplVars['error'] = T_('Form was submitted too quickly. Please wait before trying again.');
    }

    // Check if form is incomplete
    elseif (!$posteduser || !$postedpass || !$postedmail) {
      $tplVars['error'] = T_('You <em>must</em> enter a username, password and e-mail address.');
    }

    // Check if username is reserved
    elseif ($userservice->isReserved($posteduser)) {
      $tplVars['error'] = T_('This username has been reserved, please make another choice.');
    }

    // Check if username already exists
    elseif ($userservice->getUserByUsername($posteduser)) {
      $tplVars['error'] = T_('This username already exists, please make another choice.');
    }
    
    // Check that password is long enough
    elseif ($postedpass != '' && strlen($postedpass) < 6) {
      $tplVars['error'] = T_('Password must be at least 6 characters long.');       
    }

    // Check if password matches confirmation
    elseif ($postedpass != $postedconf) {
      $tplVars['error'] = T_('Password and confirmation do not match.');
    }

    // Check if e-mail address is blocked
    elseif ($userservice->isBlockedEmail($postedmail)) {
      $tplVars['error'] = T_('This e-mail address is not permitted.');
    }

    // Check if e-mail address is valid
    elseif (!$userservice->isValidEmail($postedmail)) {
      $tplVars['error'] = T_('E-mail address is not valid. Please try again.');
    }

    // Register details
    elseif ($userservice->addUser($posteduser, $_POST['password'], $postedmail)) {
      // Log in with new username
      $login = $userservice->login($posteduser, $_POST['password']);
      if ($login) {
        header('Location: '. createURL('bookmarks', $posteduser));
      }
      $tplVars['msg'] = T_('You have successfully registered. Enjoy!');
    }
    else {
      $tplVars['error'] = T_('Registration failed. Please try again.');
    }
  }
  else {
    $tplVars['msg'] = T_('Woah there, go easy on the Register button! Your registration was successful. Check your e-mail for instructions on how to verify your account.');  
  }
}

// Generate anti-CSRF token
$token = md5(uniqid(rand(), TRUE));
$_SESSION['token']      = $token;
$_SESSION['token_time'] = time();

$tplVars['loadjs']     = TRUE;
$tplVars['subtitle']   = T_('Register');
$tplVars['formaction'] = createURL('register');
$tplVars['token']      = $token;
$templateservice->loadTemplate('register.tpl', $tplVars);

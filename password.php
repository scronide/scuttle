<?php
/***************************************************************************
Copyright (c) 2005 Marcus Campbell
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
$userservice =& ServiceFactory::getServiceInstance('UserService');
$templateservice =& ServiceFactory::getServiceInstance('TemplateService');
$tplVars = array();

// IF SUBMITTED
if ($_POST['submitted']) {

    // NO USERNAME
    if (!$_POST['username']) {
        $tplVars['error'] = T_('You must enter your username.');

    // NO E-MAIL
    } elseif (!$_POST['email']) {
        $tplVars['error'] = T_('You must enter your <abbr title="electronic mail">e-mail</abbr> address.');

    // USERNAME AND E-MAIL
    } else {

        // NO MATCH
        if (!($userinfo = $userservice->getUserByUsername($_POST['username']))) {
            $tplVars['error'] = T_('No matches found for that username.');

        } elseif ($_POST['email'] != $userinfo['email']) {
            $tplVars['error'] = T_('No matches found for that combination of username and <abbr title="electronic mail">e-mail</abbr> address.');

        // MATCH
        } else {

            // GENERATE AND STORE PASSWORD
            $password = $userservice->generatePassword($userinfo['uId']);
            if (!($password = $userservice->generatePassword($userinfo['uId']))) {
                $tplVars['error'] = T_('There was an error while generating your new password. Please try again.');    

            } else {
                // SEND E-MAIL
                $message = T_('Your new password is:') ."\n". $password ."\n\n". T_('To keep your bookmarks secure, you should change this password in your profile the next time you log in.');
                $message = wordwrap($message, 70);
                $headers = 'From: '. $adminemail;
                $mail = mail($_POST['email'], sprintf(T_('%s Account Information'), $sitename), $message);

                $tplVars['msg'] = sprintf(T_('New password generated and sent to %s'), $_POST['email']);
            }
        }
    }
}

$templatename = 'password.tpl';
$tplVars['subtitle'] = T_('Forgotten Password');
$tplVars['formaction']  = createURL('password');
$templateservice->loadTemplate($templatename, $tplVars);
?>

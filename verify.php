<?php
/***************************************************************************
Copyright (C) 2006 - 2007 Scuttle project
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
$userservice        =& ServiceFactory::getServiceInstance('UserService');
$templateservice    =& ServiceFactory::getServiceInstance('TemplateService');
$tplVars = array();

@list($user, $hash) = isset($_GET['query']) ? explode('/', $_GET['query']) : NULL;

$templatename           = 'register.tpl';
$tplVars['subtitle']    = T_('Register');
$tplVars['formaction']  = createURL('register');

if ($user && $hash) {
    if ($userservice->verify($user, $hash)) {
        $templatename           = 'login.tpl';
        $tplVars['subtitle']    = T_('Log In');
        $tplVars['formaction']  = createURL('login');
        $tplVars['msg']         = sprintf(T_('Account verified. Log in to start using %s.'), $sitename);
    } else {
        $tplVars['loadjs']  = true;
        $tplVars['error']   = T_('Account verification failed');
    }
} else {
    $tplVars['loadjs']  = true;
    $tplVars['error']   = T_('Account verification failed');
}

$templateservice->loadTemplate($templatename, $tplVars);
?>
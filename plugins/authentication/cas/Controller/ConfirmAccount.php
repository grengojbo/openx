<?php

/*
+---------------------------------------------------------------------------+
| OpenX v${RELEASE_MAJOR_MINOR}                                                                |
| =======${RELEASE_MAJOR_MINOR_DOUBLE_UNDERLINE}                                                                |
|                                                                           |
| Copyright (c) 2003-2008 OpenX Limited                                     |
| For contact details, see: http://www.openx.org/                           |
|                                                                           |
| This program is free software; you can redistribute it and/or modify      |
| it under the terms of the GNU General Public License as published by      |
| the Free Software Foundation; either version 2 of the License, or         |
| (at your option) any later version.                                       |
|                                                                           |
| This program is distributed in the hope that it will be useful,           |
| but WITHOUT ANY WARRANTY; without even the implied warranty of            |
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             |
| GNU General Public License for more details.                              |
|                                                                           |
| You should have received a copy of the GNU General Public License         |
| along with this program; if not, write to the Free Software               |
| Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA |
+---------------------------------------------------------------------------+
$Id$
*/

require_once MAX_PATH . '/plugins/authentication/cas/Central/Cas.php';

/**
 * A very simple controller for sso-accounts confirmation page.
 * 
 * Its a starting point for farther refactorization. Once we will decide
 * to use full blown framework system with some controllers it should
 * be relatively easily to migrate this class.
 *
 */
class OA_Controller_SSO_ConfirmAccount
{
    var $request;
    
    /**
     * Registered actions. Only these actions may be executed from withing controller
     *
     * @var array
     */
    var $aActions = array(
        'link',
        'create',
        'check',
        'display'
    );
    var $actionDefault = 'display';
    
    var $oCentral;
    var $oPlugin;
    
    var $oTpl;
    var $aModel;
    
    var $aErrors = array();
    
    /**
     * actions specific variables
     */
    var $isVerified = false;
    var $ssoAccountId;
    var $doUsers;
    var $urlConfirm = "sso-confirm.php?id=%d&action=%s";
    
    /**
     * Messages
     */    
    var $msgErrorNoMatchingUser = 'Error: There is no matching user. Check if your link is correct or contact your OpenX administrator.';
    var $msgErrorPrefix = 'Error: ';
    var $msgErrorGeneralUpdateError = 'Error while updating an account. Please try again.';
    var $msgErrorWrongCredentials = 'Your username or password are not correct. Please try again.';
    var $msgErrorGeneralAccountCreateError = 'Could not create your new OpenX account. Please try again.';
    var $msgErrorWrongParameters = 'Wrong parameters';
    
    function init()
    {
        $this->initModel();
        $this->oCentral = &new OA_Central_Cas();
        $this->oPlugin = &MAX_Plugin::factory('authentication', 'cas');
        MAX_Plugin_Translation::registerInGlobalScope('authentication', 'cas');
    }
    
    function initModel()
    {
        $this->aModel = array(
            'hideCreate' => true,
            'hideLink' => true,
        );
    }
    
    function process($request)
    {
        $this->setRequest($request);
        $this->init();
        return $this->executeAction($this->request['action']);
    }
    
    function executeAction($action)
    {
        if (empty($action)) {
            $action = $this->actionDefault;
        }
        if (in_array($action, $this->aActions)) {
            return $this->$action();
        }
        MAX::raiseError('No such action: ' . $action, PEAR_ERROR_DIE);
    }
    
    /**
     * Verify users email and verification hash
     */
    function verify()
    {
        $this->ssoAccountId = $this->oCentral->checkEmail($this->request['vh'], $this->request['email']);
        if ($this->isError($this->ssoAccountId)) {
            $this->addError($this->ssoAccountId);
            $this->setVerified(false);
            return false;
        } else {
            $this->setVerified(true);
            return true;
        }
    }
    
    function validate()
    {
        if (empty($this->request['email']) || empty($this->request['vh'])) {
            $this->setModelProperty('errorNoMatchingAccount', true);
            return false;
        }
        return true;
    }
    
    function setVerified($isVerified)
    {
        if (!$isVerified) {
            $this->addError($this->msgErrorNoMatchingUser);
            $this->setModelProperty('errorNoMatchingAccount', true);
        }
        $this->isVerified = $isVerified;
    }
    
    function setRequest($request)
    {
        $this->request = $request;
    }
    
    function setView(&$oTpl)
    {
        $this->oTpl = &$oTpl;
    }
    
    function &getView()
    {
        return $this->oTpl;
    }
    
    function setModelProperty($propety, $value)
    {
        $this->aModel[$propety] = $value;
    }
    
    function assignModelToView(&$oTpl)
    {
        foreach ($this->aModel as $property => $value) {
            $oTpl->assign($property, $value);
        }
    }
    
    function isError($error)
    {
        return PEAR::isError($error);
    }
    
    function getErrors()
    {
        return $this->aErrors;
    }
       
    function addError($errorMsg)
    {
        if (PEAR::isError($errorMsg)) {
            $errorMsg = $this->oPlugin->translate($this->msgErrorPrefix) . $errorMsg->getMessage();
        } else {
            $errorMsg = $this->oPlugin->translate($errorMsg);
        }
        $this->aErrors[] = $errorMsg;
    }
    
    function loadDoUsers()
    {
        $this->doUsers = OA_Dal::factoryDO('users');
        if ($this->doUsers->loadByProperty('email_address', $this->request['email'])) {
            $this->setModelProperty('userName', $this->doUsers->contact_name);
            return true;
        } else {
            $this->setVerified(false);
            return false;
        }
    }
    
    /**
     * Default action.
     *
     */
    function display()
    {
        if($this->verify()) {
            return $this->loadDoUsers();
        }
        return false;
    }
    
    /**
     * Action "link". Links existing SSO account with users account.
     */
    function link()
    {
        $this->verify();
        $this->loadDoUsers();
        $this->setModelProperty('hideLink', false);
        
        if (empty($this->request['ssoexistinguser']) || empty($this->request['ssoexistingpassword'])) 
        {
            $this->addError($this->msgErrorWrongParameters);
            return false;
        }
        
        $ssoAccountId = $this->getSsoAccountId();
        if (!$ssoAccountId) {
        	return false;
        }
        
        // @todo - add a database constraint on sso_user_id
        if ($this->checkIfSsoUserExists($ssoAccountId)) {
            // TODO - bug! this needs to be fixed
            // return true;
        }
        
        $accountEmail = $this->getSsoAccountEmail($ssoAccountId);
        if ($accountEmail)
        {
            $this->doUsers->sso_user_id = $ssoAccountId;
            $this->doUsers->email_address = $accountEmail;
            $ret = $this->doUsers->update();
            if ($ret !== false && !$this->isError($ret)) {
                $this->oCentral->rejectPartialAccount($this->ssoAccountId, $this->request['vh']);
                $this->redirectToConfirmPageAndExit('linked', $this->doUsers->user_id);
            } else {
                $this->addError($this->msgErrorGeneralUpdateError);
            }
        } else {
            $this->addError($this->msgErrorWrongCredentials); 
        }
    }


    function redirectToConfirmPageAndExit($action, $userId)
    {
        $url = sprintf($this->urlConfirm, $userId, $action);
        header ("Location: " . $url);
        exit();
    }
    
    /**
     * Action "create". Creates a new SSO account
     *
     */
    function create()
    {
        $this->verify();
        $this->loadDoUsers();
        
        if (!empty($this->request['ssonewuser']) && !empty($this->request['ssonewpassword'])) 
        {
            $ret = $this->oCentral->completePartialAccount($this->ssoAccountId, $this->request['ssonewuser'],
                md5($this->request['ssonewpassword']), $this->request['vh']);
            if ($ret && !$this->isError($ret))
            {
                $this->redirectToConfirmPageAndExit('created', $this->doUsers->user_id);
            } elseif ($this->isError($ret)) {
                $this->addError($ret);
            }
        }
        $this->setModelProperty('errorCreateFailed', $this->oPlugin->translate($this->msgErrorGeneralAccountCreateError));
        $this->setModelProperty('hideCreate', false);
    }
    
    /**
     * Action "check". Checks if username is available
     *
     */
    function check()
    {
        // @todo - add validation here before passing username to xml-rpc call
        // waiting here for a product decision on minimum length of username
        $ret = false;
        if ($this->request['proposedusername']) {
            $ret = $this->oCentral->isUserNameAvailable($this->request['proposedusername']);
        }
        echo ($ret && !$this->isError($ret)) ? 'available': 'notavailable';
        exit();
    }
    
    function checkIfSsoUserExists($ssoAccountId)
    {
        $doUsersCheck = OA_Dal::factoryDO('users');
        $doUsersCheck->sso_user_id = $ssoAccountId;
        return $doUsersCheck->find();
    }
    
    function &getCasPlugin()
    {
        return $this->oPlugin;
    }
    
    function getSsoAccountId()
    {
        $md5Password = md5($this->request['ssoexistingpassword']);
        $ret = $this->oCentral->getAccountIdByUsernamePassword(
            $this->request['ssoexistinguser'], $md5Password);
        if ($this->isError($ret)) {
            $this->addError($ret);
            return false;
        }
        return $ret;
    }
    
    function getSsoAccountEmail($ssoAccountId)
    {
        $accountEmail = false;
        if ($ssoAccountId && !$this->isError($ssoAccountId)) {
            $accountEmail = $this->oCentral->getAccountEmail($ssoAccountId);
        }
        if ($this->isError($accountEmail)) {
            $accountEmail = false;
        }
        return $accountEmail;
    }    
}

?>
<?php
/**
 * The Horde_Auth_Smb class provides a SMB implementation of the Horde
 * authentication system.
 *
 * This module requires the smbauth extension for PHP:
 *   http://tekrat.com/wp/smbauth/
 *
 * At the time of this writing, the extension, and thus this module, only
 * supported authentication against a domain, and pdc and bdc must be non-null
 * and not equal to each other. In other words, to use this module you must
 * have a domain with at least one PDC and one BDC.
 *
 * Required parameters:
 * <pre>
 * 'hostspec' - IP, DNS Name, or NetBios Name of the SMB server to
 *              authenticate with.
 * 'domain' - The domain name to authenticate with.
 * </pre>
 *
 * Optional parameters:
 * <pre>
 * 'group' - Group name that the user must be a member of. Will be
 *           ignored if the value passed is a zero length string.
 * </pre>
 *
 * Copyright 1999-2010 The Horde Project (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you did
 * not receive this file, see http://opensource.org/licenses/lgpl-2.1.php
 *
 * @author  Jon Parise <jon@horde.org>
 * @author  Marcus I. Ryan <marcus@riboflavin.net>
 * @package Horde_Auth
 */
class Horde_Auth_Smb extends Horde_Auth_Base
{
    /**
     * Constructor.
     *
     * @param array $params  A hash containing connection parameters.
     *
     * @throws Horde_Auth_Exception
     */
    public function __construct($params = array())
    {
        if (!Horde_Util::extensionExists('smbauth')) {
            throw new Horde_Auth_Exception(_("Horde_Auth_Smb: Required smbauth extension not found."));
        }

        /* Ensure we've been provided with all of the necessary parameters. */
        Horde::assertDriverConfig($params, 'auth',
            array('hostspec', 'domain'),
            'authentication Samba');

        parent::__construct($params);
    }

    /**
     * Find out if the given set of login credentials are valid.
     *
     * @param string $userId      The userId to check.
     * @param array $credentials  An array of login credentials.
     *
     * @throws Horde_Auth_Exception
     */
    public function _authenticate($userId, $credentials)
    {
        if (empty($credentials['password'])) {
            throw new Horde_Auth_Exception('', Horde_Auth::REASON_BADLOGIN);
        }

        /* Authenticate. */
        $rval = validate($this->_params['hostspec'],
                         $this->_params['domain'],
                         empty($this->_params['group']) ? '' : $this->_params['group'],
                         $userId,
                         $credentials['password']);

        if ($rval === 1) {
            throw new Horde_Auth_Exception(_("Failed to connect to SMB server."));
        } elseif ($rval !== 0) {
            throw new Horde_Auth_Exception(err2str());
        }
    }

}

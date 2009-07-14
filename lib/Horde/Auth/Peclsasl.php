<?php
/**
 * The Horde_Auth_Peclsasl:: class provides a SASL-based implementation of the
 * Horde authentication system.
 *
 * SASL is the Simple Authentication and Security Layer (as defined by RFC
 * 2222). It provides a system for adding plugable authenticating support to
 * connection-based protocols.
 *
 * This driver relies on the PECL sasl package:
 *
 *      http://pecl.php.net/package/sasl
 *
 * Optional parameters:
 * <pre>
 * 'app'      The name of the authenticating application.
 *            DEFAULT: horde
 * 'service'  The name of the SASL service to use when authenticating.
 *            DEFAULT: php
 * </pre>
 *
 * Copyright 2004-2009 The Horde Project (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you did
 * not receive this file, see http://opensource.org/licenses/lgpl-2.1.php
 *
 * @author  Jon Parise <jon@horde.org>
 * @package Horde_Auth
 */
class Horde_Auth_Peclsasl extends Horde_Auth_Base
{
    /**
     * Constructor.
     *
     * @param array $params  A hash containing connection parameters.
     *
     * @throws Horde_Exception
     */
    public function __construct($params = array())
    {
        if (!Horde_Util::extensionExists('sasl')) {
            throw new Horde_Exception('Horde_Auth_Peclsasl:: requires the sasl PECL extension to be loaded.');
        }

        $params = array_merge(array(
            'app' => 'horde',
            'service' => 'php'
        ), $params);

        parent::__construct($params);

        sasl_server_init($this->_params['app']);
    }

    /**
     * Find out if a set of login credentials are valid.
     *
     * @param string $userId      The userId to check.
     * @param array $credentials  An array of login credentials.
     *
     * @throws Horde_Exception
     */
    protected function _authenticate($userId, $credentials)
    {
        if (empty($credentials['password'])) {
            throw new Horde_Exception('', Horde_Auth::REASON_BADLOGIN);
        }

        $conn = sasl_server_new($this->_params['service']);
        if (!is_resource($conn)) {
            throw new Horde_Exception(_("Failed to create new SASL connection."));
        }

        if (!sasl_checkpass($conn, $userId, $credentials['password'])) {
            throw new Horde_Exception(sasl_errdetail($conn));
        }
    }

}

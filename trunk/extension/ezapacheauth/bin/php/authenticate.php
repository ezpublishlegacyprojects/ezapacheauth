<?php
/**
 * Script used to test ezp user/passwords, in teh format needed by mod_auth_external
 *
 * @version $Id$
 * @author G. Giunta
 * @copyright Copyright (C) 2010 G. Giunta
 * @license code licensed under the GNU GPL 2.0: see README
 */

if ( isset( $_SERVER['REQUEST_METHOD'] ) )
{
    // this script is not meant to be accessed via the web!
    // note: ezscript class later does the same check, but after intializing a lot of stuff
    exit( 2 );
}

require( 'autoload.php' );

$cli = eZCLI::instance();
$script = eZScript::instance( array( 'use-extensions' => true, 'description' => 'Authenticator script' ) );
$script->startup();
// if no options passed on cli, do not waste time with parsing stuff
if ( $argc > 1 )
{
    $options = $script->getOptions();
}
else
{
    $options = array();
}
$script->initialize();

// return code: 0 = ok, all the rest = KO
$ok = 1;

// read from stdin
$fh = fopen('php://stdin', 'r');
$user = rtrim( fgets( $fh, 4096 ) );
$password = rtrim( fgets( $fh, 4096 ) );
if ( $user == "" || $password == "" )
{
    $ok = 3;
}
else
{
    // code taken from kernel/user/login.php, slightly optimized

    $ini = eZINI::instance();
    // this is defined in defaultv site.ini: no need to check if it is there
    $loginHandlers = $ini->variable( 'UserSettings', 'LoginHandler' );
    /// @todo check that it is an array with at least one value, or fallback on std
    //{
    //    $loginHandlers = array( 'standard' );
    //}
    foreach ( $loginHandlers as $loginHandler )
    {
        $userClass = eZUserLoginHandler::instance( $loginHandler );
        if ( !is_object( $userClass ) )
        {
            /// @todo log warning
            continue;
        }
        $user = $userClass->loginUser( $user, $password );
        if ( $user instanceof eZUser )
        {
            $ok = 0;
            break;
        }
    }
}

$script->shutdown( $ok );

?>
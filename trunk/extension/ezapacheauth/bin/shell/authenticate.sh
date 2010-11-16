#!/bin/sh

# Useful to set up execution of the script in the proper directory.

EZPDIR=`dirname $0`

cd $EZPDIR/../../../..
php extension/ezapacheauth/bin/php/authenticate.php $*

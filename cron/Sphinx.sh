#!/bin/sh

###############################################################################
# Sphinx helper script to simplify running simple commands on the search tools
#
# Usage:
hlp="$0 {start|stop|index} [dev]"
# The optional dev argument uses the alternative development configuration
###############################################################################

#set the working directory to the root of the source files
DIR="$( cd "$( dirname "$0" )" && pwd )"
cd "$DIR"
cd ../

if [ -z "$1" ]
then
    echo "$hlp"
    exit
fi

CONFIG="config/sphinx.conf"
if [ "$2" = "dev" ]
then
    CONFIG="config/sphinxdev.conf"
fi

case "$1" in
        start)
            sphinx-searchd --config "$CONFIG";;
        stop)
            sphinx-searchd --stop --config "$CONFIG";;
        index)
            sphinx-indexer --rotate --config "$CONFIG" mods;;
        *)
            echo "$hlp";;
esac
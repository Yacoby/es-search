#!/bin/sh

###############################################################################
# Sphinx helper script to simplify running simple commands on the search tools
#
# Usage:
hlp="$0 {start|stop|index|indexdelta|merge}"
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

case "$1" in
        start)
            sphinx-searchd --config "$CONFIG";;
        stop)
            sphinx-searchd --stop --config "$CONFIG";;

        #fairly cheap, doesn't deal with deleted mods
        indexdelta)
            sphinx-indexer --rotate --config "$CONFIG" mods_delta;;

        #less cheap, merges the main and delta indexes, slightly less expensive
        #than rebuilding everything, but still doesn't take into account deleted
        #mods
        merge)
            sphinx-indexer --merge --rotate --config "$CONFIG" mods mods_delta;;

        #reindex everyting
        index)
            sphinx-indexer --rotate --config "$CONFIG" mods;;
        *)
            echo "$hlp";;
esac

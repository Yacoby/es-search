#!/bin/sh

###############################################################################
# Sphinx helper script to simplify running simple commands on the search tools
#
# Usage:
hlp="$0 {start|stop|index|indexdelta|merge} [cmdprefix]"
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

#two diffrenent names for the sphinx tools, sometimes with a prefix of 
#sphinx-
PREFIX=""
if [ -z "$2" ]
then
    PREFIX="$2"
fi

case "$1" in
        start)
            "$PREFIX"searchd --config "$CONFIG";;
        stop)
            "$PREFIX"searchd --stop --config "$CONFIG";;

        #fairly cheap, doesn't deal with deleted mods
        indexdelta)
            "$PREFIX"indexer --rotate --config "$CONFIG" mods_delta;;

        #less cheap, merges the main and delta indexes, slightly less expensive
        #than rebuilding everything, but still doesn't take into account deleted
        #mods
        merge)
            "$PREFIX"indexer --merge --rotate --config "$CONFIG" mods mods_delta;;

        #reindex everyting
        index)
            "$PREFIX"indexer --rotate --config "$CONFIG" mods;;
        *)
            echo "$hlp";;
esac

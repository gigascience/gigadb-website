#!/bin/bash

BASEDIR=$(dirname $0)

lastTimeInsert=`cat ${BASEDIR}/data/dbchanges.txt`

indexData=`cat ${BASEDIR}/data/lastIndexer.txt`

IFS=";"
set -- $indexData

timeStart=$1
timeEnd=$2

echo ${lastTimeInsert}
echo ${timeStart}
echo ${timeEnd}

if [[ ${timeStart} -gt ${timeEnd} ]]
then
    echo "Indexer is Running, Do not run "
    exit 1
fi

if [[ ${lastTimeInsert} -gt ${timeEnd} ]]; then

    lastIndexRun=$((timeEnd-timeStart))
    now=`date +%s`
    fromLastRun=$((now-timeEnd))

    echo ${lastIndexRun}
    echo ${fromLastRun}

    if [ ${fromLastRun} -gt ${lastIndexRun} ]; then
        newTimeStart=`date +%s`
        echo "${newTimeStart};0" > ${BASEDIR}/data/lastIndexer.txt
        /usr/local/bin/indexer --all --rotate

        newTimeFinish=`date +%s`
        echo "${newTimeStart};${newTimeFinish}" > ${BASEDIR}/data/lastIndexer.txt
    else
        echo "Schedule run so frequenty, and it will run later"
    fi
else
    echo "No New Data";
fi

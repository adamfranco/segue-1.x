#!/bin/sh
# change the repository

FROM=sitesdb
TO=segue

sed "s/$FROM/$TO/" $1 > $1.tmp
mv $1.tmp $1

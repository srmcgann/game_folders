#!/bin/bash

echo "enter the new domain..."
read -r newdomain

echo "enter the new database USER name"
read -r newdbuser

echo "enter the new database name"
read -r newdb

echo "enter the new db password"
read -r newPass

echo "enter the new server/host name"
read -r newServer

php sd.php "$newdomain" "$newdbuser" "$newdb" "$newPass" "$newServer";

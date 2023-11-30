#!/bin/bash

BLUE="\e[34m"
GREEN="\e[32m"
ENDCOLOR="\e[0m"
START="[${GREEN}JLFdzDev/${ENDCOLOR}${BLUE}hestiacp-nodejs${ENDCOLOR}]"

echo -e "${GREEN}       ____    ______    __      ____           
      / / /   / ____/___/ /___  / __ \___ _   __
 __  / / /   / /_  / __  /_  / / / / / _ \ | / /
/ /_/ / /___/ __/ / /_/ / / /_/ /_/ /  __/ |/ / 
\____/_____/_/    \__,_/ /___/_____/\___/|___/${ENDCOLOR}"

echo -e "${BLUE}┬ ┬┌─┐┌─┐┌┬┐┬┌─┐┌─┐┌─┐   ┌┐┌┌─┐┌┬┐┌─┐ ┬┌─┐
├─┤├┤ └─┐ │ │├─┤│  ├─┘───││││ │ ││├┤  │└─┐
┴ ┴└─┘└─┘ ┴ ┴┴ ┴└─┘┴     ┘└┘└─┘─┴┘└─┘└┘└─┘${ENDCOLOR}"

echo -e "───────────────────────────────────────────────"

sudo cp -r quickinstall-app/NodeJs /usr/local/hestia/web/src/app/WebApp/Installers/
echo -e "${START} Copy QuickInstall App ✅"

sudo cp templates/* /usr/local/hestia/data/templates/web/nginx
echo -e "${START} Copy Templates ✅"

sudo chmod 644 /usr/local/hestia/data/templates/web/nginx/NodeJS.tpl
sudo chmod 644 /usr/local/hestia/data/templates/web/nginx/NodeJS.stpl

sudo chmod -R 644 /usr/local/hestia/web/src/app/WebApp/Installers/NodeJs/
sudo chmod 755 /usr/local/hestia/web/src/app/WebApp/Installers/NodeJs
sudo chmod 755 /usr/local/hestia/web/src/app/WebApp/Installers/NodeJs/NodeJsUtils
sudo chmod 755 /usr/local/hestia/web/src/app/WebApp/Installers/NodeJs/templates
sudo chmod 755 /usr/local/hestia/web/src/app/WebApp/Installers/NodeJs/templates/nginx
sudo chmod 755 /usr/local/hestia/web/src/app/WebApp/Installers/NodeJs/templates/web
echo -e "${START} Templates and QuickInstall App Permissions changed ✅"

sudo cp bin/v-add-pm2-app /usr/local/hestia/bin
sudo chmod 755 /usr/local/hestia/bin/v-add-pm2-app
echo -e "${START} Add pm2 manager to /usr/local/hestia/bin ✅"
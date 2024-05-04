#!/bin/bash
# start_lnmp.sh
# 2023-06-22 | CR
#
ERROR_MSG=""

export BASE_DIR="`pwd`" ;
cd "`dirname "$0"`" ;
export SCRIPTS_DIR="`pwd`" ;

RUN_CMD="$1"

echo "LNMP Stack Manager (Linux/Nginx/MySQL/PHP)"
echo ""
echo "Base directory (BASE_DIR): ${BASE_DIR}"
echo "Scripts directory (SCRIPTS_DIR): ${SCRIPTS_DIR}"
echo "Run command (RUN_CMD): ${RUN_CMD}"
echo ""

if [ "${ERROR_MSG}" = "" ]; then
	if [ -f "${SCRIPTS_DIR}/.env" ]; then
		echo "Processing ${SCRIPTS_DIR}/.env file..."
		set -o allexport;
		if ! . "${SCRIPTS_DIR}/.env"
		then
			ERROR_MSG="ERROR: could not process '${SCRIPTS_DIR}/.env' file."
		fi
		set +o allexport ;
	fi
fi

if [ "${ERROR_MSG}" = "" ]; then
	if [ -f "${BASE_DIR}/src/.env" ]; then
		echo "Processing ${BASE_DIR}/src/.env file..."
		set -o allexport;
		if ! . "${BASE_DIR}/src/.env"
		then
			ERROR_MSG="ERROR: could not process '${BASE_DIR}/src/.env' file."
		fi
		set +o allexport ;
    else
        ERROR_MSG="ERROR: '${BASE_DIR}/src/.env' file not found."
	fi
fi

if [ "${ERROR_MSG}" = "" ]; then
    if [ "${APP_NAME}" != "" ]; then
        export WORKING_DIR="/tmp/${APP_NAME}"
    else
        export WORKING_DIR="/tmp/ocr-gmap-km"
    fi
fi

if [ "${ERROR_MSG}" = "" ]; then
    # Check if Docker and Docker Compose are installed
    if ! command -v docker &> /dev/null || ! command -v docker-compose &> /dev/null; then
        ERROR_MSG="Docker and Docker Compose are required to run this script."
        # exit 1
    fi
fi

if [ "${ERROR_MSG}" = "" ]; then
    mkdir -p ${WORKING_DIR}
    # Copy all content of the scripts directory to the temporary directory
    if ! cp -r ${SCRIPTS_DIR}/* ${WORKING_DIR}
    then
        ERROR_MSG="ERROR: executing cp -r ${SCRIPTS_DIR}/* ${WORKING_DIR}"
    else
        # Remove the .env file from the temporary directory
        rm ${WORKING_DIR}/.env
    fi
fi

if [ "${ERROR_MSG}" = "" ]; then
    # Copy the nginx config file
    if [ ! -d ${WORKING_DIR}/nginx-conf ] || [ ! -f ${WORKING_DIR}/nginx-conf/default.conf ]; then
        echo "Generating Nginx config file... ${WORKING_DIR}/nginx-conf/default.conf"
        mkdir -p ${WORKING_DIR}/nginx-conf
        if ! cp ${SCRIPTS_DIR}/nginx-conf/default.conf ${WORKING_DIR}/nginx-conf/default.conf
        then
            ERROR_MSG="ERROR: executing cp ${SCRIPTS_DIR}/nginx-conf/default.conf ${WORKING_DIR}/nginx-conf/default.conf"
        fi
    fi
fi

if [ "${ERROR_MSG}" = "" ]; then
    # Set the mysql backup/restore directory
    if [ ! -d ${WORKING_DIR}/mysql-backup_restore ]; then
        echo "Creating '${WORKING_DIR}/mysql-backup_restore' directory"
        if ! mkdir -p ${WORKING_DIR}/mysql-backup_restore
        then
            ERROR_MSG="ERROR: cannot create '${WORKING_DIR}/mysql-backup_restore' directory"
        fi
    fi
fi

if [ "${ERROR_MSG}" = "" ]; then
    if [ "${RUN_CMD}" == "" ] || [ "${RUN_CMD}" == "run" ]; then
        # Generate SSL certificate if not already present
        if [ ! -f ${WORKING_DIR}/ssl-certs/server.key ] || [ ! -f ${WORKING_DIR}/ssl-certs/server.crt ]; then
            echo "Generating self-signed SSL certificate..."

            #Required
            domain="nginx-selfsigned"
            commonname="${domain}"

            #Change to your company details
            country="US"
            state="Pennsylvania"
            locality="Harrisburg"
            organization=${CERT_ORGANIZATION_DOMAIN}
            organizationalunit=IT
            email=info@${CERT_ORGANIZATION_DOMAIN}

            #Optional
            password="dummypassword"


            mkdir -p ${WORKING_DIR}/ssl-certs
            # if ! openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout ${WORKING_DIR}/ssl-certs/server.key -out ${WORKING_DIR}/ssl-certs/server.crt
            if ! openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout ${WORKING_DIR}/ssl-certs/server.key -out ${WORKING_DIR}/ssl-certs/server.crt -passin pass:${password} -subj "/C=${country}/ST=${state}/L=${locality}/O=${organization}/OU=${organizationalunit}/CN=${commonname}/emailAddress=${email}"
            then
                ERROR_MSG="ERROR: running openssl to generate the self-signed SSL certificate"
            fi
        fi
    fi
fi

if [ "${ERROR_MSG}" = "" ]; then
    if [ "${RUN_CMD}" == "" ] || [ "${RUN_CMD}" == "run" ]; then
        # Start the containers
        echo "Starting LNMP stack containers..."
        cd ${WORKING_DIR}
        if ! docker-compose up -d
        then
            ERROR_MSG="ERROR: running docker-compose up --build -d"
        else
            if ! docker ps
            then
                ERROR_MSG="ERROR: docker containers are not running"
            else
                if ! (docker ps | grep lnmp_nginx)
                then
                    ERROR_MSG="ERROR: docker containers are not running"
                else
                    echo ""
                    echo "LNMP stack is now running."
                    echo "Access the website with: https://localhost"
                fi
            fi
        fi
    fi
    if [ "${RUN_CMD}" == "down" ]; then
        echo "Stopping LNMP stack containers..."
        cd ${WORKING_DIR}
        if ! docker-compose down --remove-orphans
        then
            ERROR_MSG="ERROR: running docker-compose down"
        fi
    fi
fi

echo ""
if [ "${ERROR_MSG}" = "" ]; then
    echo "Done!"
else
    echo "${ERROR_MSG}"
fi
echo ""

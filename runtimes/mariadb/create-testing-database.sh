#!/usr/bin/env bash

/usr/bin/mariadb --user=root --password="$MYSQL_ROOT_PASSWORD" <<-EOSQL
    CREATE DATABASE IF NOT EXISTS imhotep;
    GRANT ALL PRIVILEGES ON \`imhotep%\`.* TO '$MYSQL_USER'@'%';
EOSQL
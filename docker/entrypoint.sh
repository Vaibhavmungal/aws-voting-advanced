#!/bin/bash
set -e

echo "===================================================="
echo "🗳️ VoteSecure — Initializing Container Environment"
echo "===================================================="

TARGET_DB_HOST="${DB_HOST:-localhost}"

if [ "$TARGET_DB_HOST" = "localhost" ] || [ "$TARGET_DB_HOST" = "127.0.0.1" ]; then
    echo "📦 Local database mode selected (host: $TARGET_DB_HOST)."
    echo "⏳ Starting embedded MariaDB / MySQL server..."

    mkdir -p /var/run/mysqld /var/lib/mysql /tmp
    chown -R mysql:mysql /var/lib/mysql /var/run/mysqld 2>/dev/null || true

    # Initialize data dir if empty
    if [ ! -d "/var/lib/mysql/mysql" ]; then
        echo "Running mariadb-install-db..."
        mariadb-install-db --user=mysql --datadir=/var/lib/mysql >/dev/null 2>&1 || mysql_install_db --user=mysql --datadir=/var/lib/mysql >/dev/null 2>&1 || true
    fi

    # Start service
    if [ -x /etc/init.d/mariadb ]; then
        /etc/init.d/mariadb start || service mariadb start || mysqld_safe &
    elif [ -x /etc/init.d/mysql ]; then
        /etc/init.d/mysql start || service mysql start || mysqld_safe &
    else
        mysqld_safe &
    fi

    echo "Waiting for MySQL service to become ready..."
    MAX_TRIES=30
    TRIES=0
    until mysqladmin ping --silent || [ $TRIES -ge $MAX_TRIES ]; do
        sleep 1
        TRIES=$((TRIES + 1))
    done

    # Ensure socket symlink and permissions for PHP
    ln -sf /var/run/mysqld/mysqld.sock /tmp/mysql.sock 2>/dev/null || true
    chmod 777 /var/run/mysqld/mysqld.sock 2>/dev/null || true

    if [ $TRIES -ge $MAX_TRIES ]; then
        echo "⚠️ Warning: MySQL ping timed out, continuing anyway..."
    else
        echo "✅ MySQL service is running and healthy."
    fi

    if [ ! -f /var/lib/mysql/.db_initialized ]; then
        echo "⚙️ First run detected! Setting up 'aws_voting' database..."

        mysql -e "CREATE DATABASE IF NOT EXISTS \`aws_voting\` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"

        mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED VIA mysql_native_password USING PASSWORD('');" 2>/dev/null || \
        mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED BY '';" 2>/dev/null || true

        mysql -e "CREATE USER IF NOT EXISTS 'root'@'127.0.0.1' IDENTIFIED BY '';" 2>/dev/null || true
        mysql -e "GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' WITH GRANT OPTION;" 2>/dev/null || true
        mysql -e "GRANT ALL PRIVILEGES ON *.* TO 'root'@'127.0.0.1' WITH GRANT OPTION;" 2>/dev/null || true

        mysql -e "CREATE USER IF NOT EXISTS 'voting_user'@'%' IDENTIFIED BY 'voting_secret';" 2>/dev/null || true
        mysql -e "CREATE USER IF NOT EXISTS 'voting_user'@'localhost' IDENTIFIED BY 'voting_secret';" 2>/dev/null || true
        mysql -e "CREATE USER IF NOT EXISTS 'voting_user'@'127.0.0.1' IDENTIFIED BY 'voting_secret';" 2>/dev/null || true
        mysql -e "GRANT ALL PRIVILEGES ON \`aws_voting\`.* TO 'voting_user'@'%';" 2>/dev/null || true
        mysql -e "GRANT ALL PRIVILEGES ON \`aws_voting\`.* TO 'voting_user'@'localhost';" 2>/dev/null || true
        mysql -e "GRANT ALL PRIVILEGES ON \`aws_voting\`.* TO 'voting_user'@'127.0.0.1';" 2>/dev/null || true
        mysql -e "FLUSH PRIVILEGES;" 2>/dev/null || true

        if [ -f /var/www/html/database/aws_voting.sql ]; then
            echo "📥 Importing schema and initial election data from database/aws_voting.sql..."
            mysql aws_voting < /var/www/html/database/aws_voting.sql
            echo "✅ Database schema imported successfully!"
        fi

        touch /var/lib/mysql/.db_initialized
        echo "🎉 Database initialization complete!"
    else
        echo "ℹ️ Database already initialized, skipping import."
    fi
else
    echo "🌐 External database configured (host: $TARGET_DB_HOST). Skipping embedded MySQL."
fi

mkdir -p /var/www/html/uploads
chown -R www-data:www-data /var/www/html/uploads
chmod -R 775 /var/www/html/uploads

echo "===================================================="
echo "🚀 Starting Apache Web Server in Foreground..."
echo "===================================================="
exec apache2-foreground

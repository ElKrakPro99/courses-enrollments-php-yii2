#!/bin/bash

# Script de instalación para Sistema de Inscripción de Cursos (Yii2 Básico)
# Versión: pre-0.1

# =====================================================
# COLORES Y FUNCIONES BASE
# =====================================================
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m'

print_message() { echo -e "${GREEN}[INFO]${NC} $1"; }
print_error()   { echo -e "${RED}[ERROR]${NC} $1"; }
print_warning() { echo -e "${YELLOW}[WARN]${NC} $1"; }
print_info()    { echo -e "${BLUE}[CHECK]${NC} $1"; }
print_success() { echo -e "${GREEN}[SUCCESS]${NC} $1"; }
print_header() {
    echo -e "\n${PURPLE}══════════════════════════════════════════════════════════════${NC}"
    echo -e "${CYAN}  $1${NC}"
    echo -e "${PURPLE}══════════════════════════════════════════════════════════════${NC}\n"
}

# =====================================================
# VARIABLES GLOBALES
# =====================================================
APP_DIR=""
DB_NAME=""
DB_USER=""
DB_PASS=""
DOMAIN=""
PORT=""
ADMIN_PASS=""
ADMIN_HASH=""

# =====================================================
# FUNCIONES UTILITARIAS
# =====================================================
check_root() {
    if [[ $EUID -ne 0 ]]; then
        print_error "Este script debe ejecutarse como root (sudo)"
        exit 1
    fi
}

is_package_installed() {
    dpkg -l "$1" 2>/dev/null | grep -q "^ii" 2>/dev/null
    return $?
}

command_exists() {
    command -v "$1" >/dev/null 2>&1
    return $?
}

ask_yes_no() {
    local prompt="$1"
    local default="${2:-n}"
    local answer
    while true; do
        read -p "$prompt [y/n] (default: $default): " answer
        answer=${answer:-$default}
        case "$answer" in
            y|Y|yes|YES) return 0 ;;
            n|N|no|NO) return 1 ;;
            *) print_warning "Por favor responde 'y' o 'n'." ;;
        esac
    done
}

ask_value() {
    local prompt="$1"
    local default="$2"
    local value
    read -p "$prompt (default: $default): " value
    echo "${value:-$default}"
}

# Ejecutar PostgreSQL desde /tmp
pg_exec() {
    cd /tmp
    sudo -u postgres psql -c "$1" 2>&1
}

pg_exec_silent() {
    cd /tmp
    sudo -u postgres psql -tAc "$1" 2>/dev/null
}

# =====================================================
# FORZAR ELIMINACIÓN DE BASE DE DATOS
# =====================================================
pg_force_drop_database() {
    local dbname="$1"
    
    print_info "Cerrando TODAS las conexiones a '$dbname'..."
    
    # Primer intento: Terminate backends
    sudo -u postgres psql -c "
        SELECT pg_terminate_backend(pg_stat_activity.pid) 
        FROM pg_stat_activity 
        WHERE pg_stat_activity.datname = '$dbname' 
        AND pid <> pg_backend_pid();
    " 2>/dev/null
    
    sleep 2
    
    # Segundo intento: Revocar conexiones y volver a terminar
    sudo -u postgres psql -c "
        ALTER DATABASE \"$dbname\" ALLOW_CONNECTIONS = false;
        SELECT pg_terminate_backend(pg_stat_activity.pid) 
        FROM pg_stat_activity 
        WHERE pg_stat_activity.datname = '$dbname';
    " 2>/dev/null
    
    sleep 2
    
    # Tercer intento: Forzar eliminación
    print_info "Eliminando base de datos '$dbname'..."
    if sudo -u postgres psql -c "DROP DATABASE IF EXISTS \"$dbname\";" 2>/dev/null; then
        print_success "Base de datos '$dbname' eliminada exitosamente."
        return 0
    else
        print_error "No se pudo eliminar '$dbname'. Intentando método alternativo..."
        
        # Método nuclear: reiniciar PostgreSQL y luego eliminar
        print_warning "Reiniciando PostgreSQL para liberar conexiones..."
        systemctl restart postgresql
        sleep 3
        
        if sudo -u postgres psql -c "DROP DATABASE IF EXISTS \"$dbname\";" 2>/dev/null; then
            print_success "Base de datos '$dbname' eliminada (tras reinicio de PostgreSQL)."
            return 0
        else
            print_error "ERROR CRÍTICO: No se pudo eliminar la base de datos."
            print_warning "Solución manual:"
            echo "  sudo systemctl stop apache2"
            echo "  sudo systemctl restart postgresql"
            echo "  sudo -u postgres psql -c \"DROP DATABASE $dbname;\""
            return 1
        fi
    fi
}

# =====================================================
# CONFIGURACIÓN INTERACTIVA
# =====================================================
configure_system() {
    print_header "Configuración del Sistema de Inscripción de Cursos"

    if [ -n "$APP_DIR" ]; then
        print_info "Configuración existente:"
        echo "   Directorio: $APP_DIR"
        echo "   BD: $DB_NAME"
        echo "   Dominio: $DOMAIN:$PORT"
        echo ""
        if ask_yes_no "¿Usar esta configuración?" "y"; then
            return
        fi
    fi

    APP_DIR=$(ask_value "Directorio de instalación" "/var/www/html/course-enrollment")
    DB_NAME=$(ask_value "Nombre de la base de datos" "enrollment_db")
    DB_USER=$(ask_value "Usuario de la base de datos" "enrollment_user")
    DB_PASS=$(ask_value "Contraseña para $DB_USER" "EnrollPass$(date +%s | sha256sum | base64 | head -c 12)")
    DOMAIN=$(ask_value "Dominio o IP del sitio" "enrollment.local")
    PORT=$(ask_value "Puerto HTTP" "80")
    ADMIN_PASS=$(ask_value "Contraseña del admin (usuario: admin)" "admin123")

    ADMIN_HASH=$(php -r "echo password_hash('$ADMIN_PASS', PASSWORD_BCRYPT);" 2>/dev/null)
    if [ -z "$ADMIN_HASH" ]; then
        ADMIN_HASH='$2y$13$8oX.tP5q8x6WGXOzYm0aJ.Yx9vN.m8hOeJlLkLRyAvmFQA1ZaBP7K'
    fi

    print_success "Configuración guardada"
}

# =====================================================
# INSTALACIÓN DE DEPENDENCIAS
# =====================================================
install_base_dependencies() {
    print_header "INSTALANDO DEPENDENCIAS BASE"
    apt-get update -qq

    is_package_installed "apache2" || { apt-get install -y apache2; systemctl enable --now apache2; }
    is_package_installed "postgresql" || { apt-get install -y postgresql; systemctl enable --now postgresql; }
    
    if ! command_exists composer; then
        cd /tmp
        php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
        php composer-setup.php --install-dir=/usr/local/bin --filename=composer
        php -r "unlink('composer-setup.php');"
    fi
    export COMPOSER_ALLOW_SUPERUSER=1
}

# =====================================================
# INSTALACIÓN DE LA APLICACIÓN
# =====================================================
install_app() {
    print_header "INSTALANDO SISTEMA"

    [ -z "$DB_NAME" ] && configure_system

    # Proyecto Yii2
    if [ ! -d "$APP_DIR" ]; then
        cd /tmp
        composer create-project --prefer-dist yiisoft/yii2-app-basic "$APP_DIR" --no-interaction
        print_success "Proyecto creado"
    fi

    # db.php
    cat > "$APP_DIR/config/db.php" <<EOF
<?php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'pgsql:host=localhost;dbname=$DB_NAME',
    'username' => '$DB_USER',
    'password' => '$DB_PASS',
    'charset' => 'utf8',
];
EOF

    # Base de datos
    print_info "Verificando BD..."
    DB_EXISTS=$(pg_exec_silent "SELECT 1 FROM pg_database WHERE datname='$DB_NAME'")
    USER_EXISTS=$(pg_exec_silent "SELECT 1 FROM pg_user WHERE usename='$DB_USER'")

    if [ "$DB_EXISTS" = "1" ]; then
        if ask_yes_no "La BD '$DB_NAME' ya existe. ¿Eliminarla y recrearla?" "y"; then
            pg_force_drop_database "$DB_NAME"  # ← USA LA FUNCIÓN CORRECTA
            DB_EXISTS="0"
        fi
    fi

    [ "$USER_EXISTS" != "1" ] && pg_exec "CREATE USER \"$DB_USER\" WITH PASSWORD '$DB_PASS';" || pg_exec "ALTER USER \"$DB_USER\" WITH PASSWORD '$DB_PASS';"
    [ "$DB_EXISTS" != "1" ] && pg_exec "CREATE DATABASE \"$DB_NAME\" OWNER \"$DB_USER\";"

    # Tablas
    print_info "Creando tablas..."
    cd /tmp
    sudo -u postgres psql -d "$DB_NAME" <<EOF
DROP TABLE IF EXISTS enrollments_tab CASCADE;
DROP TABLE IF EXISTS db_reference CASCADE;
DROP TABLE IF EXISTS public_user_tab CASCADE;
DROP TABLE IF EXISTS courses_tab CASCADE;
DROP TABLE IF EXISTS admin_tab CASCADE;

CREATE TABLE admin_tab (
    id SERIAL PRIMARY KEY,
    "user_nickname" VARCHAR(50) UNIQUE NOT NULL,
    "hash_pass" VARCHAR(255) NOT NULL
);

CREATE TABLE courses_tab (
    id SERIAL PRIMARY KEY,
    "course_name" VARCHAR(100) NOT NULL,
    "enrollments_counter" INTEGER DEFAULT 0,
    "date_begin_enrollments" DATE NOT NULL,
    "date_end_enrollments" DATE NOT NULL,
    "teacher_name" VARCHAR(100) NOT NULL
);

CREATE TABLE public_user_tab (
    id SERIAL PRIMARY KEY,
    "name" VARCHAR(50) NOT NULL,
    "last_name" VARCHAR(50) NOT NULL,
    "phone" VARCHAR(20),
    "email" VARCHAR(100) NOT NULL,
    "age" INTEGER,
    "ci" INTEGER,
    "public_entity" VARCHAR(100),
    "n_courses_enrollment" INTEGER DEFAULT 0
);

CREATE TABLE enrollments_tab (
    id SERIAL PRIMARY KEY,
    "course_id" INTEGER REFERENCES courses_tab(id) ON DELETE CASCADE,
    "user_id" INTEGER REFERENCES public_user_tab(id) ON DELETE CASCADE,
    "date_begin_enrollments" DATE NOT NULL,
    "date_end_enrollments" DATE NOT NULL,
    "counter_enrollments" INTEGER DEFAULT 0,
    "teacher_name" VARCHAR(100) NOT NULL
);

CREATE TABLE db_reference (
    id SERIAL PRIMARY KEY,
    "course_id" INTEGER REFERENCES courses_tab(id) ON DELETE CASCADE
);

INSERT INTO admin_tab ("user_nickname", "hash_pass") VALUES ('admin', '$ADMIN_HASH') ON CONFLICT DO NOTHING;

INSERT INTO courses_tab ("course_name", "date_begin_enrollments", "date_end_enrollments", "teacher_name") VALUES 
('Curso de Yii2 Básico', CURRENT_DATE - INTERVAL '1 day', CURRENT_DATE + INTERVAL '30 days', 'Prof. García'),
('Taller de PostgreSQL', CURRENT_DATE - INTERVAL '5 days', CURRENT_DATE + INTERVAL '15 days', 'Prof. Martínez')
ON CONFLICT DO NOTHING;

GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO "$DB_USER";
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO "$DB_USER";
EOF

    # Apache
    [ "$PORT" != "80" ] && ! grep -q "Listen $PORT" /etc/apache2/ports.conf && echo "Listen $PORT" >> /etc/apache2/ports.conf
    cat > "/etc/apache2/sites-available/enrollment.conf" <<EOF
<VirtualHost *:$PORT>
    ServerName $DOMAIN
    DocumentRoot $APP_DIR/web
    <Directory $APP_DIR/web>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
EOF
    a2ensite enrollment.conf 2>/dev/null
    [ "$PORT" = "80" ] && a2dissite 000-default.conf 2>/dev/null
    grep -q "$DOMAIN" /etc/hosts || echo "127.0.0.1 $DOMAIN" >> /etc/hosts
    a2enmod rewrite 2>/dev/null

    echo "RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . index.php [L]" > "$APP_DIR/web/.htaccess"

    cd "$APP_DIR"
    composer require yiisoft/yii2-bootstrap5 --no-interaction 2>/dev/null
    chown -R www-data:www-data "$APP_DIR"
    chmod -R 755 "$APP_DIR"
    chmod -R 777 "$APP_DIR/runtime" "$APP_DIR/web/assets"
    systemctl restart apache2
    print_success "¡Instalación completada!"
}

# =====================================================
# LIMPIEZA TOTAL
# =====================================================
 ncleanup() {
    print_header "LIMPIEZA TOTAL"

    # Detectar valores
    [ -z "$APP_DIR" ] && [ -d "/var/www/html/course-enrollment" ] && APP_DIR="/var/www/html/course-enrollment"
    APP_DIR=$(ask_value "Directorio a eliminar" "${APP_DIR:-/var/www/html/course-enrollment}")

    [ -z "$DB_NAME" ] && [ -f "$APP_DIR/config/db.php" ] && DB_NAME=$(grep "dbname=" "$APP_DIR/config/db.php" 2>/dev/null | grep -oP 'dbname=\K[^;"]+')
    DB_NAME=$(ask_value "Base de datos" "${DB_NAME:-enrollment_db}")

    [ -z "$DB_USER" ] && [ -f "$APP_DIR/config/db.php" ] && DB_USER=$(grep "username" "$APP_DIR/config/db.php" 2>/dev/null | grep -oP "'\K[^']+")
    DB_USER=$(ask_value "Usuario DB" "${DB_USER:-enrollment_user}")

    [ -z "$DOMAIN" ] && [ -f "/etc/apache2/sites-available/enrollment.conf" ] && DOMAIN=$(grep "ServerName" /etc/apache2/sites-available/enrollment.conf | awk '{print $2}')
    DOMAIN=$(ask_value "Dominio" "${DOMAIN:-enrollment.local}")

    [ -z "$PORT" ] && [ -f "/etc/apache2/sites-available/enrollment.conf" ] && PORT=$(grep "VirtualHost" /etc/apache2/sites-available/enrollment.conf | grep -oP '\*:\K[0-9]+')
    PORT=${PORT:-80}

    echo ""
    print_warning "⚠️  Se eliminará:"
    echo "   📁 $APP_DIR"
    echo "   🌐 $DOMAIN:$PORT"
    echo "   🗄️  $DB_NAME (usuario: $DB_USER)"
    echo ""

    if ! ask_yes_no "¿ELIMINAR TODO?" "n"; then
        return
    fi

    # PASO 1: Detener Apache
    print_info "Deteniendo Apache..."
    systemctl stop apache2
    sleep 1

    # PASO 2: Eliminar archivos
    print_info "Eliminando directorio..."
    rm -rf "$APP_DIR"

    # PASO 3: Eliminar VirtualHost
    print_info "Eliminando VirtualHost..."
    a2dissite enrollment.conf 2>/dev/null
    rm -f /etc/apache2/sites-available/enrollment.conf
    [ "$PORT" != "80" ] && sed -i "/^Listen $PORT\$/d" /etc/apache2/ports.conf

    # PASO 4: Eliminar BD (función corregida)
    print_info "Eliminando base de datos..."
    pg_force_drop_database "$DB_NAME"

    # PASO 5: Eliminar usuario
    print_info "Eliminando usuario..."
    OTHER_DBS=$(pg_exec_silent "SELECT count(*) FROM pg_database WHERE datdba = (SELECT usesysid FROM pg_user WHERE usename='$DB_USER')")
    if [ "$OTHER_DBS" = "0" ] || [ -z "$OTHER_DBS" ]; then
        pg_exec "DROP USER IF EXISTS \"$DB_USER\";"
    fi

    # PASO 6: Hosts y logs
    [ -n "$DOMAIN" ] && sed -i "/$DOMAIN/d" /etc/hosts
    rm -f /var/log/apache2/enrollment-*.log*

    # Resetear variables
    APP_DIR=""; DB_NAME=""; DB_USER=""; DB_PASS=""; DOMAIN=""; PORT=""; ADMIN_PASS=""; ADMIN_HASH=""

    systemctl start apache2
    print_success "¡Limpieza completada! Ya puedes reinstalar."
}

# =====================================================
# RESUMEN
# =====================================================
print_final_summary() {
    print_header "INSTALACIÓN COMPLETADA"
    echo -e "${GREEN}✅ Sistema listo${NC}\n"
    echo "   URL: http://$DOMAIN:$PORT/cursos"
    echo "   Admin: http://$DOMAIN:$PORT/admin/login"
    echo "   Usuario: admin / $ADMIN_PASS"
    echo ""
}

# =====================================================
# MENÚ
# =====================================================
main_menu() {
    clear
    print_header "SISTEMA DE INSCRIPCIÓN DE CURSOS"
    echo "1) Instalar sistema"
    echo "2) Limpiar TODO"
    echo "3) Salir"
    echo ""
    read -p "Opción [1-3]: " choice
    case $choice in
        1)
            install_base_dependencies
            configure_system
            install_app
            print_final_summary
            ;;
        2) cleanup ;;
        3) exit 0 ;;
        *) print_error "Inválido"; sleep 1; main_menu ;;
    esac
    read -p "Presiona Enter..."
    main_menu
}

check_root
cd /tmp
main_menu
## Obtention de l'image PHP depuis Docker Hub ########
FROM php:8.2.3-apache

# Étiquettes pour la description de l'image et le mainteneur
LABEL Description="SYMFONY 6.4 image" \
      Maintainer="TechTalk Team"

# Mise à jour et installation de paquets nécessaires
ENV DEBIAN_FRONTEND=noninteractive
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        locales git libicu-dev g++ libpng-dev libxml2-dev libzip-dev libonig-dev libxslt-dev unzip nodejs npm telnet yarn apt-transport-https ca-certificates curl gnupg && \
# Ajout de ServerName à la configuration d'Apache
    echo "ServerName localhost" >> /etc/apache2/apache2.conf && \
    # Configuration des locales
    echo "en_US.UTF-8 UTF-8" > /etc/locale.gen && \
    echo "fr_FR.UTF-8 UTF-8" >> /etc/locale.gen && \
    locale-gen && \
# Installation de Composer
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
# Installation de Symfony CLI
    curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash && \
    apt-get install -y symfony-cli && \
    apt-get clean && rm -rf /var/lib/apt/lists/* && \
# Installation de l'extension APCu
    pecl install apcu && docker-php-ext-enable apcu && \
# Installation des extensions PHP nécessaires (gd = gestionnaire d'images)
    curl -sSL https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions -o - | sh -s gd xdebug pdo pdo_mysql intl zip sodium xsl apcu sockets && \
# Installation de Google Chrome
    curl -sSL https://dl.google.com/linux/linux_signing_key.pub | apt-key add - && \
    echo "deb https://dl.google.com/linux/chrome/deb/ stable main" > /etc/apt/sources.list.d/google-chrome.list && \
    apt-get update && apt-get install -y google-chrome-stable && \
    echo 'google-chrome-stable --no-sandbox --disable-dev-shm-usage --disable-gpu --remote-debugging-port=9222 "$@"' > /usr/bin/google-chrome-stable && \
    chmod +x /usr/bin/google-chrome-stable && \
    apt-get purge --auto-remove -y curl gnupg && \
    apt-get clean && rm -rf /var/lib/apt/lists/*


# Set npm cache directory to a directory in the current user's home directory
ENV npm_config_cache=/var/www/project/.npm

# Définition du répertoire de travail
WORKDIR /var/www/project/

# Installation de Node.js : necessary to compile assets.
# Install Node.js, necessary to compile assets, and update npm to the latest version
ENV NODE_VERSION=20.x
RUN curl -fsSL https://deb.nodesource.com/setup_"$NODE_VERSION" | bash - && \
    apt-get install -y nodejs && \
    npm install -g --ignore-scripts npm@latest && \
    npm install --ignore-scripts sass-loader node-sass jquery @popperjs/core bootstrap moment --save-dev && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

USER root
RUN mkdir /.local && chown -R www-data:www-data /.local && chmod -R 755 /.local
USER www-data
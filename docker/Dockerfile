## Obtention de l'image PHP depuis Docker Hub ########
FROM php:8.2.3-apache

# Étiquettes pour la description de l'image et le mainteneur
LABEL Description="SYMFONY 6.4 image" \
      Maintainer="TechTalk Team"

# Ajout de ServerName à la configuration d'Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Mise à jour et installation de paquets nécessaires
ENV DEBIAN_FRONTEND=noninteractive
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        locales git libicu-dev g++ libpng-dev libxml2-dev libzip-dev libonig-dev libxslt-dev unzip nodejs npm \
    && rm -rf /var/lib/apt/lists/*

# Configuration des locales
RUN echo "en_US.UTF-8 UTF-8" > /etc/locale.gen && \
    echo "fr_FR.UTF-8 UTF-8" >> /etc/locale.gen && \
    locale-gen

# Installation de Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Installation de Symfony CLI
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash
RUN apt-get install -y symfony-cli

# Installation de l'extension APCu
RUN pecl install apcu && docker-php-ext-enable apcu

# Installation des extensions PHP nécessaires (gd = gestionnaire d'images)
RUN curl -sSL https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions -o - | sh -s \
      gd xdebug pdo pdo_mysql intl zip

# Définition du répertoire de travail
WORKDIR /var/www/

# Configuration pour utiliser l'utilisateur www-data
USER www-data
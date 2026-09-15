FROM ubuntu:24.04

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
      git curl ca-certificates \
      apache2 libapache2-mod-php8.3 \
      php8.3-cli php8.3-mysql php8.3-mbstring php8.3-curl php8.3-gd \
      php8.3-xml php8.3-zip php8.3-imap php8.3-bcmath php8.3-intl \
      mariadb-server \
      nodejs npm \
    && rm -rf /var/lib/apt/lists/* \
    && a2enmod rewrite

RUN useradd -m -s /bin/bash ubuntu || true

USER ubuntu
WORKDIR /workspace

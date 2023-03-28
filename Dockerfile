FROM debian:buster-slim

RUN apt-get update && \
    #Set up symfony-cli-repo for apt
    apt-get -y install lsb-release wget curl apt-transport-https ca-certificates && \
    curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash && \
    #Set up php-repo for apt
    wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg && \
    echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | tee /etc/apt/sources.list.d/php.list && \
    apt-get update && \
    # Install dependencies
    apt-get -y install nodejs npm symfony-cli\
                php8.2 php8.2-gd php8.2-dom php8.2-xml \
                php8.2-pdo php8.2-zip php8.2-mysql php8.2-sqlite && \
    npm install --global yarn

WORKDIR /app

EXPOSE 8000

CMD ["symfony", "server:start"]

FROM debian:buster-slim

ENV NODE_VERSION 14

RUN apt-get update && \
    #Set up symfony-cli-repo for apt
    apt-get -y install lsb-release wget curl apt-transport-https ca-certificates && \
    curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash && \
    #Set up php-repo for apt
    wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg && \
    echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | tee /etc/apt/sources.list.d/php.list && \
    apt-get update && \
    # Install dependencies
    apt-get -y install symfony-cli\
                php7.4 php7.4-gd php7.4-dom php7.4-xml \
                php7.4-pdo php7.4-zip php7.4-mysql php7.4-sqlite && \
    # Install node and yarn
    curl -fsSL https://deb.nodesource.com/setup_$NODE_VERSION.x | bash - && \
    apt-get -y install nodejs && \
    npm install --global yarn

WORKDIR /app

EXPOSE 8000

CMD ["symfony", "server:start"]

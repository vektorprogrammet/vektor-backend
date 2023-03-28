# Base image
FROM debian:buster-slim

# Define what Node Version to use
ENV NODE_VERSION 14

# Install required packages and dependencies
RUN apt-get update && \
    apt-get -y install apt-utils lsb-release wget curl gnupg2 apt-transport-https ca-certificates && \
    # Set up Symfony CLI repository for APT
    curl -sL 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash && \
    # Set up PHP repository for APT
    wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg && \
    echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | tee /etc/apt/sources.list.d/php.list && \
    apt-get update && \
    # Install required dependencies
    apt-get -y install symfony-cli\
                php8.2 php8.2-gd php8.2-dom php8.2-xml \
                php8.2-pdo php8.2-zip php8.2-mysql php8.2-sqlite && \
    # Install Node and Yarn
    curl -sL https://deb.nodesource.com/setup_$NODE_VERSION.x | bash - && \
    apt-get update && \
    apt-get install -y nodejs && \
    npm install --global yarn

# Set the working directory for the application
WORKDIR /app

# Expose port 8000 for the Symfony server
EXPOSE 8000

# Set command used to run application
CMD ["symfony", "serve"]

<!-- ABOUT THE PROJECT -->
## About The Project

This repository contains a Dockerized Symfony application designed for local development. For the local environment, this setup provides a quick way to spin up and showcase the test assignment.

<!-- GETTING STARTED -->
## Getting Started

### Prerequisites

Ensure you have Docker and Docker Compose installed on your machine.

### Setup and Build

1. Clone the repo
   ```sh
   git clone https://github.com/bumblecoder/tech-task.git
   cd tech-task
   cp code/.env code/.env.local
   cp code/.env code/.env.local && \
   sed -i 's|^DATABASE_URL=.*|DATABASE_URL="mysqli://docupet:password@mysql:3306/docupet_db"|' code/.env.local
   ```
2. Build and start the Docker containers:
   ```sh
   make build
   ```
   This command will build and start the necessary Docker containers. The process may take some time.
   
3. Access the application:
   
   By default, the application will be available on port 8088 of localhost. Ensure this port is available; otherwise, the build may fail.

   Open your browser and go to http://localhost:8088.

## Endpoints

The following API endpoints are available:

- GET /register — Registration page

## Tests

```docker compose exec php-fpm bin/phpunit```


<!-- LICENSE -->
## License

Distributed under the MIT License.

<!-- MARKDOWN LINKS & IMAGES -->
<!-- https://www.markdownguide.org/basic-syntax/#reference-style-links -->
[product-screenshot]: https://d15ywwv3do91l7.cloudfront.net/project_screenshot.png

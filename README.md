<div align="center">
<h3 align="center">TechTest Laravel Application</h3>
  <a href="https://github.com/othneildrew/Best-README-Template">
    <img src="https://d1hbpr09pwz0sk.cloudfront.net/logo_url/docupet-dd73cd96" alt="Logo" width="80" height="80">
  </a>
</div>

<!-- ABOUT THE PROJECT -->
## About The Project

[![Product Name Screen Shot][product-screenshot]](http://ec2-54-185-223-128.us-west-2.compute.amazonaws.com/)

This repository contains a Dockerized Laravel application designed for local development. The cloud setup and deployment (AWS EC2, RDS, S3, CloudFront) were performed manually to save time. For the local environment, this setup provides a quick way to spin up and showcase the test assignment.

**Note**: My primary framework is Symfony, but for this test assignment, I opted to use Laravel as specified in the requirements.

<!-- GETTING STARTED -->
## Getting Started

### Prerequisites

Ensure you have Docker and Docker Compose installed on your machine.

### Setup and Build

1. Clone the repo
   ```sh
   git clone https://github.com/bumblecoder/tech-task.git
   cd tech-task
   cp code/.env.example code/.env
   ```
2. Build and start the Docker containers:
   ```sh
   make build
   ```
   This command will build and start the necessary Docker containers. The process may take some time.
   
3. Create the stored procedure:
   ```sh
   make run-sql
   ```
   This will execute the SQL script to set up the stored procedure in the MySQL container.

4. Access the application:
   
   By default, the application will be available on port 80 of localhost. Ensure this port is available; otherwise, the build may fail.

   Open your browser and go to http://localhost. 

## AWS Test Environment

The application is also deployed on an AWS test environment for one week from the submission of the test assignment. After this period, the test environment will be removed.
**AWS EC2 Instance URL:**

http://ec2-54-185-223-128.us-west-2.compute.amazonaws.com

AWS services used for deployment:

- EC2 (Elastic Compute Cloud)
- RDS (Relational Database Service)
- S3 (Simple Storage Service)
- CloudFront (Content Delivery Network)
- Configuration of Security Groups

## API Endpoints

The following API endpoints are available:

- GET /api/articles — Retrieve a list of all articles
- POST /api/articles — Create a new article
- GET /api/articles/{id} — Retrieve an article by ID
- PUT|PATCH /api/articles/{id} — Update an article by ID
- DELETE /api/articles/{id} — Delete an article by ID

## Tests
![image](https://github.com/user-attachments/assets/f4c96200-52b7-45de-b640-abe5d86ccaa3)


<!-- LICENSE -->
## License

Distributed under the MIT License.

<!-- MARKDOWN LINKS & IMAGES -->
<!-- https://www.markdownguide.org/basic-syntax/#reference-style-links -->
[product-screenshot]: https://d15ywwv3do91l7.cloudfront.net/project_screenshot.png

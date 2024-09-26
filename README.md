# Docker & PHP Core Project

This project serves as an example application demonstrating modern PHP development practices and Docker containerization. It includes key features such as LDAP integration, Doctrine ORM usage, and running Nginx with PHP-FPM in Docker containers.

## Features

- PHP 8.2 application running on Docker
- LDAP integration
- Doctrine ORM usage
- Docker containers with Nginx and PHP-FPM
- PostgreSQL database support
- Basic CRUD operations example

## Requirements

- Docker and Docker Compose
- Git

## Technology Stack

- PHP 8.2
- Nginx 1.25
- PostgreSQL
- Doctrine ORM
- Symfony HttpFoundation
- FastRoute
- LDAP

## Installation

1. Clone the project:
- git clone https://github.com/username/project-name.git
2. Navigate to the project directory:
- cd project-name
3. Start the Docker containers:
- composer install
4. Start the Docker containers:
- docker-compose up -d
5. Install Composer dependencies:
- docker-compose exec php composer install

## Configuration

All necessary configurations are located in the `src/config.php` file. Adjust the settings according to your environment:

- PostgreSQL connection settings
- LDAP connection settings
- Other application-specific configurations

## Usage

The project provides an example application with basic CRUD operations. You can customize and extend these examples according to your needs.

API endpoints:

- GET /users: List all users
- GET /users/{id}: Retrieve a specific user
- POST /users: Create a new user
- PUT /users/{id}: Update a user
- DELETE /users/{id}: Delete a user

## Development

1. Edit the source code
2. Test your changes
3. Rebuild Docker images if necessary:
- docker-compose build
4. Restart containers:
- docker-compose up -d

## Notes

- This project is a starting point that requires customization. You need to adjust it according to your specific requirements.
- Database schema and migrations are not included. You need to create and manage your own database structure.
- Review security settings and take necessary precautions before moving to production.

## Contributing

1. Fork this repository
2. Create a new feature branch (`git checkout -b new-feature`)
3. Commit your changes (`git commit -am 'Add new feature: Description'`)
4. Push to the branch (`git push origin new-feature`)
5. Create a new Pull Request

## License

This project is licensed under the [MIT License](LICENSE).

## Contact

M. Mustafa Ozcan - [mustfaozcn.1994@gmail.com](mailto:mustfaozcn.1994@gmail.com)

Project Link: [https://github.com/mustfaozcn/php-docker-base.git](https://github.com/mustfaozcn/php-docker-base)
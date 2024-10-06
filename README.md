# Kaizen

**Kaizen** is a custom PHP framework designed to promote best practices in software architecture. Inspired by the philosophy of continuous improvement, Kaizen leverages hexagonal architecture to empower developers to build robust, maintainable applications with ease.

## Table of Contents

- [Features](#Features)
- [Installation](#Installation)
- [Getting Started](#Getting-Started)
- [Architecture](#Architecture)
- [Best Practices](#Best-practices)
- [Contributing](#Contributing)
- [License](#License)

## Features

- **Hexagonal Architecture:** Embrace a modular approach to application design, facilitating easy testing and maintenance.
- **Best Practices:** Encourages the implementation of SOLID principles and other software design patterns.
- **Lightweight and Flexible:** Designed to be easy to use and integrate into existing projects.
- **Community-Driven:** Open for contributions and feedback from developers.

## Installation

To get started with Kaizen, follow these steps:

1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/kaizen.git
   ```
2. Navigate to the project directory:
   ```bash
   cd kaizen
   ```
3. Install the dependencies using Composer:
   ```bash
   composer install
   ```

## Getting Started

Create a new application using Kaizen by following these steps:

1. Create a new project directory:
   ```bash
   mkdir my-kaizen-app
   cd my-kaizen-app
   ```
2. Set up the project structure:
   ```bash
   mkdir src public
   ```
3. Create an entry point file (`index.php`) in the `public` directory and include the framework:
   ```php
   <?php
   require '../kaizen/bootstrap.php';
   // Your application logic goes here.
   ?>
   ```
4. Start building your application!

## Architecture

Kaizen is built on the principles of hexagonal architecture, which promotes separation of concerns by isolating the application core from external dependencies. This structure allows for greater flexibility, easier testing, and improved maintainability.

### Key Components

- **Domain Layer:** Contains the business logic and domain models.
- **Application Layer:** Handles the application's use cases and orchestrates the interaction between the domain and presentation layers.
- **Infrastructure Layer:** Manages external dependencies, such as databases and APIs.

## Best Practices

To make the most of Kaizen, we recommend following these best practices:

- Implement SOLID principles in your code.
- Write tests for your code to ensure reliability and maintainability.
- Keep your application modular by breaking it into smaller components.
- Document your code if necessary.

## Contributing

### Prerequisites

Make sure you have [Docker](https://www.docker.com/) and [Docker Compose](https://docs.docker.com/compose/) installed on your machine.

Start the application using Docker Compose:
   ```bash
   docker-compose up -d
   ```

### Steps

Contributions to Kaizen are welcome! If you’d like to contribute, please follow these steps:

1. Fork the repository.
2. Create a new branch for your feature or bug fix.
3. Make your changes
4. Write tests (TDD is highly encouraged :wink:)
5. commit your changes with the [angular commit convention](https://gist.github.com/brianclements/841ea7bffdb01346392c).
6. Push your changes to your forked repository.
7. Submit a pull request.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

Feel free to reach out if you have any questions or feedback!

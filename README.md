# README

## Introduction

This document explains how to install, configure, and use the ISEN Bank application.

## Installation

### Prerequisites

- Docker
- Docker Compose

#### Installing Docker

1. Download and install Docker Desktop from [Docker's official website](https://www.docker.com/products/docker-desktop).
2. Follow the installation instructions for your operating system.
3. Once the installation is complete, launch Docker Desktop and ensure it is running correctly.

### Installation Steps

1. Clone the repository:
    ```bash
    git clone https://github.com/MeyWast/War-game-Banque.git
    cd War-game-Banque
    ```

2. Build and start the Docker containers:
    ```bash
    docker-compose up --build
    ```

## Usage

### File Structure

- `index.html`: Login page.
- `account.html`: Account creation page.
- `synthese.html`: Account summary page.
- `js/`: Contains JavaScript scripts for front-end interactions.
- `php/`: Contains PHP scripts for back-end interactions.
- `sql/creation.sql`: SQL script to create and initialize the database.

### Application Logic

1. **Authentication**:
    - The user logs in via `index.html`.
    - The information is sent to `php/requests.php` for verification.

2. **Account Creation**:
    - The user creates an account via `account.html`.
    - The information is sent to `php/requests.php` for registration.

3. **Account Summary**:
    - The user can view the balance and transactions via `synthese.html`.
    - The information is retrieved from the database and displayed.

### Security

- Passwords are stored using MD5 hashing.
- Transactions are verified to ensure the balance is sufficient before processing.

## Conclusion

This document provides the necessary information to install, configure, and understand the operation of the banking application. For any questions or issues, please contact the development team.


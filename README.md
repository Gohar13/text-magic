# Simple Testing System - Test task for TextMagic

## Description
This project is a simple testing system that supports questions with fuzzy logic and allows for multiple-choice answers. Users can take a test and view results for questions they answered correctly or incorrectly.

## Technologies
- Symfony
- PostgreSQL
- Docker

## Installation
1. Clone the repository:

    ```
    git clone https://github.com/Gohar13/text-magic.git
    ```
2. Copy .env.local into .env file

    ```
    cp .env.local .env

    ```
3. Start the Docker containers:
    Ensure you have Docker installed, then run:
    ```
    docker-compose up -d --build
    ```

4. Create the database: (Run command from the container)
   After starting the containers, run the migrations to set up the database structure:
    ```
    php bin/console doctrine:migrations:migrate
    ```
5. Add questions and answers: (Run command from the container)
   You can add questions and answers to the database using the provided console command.
   ```
   php bin/console app:import-questions dump_data.json
    ```

## Usage
1. Take the test:
   ```
   php bin/console app:take-test
    ```

Select questions and provide your answers. You can select options both by key and by value, and separate it by comma 
At the end of the test, you will see two lists:


- Questions you answered correctly
- Questions with mistakes
- Retake the test

You can take the test as many times as you like. Each result is saved in the database.



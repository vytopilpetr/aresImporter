# ARES Company Importer App

## Overview
The Company Importer App is a Symfony-based web application that allows importing company data from ARES using simple
form and company ICO and retrieving information about a company in JSON format through an API.

## Installation
### Prerequisites
- Docker installed on your machine.

### Steps
1. Build and run the Docker containers:
```bash
docker-compose up --build
```

2. Run composer install inside the ares-php container:
```bash
docker exec ares-php composer install
```

3. Run migrations:
```bash
docker exec ares-php bin/console doctrine:migrations:migrate
```

## Usage
### Importing a Company
1. Open your web browser and go to http://localhost:8080/importCompany.
2. Fill in the Company ICO in the provided form and click the "Import" button.
3. View the import result on the page - if the import was successful or not.

### Retrieving Company Information through API
To retrieve information about a company, send a GET request to the API endpoint:

- API Endpoint: http://localhost:8080/rest/api/{companyId}.
Replace {companyId} with the actual Company ID you want to retrieve.
Example: http://localhost:8080/rest/api/27074358
- To retrieve some data, you first need to import it. You should do the importing of company as mentioned above.


## Running Tests
Tests are stored in the `tests/` folder. DataFixtures for Functional tests are stored in `src/DataFixtures/`. 
Dummy xml data from ARES are stored in the `tests/Data/`, these are provided by the `DummyDataFetcher` to avoid 
dependency on ARES data endpoint in tests.

- Run tests:
```bash
docker exec ares-php vendor/bin/phpunit tests
```
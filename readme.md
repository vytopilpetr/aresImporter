# ARES Company Importer App

## Overview
The Company Importer App is a Symfony-based web application that allows importing company data from ARES using simple
form and company ICO and retrieving information about a company in JSON format through an API.

## Installation
### Prerequisites
- Docker installed on your machine.

### Steps
1. Clone the repository:
```bash
git clone <repository-url>
```

2. Navigate to the project directory:
```bash
cd aresImporter
```

3. Build and run the Docker containers:
```bash
docker-compose up --build
```

4. Run composer install inside the ares-php container:
```bash
docker exec ares-php composer install
```

5. Run migrations:
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
Example: http://localhost:8080/rest/api/07385285
- To retrieve some data, you first need to import it. You should do the importing of company as mentioned above.


## Running Tests

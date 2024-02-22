# GEO Country data API

This project is to get the details of a specific country by providing the country name.

## Installation

To set up the project, follow these steps:

1. Clone the repository.
   ```bash
   git clone https://github.com/Gayan-Muthukumarana/geo-data-api-v1.git
   
2. Install project dependencies using Composer:
   ```bash
   composer install

3. Copy the `.env.example` file to `.env` and configure your environment settings.
   * Please make sure the values of the following data in it:
     ```
     GEO_API_URL
     GEO_NEAR_CITY_RADIUS
     X_RAPID_API_HOST
     X_RAPID_API_KEY

4. Then run the following command:
    ```bash
   php artisan serve

## Usage
To access the site, use the following URL:

    http://127.0.0.1:8000/

It will give you a form which you can get a from to insert the country name and get the required data.

### Also using the postman you can check the api by using the following URL:

    http://127.0.0.1:8000/api/get-geo-data

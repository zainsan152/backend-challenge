# Laravel News Aggregator API

This project is a RESTful API built with Laravel to aggregate news from various sources. The project includes user authentication, preferences management, personalized news feeds, and Swagger-based API documentation.

---

## **Features**

- **User Authentication**: Register, login, and manage user credentials.
- **News Sources**: Fetch news from multiple sources (e.g., NewsAPI, The Guardian, New York Times).
- **Personalized News Feed**: Users can set preferences for sources, categories, and authors.
- **API Documentation**: Swagger (OpenAPI) documentation is included.

---

## **Quick Setup Instructions**

1. **Clone the repository**:  
   `git clone https://github.com/zainsan152/backend-challenge.git && cd backend-challenge`

2. **Copy `.env` file**:  
   `cp .env.example .env`

3. **Build and run the Docker containers**:  
   `docker compose up --build`

4. **Run database migrations**:  
   `docker exec -it laravel_app bash -c "php artisan migrate && php artisan key:generate"`

5. **Fetch news articles**:  
   `docker exec -it laravel_app bash -c "php artisan articles:fetch"`

6. **Access the application**:  
   Open [http://localhost:8000](http://localhost:8000).

7. **Access Swagger documentation**:  
   Open [http://localhost:8000/api/documentation](http://localhost:8000/api/documentation).

---

## **Available Endpoints**

### **Authentication**
- **POST** `/register`: Register a new user.
- **POST** `/login`: Login a user.

### **User Preferences**
- **GET** `/preferences`: Get user preferences.
- **POST** `/preferences`: Set user preferences.

### **Personalized News**
- **GET** `/personalized-news`: Get personalized news based on user preferences.

---

## **Stop Docker Containers**

To stop the containers, run:  
`docker compose down`

---

## **Project Structure**

```plaintext
backend-challenge/
│── Dockerfile
│── docker-compose.yml
│── README.md
│── .env
│── app/
│── bootstrap/
│── config/
│── database/
│── public/
│── resources/
│── routes/
│── storage/
│── vendor/
│── composer.json
│── artisan

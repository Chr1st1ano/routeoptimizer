# Predictive Route Optimizer 🚛 📍<img width="1920" height="1020" alt="Screenshot 2025-12-13 225957" src="https://github.com/user-attachments/assets/b2c89e10-8fa5-40e4-b139-c187c17b85d2" />
<img width="1920" height="1020" alt="Screenshot 2025-12-13 225926" src="https://github.com/user-attachments/assets/55c6200c-6976-42e0-9faf-55f08d0a3cc9" />
<img width="1920" height="1080" alt="Screenshot 2025-12-13 230553" src="https://github.com/user-attachments/assets/bfda0ccf-423e-4441-a315-f74bd05baf01" />


[![View Presentation](https://img.shields.io/badge/View_Presentation-Canva-00C4CC?style=for-the-badge&logo=canva&logoColor=white)](https://www.canva.com/design/DAG7G2LSByM/kstmMU5P6DpEA2K87XsPuQ/edit?utm_content=DAG7G2LSByM&utm_campaign=designshare&utm_medium=link2&utm_source=sharebutton)

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)

**A native PHP-based intelligent routing engine built with Laravel and php-ml.**

---

## 📖 Overview

The **Predictive Route Optimizer** is a web application that calculates the most efficient delivery paths based on historical data. Unlike traditional routing that relies solely on external APIs for logic, this project utilizes **php-ml** to perform machine learning operations directly within the PHP environment, predicting traffic coefficients and optimizing routes locally.

### 🎯 Key Features
* **Native ML Integration:** Uses `php-ml` (Regression/Classification) to forecast route times without requiring a separate Python microservice.
* **Dynamic Routing:** Calculates optimal paths between multiple coordinates.
* **Database Management:** Robust data handling using MySQL and manageable via phpMyAdmin.
* **Server-Side Rendering:** Fast, SEO-friendly interface built with Laravel Blade templates.

---

## 🛠️ Tech Stack

### Core Framework & Backend
* **Language:** PHP
* **Framework:** [Laravel](https://laravel.com/)
* **Dependency Manager:** [Composer](https://getcomposer.org/)

### Database
* **RDBMS:** MySQL
* **Management Tool:** phpMyAdmin

### Machine Learning
* **Library:** [php-ml](https://php-ml.org/) (Machine Learning for PHP)
* **Usage:** Used for training traffic prediction models and regression analysis on historical route data.

### Frontend
* **Templating:** Blade Engine
* **Styling:** CSS / Bootstrap / Tailwind (Customize as needed)

---

## 🚀 Installation & Setup

Follow these steps to set up the project locally.

### Prerequisites
* PHP >= 8.1
* Composer
* MySQL

### Step-by-Step Guide

1. **Clone the Repository**
    ```bash
    git clone [https://github.com/yourusername/predictive-route-optimizer.git](https://github.com/yourusername/predictive-route-optimizer.git)
    cd predictive-route-optimizer
    ```

2. **Install Dependencies**
    Use Composer to install the Laravel framework and the `php-ml` library.
    ```bash
    composer install
    ```

3. **Environment Configuration**
    Copy the example environment file and configure your database settings.
    ```bash
    cp .env.example .env
    ```
    *Open `.env` and set your database credentials:*
    ```ini
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=route_optimizer
    DB_USERNAME=root
    DB_PASSWORD=
    ```

4. **Generate App Key**
    ```bash
    php artisan key:generate
    ```

5. **Database Migration**
    Create the tables for routes, historical data, and user logs.
    ```bash
    php artisan migrate
    ```

6. **Train the Model (Optional)**
    If you have a seeding script to pre-train the `php-ml` model:
    ```bash
    php artisan ml:train
    ```

7. **Serve the Application**
    ```bash
    php artisan serve
    ```
    Access the app at: `http://127.0.0.1:8000`

---

## 🧠 How It Works (php-ml)

This project avoids the complexity of microservices by keeping the ML logic within PHP.

1. **Data Ingestion:** Historical trip data (distance, time of day, weather condition) is stored in MySQL.
2. **Training:** The `php-ml` library uses this data to train a **LeastSquares Regression** (or similar) model.
3. **Prediction:** When a user requests a route via the Blade interface, the controller passes the coordinates to the model, which predicts the estimated time of arrival (ETA) based on learned patterns.

---

## 📂 Project Structure

```text
/app
├── /Http/Controllers    # Handles Routing Logic
├── /Models              # Eloquent Models (Route, Location)
├── /Services/ML         # Logic for php-ml training and prediction
/resources
├── /views               # Blade Templates
/database
├── /migrations          # Database schemas

Canva Presentation
https://www.canva.com/design/DAG7G2LSByM/kstmMU5P6DpEA2K87XsPuQ/edit?utm_content=DAG7G2LSByM&utm_campaign=designshare&utm_medium=link2&utm_source=sharebutton

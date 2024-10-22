# Capstone Wheels

**Capstone Wheels** is a web application designed for driving school enrollment. It allows users to schedule driving lessons and make payments through PayMongo. The app is built using Laravel for the backend and Vue.js for the frontend.

[Website Link](https://laravel.shiinaneet.site/)

Credentials
user    : admin@email.com
password: password

## Features

- **User Registration and Login**: Users can sign up and log in to manage their driving lessons.
- **Driving Lesson Scheduling**: Students can book available slots for driving lessons.
- **Payment Integration**: Payments are processed securely through the PayMongo payment gateway.
- **Admin Dashboard**: Administrators can manage students, lessons, and payments.
- **Account Management**: Manage user accounts, including registration, updates, and permissions.
- **Vehicle Management**: Keep track of available vehicles for driving lessons.
- **Room Management**: Schedule and assign rooms for in-class sessions or driving test preparations.
- **Database Backup and Restore**: Provides features to back up and restore the database for safe data management.
- **Inquiry and FAQ**: Students and visitors can submit inquiries, and a FAQ section provides answers to common questions.
- **About Us and Help Pages**: Information about the driving school and help resources for users.
- **Calendar Integration**: Users can view lesson schedules and availability on a calendar.
- **Service List Page**: Displays the driving school services offered.
- **Driving School Service Offered Calendar**: A calendar that showcases the various services provided by the driving school.
- **Service Management with Batching and Rescheduling**: Manage driving lessons with options for batching and rescheduling.

## Tech Stack

- **Backend**: Laravel (PHP Framework)
- **Frontend**: Vue.js
- **Database**: MySQL
- **Payment Gateway**: PayMongo
- **Server**: Nginx
- **Deployment**: Ubuntu VPS

## Prerequisites

To run this project locally, you need to have the following installed:

- [PHP 8.3](https://www.php.net/)
- [Composer](https://getcomposer.org/)
- [Node.js](https://nodejs.org/en/)
- [NPM](https://www.npmjs.com/)
- [MySQL](https://www.mysql.com/)
- [Laravel 10.x](https://laravel.com/)
- [Vue.js 3.x](https://vuejs.org/)

## Installation

Follow these steps to set up the project locally:

1. **Clone the repository**:
    ```bash
    git clone https://github.com/ShiinaNeet/capstonewheels.git
    cd capstonewheels
    ```

2. **Install backend dependencies**:
    ```bash
    composer install
    ```

3. **Install frontend dependencies**:
    ```bash
    npm install
    ```

4. **Set up the environment variables**:
    - Copy the `.env.example` file and rename it to `.env`:
    ```bash
    cp .env.example .env
    ```
    - Update the database credentials and other environment variables in the `.env` file.

5. **Generate the application key**:
    ```bash
    php artisan key:generate
    ```

6. **Run database migrations**:
    ```bash
    php artisan migrate:fresh --seed
    ```
    The code creates test data with instructor, secretary, student and admin accounts.
   
8. **Build the frontend assets**:
    ```bash
    npm run dev
    ```

9. **Run the application**:
    ```bash
    php artisan serve
    ```

## Screenshots

Add screenshots of your app here:

1. **Dashboard**  
    ![Dashboard - Enrollment](https://github.com/user-attachments/assets/60c22923-f3e5-4eb9-a670-37c2fe4063b6)
    ![Dashboard - Schedule](https://github.com/user-attachments/assets/de0871ff-fafe-4d2f-a7c8-e1a6cb55dba5)
    ![Dashboard - Reschedule](https://github.com/user-attachments/assets/aa6e2e55-1780-4dc7-aeaf-dd1af0aa07f1)
    ![Dashboard - Payment](https://github.com/user-attachments/assets/796dd89d-e9e1-4722-aeea-d75025b5f659)
    ![Dashboard - Accounts](https://github.com/user-attachments/assets/da444315-7da7-45ce-955d-53166051974e)

2. **Scheduling**  
    ![Scheduling module](https://github.com/user-attachments/assets/a422c743-5a7b-4acb-8389-a775d005bff4)


3. **Reports**  
    ![Reports 3](https://github.com/user-attachments/assets/1c878e05-8f8f-49cf-ba64-a5b6dba3cbcd)
    ![Reports 1](https://github.com/user-attachments/assets/a4e26e6b-44bf-42bd-ac12-b46ed55b3c95)
   
5. **Services**
    ![Service Create](https://github.com/user-attachments/assets/d5b6c808-16d8-4128-8350-d58bce28f035)
    ![Service Table](https://github.com/user-attachments/assets/81f8fa45-2cec-40a9-b842-0cd2bee5d6fd)

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## Contact

For any inquiries, please contact:
- **Name**: Gene Paolo Dayandayan
- **Email**: dayandayangenepaolo@gmail.com
- **GitHub**: [github.com/ShiinaNeet](https://github.com/ShiinaNeet)

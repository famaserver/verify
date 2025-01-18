#  Verification mobilenumber and national_code
#  https://famaserver.com
This project is an online verification system for checking the match between mobile numbers and national codes. The system first checks if the entered information has been previously saved in the database. If the data is duplicate, it displays a "Duplicate inquiry" message. Otherwise, the request is sent to a web service to get the result, which is then saved in the database.

## Features
- Check if the mobile number and national code have been previously stored in the database.
- If the inquiry is duplicate, the request to the web service is not sent.
- If the information is new, the request is sent to the web service, and the result (success or failure) is stored in the database.
- Use of a web service to perform the verification.

## Technologies and Tools
- **PHP**: For writing server-side scripts.
- **MySQL**: For storing inquiry results.
- **cURL**: For sending HTTP requests to the web service.
- **HTML/CSS**: For building the user interface.

## Prerequisites
Before running this project, you need to have the following installed on your system:
- **PHP** (version 7.0 or higher)
- **MySQL** or **MariaDB**
- **Apache** or **Nginx** as the web server
- **cURL** (usually pre-installed with PHP)

## Setup Instructions

1. **Clone the Project**
   First, clone or download the project code:

   ```bash
   git clone https://github.com/famaserver/verify.git
---------------------------------------
2.   Create the Database and Tables After downloading the project, create the database by logging into MySQL or MariaDB and running the following commands to create the necessary tables:
   ------------------------------------
CREATE DATABASE verification_system;

USE verification_system;

CREATE TABLE inquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mobile VARCHAR(15) NOT NULL,
    national_code VARCHAR(10) NOT NULL,
    matched BOOLEAN NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

   ------------------------------------


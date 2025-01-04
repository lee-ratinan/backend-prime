# BACKOFFICE PRIMER

## What is the Primer

This is the project to build the foundation of most of the backoffice system. It is built on top of CodeIgniter 4.
CodeIgniter is a PHP full-stack web framework that is light, fast, flexible and secure.
More information can be found at the [official site](https://codeigniter.com).
The original repository for the framework can be found [here](https://github.com/codeigniter4/CodeIgniter4).
You can read the [user guide](https://codeigniter.com/user_guide/) corresponding to the latest version of the framework.

## System Requirements and Installation

PHP 8.2 or higher with the following extensions
- json (enabled by default - don't turn it off)
- [intl](http://php.net/manual/en/intl.requirements.php)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)
- [mysqlnd](http://php.net/manual/en/mysqlnd.install.php)
- [libcurl](http://php.net/manual/en/curl.requirements.php) if you plan to use the HTTP\CURLRequest library

Any database. The SQL must be run to create the database schema and the initial data.
The `.env` file must be updated accordingly to connect this system to the database.

The access to the file system to ensure the write permission to the `writable` folder.

The root folder of the project must be the `public` folder. The `index.php` file is located in this folder.

[Mailgun](https://www.mailgun.com/) is used to send emails. The API key and the domain must be updated in the `.env` file.

## Theme

The theme, [Nice Admin](https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/) theme, is purchased from BootstrapMade.

## Features included in this Primer

### User Management

- User accounts are protected by a password. The password is hashed using the `SHA256` hash algorithm, or it can be changed in the model if required.
- All passwords must be set to a certain standards by default. And it will expire after 180 days.
- All new users must change their password after the first login.
- All login attempts are logged. The user will be locked out after 3 failed attempts. The user account can be unlocked by the admin.
- The user can reset the password by email. The email will be sent to the user's email address. The user must click on the link in the email to reset the password.
- Currently, only the administrators can create the new user account. The user can be assigned to a role.
- The role must be assigned to the user.
- The user can update their own profile, including uploading the profile picture and setting the profile status.

### Role and Permission Management

- All users must be assigned to at least 1 role. 
- The user can switch between roles if they have more than 1 role.
- Each role specifies the permissions that the user has.
- There are 3 permission levels: NONE, READ, WRITE.

### Organization Management and Configuration

- The system allows the super administrator to update the organization's information and configuration.
- The logo and favicon can be uploaded.

### Logging

- All login attempts are logged.
- All user activities that are resulted in the change of database data or files are logged.
- All role switching are logged.
- All logs can be viewed by the super administrator.
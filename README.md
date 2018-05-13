# Open Source Machining Resource (OMR)
CMSC389L Final Project

## Overview
OMR, in it's current state, is a website based resource where users are able to search the proprietary database system of known, best practice machining processes and techniques for specific tools and materials. 

If the user is not satisfied with the current state of the database, the user has the ability to submit a form detailing the process that is desired. The information inputted into the form will cross-reference a Knowledge Based Machining (KBM) database verifying that the desired process is within constraints defined by the tool manufacturer and the cutting material's properties. 

Upon completion of the verification process, a message will be sent to the user updating them of the results from the verification process, and whether or not the main OMR has been updated to support the user-defined process.

## Services Used
- Dynamo DB
- ALB (Load Balancer)
- EC2
- SNS

## Architecture

![Click here to view the architecture diagram](https://raw.githubusercontent.com/pgarves/omr/master/OMR%20Diagram%20v3.png)

## EC2 Setup
The website is PHP based in order to handle the back end database operations, which means that a little extra set up is necessary to prepare an instance to process requests. 

### Nginx
Nginx is needed to process website files, including PHP, properly. The following is a good guide to installing Nginx to the EC2 instance for the first time. https://www.nginx.com/blog/setting-up-nginx/#open-web-page

### PHP Processing
This can be a little tricky as documentation online is not necessarily up to date however the following two resources will help you get PHP processing up and working, so that it is possible to serve php files:
https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/getting-started_installation.html
https://www.digitalocean.com/community/tutorials/how-to-install-linux-nginx-mysql-php-lemp-stack-in-ubuntu-16-04
https://www.scalescale.com/tips/nginx/php5-fpm-sock-failed-13-permission-denied-error/

As reference this is how the my .conf looked like: 
![Click here to view the .conf file]()

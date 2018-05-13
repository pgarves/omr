# Open Source Machining Resource (OMR)
CMSC389L Final Project

## Overview
OMR, in it's current state, is a website based resource where users are able to search the proprietary database system of known, best practice machining processes and techniques for specific tools and materials. 

If the user is not satisfied with the current state of the database, the user has the ability to submit a form detailing the process that is desired. The information inputted into the form will cross-reference a Knowledge Based Machining (KBM) database verifying that the desired process is within constraints defined by the tool manufacturer and the cutting material's properties. 

Upon completion of the verification process, a message will be sent to the user updating them of the results from the verification process, and whether or not the main OMR has been updated to support the user-defined process.

## Services Used
- DynamoDB
- ALB (Load Balancer)
- EC2
- SNS

## Architecture

![Click here to view the architecture diagram](https://raw.githubusercontent.com/pgarves/omr/master/OMR%20Diagram%20v3.png)

## EC2 Setup
The website is PHP based in order to handle the back end database operations, which means that a little extra set up is necessary to prepare an instance to process requests. 

### Nginx
Nginx is needed to process website files, including PHP, properly. The following is a good guide to installing Nginx to the EC2 instance for the first time. 

https://www.nginx.com/blog/setting-up-nginx/#open-web-page

### PHP Processing
This can be a little tricky as documentation online is not necessarily up to date however the following two resources will help you get PHP processing up and working, so that it is possible to serve php files: 

https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/getting-started_installation.html 

https://www.digitalocean.com/community/tutorials/how-to-install-linux-nginx-mysql-php-lemp-stack-in-ubuntu-16-04 

https://www.scalescale.com/tips/nginx/php5-fpm-sock-failed-13-permission-denied-error/ 

As reference this is how the my .conf looked like: 
![Click here to view the .conf file](https://raw.githubusercontent.com/pgarves/omr/master/nginx%20conf%20file.PNG)

### AWS API
In order to able to interface with all of the AWS services using PHP via the EC2 instance I have included a aws folder with the AWS SDK for PHP. In order to take advantage of the SDK, include the following in php files where you require communication with the AWS API:
```
require 'aws/aws-autoloader.php';
```

### IAM Roles
It is necessary to create a role with at least the ```AmazonDynamoDBFullAccess``` policy and attaching it to the EC2 Instance, giving it the required permissions.

### Credentials
Access to AWS services will only be granted with the right credentials for your IAM so for this application simply input your credentials into the enviornment variables for aws. 

https://docs.aws.amazon.com/cli/latest/userguide/cli-chap-getting-started.html

## ALB
The load balancer is pretty simple to set up as all that is needed is to register the created EC2 Instance with the website to the target for the ALB.

## DynamoDB
At least two tables are required to get started:
- a main OMR table
- a Manufacturer table

These tables are easily created in the AWS console. In the OMR, the main table is read and updated while the Manufacturer table is only read and separate process, that is not included would add and update the Manufacturer's tables.  

The main OMR table uses a primary key, "Manufacturer", and sorting key, "Tool ID", this will keep all info about a unique tool in a single item where it will be easy to see all compatible materials and verified paramters that the tool was used for. 

The Manufacturer's table keeps track of all specs for its tools using Tool ID as its primary key. As Manufacturers join the system, tables can be created using the AWS console to add to the KBM that the forms will use to verify data before submitting an update to the main database. 

## SNS
With the AWS SDK, everything needed for SNS is included!

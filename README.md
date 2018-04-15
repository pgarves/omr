# Open Source Machining Resource (OMR)
CMSC389L Final Project

## Overview
OMR, in it's current state, is a website based resource where users are able to search the proprietary database system of known, best practice machining processes and techniques for specific tools and materials. 

If the user is not satisfied with the current state of the database, the user has the ability to submit a form detailing the process that that is desired. The information inputted into the form will cross-reference a Knowledge Based Machining (KBM) database verifying that the desired process is within constraints defined by the tool manufacturer and the cutting material's properties. 

Upon completion of the verification process, a message will be sent to the user updating them of the results from the verification process, and whether or not the main OMR has been updated to support the user-defined process.

## Services Used
- Dynamo DB
- ALB (Load Balancer)
- EC2
- SNS

## Architecture

![Click here to view the architecture diagram](https://drive.google.com/open?id=1o3HmvvoiiDCskpRzj8b8JodnZXkxOzbj)

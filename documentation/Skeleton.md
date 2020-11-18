## App Info

Routes:

On the server side we need to register a callback that is executed once the request comes in. The callback itself will be a method on a controller and the controller will be connected to the URL with a route. The controller and route for the page are already set up in /appinfo/routes.php:

## Client side

Folders:
* \css - Style Sheets
* \img - Images (App Icon)
* \js - javascripts

## Server side
php scripts located at \lib and html stuff located at \templates

* \lib\Controller
* \templates\Controller

## How Calls are executed

1. Basic view

'''mermaid
sequenceDiagram
    participant Client Side
    participant Nextcloud Server
    Client Side->>Nextcloud Server: GET .../apps/timesheet
    Nextcloud Server->>Client Side: html,css,js        

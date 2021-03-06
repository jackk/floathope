Float Hope started as a weekend project after I realized that I was wasting a bunch of time cruising around the Internet on sites like Twitter, Facebook and ESPN. I thought it could be fun to track how much time gets wasted on these sites and transfer it into some good.

The idea morphed into an exercise to teach myself how to build Chrome Extensions with a server side component. The next step was to integrate in with a payment platform to facilitate a passive transaction each month to the charity of your choice. 

The problem was that I got distracted onto another project. Without continued drive, but a few months of work under its belt, I would like to open Float Hope to anyone who wants to take it to its next level of concept.

Things that exist and work:
- Chrome Extension to track when you visit a domain of your choosing across browser tabs
- MYSQL database structure (create-tables.sql)
- A reporting UI that shows how often you're visiting each of the sites you have chosen
- An automated email notification system

Things that aren't fully baked:
- A transaction platform for charity donations
- Backward looking reporting on previous months

Please note: 
- the prod-extension directory contains information on how to package and access the Chrome Extension
- make sure you include php.ini in your root directory. this is required for accessing the news feed in home.php

Happy hacking! Feel free to use as much or as little as you'd like.

If you have any questions - feel free to tweet directly at me: @jackk

******************************************
Product Structure:

The main object is defined as a user class within user.php.

All of the Chrome Extension is packaged within the prod-extension directory. Be sure to take a gander at the readme in there. background.html is where the meat of the passive listening takes place. It accesses to php controllers. The UI of the Chrome Extension is within popup.html -- using an iframe into popup-frame.php

All of the user interface is centered around home.php. Each page is suggested to contain includes.php to access all of the classes and methods.

This is a first draft project so you may find a ton of hacky implemented code.
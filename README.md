# CHEERS - Instructions on Running the Website

### Running the Node Servers

1. In the virtual host, open a command line tool and change directory to the oventoaster-finals directory.
2. Enter the following command: node server.js.
3. Open another command line tool window and change to the same oventoaster-finals directory.
4. Enter the following command: npm start
   > **_NOTE:_** Make sure that ports 8080 and 8081 are free as these will be utilized for the node servers.

### Running Wamp Server

1. To run PHP code and enable database access, open wampmanager and start wamp server.

### Enabling Camera Accessibility for Livestreaming

1. Enter the following url in your Google Chrome browser: chrome://flags/#unsafely-treat-insecure-origin-as-secure
2. Navigate to the "Insecure origins treated as secure" section and enter the URL to the content manager's livestream interface.
3. Enable the feature by clicking on the drop down next to the feature and selecting "Enabled".

> **_NOTE:_** These steps must be taken on any terminal accessing the website as a content manager.

### Files to Access Based on User Role

To access the content manager and viewer side interfaces, you must first enter the following URL: localhost/cheers

1. To access the content manager interface, click on loginCM.html.
2. To access the viewer interface, click on liveStreamViewer.html.

To access the admin side website, you must enter the following URL: localhost:5000

1. To access the admin side interface, click on login.html.

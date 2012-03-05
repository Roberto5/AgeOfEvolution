README
======

This directory should be used to place project specfic documentation including
but not limited to project notes, generated API/phpdoc documentation, or 
manual files generated or hand written.  Ideally, this directory would remain
in your development environment only and should not be deployed with your
application to it's final production location.


Setting Up Your VHOST
=====================

The following is a sample VHOST you might want to consider for your project.

<VirtualHost *:80>
   DocumentRoot "/var/www/evolutionZf/public"
   ServerName evolutionZf.local

   # This should be omitted in the production environment
   SetEnv APPLICATION_ENV development
    
   <Directory "/var/www/evolutionZf/public">
       Options Indexes MultiViews FollowSymLinks
       AllowOverride All
       Order allow,deny
       Allow from all
   </Directory>
    
</VirtualHost>


Public Key: 	6LeZUsISAAAAABXfDdeAHWcm3VuyRno0V4h3cGDr

Use this in the JavaScript code that is served to your users
Private Key: 	6LeZUsISAAAAAOZITe6cRWgSXKG6X1GfM-1t8_Xo 

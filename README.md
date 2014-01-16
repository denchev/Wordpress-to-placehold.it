Wordpress-to-placehold.it
=========================

Replaces all images found in an Wordpress export file with images from placehold.it (Great for Themeforest demo content)

This is a drop-in file. There is no need for installation!

### Usage:
* Upload the file replacer.php (via FTP) to your wordpress root directory (where wp-content, wp-includes and wp-admin are).
2. Access it via web browser (example.com/replacer.php)
3. Pick a Wordpress export file with the presented HTML form.
4. Click submit
5. Wait till the process is over (Some stats will appear)
6. Use the download link to download your new file.

That is it. The newly downloaded file is ready to be imported in new location. 

NOTE: This file will create a new folder in your uploads directory. Folder name is placehold.it. It is necessary for the import process to download images to new host.
NOTE: Try not to abuse the script as you might get banned from placehold.it

There is a ... unwanted behaviour. Each image from placehold.it has the image size engraved on it. So when Wordpress makes the import and creates new thumbnails, all thumbnails no matter their size have engraved the size of the original one. So you can have a 80x80 thumbnail but with 500x300 writen over it. Which sucks. One way to overcome this is to add this line of code in the .htaccess: 

RewriteRule uploads/(\d+)/(\d+)/placehold.it-(\d+)x(\d+)-(\d+)x(\d+).gif http://placehold.it/$5x$6\.gif [NC,L]

The problem with that is that this has to in the customer's .htaccess ... 

No idea how to overcome this issue.

PLEASE TEST THE NEWLY IMPORTED CONTENT WELL!!!




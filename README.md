# inatmycoportal
This command line script allows you to easily post MyCoPortal data to iNaturalist observations.

It takes an occurrences.csv file as input and automatically updates all the corresponding iNaturalist observations with the "MyCoPortal Link" and "Fungarium Accession Location" observation fields.

This program requires an iNaturalist application API key. If you don't already have one, you can register your application at https://www.inaturalist.org/oauth/applications.

To use this program, first install the files on a computer with PHP and curl. Then add your iNaturalist API key and account credentials to the `conf.sample.php` file and rename it `conf.php`. Next make sure that all of your iNaturalist observations have the "Accession Number" observation field set and that this number matches the Accession Number set in MyCoPortal. In MyCoPortal, go to the Collection Profile of the collection you want to update and click the "DwC-Archive File" link. This will download an archive that contains the occurrences.csv file. Put the occurrences.csv file in the same directory as the inatmycoportal.php script. Then run the script from the command line:
```
php ./inatmycoportal.php
```
If you want to test the script by only running it for a few occurence records (rather than for the entire file), you can pass the number of records as a command line argument:
```
php ./inatmycoportal.php 5
```
If you would like the script to keep a log, create a writable file in the same directory named `log.txt`. The script will then append a log of all actions to the log file.

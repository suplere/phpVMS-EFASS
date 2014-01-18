How install this solution for phpVMS? This is simple.

Here are your step:

1.Create table phpvms_pirepfields (if you use another prefix use own). You may use Efassflightdata.sql from zip file.

2.Create additional PIREPs field EFASS_UNIQUEFLIGHTID is must be name. You may use InsertPirepsField.sql from zip file.

3.Extract core folder to core folder in phpVMM on the server. PIREPData.class.php will be overwrite, you may backup your original file.

4.In http://froom.de/efass/?a=accountstatus to the field ACARS-Ping URL: inster this: http://yourserver/phpvms/action.php/acars/efass/efass.

5.PIREP is not automatic, but after taxi to the gate/parking you must fire PIREP from EFASS menu.
Thats all.

What extension does do:
- track your flight by EFASS Acars protocol
- save all ACARS position and other data to the table phpvms_efassflightdata. Its may be join to the PIREPS table by idefassflight and EFASS_UNIQUEFLIGHTID additional PIREPs field
- save PIREP by click to the PIREP in EFASS (only when you in TAXI IN or DEBOARDING flight stage) 

I recomended update your navdata table in phpVMS. In ZIP is \navdata\ and this is parser from FSBuild 2.x FMS data from Navigraph.
Here is tutorial:

1. Download FSBuild 2.x FMS data from Navigraph.

2. Extract awys.txt, ints.txt and navs.txt to the folder \navdata\fsbuild\.

3. Edit db.php and set correct username, password and dbname in this file.

4. Upload whole folder navdata to your phpVMS server to the root site.

5. Connect by SSH to the server, navigate to the navdata folder, and then run "php -f fsbuildparse.php".

6. Thats all.

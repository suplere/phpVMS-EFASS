#phpVMS-EFASS

**This extension will make it possible to track and log flights in phpVMS through EFASS, via the ACARS protocol.**

ACARS position and other data will be saved to the table ``phpvms_efassflightdata``. It may be joined to the ``phpvms_pireps`` table by ``idefassflight``, and an additional PIREP field, ``EFASS_UNIQUEFLIGHTID``.

##Credits


Copyright (c) 2014, [Evžen Šupler](https://github.com/suplere/).

This extension is licensed under the [Creative Commons Attribution Non-commercial Share Alike (by-nc-sa)](http://creativecommons.org/licenses/by-nc-sa/3.0/) license.

Includes NavData parser code from [Nabeel Shahzad](https://github.com/nshahzad/phpvms_navdata).

With contributions from [Pierre Lavaux](https://github.com/PierreLvx) (pierre@zonexecutive.com).

##Server Setup


1. Import the included ``EfassFlightData.sql`` to have the proper table structure created for you. If you are not using the default ``phpvms_`` prefix for your tables, edit accordingly ;

2. Import the included ``InsertPirepsField.sql``, to have the additional ``EFASS_UNIQUEFLIGHTID`` field added to your ``phpvms_pirepfields`` table ;

3. Extract core folder to core folder in phpVMS on the server. PIREPData.class.php will be overwrite, you may backup your original file.

##Client Setup

* In ``http://froom.de/efass/?a=accountstatus`` to the field ACARS-Ping URL: insert this ``http://your-phpvms-server/action.php/acars/efass/efass``.

* PIREP forwarding is not automatic. After taxi to the gate/parking, you must manually send your PIREP from EFASS. Save PIREP by clicking on the PIREP in EFASS (only during TAXI IN or the DEBOARDING flight stage).

##NavData

We recommend that you use up-to-date navdata with phpVMS. A parser is included within this download, that will convert FSBuild 2.x FMS data from Navigraph.

Here's how to use it:

1. Download FSBuild 2.x FMS data from Navigraph ;

2. Extract ``awys.txt``, ``ints.txt`` and ``navs.txt`` to the folder ``navdata/fsbuild/`` ;

3. Edit ``db.php`` with your database credentials ;

4. Upload the whole navdata folder and its contents to your server, at the root of your phpVMS installation ;

5. Connect by SSH to the server, navigate to the navdata folder, and then run ``php -f fsbuildparse.php``.

6. That's it!
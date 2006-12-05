@echo off
xgettext -kT_ngettext:1,2 -kT_ -L PHP -o ..\..\..\locales\messages.po ..\..\..\*.php ..\..\..\services\*.php ..\..\..\templates\*.php
if /i "%1" == "-p" goto stats
if exist "..\..\..\locales\%1.po" goto merge
echo "Usage: $0 [-p|<basename>]"
goto end

:stats
msgfmt --statistics ..\..\..\locales\messages.po
goto end

:merge
msgmerge -o ..\..\..\locales\tmp%1.po ..\..\..\locales\%1.po ..\..\..\locales\messages.po
if exist "..\..\..\locales\%1.po" rename ..\..\..\locales\%1.po %1.po.bak
rename ..\..\..\locales\tmp%1.po %1.po
if exist "..\..\..\locales\%1.po.bak" del ..\..\..\locales\%1.po.bak
msgfmt --statistics "..\..\..\locales\%1.po"

:end
echo Finished
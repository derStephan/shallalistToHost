# shallalistToHosts
Generate a hosts file from URL-blacklist of http://www.shallalist.de/

Goal is to redirect domains of selected categories to 127.0.0.1 thus these domains are blocked on DNS basis

language: PHP
needs cURL and PHAR

Installation: simply put php-file to your web-root and run it in browser.

When run first, the current full blacklist is downloaded: http://www.shallalist.de/Downloads/shallalist.tar.gz
The list is decompressed and saved to a subfolder.

After initial download the list will be updated if last update is older than 1 week. 

Based on downloaded list you can select the categories to be included in the hosts file. 

After pressing download, a hosts file is sent to the user.

hosts file looks like this:

```
#######################################################################################
#This file is derived from URL blacklist maintained by http://www.shallalist.de/
#using an automatic conversion tool: https://github.com/derStephan/shallalistToHosts
#Note: The author of this tool is in no way connected to shalla.
#
#creation date: 2015-06-02T13:09:47+02:00
#last list update: 2015-06-02T11:17:01+02:00
#For automated download use: wget "http://10.0.0.3/develop/shalla/shalla2hosts.php?download" -O hosts
#######################################################################################
#category: list/BL/adv/domains 
127.0.0.1 000freexxx.com
127.0.0.1 004.frnl.de
127.0.0.1 clipsguide.com
...
```

hosts file is tested to be working in windows. just drop it into c:\Windows\System32\drivers\etc\

Please note: resuling file may very well exceed 40 MB with 1.500.000+ lines.

#usage with wget

You can do one of the following:

1. change the very first variable in php file to meet your requirements. Now you can download your list with ```wget "<URLofPhpFile>?download" -O hosts```
2. select your categories from the list and download the file manually. Open it in an editor. In the second line, there is the complete wget command for the selected categories. Just copy and paste it into terminal.

#screenshot of category selection
![Screenshot](https://cloud.githubusercontent.com/assets/7764931/7933941/5688de0c-0923-11e5-8233-3b3bfb3c5e66.png)

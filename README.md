# shallalistToHost
Generate a hosts file from URL-blacklist of shalla.de

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
#last list update: 2015-06-02T09:37:14+02:00
#domainlist: list/BL/adv/domains 
127.0.0.1 000freexxx.com
127.0.0.1 004.frnl.de
127.0.0.1 clipsguide.com
...
```

hosts file is tested to be working in windows. just drop it into c:\Windows\System32\drivers\etc\

Please note: resuling file may very well exceed 30 MB with 900.000+ lines.

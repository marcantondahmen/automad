FROM litespeedtech/openlitespeed:1.8.4-lsphp84

ARG UID=1000

RUN userdel ubuntu
RUN usermod -u ${UID} lsadm

COPY httpd_config.conf /usr/local/lsws/conf/httpd_config.conf
COPY vhconf.conf /usr/local/lsws/conf/vhosts/Example/vhconf.conf


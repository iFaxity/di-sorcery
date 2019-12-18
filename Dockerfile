FROM anax/dev:cli

COPY .php*.xml composer.json Makefile ./

USER root
RUN printf '%s\n' \
  'xdebug.remote_autostart=1' \
  'xdebug.remote_connect_back=0' \
  'xdebug.remote_host=host.docker.internal' \
  'xdebug.remote_port=9005' >> /usr/local/etc/php/conf.d/xdebug.ini

RUN make install

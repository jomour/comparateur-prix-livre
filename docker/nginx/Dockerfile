# ARG NGINX_VERSION=1.21

FROM nginx:latest

ENV NGINX_ROOT /var/www/public
ENV NGINX_FPM_HOST localhost
ENV NGINX_FPM_PORT 9000

RUN rm -f /etc/nginx/conf.d/default.conf
RUN rm -f /etc/nginx/nginx.conf

COPY ./docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY ./docker/nginx/fpm-template.conf /etc/nginx/fpm.tmpl
COPY ./docker/nginx/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

ENTRYPOINT ["entrypoint.sh"]

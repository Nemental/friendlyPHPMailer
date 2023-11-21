FROM nginx:1.25-alpine

COPY ./docker/nginx/conf/nginx.conf /etc/nginx/conf.d/default.conf

COPY --chown=nginx:nginx ./src /var/www/html

WORKDIR /var/www/html


FROM nginx:alpine

COPY ./conf/nginx.conf /etc/nginx/conf.d/default.conf

COPY --chown=nginx:nginx ./src /var/www/html

WORKDIR /var/www/html


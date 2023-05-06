FROM nginx:latest

COPY ./docker/nginx/nginx.conf /etc/nginx/nginx.conf

RUN mkdir -p /var/www/html/public

WORKDIR /var/www/html

EXPOSE 80

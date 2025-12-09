FROM debian AS builder

RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    nodejs \
    npm \
    && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

WORKDIR /var/www/html

COPY . /var/www/html

RUN npm install && npm run build

FROM nginx:alpine

COPY ./docker/production/nginx/nginx.conf /etc/nginx/nginx.conf
COPY ./docker/production/nginx/conf.d /etc/nginx/conf.d

COPY --from=builder /var/www/html/public /var/www/html/public

WORKDIR /var/www/html/public

EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]

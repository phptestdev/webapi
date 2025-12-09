FROM debian AS builder

RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    nodejs \
    npm \
    && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

WORKDIR /var/www

COPY . /var/www

RUN npm install && npm run build

FROM nginx:alpine

COPY --from=builder /var/www /var/www

WORKDIR /var/www

EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]

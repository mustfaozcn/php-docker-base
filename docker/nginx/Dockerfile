FROM nginx:1.25-alpine

# Copy the public directory
COPY ./public/ /app/public/

# Copy the nginx config file
COPY ./docker/nginx/nginx.conf /etc/nginx/conf.d/default.conf

composer install
symfony server:start

sudo certbot certonly --standalone --email support@cashrewards.fr --agree-tos --no-eff-email -d cashrewards.fr -d www.cashrewards.fr

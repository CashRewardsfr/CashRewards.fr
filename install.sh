sudo cp -r /var/www/cashrewards.fr/public/media/* /home/ubuntu/tempdata/
cd CashRewards.fr
git checkout .
git pull
composer install
sudo rm -rf /var/www/cashrewards.fr/*
sudo cp -r /home/ubuntu/CashRewards.fr/* /var/www/cashrewards.fr/
sudo chown -R www-data:www-data /var/www/cashrewards.fr/var/cache/
sudo chown -R www-data:www-data /var/www/cashrewards.fr/var/log/
sudo mkdir /var/www/cashrewards.fr/public/media/
sudo cp -r /home/ubuntu/tempdata/* /var/www/cashrewards.fr/public/media/
sudo rm -rf /home/ubuntu/tempdata/*
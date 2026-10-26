server {
    listen 80;
    server_name localhost;

    root /var/www/panel/public;
    index index.php index.html;

    # Laravel entry point
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP handler
    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass admin:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
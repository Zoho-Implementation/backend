# zoho-integration-backend

## Close repository
```
git clone https://github.com/Zoho-Implementation/backend.git
```

### Go to the folder with the project
```
cd backend
```

### Сopy .env.example to .env and complete them with variables
```
cp .env.example .env
```

### Execute the command
```
docker-compose up -d --build
```

### Enter the php container
```
docker exec -it php-app bas
```

### Install the dependencies
```
composer install
```

### Start migrations
```
php artisan migrate
```

### Done

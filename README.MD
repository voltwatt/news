### service containers:
```bash
docker compose -f docker-compose.services.yml -p news-services up -d
```

### php and caddy containers:
```bash
docker compose up -d --build
```

see http://localhost:8955/api

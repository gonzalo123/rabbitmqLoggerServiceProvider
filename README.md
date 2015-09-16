Silex logging with RabbitMQ
========

* Start RabbitMQ  server
```
rabbitmq-server
```

* start Silex server
```
php -S 0.0.0.0:8080 -t www
```

* start worker
```
php worker/worker.php
```
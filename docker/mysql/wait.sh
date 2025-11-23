#!/bin/bash
# wait-for-mysql.sh

timeout="${1:-60}"  # Default timeout of 60 seconds

echo "Waiting for MySQL to be ready..."

# Wait for MySQL to be available with a timeout
count=0
until php artisan db:check-connection 2>/dev/null; do
  if [ "$count" -ge "$timeout" ]; then
    echo "Timeout waiting for MySQL after $timeout seconds"
    exit 1
  fi
  echo "MySQL not ready, retrying..."
  sleep 1
  ((count++))
done

echo "MySQL is available."
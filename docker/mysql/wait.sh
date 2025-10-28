#!/bin/bash
# wait-for-mysql.sh

host="$1"
port="$2"
shift 2
cmd="$@"

until nc -z "$host" "$port"; do
  echo "Waiting for MySQL at $host:$port..."
  sleep 1
done

echo "MySQL at $host:$port is available. Executing command..."
exec $cmd
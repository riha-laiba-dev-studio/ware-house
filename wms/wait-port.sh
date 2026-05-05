#!/bin/bash
# Wait for port 3000 to be available (started by "WMS - Warehouse Management" workflow)
echo "WMS Artifact: waiting for port 3000..."
while ! curl -s -o /dev/null http://localhost:3000 2>/dev/null; do
  sleep 2
done
echo "WMS port 3000 is up. Staying alive..."
# Keep process alive to maintain the workflow
tail -f /home/runner/workspace/wms/storage/logs/laravel.log 2>/dev/null || while true; do sleep 60; done

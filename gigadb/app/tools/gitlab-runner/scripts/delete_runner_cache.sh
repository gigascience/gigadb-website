#!/bin/bash

LOG_FILE="/var/log/delete_runner_cache.log"
CACHE_DIR="/var/runner/cache/gigascience"

# Function to log with timestamp
log_message() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$LOG_FILE"
}

# Start cleanup
log_message "=== Starting GitLab Runner Cache Cleanup ==="

# Check if cache directory exists
if [[ ! -d "$CACHE_DIR" ]]; then
    log_message "ERROR: Cache directory $CACHE_DIR does not exist"
    exit 1
fi

# Log disk usage before cleanup
log_message "Disk usage before cleanup:"
du -h "$CACHE_DIR" 2>&1 | tee -a "$LOG_FILE"

# Count files/directories to be deleted
ITEM_COUNT=$(find "$CACHE_DIR" -mindepth 1 | wc -l)
log_message "Items to be deleted: $ITEM_COUNT"

# Perform cleanup
log_message "Starting deletion process..."
if rm -rf "$CACHE_DIR"/* 2>&1 | tee -a "$LOG_FILE"; then
    log_message "Cache cleanup completed successfully"
else
    log_message "ERROR: Cache cleanup failed with exit code $?"
    exit 1
fi

# Log disk usage after cleanup
log_message "Disk usage after cleanup:"
du -h "$CACHE_DIR" 2>&1 | tee -a "$LOG_FILE"

# Log system disk space
log_message "Current disk space:"
df -h / 2>&1 | tee -a "$LOG_FILE"

log_message "=== Cache Cleanup Process Finished ==="
echo "" >> "$LOG_FILE"

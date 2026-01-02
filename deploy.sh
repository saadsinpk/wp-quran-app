#!/bin/bash

# Quran Simple Plugin Deployment Script
# Run this script to deploy to your server

SERVER="rehmani@66.29.132.60"
REMOTE_PATH="/home/rehmani/public_html/wp-content/plugins/quran-simple"
LOCAL_PATH="/Users/sidtechno/Documents/GitHub/wp-quran-app"

echo "========================================"
echo "  Quran Simple Plugin Deployment"
echo "========================================"
echo ""
echo "Deploying to: $SERVER:$REMOTE_PATH"
echo ""

# Deploy using rsync (preserves permissions, only transfers changed files)
rsync -avz --progress \
    --exclude '.git' \
    --exclude '.DS_Store' \
    --exclude 'deploy.sh' \
    --exclude '*.md' \
    "$LOCAL_PATH/" "$SERVER:$REMOTE_PATH/"

if [ $? -eq 0 ]; then
    echo ""
    echo "========================================"
    echo "  Deployment successful!"
    echo "========================================"
else
    echo ""
    echo "========================================"
    echo "  Deployment failed!"
    echo "  Try using SCP instead:"
    echo "  scp -r $LOCAL_PATH/* $SERVER:$REMOTE_PATH/"
    echo "========================================"
fi

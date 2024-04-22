FOLDER=/var/www/html/storage/app

if [ ! -d "$FOLDER" ]; then
    echo "$FOLDER is not a directory, copying skeleton content to storage"
    cp -r /var/www/html/storage-skeleton/. /var/www/html/storage
    echo "deleting storage skeleton..."
    rm -rf /var/www/html/storage-skeleton
fi

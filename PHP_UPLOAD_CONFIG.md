# PHP Upload Configuration

If users are experiencing issues uploading files close to or over 10MB, ensure your PHP configuration allows it:

## PHP.ini Settings Required

```ini
; Maximum size of POST data in bytes (should be >= max_file_size)
post_max_size = 50M

; Maximum allowed size of an uploaded file
upload_max_filesize = 50M

; Maximum execution time in seconds
max_execution_time = 300

; Maximum input time in seconds
max_input_time = 300
```

## Laravel .env Configuration

```env
# File upload configuration
FILE_UPLOAD_MAX_SIZE_MB=10         # Max individual file size
FILE_UPLOAD_MAX_FILES=10           # Max files per collection
FILE_UPLOAD_DISK=local             # Storage disk
FILE_UPLOAD_PATH=uploads           # Storage path
FILE_UPLOAD_VIRUS_SCAN=false       # Enable virus scanning (ClamAV)
```

## Nginx Configuration (if using Nginx)

Add to your server block:

```nginx
# Allow up to 50MB uploads
client_max_body_size 50M;
```

## Apache Configuration (if using Apache)

Add to .htaccess or Apache config:

```apache
# Allow up to 50MB uploads
LimitRequestBody 52428800
```

## Troubleshooting

1. **413 Payload Too Large**: Increase `client_max_body_size` in Nginx or `LimitRequestBody` in Apache
2. **File upload silently fails**: Check `post_max_size` and `upload_max_filesize` in php.ini
3. **Timeout errors**: Increase `max_execution_time` and `max_input_time`
4. **Storage full**: Check disk space with `df -h`

## Server Commands

Check current PHP configuration:
```bash
php -i | grep -E 'upload_max_filesize|post_max_size|max_execution_time'
```

Check disk space:
```bash
df -h
du -sh storage/app/*
```

# Fix WSL Linux PHP Upload Limits

## Quick Method (Using Script)

1. **Open WSL terminal** (or run `wsl` in PowerShell)

2. **Navigate to the project:**
   ```bash
   cd /mnt/d/Programming/GitHub/yoprint-laravel-coding-project-iv/web-app
   ```

3. **Make the script executable and run it:**
   ```bash
   chmod +x fix-wsl-php.sh
   bash fix-wsl-php.sh
   ```

4. **Restart php artisan serve**

## Manual Method

1. **Open WSL terminal**

2. **Edit the php.ini file:**
   ```bash
   sudo nano /etc/php/8.3/cli/php.ini
   ```
   
   Or use your preferred editor:
   ```bash
   sudo vim /etc/php/8.3/cli/php.ini
   ```

3. **Find these lines** (use Ctrl+W to search):
   - `post_max_size`
   - `upload_max_filesize`
   - `max_execution_time`
   - `max_input_time`
   - `memory_limit`

4. **Change them to:**
   ```ini
   post_max_size = 1024M
   upload_max_filesize = 1024M
   max_execution_time = 300
   max_input_time = 300
   memory_limit = 512M
   ```

5. **Save the file:**
   - In nano: Ctrl+O, Enter, Ctrl+X
   - In vim: `:wq`

6. **Restart php artisan serve**

## Verify the Fix

Visit `http://localhost:8000/php-info` and check:
- `php_ini_loaded_file` should be `/etc/php/8.3/cli/php.ini`
- `post_max_size` should be `1024M`
- `upload_max_filesize` should be `1024M`

## Alternative: Check for Multiple PHP Versions

If you have multiple PHP versions, you might need to update different ini files:

```bash
# List all PHP ini files
ls -la /etc/php/*/cli/php.ini

# Update all of them
sudo nano /etc/php/8.3/cli/php.ini
sudo nano /etc/php/8.3/fpm/php.ini  # If using PHP-FPM
```

## Note

- The script creates a backup before making changes
- You need sudo/root access to edit system php.ini files
- After editing, restart `php artisan serve` for changes to take effect


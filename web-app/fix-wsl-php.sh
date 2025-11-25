#!/bin/bash
# Script to fix PHP upload limits in WSL Linux PHP
# Run this in WSL: bash fix-wsl-php.sh

PHP_INI="/etc/php/8.3/cli/php.ini"

echo "=== WSL PHP Upload Limits Fixer ==="
echo ""

if [ ! -f "$PHP_INI" ]; then
    echo "ERROR: php.ini not found at: $PHP_INI"
    echo "Please check your PHP version and update the path in this script"
    exit 1
fi

echo "Found php.ini at: $PHP_INI"
echo ""

# Create backup
BACKUP="${PHP_INI}.backup.$(date +%Y%m%d-%H%M%S)"
sudo cp "$PHP_INI" "$BACKUP"
echo "Created backup: $BACKUP"
echo ""

# Settings to update
declare -A SETTINGS=(
    ["post_max_size"]="1024M"
    ["upload_max_filesize"]="1024M"
    ["max_execution_time"]="300"
    ["max_input_time"]="300"
    ["memory_limit"]="512M"
)

# Update each setting
for SETTING in "${!SETTINGS[@]}"; do
    VALUE="${SETTINGS[$SETTING]}"
    
    # Check if setting exists
    if grep -q "^${SETTING}" "$PHP_INI"; then
        # Update existing setting (uncomment if commented, update value)
        sudo sed -i "s/^;*${SETTING}.*/${SETTING} = ${VALUE}/" "$PHP_INI"
        echo "✓ Updated $SETTING = $VALUE"
    elif grep -q "^;${SETTING}" "$PHP_INI"; then
        # Uncomment and set value
        sudo sed -i "s/^;${SETTING}.*/${SETTING} = ${VALUE}/" "$PHP_INI"
        echo "✓ Uncommented and set $SETTING = $VALUE"
    else
        # Add new setting (add after a similar section or at end)
        echo "" | sudo tee -a "$PHP_INI" > /dev/null
        echo "${SETTING} = ${VALUE}" | sudo tee -a "$PHP_INI" > /dev/null
        echo "✓ Added $SETTING = $VALUE"
    fi
done

echo ""
echo "=== Verification ==="
echo "Current settings:"
for SETTING in "${!SETTINGS[@]}"; do
    VALUE=$(grep "^${SETTING}" "$PHP_INI" | head -1 | awk -F'=' '{print $2}' | xargs)
    echo "  $SETTING = $VALUE"
done

echo ""
echo "=== Next Steps ==="
echo "1. Restart 'php artisan serve' (stop with Ctrl+C, then start again)"
echo "2. Visit http://localhost:8000/php-info to verify"
echo "3. The post_max_size should show 1024M"
echo ""


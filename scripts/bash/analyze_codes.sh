#!/bin/bash

# Initialize counters
php_files=0
php_lines=0

scss_files=0
scss_lines=0

css_files=0
css_lines=0

js_files=0
js_lines=0

# Count files and lines
while IFS= read -r -d '' file; do
    case "$file" in
        *.php)
            php_files=$((php_files + 1))
            php_lines=$((php_lines + $(wc -l < "$file")))
            ;;
        *.scss)
            scss_files=$((scss_files + 1))
            scss_lines=$((scss_lines + $(wc -l < "$file")))
            ;;
        *.css)
            css_files=$((css_files + 1))
            css_lines=$((css_lines + $(wc -l < "$file")))
            ;;
        *.js)
            js_files=$((js_files + 1))
            js_lines=$((js_lines + $(wc -l < "$file")))
            ;;
    esac
done < <(find . -type f \( -name "*.php" -o -name "*.scss" -o -name "*.css" -o -name "*.js" \) ! -path "./vendor/*" ! -path "./node_modules/*" -print0)

# Print results
echo "PHP Files: $php_files"
echo "PHP Lines: $php_lines"

echo "SCSS Files: $scss_files"
echo "SCSS Lines: $scss_lines"

echo "CSS Files: $css_files"
echo "CSS Lines: $css_lines"

echo "JS Files: $js_files"
echo "JS Lines: $js_lines"

#!/bin/bash

# Ladwire Module Test Runner
# This script runs tests for the Ladwire module package

set -e

echo "🧪 Running Ladwire Module Tests..."

# Change to the tests directory
cd "$(dirname "$0")"

# Check if vendor directory exists
if [ ! -d "vendor" ]; then
    echo "📦 Installing dependencies..."
    composer install --prefer-dist --no-progress --no-suggest --optimize-autoloader --no-scripts
fi

# Create storage directory for logs if it doesn't exist
mkdir -p storage/logs

# Run PHPUnit with proper configuration
echo "🧪 Running PHPUnit..."
vendor/bin/phpunit --configuration=phpunit.xml --coverage-text --coverage-html=storage/logs/coverage.html --coverage-clover=storage/logs/clover.xml

# Check test results
if [ $? -eq 0 ]; then
    echo "✅ All tests passed!"
    echo "📊 Coverage report generated: storage/logs/coverage.html"
else
    echo "❌ Some tests failed!"
    exit 1
fi

#!/bin/bash

echo "Starting Stock Management System with Docker..."

# Stop any existing containers
echo "Stopping existing containers..."
docker-compose down

# Remove any existing volumes to start fresh (optional)
# docker volume rm martes_mysql_data

# Build and start the services
echo "Building and starting services..."
docker-compose up --build -d

# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
sleep 10

# Check if containers are running
echo "Checking container status..."
docker-compose ps

# Show logs
echo "Showing recent logs..."
docker-compose logs --tail=20

echo "Application should be available at: http://localhost:8080"
echo "MySQL is available at: localhost:3306"
echo ""
echo "To view logs: docker-compose logs -f"
echo "To stop: docker-compose down"

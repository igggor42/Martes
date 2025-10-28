#!/bin/bash

echo "Starting Stock Management System with Docker..."

echo "Stopping existing containers..."
docker-compose down

echo "Building and starting services..."
docker-compose up --build -d

echo "Waiting for MySQL to be ready..."
sleep 10

echo "Checking container status..."
docker-compose ps

echo "Showing recent logs..."
docker-compose logs --tail=20

echo "Application should be available at: http://localhost:8080"
echo "MySQL is available at: localhost:3306"
echo ""
echo "To view logs: docker-compose logs -f"
echo "To stop: docker-compose down"

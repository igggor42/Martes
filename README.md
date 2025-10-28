# Stock Management System

A PHP-based stock management application with AJAX functionality, designed to work with Docker and Render's free tier.

**Live Demo**: https://martes-1.onrender.com

## Features

- **Stock Movement Tracking**: View and filter stock movements
- **Real-time Filtering**: Filter by article code, description, lot number, date, movement type, and unit of measure
- **Sortable Columns**: Click column headers to sort data
- **AJAX Interface**: Smooth, responsive user experience without page reloads
- **MySQL Database**: Persistent data storage with proper relationships

## Quick Start

### Local Development with Docker

1. **Start the application**:
   ```bash
   ./start.sh
   ```

2. **Access the application**:
   - Main application: http://localhost:8080
   - Stock management: http://localhost:8080/AJAX/ListaOrdenarFiltrar/
   - MySQL database: localhost:3307

3. **View logs**:
   ```bash
   docker-compose logs -f
   ```

4. **Stop the application**:
   ```bash
   docker-compose down
   ```

### Manual Docker Commands

```bash
# Build and start services
docker-compose up --build -d

# View logs
docker-compose logs -f

# Stop services
docker-compose down

# Remove volumes (fresh start)
docker-compose down -v
```

## Database Schema

The application uses two main tables:

### TipoDeMov (Movement Types)
- `IdMov`: Primary key
- `Codigo`: Movement code (ENT, SAL, etc.)
- `Descripcion`: Movement description

### MovimientosDeStock (Stock Movements)
- `IdMov`: Foreign key to TipoDeMov
- `CodArticulo`: Article code
- `Descripcion`: Article description
- `NroDeLote`: Lot number
- `FechaMovimiento`: Movement date
- `UnidadMedida`: Unit of measure
- `Cantidad`: Quantity
- `FotoArticulo`: Article photo path

## Deployment on Render

The application is configured for Render's free tier with:

- **Web Service**: PHP/Apache application
- **Database Service**: MySQL 8.0 with persistent storage
- **Environment Variables**: Automatically configured for database connection

### Render Configuration

The `render.yaml` file contains the complete configuration for both services:

- **stock-management-app**: Web service running the PHP application
- **mysql-stock-db**: Database service with MySQL and initialization script

## Troubleshooting

### Common Issues

1. **"Error en la solicitud al servidor"**:
   - Check if MySQL container is running: `docker-compose ps`
   - View logs: `docker-compose logs mysql`
   - Ensure database is initialized properly

2. **Database Connection Issues**:
   - Verify environment variables in `conexionBase.php`
   - Check MySQL container logs for connection errors
   - Ensure database schema is properly initialized

3. **AJAX Requests Failing**:
   - Check browser console for JavaScript errors
   - Verify PHP files are accessible
   - Check Apache error logs: `docker-compose logs php-app`

### Debug Commands

```bash
# Check container status
docker-compose ps

# View all logs
docker-compose logs

# View specific service logs
docker-compose logs mysql
docker-compose logs php-app

# Access MySQL container
docker-compose exec mysql mysql -u stock_user -p stock_db

# Access PHP container
docker-compose exec php-app bash
```

## File Structure

```
Martes/
├── AJAX/ListaOrdenarFiltrar/
│   ├── index.php              # Main application interface
│   ├── conexionBase.php       # Database connection configuration
│   ├── salidaJsonMovimientos.php  # AJAX endpoint for movements
│   ├── salidaJsonTiposMov.php     # AJAX endpoint for movement types
│   ├── init-mysql.sql         # MySQL initialization script
│   └── Tablas.sql            # Original SQL file (MySQL syntax)
├── Dockerfile                 # PHP/Apache container configuration
├── docker-compose.yml         # Multi-service Docker configuration
├── render.yaml               # Render deployment configuration
└── start.sh                  # Quick start script
```

## Environment Variables

- `DB_HOST`: Database host (default: mysql)
- `DB_NAME`: Database name (default: stock_db)
- `DB_USER`: Database user (default: stock_user)
- `DB_PASSWORD`: Database password (default: stock_password)
- `APACHE_DOCUMENT_ROOT`: Apache document root (default: /var/www/html)

## Development Notes

- The application uses MySQL 8.0 with native password authentication
- All database operations use PDO with prepared statements
- AJAX requests use Fetch API with proper error handling
- The interface is responsive and works on mobile devices
- Data is automatically initialized with sample records on first run


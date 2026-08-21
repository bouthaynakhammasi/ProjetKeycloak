import { execSync } from 'child_process';

export class DatabaseHelper {
  static setupTestDatabase() {
    try {
      // Create test database if it doesn't exist
      execSync('php artisan db:create --database=gestion_client_test', {
        cwd: process.cwd(),
        stdio: 'inherit'
      });
    } catch (error) {
      // Database might already exist, continue
      console.log('Database setup completed or already exists');
    }
  }

  static runMigrations() {
    execSync('php artisan migrate --database=gestion_client_test --force', {
      cwd: process.cwd(),
      stdio: 'inherit'
    });
  }

  static seedDatabase() {
    execSync('php artisan db:seed --database=gestion_client_test --force', {
      cwd: process.cwd(),
      stdio: 'inherit'
    });
  }

  static resetDatabase() {
    execSync('php artisan migrate:reset --database=gestion_client_test --force', {
      cwd: process.cwd(),
      stdio: 'inherit'
    });
  }

  static freshDatabase() {
    execSync('php artisan migrate:fresh --database=gestion_client_test --force', {
      cwd: process.cwd(),
      stdio: 'inherit'
    });
  }
}

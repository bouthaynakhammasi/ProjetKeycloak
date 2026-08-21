# E2E Tests - Documentation

## Overview

This document describes the End-to-End (E2E) testing implementation for the Projet-SSO application using Playwright. The E2E tests cover both the `gestion-client` (HR management) and `mailbox` (email) applications.

## Prerequisites

- Node.js (v18 or higher)
- PHP (v8.1 or higher)
- Composer
- PostgreSQL
- Keycloak server running
- Laravel applications configured

## Installation

### 1. Install Dependencies

#### For gestion-client:
```bash
cd gestion-client
npm install
npx playwright install
```

#### For mailbox:
```bash
cd mailbox
npm install
npx playwright install
```

### 2. Configure Test Environment

#### gestion-client:
- Copy `.env.testing` to `.env` or configure your test environment
- Set up test database: `gestion_client_test`
- Configure Keycloak test realm

#### mailbox:
- Configure `.env` for testing
- Set up test database
- Configure Keycloak test realm

### 3. Database Setup

```bash
# For gestion-client
php artisan migrate --database=gestion_client_test --force
php artisan db:seed --database=gestion_client_test --force

# For mailbox
php artisan migrate --force
php artisan db:seed --force
```

## Test Structure

### gestion-client E2E Tests

```
gestion-client/e2e/
├── auth/
│   └── sso-auth.spec.ts          # SSO authentication flow tests
├── employees/
│   └── employee-management.spec.ts # Employee management tests
├── salaries/
│   └── salary-management.spec.ts  # Salary management tests
├── absences/
│   └── absence-management.spec.ts  # Absence management tests
├── websocket/
│   └── websocket-notifications.spec.ts # WebSocket notification tests
├── fixtures/
│   └── test-data.ts               # Test data fixtures
└── helpers/
    ├── auth-helper.ts            # Authentication helper
    └── db-helper.ts              # Database helper
```

### mailbox E2E Tests

```
mailbox/e2e/
└── mailbox/
    └── mailbox-app.spec.ts       # Mailbox application tests
```

## Running Tests

### Run All Tests

#### gestion-client:
```bash
cd gestion-client
npx playwright test
```

#### mailbox:
```bash
cd mailbox
npx playwright test
```

### Run Specific Test Files

```bash
# Run only authentication tests
npx playwright test e2e/auth/sso-auth.spec.ts

# Run only employee management tests
npx playwright test e2e/employees/employee-management.spec.ts

# Run only salary management tests
npx playwright test e2e/salaries/salary-management.spec.ts
```

### Run Tests in Specific Browser

```bash
# Run in Chrome only
npx playwright test --project=chromium

# Run in Firefox only
npx playwright test --project=firefox

# Run in Safari only
npx playwright test --project=webkit
```

### Run Tests in Headed Mode

```bash
npx playwright test --headed
```

### Run Tests in Debug Mode

```bash
npx playwright test --debug
```

### Run Tests with UI

```bash
npx playwright test --ui
```

## Test Coverage

### SSO Authentication Flow Tests

- ✅ Redirect to Keycloak login page
- ✅ Login with valid admin credentials
- ✅ Login with valid employee credentials
- ✅ Show error with invalid credentials
- ✅ Handle pending user account
- ✅ Logout successfully
- ✅ Maintain session across navigation
- ✅ Handle session expiration
- ✅ Sync user data from Keycloak
- ✅ Redirect to correct dashboard based on role

### Employee Management Tests (Admin)

- ✅ Display employee list
- ✅ Create new employee
- ✅ Edit existing employee
- ✅ Delete employee
- ✅ View employee details
- ✅ Filter employees by department
- ✅ Search employees by name
- ✅ Validate employee form
- ✅ Handle duplicate email error
- ✅ Export employee list

### Employee Management Tests (Employee)

- ✅ Deny access to employee list
- ✅ Deny access to create employee
- ✅ Deny access to edit employee
- ✅ Deny access to delete employee

### Salary Management Tests (Admin)

- ✅ Display salary list
- ✅ Create salary with automatic net calculation
- ✅ Edit salary with recalculation
- ✅ Delete salary
- ✅ View salary details
- ✅ Mark salary as paid
- ✅ Generate salary PDF
- ✅ Filter salaries by employee
- ✅ Filter salaries by month and year
- ✅ Filter salaries by payment status
- ✅ Prevent duplicate salary for same period
- ✅ Access salary dashboard
- ✅ Validate salary form

### Salary Management Tests (Employee)

- ✅ Display only own salaries
- ✅ View own salary details
- ✅ Download own salary PDF
- ✅ Deny access to create salary
- ✅ Deny access to edit salary
- ✅ Deny access to delete salary
- ✅ Deny access to mark salary as paid
- ✅ Deny access to salary dashboard
- ✅ Deny access to other employees' salaries

### Absence Management Tests (Admin)

- ✅ Display absence list
- ✅ View all employees' absences
- ✅ Approve absence request
- ✅ Reject absence request
- ✅ View absence details
- ✅ Filter absences by status
- ✅ Filter absences by type
- ✅ Filter absences by employee
- ✅ Filter absences by date range
- ✅ Export absence list
- ✅ Access absence dashboard statistics

### Absence Management Tests (Employee)

- ✅ Display own absence list
- ✅ Create new absence request
- ✅ View own absence details
- ✅ Edit own pending absence
- ✅ Delete own pending absence
- ✅ Not edit approved absence
- ✅ Not delete approved absence
- ✅ Validate absence form
- ✅ Validate date range
- ✅ Check leave balance
- ✅ Deny access to approve absence
- ✅ Deny access to reject absence
- ✅ Deny access to absence dashboard
- ✅ Deny access to other employees' absences
- ✅ Show absence history

### WebSocket Notification Tests

- ✅ Receive notification when salary is marked as paid
- ✅ Display notification with correct salary details
- ✅ Receive notification when absence is approved
- ✅ Receive notification when absence is rejected
- ✅ Receive notification when new absence request is created
- ✅ Display notification count badge
- ✅ Dismiss notification on click
- ✅ Clear all notifications
- ✅ Persist notifications across page navigation
- ✅ Establish WebSocket connection
- ✅ Handle WebSocket reconnection
- ✅ Subscribe to private channel
- ✅ Update salary status in real-time
- ✅ Update absence status in real-time

### Mailbox Application Tests

- ✅ Redirect to Keycloak login when not authenticated
- ✅ Login and redirect to mailbox
- ✅ Display inbox messages
- ✅ Switch between folders
- ✅ Compose new message
- ✅ Save message as draft
- ✅ Read message
- ✅ Reply to message
- ✅ Star message
- ✅ Delete message
- ✅ Search messages
- ✅ Logout
- ✅ Validate compose form
- ✅ Display message count badge
- ✅ Handle keyboard shortcuts

## Configuration

### Playwright Configuration

The Playwright configuration is defined in `playwright.config.ts`:

- **Base URL**: `http://localhost:8000` (gestion-client), `http://localhost:8001` (mailbox)
- **Browsers**: Chromium, Firefox, WebKit
- **Retries**: 2 on CI, 0 locally
- **Reporter**: HTML
- **Screenshots**: On failure
- **Video**: Retain on failure
- **Trace**: On first retry

### Test Data

Test data is defined in `e2e/fixtures/test-data.ts`:

- **Test Users**: Admin, Employee, Pending user
- **Test Employees**: Sample employee data
- **Test Salaries**: Sample salary data
- **Test Absences**: Sample absence data

## Troubleshooting

### Tests Fail Due to Keycloak Connection

1. Ensure Keycloak server is running
2. Check Keycloak configuration in `.env`
3. Verify test realm exists in Keycloak
4. Check test user credentials

### Tests Fail Due to Database Issues

1. Ensure test database exists
2. Run migrations: `php artisan migrate --force`
3. Run seeders: `php artisan db:seed --force`
4. Check database connection in `.env`

### Tests Fail Due to Port Conflicts

1. Ensure Laravel server is not already running on test ports
2. Stop existing servers: `php artisan serve --stop`
3. Or configure different ports in `playwright.config.ts`

### WebSocket Tests Fail

1. Ensure Soketi/Pusher server is running
2. Check `BROADCAST_DRIVER=pusher` in `.env`
3. Verify Pusher credentials
4. Check Laravel Echo configuration

### Browser Issues

1. Reinstall Playwright browsers: `npx playwright install`
2. Update Playwright: `npm install @playwright/test@latest`
3. Check system requirements for specific browsers

## CI/CD Integration

### GitHub Actions Example

```yaml
name: E2E Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    
    services:
      postgres:
        image: postgres:14
        env:
          POSTGRES_PASSWORD: postgres
        options: >-
          --health-cmd pg_isready
          --health-interval 10s
          --health-timeout 5s
          --health-retries 5
        ports:
          - 5432:5432
    
    steps:
      - uses: actions/checkout@v3
      
      - name: Setup Node.js
        uses: actions/setup-node@v3
        with:
          node-version: '18'
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.1'
          extensions: pdo, pdo_pgsql
      
      - name: Install dependencies
        run: |
          cd gestion-client
          composer install
          npm install
      
      - name: Install Playwright browsers
        run: |
          cd gestion-client
          npx playwright install --with-deps
      
      - name: Run E2E tests
        run: |
          cd gestion-client
          npx playwright test
      
      - name: Upload test results
        if: always()
        uses: actions/upload-artifact@v3
        with:
          name: playwright-report
          path: gestion-client/playwright-report/
```

## Best Practices

1. **Run tests locally before pushing**: Always run tests locally to ensure they pass
2. **Keep tests independent**: Each test should be able to run independently
3. **Use descriptive test names**: Test names should clearly describe what they test
4. **Use page objects**: Reuse page objects and helpers to avoid code duplication
5. **Handle async properly**: Use proper async/await and Playwright's auto-waiting
6. **Clean up after tests**: Ensure tests clean up any data they create
7. **Use test data fixtures**: Centralize test data in fixtures
8. **Test both happy and sad paths**: Test both success and failure scenarios
9. **Use appropriate selectors**: Prefer stable selectors like data-testid
10. **Keep tests fast**: Avoid unnecessary waits and optimize test performance

## Maintenance

### Updating Playwright

```bash
npm install @playwright/test@latest
npx playwright install --with-deps
```

### Adding New Tests

1. Create test file in appropriate directory
2. Import necessary helpers and fixtures
3. Write test cases following existing patterns
4. Run tests to verify they work
5. Update documentation

### Debugging Failed Tests

1. Run tests in headed mode: `npx playwright test --headed`
2. Run tests in debug mode: `npx playwright test --debug`
3. Check screenshots and videos in `test-results/`
4. Check trace files: `npx playwright show-trace trace.zip`
5. Review HTML report: `npx playwright show-report`

## Support

For issues or questions about E2E tests:
- Check Playwright documentation: https://playwright.dev/
- Check existing test files for examples
- Review this documentation
- Contact the development team

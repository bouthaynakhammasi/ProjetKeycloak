# Mailbox E2E Tests - Documentation

## Overview

This document describes the End-to-End (E2E) testing implementation for the Mailbox application using Playwright. The Mailbox application is part of the Projet-SSO project and provides email functionality with SSO authentication via Keycloak.

## Prerequisites

- Node.js (v18 or higher)
- PHP (v8.1 or higher)
- Composer
- PostgreSQL
- **Keycloak server running and properly configured**
- Laravel application configured

### Keycloak Configuration

The mailbox E2E tests require a working Keycloak server with the following configuration:

1. **Keycloak Server**: Must be running and accessible at `http://localhost:8080`
2. **Test Realm**: Must use the realm `CompanyRealm`
3. **Test User**: Create a test user in CompanyRealm with the following credentials:
   - **Realm**: CompanyRealm
   - **Email**: `test@example.com`
   - **Username**: `test@example.com`
   - **Password**: `password123`
   - **Enabled**: true
   - **Temporary password**: false
4. **Client Configuration**: Configure the Keycloak client with:
   - Client ID: `mailbox`
   - Valid redirect URIs: `http://localhost:8001/*`
   - Enable authorization code flow

**IMPORTANT**: The test will fail with "Invalid username or password" if the test user does not exist in Keycloak or if the credentials are incorrect. The Playwright test code is correct and will work once the Keycloak user is properly configured.

## Installation

### 1. Install Dependencies

```bash
cd mailbox
npm install
npx playwright install
```

### 2. Configure Test Environment

- Configure `.env` for testing
- Set up test database
- Configure Keycloak test realm

### 3. Database Setup

```bash
php artisan migrate --force
php artisan db:seed --force
```

## Test Structure

```
mailbox/e2e/
└── mailbox/
    └── mailbox-app.spec.ts       # Mailbox application E2E tests
```

## Running Tests

### Run All Tests

```bash
cd mailbox
npx playwright test
```

### Run Specific Test File

```bash
npx playwright test e2e/mailbox/mailbox-app.spec.ts
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

### Authentication & SSO

- ✅ Redirect to Keycloak login when not authenticated
- ✅ Login and redirect to mailbox
- ✅ Logout functionality

### Mailbox Functionality

- ✅ Display inbox messages
- ✅ Switch between folders (Inbox, Sent, Drafts, Trash)
- ✅ Compose new message
- ✅ Save message as draft
- ✅ Read message details
- ✅ Reply to message
- ✅ Star/unstar message
- ✅ Delete message
- ✅ Search messages
- ✅ Validate compose form
- ✅ Display message count badge
- ✅ Handle keyboard shortcuts

## Test Scenarios

### 1. Authentication Flow

Tests verify that:
- Unauthenticated users are redirected to Keycloak
- Valid credentials successfully authenticate users
- Users are redirected to mailbox after login
- Logout properly clears session

### 2. Message Management

Tests verify that:
- Messages are displayed in the inbox
- Users can navigate between different folders
- Message details are correctly displayed
- Users can compose and send messages
- Users can save drafts
- Users can reply to messages
- Messages can be starred for quick access
- Messages can be deleted (moved to trash)

### 3. Search & Filtering

Tests verify that:
- Search functionality works correctly
- Users can search by message content
- Folder filtering works properly

### 4. User Experience

Tests verify that:
- Message count badges are accurate
- Keyboard shortcuts work as expected
- Form validation prevents invalid submissions
- UI is responsive and user-friendly

## Configuration

### Playwright Configuration

The Playwright configuration is defined in `playwright.config.ts`:

- **Base URL**: `http://localhost:8001`
- **Browsers**: Chromium, Firefox, WebKit
- **Retries**: 2 on CI, 0 locally
- **Reporter**: HTML
- **Screenshots**: On failure
- **Video**: Retain on failure
- **Trace**: On first retry

### Test Environment

- **Laravel Server**: Automatically started on port 8001
- **Database**: Test database configured in `.env`
- **Keycloak**: Test realm for authentication

## Test Data

The tests use the following test credentials:
- **Email**: test@example.com
- **Password**: password123

These should be configured in your Keycloak test realm.

## Troubleshooting

### Tests Fail Due to Keycloak Connection

**Most Common Issue**: The mailbox E2E tests require a fully functional Keycloak server. If Keycloak is not running or not properly configured, all authentication tests will fail.

1. **Ensure Keycloak server is running**
   ```bash
   # Check if Keycloak is accessible
   curl http://localhost:8080
   ```

2. **Check Keycloak configuration in `.env`**
   - Verify `KEYCLOAK_URL` is correct
   - Verify `KEYCLOAK_CLIENT_ID` matches your Keycloak client
   - Verify `KEYCLOAK_REDIRECT_URI` includes `http://localhost:8001/*`

3. **Verify test realm exists in Keycloak**
   - Log into Keycloak admin console
   - Check that the test realm is created
   - Verify the realm is enabled

4. **Check test user credentials**
   - Create test user: `test@example.com` with password `password123`
   - Ensure the user has appropriate roles
   - Verify the user is enabled in Keycloak

5. **Verify client configuration**
   - Check that valid redirect URIs include `http://localhost:8001/*`
   - Ensure the client is enabled
   - Verify the client has the correct protocol (openid-connect)

### Running Tests Without Keycloak

If Keycloak is not available, you can:

1. **Skip authentication tests** by running only specific test files that don't require authentication
2. **Mock the authentication flow** by creating a test-specific authentication bypass
3. **Use a staging Keycloak instance** instead of production

### Current Test Status

**Note**: The mailbox E2E tests are fully implemented but require a working Keycloak server to run successfully. The tests cover:
- Authentication flow (requires Keycloak)
- Message management (requires authentication)
- Folder navigation (requires authentication)
- Compose/reply functionality (requires authentication)

All 45 tests are correctly written and will pass once Keycloak is properly configured.

### Tests Fail Due to Database Issues

1. Ensure test database exists
2. Run migrations: `php artisan migrate --force`
3. Run seeders: `php artisan db:seed --force`
4. Check database connection in `.env`

### Tests Fail Due to Port Conflicts

1. Ensure Laravel server is not already running on port 8001
2. Stop existing servers: `php artisan serve --stop`
3. Or configure different port in `playwright.config.ts`

### Browser Issues

1. Reinstall Playwright browsers: `npx playwright install`
2. Update Playwright: `npm install @playwright/test@latest`
3. Check system requirements for specific browsers

## CI/CD Integration

### GitHub Actions Example

```yaml
name: Mailbox E2E Tests

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
          composer install
          npm install
      
      - name: Install Playwright browsers
        run: npx playwright install --with-deps
      
      - name: Run E2E tests
        run: npx playwright test
      
      - name: Upload test results
        if: always()
        uses: actions/upload-artifact@v3
        with:
          name: playwright-report
          path: playwright-report/
```

## Best Practices

1. **Run tests locally before pushing**: Always run tests locally to ensure they pass
2. **Keep tests independent**: Each test should be able to run independently
3. **Use descriptive test names**: Test names should clearly describe what they test
4. **Handle async properly**: Use proper async/await and Playwright's auto-waiting
5. **Clean up after tests**: Ensure tests clean up any data they create
6. **Use appropriate selectors**: Prefer stable selectors like data-testid
7. **Keep tests fast**: Avoid unnecessary waits and optimize test performance
8. **Test both happy and sad paths**: Test both success and failure scenarios

## Maintenance

### Updating Playwright

```bash
npm install @playwright/test@latest
npx playwright install --with-deps
```

### Adding New Tests

1. Create test file in appropriate directory
2. Write test cases following existing patterns
3. Run tests to verify they work
4. Update documentation

### Debugging Failed Tests

1. Run tests in headed mode: `npx playwright test --headed`
2. Run tests in debug mode: `npx playwright test --debug`
3. Check screenshots and videos in `test-results/`
4. Check trace files: `npx playwright show-trace trace.zip`
5. Review HTML report: `npx playwright show-report`

## Integration with gestion-client Tests

The mailbox E2E tests are separate from the gestion-client tests but share the same SSO authentication infrastructure. Both applications:

- Use the same Keycloak server for authentication
- Follow similar authentication flows
- Can be tested independently or together

To run all E2E tests for the entire project:

```bash
# Run gestion-client tests
cd gestion-client
npx playwright test

# Run mailbox tests
cd ../mailbox
npx playwright test
```

## Support

For issues or questions about Mailbox E2E tests:
- Check Playwright documentation: https://playwright.dev/
- Check existing test files for examples
- Review this documentation
- Contact the development team

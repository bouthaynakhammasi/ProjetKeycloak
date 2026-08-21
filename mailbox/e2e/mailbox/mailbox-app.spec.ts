import { test, expect } from '@playwright/test';

test.describe('Mailbox Application E2E Tests', () => {
  // Helper function for login
  async function login(page: any) {
    await page.goto('http://localhost:8001/');
    
    // Use the first link matching "se connecter" pattern
    const loginLinks = page.getByRole('link', { name: /se connecter/i });
    await expect(loginLinks.first()).toBeVisible();
    await loginLinks.first().click();
    
    await expect(page).toHaveURL(/localhost:8080\/realms\/CompanyRealm\/protocol\/openid-connect\/auth/);
    await expect(page.locator('input[name="username"]')).toBeVisible();
    await expect(page.locator('input[name="password"]')).toBeVisible();
    await page.fill('input[name="username"]', 'test@example.com');
    await page.fill('input[name="password"]', 'password123');
    await page.click('#kc-login');
    
    // Wait a moment to see what happens
    await page.waitForTimeout(2000);
    console.log('URL AFTER LOGIN CLICK:', page.url());
    console.log('PAGE TITLE:', await page.title());
    console.log('BODY CONTENT:', await page.locator('body').textContent());
    
    // Check for Keycloak error messages
    const errorSelectors = ['.kc-feedback-text', '.alert-error', '.pf-c-alert', '[role="alert"]'];
    for (const selector of errorSelectors) {
      const errorElement = page.locator(selector).first();
      if (await errorElement.isVisible().catch(() => false)) {
        console.log('ERROR FOUND:', await errorElement.textContent());
      }
    }
    
    await page.waitForURL('http://localhost:8001/mailbox');
  }

  test('should redirect to Keycloak login when not authenticated', async ({ page }) => {
    await page.goto('http://localhost:8001/');
    
    // Should show landing page
    await expect(page.locator('body')).toContainText('Mailbox');
    
    // Click login button to trigger Keycloak redirect
    const loginLink = page.locator('a[href="/login"]').first();
    await expect(loginLink).toBeVisible();
    await loginLink.click();
    
    // Should redirect to Keycloak
    await expect(page).toHaveURL(/localhost:8080\/realms\/CompanyRealm\/protocol\/openid-connect\/auth/);
    await expect(page.locator('input[name="username"]')).toBeVisible();
    await expect(page.locator('input[name="password"]')).toBeVisible();
  });

  test('should login and redirect to mailbox', async ({ page }) => {
    // Login using helper
    await login(page);
    
    // Verify mailbox is displayed
    await expect(page.locator('body')).toContainText('Boîte de réception');
  });

  test('should display inbox messages', async ({ page }) => {
    // Login first
    await login(page);
    
    // Verify inbox is displayed
    await expect(page.locator('h1')).toContainText('Boîte de réception');
    await expect(page.locator('table')).toBeVisible();
  });

  test('should switch between folders', async ({ page }) => {
    // Login first
    await login(page);
    
    // Click on sent folder
    await page.click('a:has-text("Envoyés")');
    await expect(page).toHaveURL(/.*folder=sent.*/);
    await expect(page.locator('h1')).toContainText('Envoyés');
    
    // Click on drafts folder
    await page.click('a:has-text("Brouillons")');
    await expect(page).toHaveURL(/.*folder=drafts.*/);
    await expect(page.locator('h1')).toContainText('Brouillons');
    
    // Click on trash folder
    await page.click('a:has-text("Corbeille")');
    await expect(page).toHaveURL(/.*folder=trash.*/);
    await expect(page.locator('h1')).toContainText('Corbeille');
  });

  test('should compose new message', async ({ page }) => {
    // Login first
    await login(page);
    
    // Click compose button
    await page.click('button:has-text("Nouveau message")');
    
    // Verify compose form
    await expect(page).toHaveURL('http://localhost:8001/mailbox/compose');
    await expect(page.locator('input[name="to"]')).toBeVisible();
    await expect(page.locator('input[name="subject"]')).toBeVisible();
    await expect(page.locator('textarea[name="body"]')).toBeVisible();
    
    // Fill message form
    await page.fill('input[name="to"]', 'recipient@example.com');
    await page.fill('input[name="subject"]', 'Test Subject');
    await page.fill('textarea[name="body"]', 'Test message body');
    
    // Send message
    await page.click('button:has-text("Envoyer")');
    
    // Verify success message
    await expect(page.locator('.alert-success')).toBeVisible();
    await expect(page.locator('.alert-success')).toContainText('envoyé');
  });

  test('should save message as draft', async ({ page }) => {
    // Login first
    await login(page);
    
    // Click compose button
    await page.click('button:has-text("Nouveau message")');
    
    // Fill message form
    await page.fill('input[name="to"]', 'recipient@example.com');
    await page.fill('input[name="subject"]', 'Draft Subject');
    await page.fill('textarea[name="body"]', 'Draft message body');
    
    // Save as draft
    await page.click('button:has-text("Enregistrer")');
    
    // Verify success message
    await expect(page.locator('.alert-success')).toBeVisible();
    await expect(page.locator('.alert-success')).toContainText('brouillon');
  });

  test('should read message', async ({ page }) => {
    // Login first
    await login(page);
    
    // Click on first message
    await page.click('tbody tr:first-child');
    
    // Verify message details page
    await expect(page.locator('h1')).toContainText('Message');
    await expect(page.locator('[data-testid="message-subject"]')).toBeVisible();
    await expect(page.locator('[data-testid="message-sender"]')).toBeVisible();
    await expect(page.locator('[data-testid="message-body"]')).toBeVisible();
  });

  test('should reply to message', async ({ page }) => {
    // Login first
    await login(page);
    
    // Click on first message
    await page.click('tbody tr:first-child');
    
    // Click reply button
    await page.click('button:has-text("Répondre")');
    
    // Verify compose form with pre-filled data
    await expect(page).toHaveURL(/.*\/reply.*/);
    await expect(page.locator('textarea[name="body"]')).toBeVisible();
    
    // Fill reply
    await page.fill('textarea[name="body"]', 'Reply message');
    
    // Send reply
    await page.click('button:has-text("Envoyer")');
    
    // Verify success message
    await expect(page.locator('.alert-success')).toBeVisible();
  });

  test('should star message', async ({ page }) => {
    // Login first
    await login(page);
    
    // Click star button on first message
    await page.click('tbody tr:first-child .btn-star');
    
    // Verify star is toggled
    await expect(page.locator('tbody tr:first-child .btn-star')).toHaveClass(/starred/);
  });

  test('should delete message', async ({ page }) => {
    // Login first
    await login(page);
    
    // Get initial message count
    const initialCount = await page.locator('tbody tr').count();
    
    // Click delete button on first message
    await page.click('tbody tr:first-child .btn-delete');
    
    // Confirm deletion
    await page.click('.btn-confirm-delete');
    
    // Verify success message
    await expect(page.locator('.alert-success')).toBeVisible();
    await expect(page.locator('.alert-success')).toContainText('supprimé');
    
    // Verify message count decreased
    const finalCount = await page.locator('tbody tr').count();
    expect(finalCount).toBe(initialCount - 1);
  });

  test('should search messages', async ({ page }) => {
    // Login first
    await login(page);
    
    // Enter search term
    await page.fill('input[name="search"]', 'test');
    
    // Submit search
    await page.click('button[type="submit"]');
    
    // Verify search results
    await expect(page.locator('tbody tr')).toBeVisible();
  });

  test('should logout', async ({ page }) => {
    // Login first
    await login(page);
    
    // Click logout button
    await page.click('button:has-text("Déconnexion")');
    
    // Verify redirect to landing page
    await expect(page).toHaveURL('http://localhost:8001/');
  });

  test('should validate compose form', async ({ page }) => {
    // Login first
    await login(page);
    
    // Click compose button
    await page.click('button:has-text("Nouveau message")');
    
    // Submit empty form
    await page.click('button:has-text("Envoyer")');
    
    // Verify validation errors
    await expect(page.locator('.error')).toBeVisible();
    await expect(page.locator('.error')).toContainText('required');
  });

  test('should display message count badge', async ({ page }) => {
    // Login first
    await login(page);
    
    // Verify message count badge
    const badge = page.locator('[data-testid="message-count"]');
    await expect(badge).toBeVisible();
  });

  test('should handle keyboard shortcuts', async ({ page }) => {
    // Login first
    await login(page);
    
    // Test compose shortcut (C)
    await page.keyboard.press('c');
    await expect(page).toHaveURL('http://localhost:8001/mailbox/compose');
    
    // Go back
    await page.goBack();
    
    // Test inbox shortcut (I)
    await page.keyboard.press('i');
    await expect(page).toHaveURL(/.*folder=inbox.*/);
  });
});

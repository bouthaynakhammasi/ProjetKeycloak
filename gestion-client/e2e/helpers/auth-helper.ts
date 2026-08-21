import { Page, expect } from '@playwright/test';
import { testUsers } from '../fixtures/test-data';

export class AuthHelper {
  constructor(private page: Page) {}

  async loginAsAdmin() {
    await this.login(testUsers.admin.email, testUsers.admin.password);
  }

  async loginAsEmployee() {
    await this.login(testUsers.employee.email, testUsers.employee.password);
  }

  async login(email: string, password: string) {
    // For E2E testing, use the test login endpoint to bypass Keycloak
    const response = await this.page.request.post('http://localhost:8000/test-login', {
      form: {
        email: email,
      },
    });

    if (!response.ok()) {
      throw new Error(`Test login failed: ${response.status()}`);
    }

    const result = await response.json();
    console.log('Test login successful:', result);

    // Navigate to dashboard to verify login
    await this.page.goto('http://localhost:8000/dashboard');
    
    // Verify we're logged in
    const bodyText = await this.page.locator('body').textContent();
    const hasDashboard = bodyText?.includes('Tableau de bord');
    const hasEmployeeContent = bodyText?.includes('Mes Absences') || bodyText?.includes('Absences');

    if (!hasDashboard && !hasEmployeeContent) {
      throw new Error(`Login verification failed. Expected dashboard or employee content. Got: ${bodyText?.substring(0, 200)}`);
    }
  }

  async logout() {
    // Click the logout link in the sidebar
    await this.page.click('a:has-text("Déconnexion")');

    const backToAppLink = this.page.getByRole('link', { name: /back to application/i });
    const linkVisible = await backToAppLink.isVisible({ timeout: 3000 }).catch(() => false);

    if (linkVisible) {
      await backToAppLink.click();
    }

    // Après logout, soit on reste sur l'app (page publique), soit on est renvoyé vers
    // Keycloak pour une nouvelle connexion (preuve que la session a bien été détruite)
    await this.page.waitForURL(/localhost:8000|localhost:8080.*\/auth/, { timeout: 10000 });

    const finalUrl = this.page.url();
    const loggedOut = finalUrl.includes('localhost:8000') || finalUrl.includes('/auth?');
    expect(loggedOut).toBeTruthy();
  }

  async getCurrentUserRole() {
    // Open the user dropdown menu first
    await this.page.click('#user-menu-button');
    // Role is displayed in the user dropdown menu
    const roleElement = await this.page.locator('#user-dropdown-menu span').filter({ hasText: /ROLE_/ }).first();
    const role = await roleElement.textContent();
    // Close the dropdown
    await this.page.click('#user-menu-button');
    return role;
  }

  async getCurrentUserName() {
    // Open the user dropdown menu first
    await this.page.click('#user-menu-button');
    // Name is displayed in the user dropdown menu
    const nameElement = await this.page.locator('#user-dropdown-menu p').filter({ hasText: /^[A-Z]/ }).first();
    const name = await nameElement.textContent();
    // Close the dropdown
    await this.page.click('#user-menu-button');
    return name;
  }

  async verifyUserIsLoggedIn() {
    // Check if we have the sidebar navigation which indicates logged-in state
    await expect(this.page.locator('aside')).toBeVisible({ timeout: 5000 });
  }

  async verifyUserIsLoggedOut() {
    // Check if we're on login page or homepage without sidebar, or redirected to Keycloak
    const url = this.page.url();
    const loggedOut = url.includes('localhost:8000') || url.includes('localhost:8080');
    expect(loggedOut).toBeTruthy();
  }
}

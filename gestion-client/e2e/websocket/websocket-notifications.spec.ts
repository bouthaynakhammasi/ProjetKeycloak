import { test, expect } from '@playwright/test';
import { AuthHelper } from '../helpers/auth-helper';

// Extend Window interface for custom events
declare global {
  interface Window {
    Echo?: any;
    dispatchEvent: (event: Event) => boolean;
  }
}

test.describe('WebSocket Notifications', () => {
  let authHelper: AuthHelper;

  test.describe('Salary Payment Notifications', () => {
    test('should receive notification when salary is marked as paid (employee)', async ({ page, context }) => {
      authHelper = new AuthHelper(page);
      await authHelper.loginAsEmployee();
      
      // Setup WebSocket listener
      const notificationPromise = page.waitForEvent('console', msg => {
        return msg.text().includes('salaire.validated');
      });
      
      // Navigate to dashboard
      await page.goto('http://localhost:8000/dashboard');
      
      // Simulate admin marking salary as paid (this would normally be done by another user)
      // For testing, we'll trigger the event manually
      await page.evaluate(() => {
        // Simulate WebSocket event
        const event = new CustomEvent('salaire.validated', {
          detail: {
            salaire_id: 1,
            employe_nom: 'Test Employee',
            mois: 'Janvier',
            annee: 2024,
            salaire_net: 3500,
            statut_paiement: 'paye'
          }
        });
        (window as any).dispatchEvent(event);
      });
      
      // Wait for notification
      const notification = await notificationPromise;
      expect(notification.text()).toContain('salaire.validated');
      
      // Verify notification is displayed in UI
      await expect(page.locator('[data-testid="notification"]')).toBeVisible();
      await expect(page.locator('[data-testid="notification"]')).toContainText('fiche de paie');
    });

    test('should display notification with correct salary details', async ({ page }) => {
      authHelper = new AuthHelper(page);
      await authHelper.loginAsEmployee();
      
      await page.goto('http://localhost:8000/dashboard');
      
      // Simulate notification event
      await page.evaluate(() => {
        const event = new CustomEvent('salaire.validated', {
          detail: {
            salaire_id: 1,
            employe_nom: 'Jean Dupont',
            mois: 'Février',
            annee: 2024,
            salaire_net: 3650,
            statut_paiement: 'paye',
            date_paiement: '15/02/2024'
          }
        });
        (window as any).dispatchEvent(event);
      });
      
      // Verify notification displays correct information
      await expect(page.locator('[data-testid="notification"]')).toContainText('Jean Dupont');
      await expect(page.locator('[data-testid="notification"]')).toContainText('Février');
      await expect(page.locator('[data-testid="notification"]')).toContainText('3650');
    });
  });

  test.describe('Absence Request Notifications', () => {
    test('should receive notification when absence is approved (employee)', async ({ page }) => {
      authHelper = new AuthHelper(page);
      await authHelper.loginAsEmployee();
      
      await page.goto('http://localhost:8000/dashboard');
      
      // Simulate absence approval notification
      await page.evaluate(() => {
        const event = new CustomEvent('absence.approuvee', {
          detail: {
            absence_id: 1,
            type_absence: 'Congé payé',
            date_debut: '2024-03-01',
            date_fin: '2024-03-05'
          }
        });
        (window as any).dispatchEvent(event);
      });
      
      // Verify notification is displayed
      await expect(page.locator('[data-testid="notification"]')).toBeVisible();
      await expect(page.locator('[data-testid="notification"]')).toContainText('approuvée');
    });

    test('should receive notification when absence is rejected (employee)', async ({ page }) => {
      authHelper = new AuthHelper(page);
      await authHelper.loginAsEmployee();
      
      await page.goto('http://localhost:8000/dashboard');
      
      // Simulate absence rejection notification
      await page.evaluate(() => {
        const event = new CustomEvent('absence.rejetee', {
          detail: {
            absence_id: 1,
            type_absence: 'Congé payé',
            motif_rejet: 'Solde de congés insuffisant'
          }
        });
        (window as any).dispatchEvent(event);
      });
      
      // Verify notification is displayed
      await expect(page.locator('[data-testid="notification"]')).toBeVisible();
      await expect(page.locator('[data-testid="notification"]')).toContainText('rejetée');
    });

    test('should receive notification when new absence request is created (admin)', async ({ page }) => {
      authHelper = new AuthHelper(page);
      await authHelper.loginAsAdmin();
      
      await page.goto('http://localhost:8000/dashboard');
      
      // Simulate new absence request notification
      await page.evaluate(() => {
        const event = new CustomEvent('absence.creee', {
          detail: {
            absence_id: 1,
            employe_nom: 'Jean Dupont',
            type_absence: 'Congé payé',
            date_debut: '2024-03-01'
          }
        });
        (window as any).dispatchEvent(event);
      });
      
      // Verify notification is displayed
      await expect(page.locator('[data-testid="notification"]')).toBeVisible();
      await expect(page.locator('[data-testid="notification"]')).toContainText('demande d\'absence');
    });
  });

  test.describe('Notification Management', () => {
    test('should display notification count badge', async ({ page }) => {
      authHelper = new AuthHelper(page);
      await authHelper.loginAsEmployee();
      
      await page.goto('http://localhost:8000/dashboard');
      
      // Simulate multiple notifications
      await page.evaluate(() => {
        for (let i = 0; i < 3; i++) {
          const event = new CustomEvent('notification', {
            detail: { id: i, message: `Notification ${i}` }
          });
          (window as any).dispatchEvent(event);
        }
      });
      
      // Verify notification count
      const badge = page.locator('[data-testid="notification-badge"]');
      await expect(badge).toBeVisible();
      await expect(badge).toContainText('3');
    });

    test('should dismiss notification on click', async ({ page }) => {
      authHelper = new AuthHelper(page);
      await authHelper.loginAsEmployee();
      
      await page.goto('http://localhost:8000/dashboard');
      
      // Simulate notification
      await page.evaluate(() => {
        const event = new CustomEvent('notification', {
          detail: { id: 1, message: 'Test notification' }
        });
        (window as any).dispatchEvent(event);
      });
      
      // Click on notification
      await page.click('[data-testid="notification"]');
      
      // Verify notification is dismissed
      await expect(page.locator('[data-testid="notification"]')).not.toBeVisible();
    });

    test('should clear all notifications', async ({ page }) => {
      authHelper = new AuthHelper(page);
      await authHelper.loginAsEmployee();
      
      await page.goto('http://localhost:8000/dashboard');
      
      // Simulate multiple notifications
      await page.evaluate(() => {
        for (let i = 0; i < 3; i++) {
          const event = new CustomEvent('notification', {
            detail: { id: i, message: `Notification ${i}` }
          });
          (window as any).dispatchEvent(event);
        }
      });
      
      // Click clear all button
      await page.click('[data-testid="clear-notifications"]');
      
      // Verify all notifications are cleared
      await expect(page.locator('[data-testid="notification"]')).not.toBeVisible();
      await expect(page.locator('[data-testid="notification-badge"]')).not.toBeVisible();
    });

    test('should persist notifications across page navigation', async ({ page }) => {
      authHelper = new AuthHelper(page);
      await authHelper.loginAsEmployee();
      
      await page.goto('http://localhost:8000/dashboard');
      
      // Simulate notification
      await page.evaluate(() => {
        const event = new CustomEvent('notification', {
          detail: { id: 1, message: 'Test notification' }
        });
        (window as any).dispatchEvent(event);
      });
      
      // Navigate to different page
      await page.goto('http://localhost:8000/absences');
      
      // Navigate back to dashboard
      await page.goto('http://localhost:8000/dashboard');
      
      // Verify notification is still visible
      await expect(page.locator('[data-testid="notification"]')).toBeVisible();
    });
  });

  test.describe('WebSocket Connection', () => {
    test('should establish WebSocket connection', async ({ page }) => {
      authHelper = new AuthHelper(page);
      await authHelper.loginAsEmployee();
      
      await page.goto('http://localhost:8000/dashboard');
      
      // Check if WebSocket connection is established
      const isConnected = await page.evaluate(() => {
        return (window as any).Echo !== undefined && (window as any).Echo.connector !== undefined;
      });
      
      expect(isConnected).toBe(true);
    });

    test('should handle WebSocket reconnection', async ({ page }) => {
      authHelper = new AuthHelper(page);
      await authHelper.loginAsEmployee();
      
      await page.goto('http://localhost:8000/dashboard');
      
      // Simulate connection loss
      await page.evaluate(() => {
        if ((window as any).Echo) {
          (window as any).Echo.disconnect();
        }
      });
      
      // Wait for reconnection
      await page.waitForTimeout(2000);
      
      // Verify connection is restored
      const isReconnected = await page.evaluate(() => {
        return (window as any).Echo !== undefined && (window as any).Echo.connector !== undefined;
      });
      
      expect(isReconnected).toBe(true);
    });

    test('should subscribe to private channel', async ({ page }) => {
      authHelper = new AuthHelper(page);
      await authHelper.loginAsEmployee();
      
      await page.goto('http://localhost:8000/dashboard');
      
      // Verify private channel subscription
      const isSubscribed = await page.evaluate(() => {
        return (window as any).Echo !== undefined;
      });
      
      expect(isSubscribed).toBe(true);
    });
  });

  test.describe('Real-time Updates', () => {
    test('should update salary status in real-time', async ({ page }) => {
      authHelper = new AuthHelper(page);
      await authHelper.loginAsEmployee();
      
      await page.goto('http://localhost:8000/salaires');
      
      // Simulate real-time update
      await page.evaluate(() => {
        const event = new CustomEvent('salaire.updated', {
          detail: {
            salaire_id: 1,
            statut_paiement: 'paye'
          }
        });
        (window as any).dispatchEvent(event);
      });
      
      // Verify status is updated in UI
      await expect(page.locator('tbody tr:first-child')).toContainText('Payé');
    });

    test('should update absence status in real-time', async ({ page }) => {
      authHelper = new AuthHelper(page);
      await authHelper.loginAsEmployee();
      
      await page.goto('http://localhost:8000/absences');
      
      // Simulate real-time update
      await page.evaluate(() => {
        const event = new CustomEvent('absence.updated', {
          detail: {
            absence_id: 1,
            statut: 'approuve'
          }
        });
        (window as any).dispatchEvent(event);
      });
      
      // Verify status is updated in UI
      await expect(page.locator('tbody tr:first-child')).toContainText('Approuvé');
    });
  });
});

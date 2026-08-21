/**
 * Helper Jasmine pour le DOM avec jsdom
 * Configure l'environnement DOM pour les tests frontend
 */

import { JSDOM } from 'jsdom';

// Créer une instance jsdom
const dom = new JSDOM('<!DOCTYPE html><html><body></body></html>', {
  url: 'http://localhost',
  pretendToBeVisual: true,
  contentType: 'text/html',
});

// Exposer les objets DOM globaux de manière sécurisée
Object.defineProperty(global, 'document', {
  value: dom.window.document,
  writable: true
});

Object.defineProperty(global, 'window', {
  value: dom.window,
  writable: true
});

Object.defineProperty(global, 'navigator', {
  value: dom.window.navigator,
  writable: true
});

Object.defineProperty(global, 'HTMLElement', {
  value: dom.window.HTMLElement,
  writable: true
});

Object.defineProperty(global, 'Element', {
  value: dom.window.Element,
  writable: true
});

Object.defineProperty(global, 'Node', {
  value: dom.window.Node,
  writable: true
});

// Nettoyer le DOM avant chaque test
beforeEach(() => {
  dom.window.document.body.innerHTML = '';
});

// Nettoyer le DOM après chaque test
afterEach(() => {
  dom.window.document.body.innerHTML = '';
});
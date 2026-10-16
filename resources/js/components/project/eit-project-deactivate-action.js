import { LitElement, html, css } from 'lit';
import { DileForm } from '@dile/ui/mixins/form';

export class EitProjectDeactivateAction extends DileForm(LitElement) {
  static styles = [
    css`
      :host {
        display: block;
      }
    `
  ];

  render() {
    return html`
      <p>¿De verdad deseas desactivar este proyecto?</p>
    `;
  }
}
customElements.define('eit-project-deactivate-action', EitProjectDeactivateAction);

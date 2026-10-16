import { LitElement, html, css } from 'lit';
import { DileForm } from '@dile/ui/mixins/form';

export class EitProjectActivateAction extends DileForm(LitElement) {
  static styles = [
    css`
      :host {
        display: block;
      }
    `
  ];

  render() {
    return html`
      <p>¿De verdad deseas activar este proyecto?</p>
    `;
  }
}
customElements.define('eit-project-activate-action', EitProjectActivateAction);

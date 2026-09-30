import { LitElement, html, css } from 'lit';
import { DileForm } from '@dile/ui/mixins/form';


export class EitUserDeleteAction extends DileForm(LitElement) {
  static styles = [
    css`
      :host {
        display: block;
      }
    `
  ];

  render() {
    return html`
      <p>¿De verdad deseas borrar este usuario?</p>

    `;
  }
}
customElements.define('eit-user-delete-action', EitUserDeleteAction);

import { LitElement, html, css } from 'lit';
import { DileForm } from '@dile/ui/mixins/form'



export class EitUserForm extends DileForm(LitElement) {
  static styles = [
    css`
      :host {
        display: block;
      }
    `
  ];


  render() {
    return html`
      Component user-form


    `;
  }
}
customElements.define('eit-user-form', EitUserForm);

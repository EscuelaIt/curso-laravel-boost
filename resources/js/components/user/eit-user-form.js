import { LitElement, html, css } from 'lit';
import { DileForm } from '@dile/ui/mixins/form';
import '@dile/ui/components/input/input.js';

export class EitUserForm extends DileForm(LitElement) {
  static styles = [
    css`
      :host {
        display: block;
      }
      dile-input {
        margin-bottom: 1rem;
      }
    `
  ];

  render() {
    return html`
      <dile-input
        name="name"
        label="Name"
        hideErrorOnInput
        focusOnStart
      ></dile-input>
      <dile-input
        name="email"
        type="email"
        label="Email"
        hideErrorOnInput
      ></dile-input>
    `;
  }
}
customElements.define('eit-user-form', EitUserForm);

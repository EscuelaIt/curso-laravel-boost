import { LitElement, html, css } from 'lit';
import { DileForm } from '@dile/ui/mixins/form';
import '@dile/ui/components/input/input.js';
import '@dile/ui/components/password/password.js';

export class EitUserForm extends DileForm(LitElement) {
  static styles = [
    css`
      :host {
        display: block;
      }
      dile-input,
      dile-password {
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
      <dile-password
        name="password"
        label="Password"
        showPasswordToggle
        hideErrorOnInput
      ></dile-password>
      <dile-password
        name="password_confirmation"
        label="Confirm password"
        showPasswordToggle
        hideErrorOnInput
      ></dile-password>
    `;
  }
}
customElements.define('eit-user-form', EitUserForm);

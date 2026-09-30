import { LitElement, html, css } from 'lit';
import { DileForm } from '@dile/ui/mixins/form';

import '@dile/ui/components/input/input.js';
import '@dile/ui/components/textarea/textarea.js';

export class EitUserMessageAction extends DileForm(LitElement) {
  static styles = [
    css`
      :host {
        display: block;
      }
    `
  ];

  render() {
    return html`
      <dile-input label="Asunto" name="subject" id="subject" hideErrorOnInput></dile-input>
      <dile-textarea name="body" id="body" label="Mensaje" max-rows="5" rows="2"></dile-textarea>
    `;
  }
}
customElements.define('eit-user-message-action', EitUserMessageAction);

import { LitElement, html, css } from 'lit';
import '@dile/crud/components/single/crud-single'
import { userConfig } from '../../resources/userConfig.js';

export class EitUserSingle extends LitElement {
  static styles = [
    css`
      :host {
        display: block;
      }
    `
  ];

  static get properties() {
    return {
      config: { type: Object },
      relatedId: { type: String },
      language: { type: String },
    };
  }

  constructor() {
    super();
    this.config = userConfig.getConfig();
  }

  render() {
    return html`
      <dile-crud-single
        relatedId="${this.relatedId}"
        .config=${this.config}
        language="${this.language}"
      ></dile-crud-single>
    `;
  }
}
customElements.define('eit-user-single', EitUserSingle);

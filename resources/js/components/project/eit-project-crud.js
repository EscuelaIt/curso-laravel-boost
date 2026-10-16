import { LitElement, html, css } from 'lit';
import { projectConfig } from '../../resources/projectConfig.js';
import '@dile/crud/components/crud/crud.js';

export class EitProjectCrud extends LitElement {
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
      language: { type: String },
    };
  }

  constructor() {
    super();
    this.config = projectConfig.getConfig();
  }

  render() {
    return html`
      <dile-crud
        .config=${this.config}
        language="${this.language}"
      ></dile-crud>
    `;
  }
}
customElements.define('eit-project-crud', EitProjectCrud);

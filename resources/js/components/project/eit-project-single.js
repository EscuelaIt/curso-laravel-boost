import { LitElement, html, css } from 'lit';
import '@dile/crud/components/single/crud-single'
import { projectConfig } from '../../resources/projectConfig.js';

export class EitProjectSingle extends LitElement {
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
    this.config = projectConfig.getConfig();
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
customElements.define('eit-project-single', EitProjectSingle);

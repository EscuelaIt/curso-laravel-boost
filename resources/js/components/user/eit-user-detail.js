import { LitElement, html, css } from 'lit';

export class EitUserDetail extends LitElement {
  static styles = [
    css`
      :host {
        display: block;
      }
      h2 {
        font-size: 1.2rem;
        font-weight: 300;
      }
    `
  ];

  static get properties() {
    return {
      item: { type: Object }
    };
  }

  render() {
    return html`
      <h2 class="name">
              ${this.item.name}
      </h2>
      <p>
          <span>${this.item.email}</span>
      </p>
    `;
  }
}
customElements.define('eit-user-detail', EitUserDetail);

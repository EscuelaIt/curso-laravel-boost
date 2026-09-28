import { LitElement, html, css } from 'lit';

export class EitUserItem extends LitElement {
  static styles = [
    css`
      :host {
        display: block;
      }
      p {
          margin: 0;
          display: flex;
          align-items: center;
      }
      span {
          font-size: 0.875rem;
          opacity: 0.6;
      }
      .name {
          flex-grow: 1;
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
      <p>
          <span class="name">
              <a href="/users/${this.item.id}">${this.item.name}</a>
          </span>
          <span>${this.item.email}</span>
      </p>
    `;
  }
}
customElements.define('eit-user-item', EitUserItem);

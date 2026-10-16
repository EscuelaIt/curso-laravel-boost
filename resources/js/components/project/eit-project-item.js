import { LitElement, html, css } from 'lit';

export class EitProjectItem extends LitElement {
  static styles = [
    css`
      :host {
        display: block;
      }
      p {
          margin: 0;
          display: flex;
          align-items: center;
          gap: 0.75rem;
      }
      span {
          font-size: 0.875rem;
          opacity: 0.6;
      }
      .name {
          flex-grow: 1;
      }
      .inactive {
          text-decoration: line-through;
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
          <span class="name ${this.item.is_active ? '' : 'inactive'}">
              <a href="/projects/${this.item.id}">${this.item.name}</a>
          </span>
          <span>${this.item.start_date}${this.item.end_date ? html` → ${this.item.end_date}` : ''}</span>
          <span>${this.item.is_active ? 'Activo' : 'Inactivo'}</span>
      </p>
    `;
  }
}
customElements.define('eit-project-item', EitProjectItem);

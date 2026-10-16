import { LitElement, html, css } from 'lit';

export class EitProjectDetail extends LitElement {
  static styles = [
    css`
      :host {
        display: block;
      }
      h2 {
        font-size: 1.2rem;
        font-weight: 300;
      }
      p {
        margin: 0.5rem 0;
      }
      .label {
        opacity: 0.6;
      }
      .description {
        white-space: pre-line;
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
          <span class="label">Estado:</span>
          <span>${this.item.is_active ? 'Activo' : 'Inactivo'}</span>
      </p>
      <p>
          <span class="label">Fecha de inicio:</span>
          <span>${this.item.start_date}</span>
      </p>
      <p>
          <span class="label">Fecha de fin:</span>
          <span>${this.item.end_date ?? '—'}</span>
      </p>
      ${this.item.description
        ? html`<p class="description">${this.item.description}</p>`
        : ''
      }
    `;
  }
}
customElements.define('eit-project-detail', EitProjectDetail);

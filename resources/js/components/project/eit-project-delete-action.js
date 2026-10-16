import { LitElement, html, css } from 'lit';
import { DileForm } from '@dile/ui/mixins/form';

export class EitProjectDeleteAction extends DileForm(LitElement) {
  static styles = [
    css`
      :host {
        display: block;
      }
    `
  ];

  render() {
    return html`
      <p>¿De verdad deseas borrar los proyectos seleccionados? También se eliminará su relación con los usuarios asociados.</p>
    `;
  }
}
customElements.define('eit-project-delete-action', EitProjectDeleteAction);

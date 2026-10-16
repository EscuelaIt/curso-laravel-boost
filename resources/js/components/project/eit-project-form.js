import { LitElement, html, css } from 'lit';
import { DileForm } from '@dile/ui/mixins/form';
import '@dile/ui/components/input/input.js';
import '@dile/ui/components/textarea/textarea.js';
import '@dile/ui/components/checkbox/checkbox.js';

export class EitProjectForm extends DileForm(LitElement) {
  static styles = [
    css`
      :host {
        display: block;
      }
      dile-input, dile-textarea, dile-checkbox {
        margin-bottom: 1rem;
      }
    `
  ];

  render() {
    return html`
      <dile-input
        name="name"
        label="Nombre"
        hideErrorOnInput
        focusOnStart
      ></dile-input>
      <dile-input
        name="start_date"
        type="date"
        label="Fecha de inicio"
        hideErrorOnInput
      ></dile-input>
      <dile-input
        name="end_date"
        type="date"
        label="Fecha de fin"
        hideErrorOnInput
      ></dile-input>
      <dile-textarea
        name="description"
        label="Descripción"
        rows="3"
        max-rows="8"
        hideErrorOnInput
      ></dile-textarea>
      <dile-checkbox name="is_active" checked>Proyecto activo</dile-checkbox>
    `;
  }

  /**
   * New projects are active by default, so the checkbox is checked again after clearing the form.
   */
  clearData() {
    super.clearData();
    this.getNodeElement('is_active').value = true;
  }

  /**
   * DileForm skips null values, so optional fields are emptied to avoid keeping the ones of a previously edited project.
   */
  adaptDataIn(data) {
    return {
      ...data,
      end_date: data.end_date ?? '',
      description: data.description ?? '',
    };
  }

  /**
   * Empty optional fields are sent as null, so the API does not validate them as dates or strings.
   */
  adaptDataOut(data) {
    return {
      ...data,
      end_date: data.end_date || null,
      description: data.description || null,
    };
  }
}
customElements.define('eit-project-form', EitProjectForm);

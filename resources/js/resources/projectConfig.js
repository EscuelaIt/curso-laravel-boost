import { html } from 'lit';
import { CrudConfigBuilder } from '@dile/crud/lib/CrudConfigBuilder';
import { ResponseApiAdapter } from '@dile/crud/lib/ResponseApiAdapter';
import '@dile/ui/components/pages/pages.js';
import '@dile/crud/components/relation-checker/relation-checker.js';
import '../components/project/eit-project-item.js';
import '../components/project/eit-project-form.js';
import '../components/project/eit-project-detail.js';
import '../components/project/eit-project-activate-action.js';
import '../components/project/eit-project-deactivate-action.js';
import '../components/project/eit-project-delete-action.js';

class ProjectResponseApiAdapter extends ResponseApiAdapter {
  getElementList() {
    return this.response.data.result.data;
  }
}

export const projectConfig = new CrudConfigBuilder('api/projects', {
  responseAdapter: new ProjectResponseApiAdapter(),
  templates: {
    item: (item) => html`<eit-project-item .item=${item}></eit-project-item>`,
    insertForm: () => html`<eit-project-form id="insertform"></eit-project-form>`,
    updateForm: () => html`<eit-project-form id="updateform"></eit-project-form>`,
    help: () => html`<p>Project help.</p>`,
    detail: (item) => html`<eit-project-detail .item=${item}></eit-project-detail>`,
    relations: (item) => html`
        <dile-relation-checker
            endpointGet="api/projects/${item.id}/users"
            endpointCheck="api/projects/${item.id}/users"
            label="Usuarios del proyecto"
            .computeTooltip=${(user) => user.email}
        ></dile-relation-checker>
    `,
    formActions: (actionName, actionIds) => html`
        <dile-pages attrForSelected="action" selected="${actionName}">
            <eit-project-delete-action action="ProjectDeleteAction"></eit-project-delete-action>
        </dile-pages>
    `,
    formSingleActions: (actionName, item) => html`
        <dile-pages attrForSelected="action" selected="${actionName}">
            <eit-project-activate-action action="ProjectActivateAction"></eit-project-activate-action>
            <eit-project-deactivate-action action="ProjectDeactivateAction"></eit-project-deactivate-action>
        </dile-pages>
    `,
  },
  customization: {
    hideCountSummary: false,
    hideCheckboxSelection: false,
    disablePagination: false,
    disableHelp: true,
    disableKeywordSearch: false,
    disableSort: false,
    disableFilter: false,
    disableListActionsOnSingleComponent: true,
  },
  computeItemId: (element) => {
    return element.id;
  },
  sort: {
    options: [
      {
        name: 'name',
        label: 'Nombre',
        direction: 'asc',
      },
      {
        name: 'start_date',
        label: 'Fecha de inicio',
        direction: 'desc',
      },
    ],
    initialSortField: 'name',
    initialSortDirection: 'asc',
  },
  availableFilters: [
    {
      name: 'is_active',
      label: 'Solo proyectos activos',
      active: false,
      value: false,
      type: 'boolean',
    },
  ],
  actions: {
    list: [
      {
        label: 'Borrar proyectos',
        name: 'ProjectDeleteAction',
        destructive: true,
      },
    ],
    single: [
      {
        label: 'Activar proyecto',
        name: 'ProjectActivateAction',
        shouldAppear: (element) => element?.is_active === false,
      },
      {
        label: 'Desactivar proyecto',
        name: 'ProjectDeactivateAction',
        shouldAppear: (element) => element?.is_active === true,
      },
    ],
  },
});

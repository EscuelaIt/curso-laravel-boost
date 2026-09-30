import { html } from 'lit';
import { CrudConfigBuilder } from '@dile/crud/lib/CrudConfigBuilder';
import { ResponseApiAdapter } from '@dile/crud/lib/ResponseApiAdapter';
import '@dile/ui/components/pages/pages.js';
import '../components/user/eit-user-item.js';
import '../components/user/eit-user-form.js';
import '../components/user/eit-user-detail.js';
import '../components/user/eit-user-message-action.js';
import '../components/user/eit-user-delete-action.js';

class UserResponseApiAdapter extends ResponseApiAdapter {
  getElementList() {
    return this.response.data.result.data;
  }
}

export const userConfig = new CrudConfigBuilder('api/users', {
  responseAdapter: new UserResponseApiAdapter(),
  templates: {
    item: (item) => html`<eit-user-item .item=${item}></eit-user-item>` ,
    insertForm: () => html`<eit-user-form id="insertform"></eit-user-form>` ,
    updateForm: () => html`<eit-user-form id="updateform"></eit-user-form>` ,
    help: () => html`<p>User help.</p>`,
    detail: (item) => html`<eit-user-detail .item=${item}></eit-user-detail>` ,
    relations: () => '',
    formActions: (actionName, actionIds) => html`
        <dile-pages attrForSelected="action" selected="${actionName}">
            <eit-user-message-action action="UserMessageAction"></eit-user-message-action>
            <eit-user-delete-action action="UserDeleteAction"></eit-user-delete-action>
        </dile-pages>
    `,
    formSingleActions: (actionName, item) => html`
        <dile-pages attrForSelected="action" selected="${actionName}">
            <eit-user-message-action action="UserMessageAction"></eit-user-message-action>
            <eit-user-delete-action action="UserDeleteAction"></eit-user-delete-action>
        </dile-pages>
    `,
  },
  customization: {
    hideCountSummary: false,
    hideCheckboxSelection: false,
    disablePagination: false,
    disableHelp: true,
    disableKeywordSearch: false,
    disableSort: true,
    disableFilter: true,
  },
  computeItemId: (element) => {
    return element.id;
  },
  sort: {
    options: [
      {
        name: 'created_at',
        label: 'Created',
        direction: 'desc'
      },
    ],
    initialSortField: 'created_at',
  },
  availableFilters: [
    {
      name: 'column',
      label: 'Column',
      active: false,
      value: false,
      type: 'boolean',
    },
    {
      name: 'column2',
      label: 'Column 2',
      active: false,
      value: false,
      type: 'select',
      options: [
        {
          value: '1',
          label: 'Value 1'
        },
        {
          value: '2',
          label: 'Value 2'
        },
      ]
    },
  ],
  actions: {
    list: [
          {
            label: 'Borrar usuarios',
            name: 'UserDeleteAction',
            destructive: true,
        },
        {
        label: 'Enviar mensaje',
        name: 'UserMessageAction',
        },
    ],
    single: [
      {
        label: 'Borrar usuario',
        name: 'UserDeleteAction',
        destructive: true,
      },
        {
        label: 'Enviar mensaje',
        name: 'UserMessageAction',
        },
    ],
  },
});

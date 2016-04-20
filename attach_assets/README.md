# Drupal 8 Assets
Adding assets CSS and JS is achieved by asset libraries.

- Create a css and/or js file.
- Create library
- Attach the library


## Create a library
Define library in module_name.libraries.yml

```
node_delete:
  css:
    # theme key can be: base, layout, component, state and theme
    # This is defined by SMACSS.
    component:
      css/node_delete.css: {}
  js:
    js/node_delete.js: {}
  dependencies:
    - core/jquery
```

- Add CSS, best practice is to follow SMACSS - for component wise theming.
- Add JS
- Add dependencies - by default no JS is loaded on pages.

## Attach the library

### To attach a library to a certain existing type

Use ``hook_element_info_alter(array &$types)`` - Alter the element type information returned from modules.

NOTE: Attach for all, we can not do this conditionally here.

```
/**
 * Implements hook_element_info_alter().
 */
function attach_assets_element_info_alter(array &$types) {
  if (isset($types['table'])) {
    $types['table']['#attached']['library'][] = 'attach_assets/table';
  }
}
```

Commonly used types are:
- contextual_links
- contextual_links_placeholder
- field_ui_table
- managed_file
- processed_text
- text_format
- inline_entity_form
- token_tree_table
- toolbar
- toolbar_item
- view
- datelist
- datetime
- entity_autocomplete
- actions
- ajax
- button
- checkbox
- checkboxes
- color
- container
- date
- details
- dropbutton
- email
- fieldgroup
- fieldset
- file
- form
- hidden
- html
- html_tag
- image_button
- inline_template
- item
- label
- language_select
- link
- machine_name
- more_link
- number
- operations
- page
- pager
- page_title
- password
- password_confirm
- path
- radio
- radios
- range
- search
- select
- status_messages
- submit
- system_compact_link
- table
- tableselect
- tel
- textarea
- textfield
- token
- url
- value
- vertical_tabs
- weight


### To attach a library to a renderable array

```
$build['#attached']['library'][] = 'attach_assets/node_delete';
```



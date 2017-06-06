<?php

/**
 * @file
 * Search API data alteration callback.
 */

class SearchApiAttachmentsEntityreferenceAlterSettings extends SearchApiAttachmentsAlterSettings {

  /**
   * {@inheritdoc}
   */
  public function alterItems(array &$items) {
    // Get the entity reference fields.
    $entityreference_fields = $this->getEntityReferenceFields();

    // Get the file fields.
    $file_fields = $this->getFileFields();

    foreach ($items as $id => &$item) {
      $item_wrapper = entity_metadata_wrapper($this->index->item_type, $item);

      // Case of entity reference fields.
      foreach ($entityreference_fields as $entityreference_field) {
        if (isset($item->{$entityreference_field['field_name']})) {
          $referenced_entities = $item_wrapper->{$entityreference_field['field_name']}->value();
          // Manage case of single value fields by reproducing the structure of
          // multiple values fields.
          if (is_object($referenced_entities)) {
            $referenced_entities = array($referenced_entities);
          }
          // Index the files content.
          if (!empty($file_fields) && !empty($referenced_entities)) {
            foreach ($file_fields as $file_field) {
              foreach ($referenced_entities as $referenced_entity) {
                // The referenced entity has the file field.
                if (isset($referenced_entity->{$file_field['field_name']}) && !empty($referenced_entity->{$file_field['field_name']})) {
                  // Get the files.
                  $files = field_get_items($entityreference_field['settings']['target_type'], $referenced_entity, $file_field['field_name']);
                  if (!empty($files)) {
                    // Limit to the max number of value per field.
                    if (isset($this->options['number_indexed']) && $this->options['number_indexed'] != '0' && count($files) > $this->options['number_indexed']) {
                      $files = array_slice($files, 0, $this->options['number_indexed']);
                    }
                    foreach ($files as $file) {
                      if ($this->isFileIndexable($file, $item, $entityreference_field['field_name'])) {
                        $attachments = 'attachments_' . $file_field['field_name'];
                        if (isset($item->{$attachments})) {
                          $item->{$attachments} .= ' ' . $this->getFileContent($file);
                        }
                        else {
                          $item->{$attachments} = $this->getFileContent($file);
                        }
                      }
                    }
                  }
                }
              }
            }
          }
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function propertyInfo() {
    $ret = array();
    if ($this->index->item_type == 'file') {
      $ret['attachments_content'] = array(
        'label' => 'File content',
        'description' => 'File content',
        'type' => 'text',
      );
    }
    else {
      $fields = $this->getFileFields();
      foreach ($fields as $name => $field) {
        $ret['attachments_' . $name] = array(
          'label' => 'Attachment content: ' . $name,
          'description' => $name,
          'type' => 'text',
        );
      }
    }
    return $ret;
  }

  /**
   * {@inheritdoc}
   */
  protected function getFileFields() {
    $ret = array();
    foreach (field_info_fields() as $name => $field) {
      if ($field['type'] == 'file') {
        $ret[$name] = $field;
      }
    }
    return $ret;
  }

  /**
   * {@inheritdoc}
   */
  protected function getEntityReferenceFields() {
    $ret = array();
    foreach (field_info_fields() as $name => $field) {
      if ($field['type'] == 'entityreference') {
        $ret[$name] = $field;
      }
    }
    return $ret;
  }
}

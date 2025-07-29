<?php

namespace EventLayer\Data;

use \wpdb;

class EventRuleRepository
{
    protected $table;

    public function __construct() 
    {
        global $wpdb;
        $this->table = $wpdb->prefix . 'eventlayer_rules';
    }

    public function create($eventType, $triggers, $parameters)
    {
        global $wpdb;

        // sanitize data before inserting
        $eventType = sanitize_text_field($eventType);
        $triggers = sanitize_textarea_field($triggers);
        $parameters = sanitize_textarea_field($parameters);

        $insertData = array(
            'event_type' => $eventType,
            'triggers' => $triggers,
            'parameters' => $parameters
        );

        return $wpdb->insert($this->table, $insertData);
    }

    public function getAll()
    {
        global $wpdb;

        return $wpdb->get_results("SELECT * FROM $this->table");
    }

    public function getById($id)
    {
        global $wpdb;

        $sql = $wpdb->prepare("SELECT * FROM $this->table WHERE id = %d", intval($id));

        return $wpdb->get_row($sql);
    }

    public function update($id, $eventType, $triggers, $parameters)
    {
        global $wpdb;

        // sanitize data before updating
        $eventType = sanitize_text_field($eventType);
        $triggers = sanitize_textarea_field($triggers);
        $parameters = sanitize_textarea_field($parameters);

        $updateData = array(
            'event_type' => $eventType,
            'triggers' => $triggers,
            'parameters' => $parameters
        );

        $where = array('id' => intval($id));

        return $wpdb->update($this->table, $updateData, $where);
    }

    public function delete($id)
    {
        global $wpdb;

        $where = array('id' => intval($id));

        return $wpdb->delete($this->table, $where);
    }
}
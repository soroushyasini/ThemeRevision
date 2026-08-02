<?php

namespace Kanboard\Plugin\ThemeRevision\Controller;

use Kanboard\Controller\TaskModificationController;

/**
 * Keep Kanboard's task editor modal-first while providing a complete document
 * when the edit URL is opened directly or refreshed.
 */
class TaskEditController extends TaskModificationController
{
    protected function renderTemplate(array &$task, array &$params)
    {
        if (! empty($task['external_uri'])) {
            parent::renderTemplate($task, $params);
            return;
        }

        $this->response->html($this->helper->layout->task('task_modification/show', $params));
    }
}

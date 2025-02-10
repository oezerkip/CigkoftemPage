<?php

namespace library;

class View
{
    public function render(string $view, array $data, string $layout = 'default') {
        /**
         * Prüfen, ob es ein View-Template mit dem gegebenen Namen gibt
         */
        if (file_exists(Application::TEMPLATE_BASE_PATH.'views/'.$view.'.phtml')) {
            /**
             * Prüfen, ob es das gegebene Layout Verzeichnis gibt
             */
            if (is_dir(Application::TEMPLATE_BASE_PATH.'layouts/'.$layout)) {
                /**
                 * Gegebene Layout und View Dateien einbinden
                 */
                require Application::TEMPLATE_BASE_PATH.'layouts/'.$layout.DIRECTORY_SEPARATOR.'head.phtml';
                require Application::TEMPLATE_BASE_PATH.'views/'.$view.'.phtml';
                require Application::TEMPLATE_BASE_PATH.'layouts/'.$layout.DIRECTORY_SEPARATOR.'foot.phtml';
            }
        }
    }
}
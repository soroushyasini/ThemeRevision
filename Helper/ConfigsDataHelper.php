<?php

namespace Kanboard\Plugin\ThemeRevision\Helper;

use Kanboard\Core\Base;
use Kanboard\Plugin\ThemeRevision\Helper\BaseHelper;
use Kanboard\Plugin\ThemeRevision\Model\DefaultConfigsModel;

class ConfigsDataHelper extends BaseHelper
{
    private $defConfigsModel;

    public function __construct($c) {
        parent::__construct($c);

        $this->defConfigsModel = new DefaultConfigsModel();
    }

    public function loadConfigs(){
        $data = $this->configModel->get("ThemeRevision");
        
        if (!empty($data)){
            return json_decode($data, true);
        }
    }

    public function saveConfigs($configs){
        $this->configModel->save(array("ThemeRevision" => json_encode($configs)));
    }

    public function removeConfigs(){
        $this->configModel->remove("ThemeRevision");
    }

    public function loadColorDiffs(){
        $data = $this->configModel->get("ThemeRevision_ColorDiffs");

        if (!empty($data)){
            return json_decode($data, true);
        }
    }

    public function saveColorDiffs($colorDiffs){
        $this->configModel->save(array(
            "ThemeRevision_ColorDiffs" => json_encode($colorDiffs)
        ));
    }

    public function removeColorDiffs(){
        $this->configModel->remove("ThemeRevision_ColorDiffs");
    }

    public function calcColorDiffs($oldConfigs){
        $lightDiffs = $this->defConfigsModel->checkDiffColor('light_palette', $oldConfigs);
        $darkDiffs = $this->defConfigsModel->checkDiffColor('dark_palette', $oldConfigs);

        $saveList = array();
        
        if (!empty($lightDiffs)){
            $saveList['light_palette'] = $lightDiffs;
        }
        if (!empty($darkDiffs)){
            $saveList['dark_palette'] = $darkDiffs;
        }

        return $saveList;
    }

    public function calcOldConfigs($dbConfigs){
        $oldConfigs = array();
        // has data
        if (!empty($dbConfigs)){
            // old data
            if (empty($dbConfigs['version']) || $dbConfigs['version'] !=  $this->defConfigsModel->getVersion()){
                return $dbConfigs;
            }
            // new data
            return $oldConfigs;
        }  
        // no data in db and no config file
        if (empty($dbConfigs) && !file_exists('plugins/ThemeRevision/config.php')){
            return $oldConfigs;
        }
        // no data and has config file
        if (empty($dbConfigs) && file_exists('plugins/ThemeRevision/config.php')){
            $themeRevisionConfig;
            // load data
            if (file_exists('plugins/ThemeRevision/config-default.php')){
                require_once('plugins/ThemeRevision/config-default.php');
            }
            if (file_exists('plugins/ThemeRevision/config.php')){
                require_once('plugins/ThemeRevision/config.php');
            }
            if (!empty($themeRevisionConfig)){
                $themeRevisionConfig = $this->toV2Format($themeRevisionConfig);
            }
            return $themeRevisionConfig;
        }
        // other
        return $oldConfigs;
    }

    public function calcMergedConfigs($oldConfigs, $defConfigs){
        $mergedConfigs = $defConfigs;
        
        if (is_array($oldConfigs)){
            // Keep administrator customizations while adding any newly
            // introduced settings and palette entries from the defaults.
            $mergedConfigs = array_replace_recursive($defConfigs, $oldConfigs);
            $mergedConfigs['version'] = $defConfigs['version'];

            // Migrate the former low-contrast default while preserving any
            // opacity value that an administrator explicitly customized.
            if (isset($oldConfigs['task_footer_opacity']) && abs(floatval($oldConfigs['task_footer_opacity']) - 0.08) < 0.001){
                $mergedConfigs['task_footer_opacity'] = $defConfigs['task_footer_opacity'];
            }

            if (isset($oldConfigs['corner_radius']) && trim($oldConfigs['corner_radius']) === '4px'){
                $mergedConfigs['corner_radius'] = $defConfigs['corner_radius'];
            }

            $mergedConfigs = $this->migratePaletteDefaults($mergedConfigs, $oldConfigs, $defConfigs);
        }

        return $mergedConfigs;
    }

    private function migratePaletteDefaults(array $mergedConfigs, array $oldConfigs, array $defConfigs){
        $legacyDefaults = array(
            'light_palette' => array(
                'brand-prim' => '#3860f4',
                'brand-cont' => '#fff',
                'brand-secd' => '#dae4fe',
                'info-prim' => '#3860f4',
                'info-cont' => '#d9e7ff',
                'success-prim' => '#428b43',
                'success-cont' => '#c4f7c5',
                'greyscale-1' => '#02021d',
                'greyscale-2' => 'rgba(5, 12, 77, .15)',
                'greyscale-3' => '#e4e9f0',
                'greyscale-4' => '#f0f3f7',
                'greyscale-5' => '#f7f9fc',
                'task-grey-bdr' => '#dce0e7',
                'task-dark-grey-bg' => '#e7eaef',
                'task-dark-grey-bdr' => '#cfd2d9',
                'task-light-green-bg' => '#dcedc8',
                'task-light-green-bdr' => '#acdb82',
                'task-green-bg' => '#bdf4cb',
                'task-green-bdr' => '#87eda1',
            ),
            'dark_palette' => array(
                'brand-prim' => '#3860f4',
                'brand-cont' => '#e7f0ff',
                'brand-secd' => '#051646',
                'info-prim' => '#3860f4',
                'info-cont' => '#d4d7ff',
                'success-prim' => '#09590d',
                'success-cont' => '#82c483',
                'greyscale-1' => '#ccc',
                'greyscale-2' => 'rgba(255, 255, 255, .15)',
                'greyscale-3' => 'rgba(255, 255, 255, .043)',
                'greyscale-4' => '#27262c',
                'greyscale-5' => '#2b292f',
                'greyscale-6' => '#302e35',
                'task-grey-bg' => '#302e35',
                'task-grey-bdr' => 'rgba(255, 255, 255, .043)',
                'task-dark-grey-bg' => '#29272d',
                'task-dark-grey-bdr' => 'rgba(255, 255, 255, .07)',
                'task-light-green-bg' => '#528248',
                'task-light-green-bdr' => '#57934a',
                'task-green-bg' => '#054208',
                'task-green-bdr' => '#054e09',
            ),
        );

        foreach ($legacyDefaults as $palette => $colors){
            foreach ($colors as $key => $legacyValue){
                if (isset($oldConfigs[$palette][$key]) && strcasecmp(trim($oldConfigs[$palette][$key]), $legacyValue) === 0){
                    $mergedConfigs[$palette][$key] = $defConfigs[$palette][$key];
                }
            }
        }

        return $mergedConfigs;
    }

    public function getDefaultConfigs(){
        return $this->defConfigsModel->getDefaultConfigs();
    }

    public function getVersion(){
        return $this->defConfigsModel->getVersion();
    }

    private function toV2Format(array $config){
        $return = (object)[];
        foreach($config as $key => $value){
            $return->{strtr($key, " ", "_")} = $value;
        }
        return (array) $return;
    }

    public function getCandidates($configName){
        return $this->defConfigsModel->getCandidates($configName);
    }

    public function getCandidatesInTemplate($configName){
        $list = array();
        foreach($this->helper->configsDataHelper->getCandidates($configName) as $candidate){
            $list[$candidate] = t(ucwords($candidate));
        }
        return $list;
    }

    private function toV1Format(array $config){
        $return = (object)[];
        foreach($config as $key => $value){
            $return->{strtr($key, "_", " ")} = $value;
        }
        return (array) $return;
    }
}
